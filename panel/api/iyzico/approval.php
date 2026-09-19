<?php
/**
 * Delivery Approval â€” Release escrow payment to producer
 * POST /api/iyzico/approval.php
 */
require_once __DIR__ . '/../../config.php';
Response::handleOptions();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('POST method required.', 405);
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
$subOrderId = (int)($data['sub_order_id'] ?? 0);

if (!$subOrderId) {
    Response::error('sub_order_id gereklidir.', 400);
}

$subOrder = Database::query("SELECT * FROM order_sub_orders WHERE id = ?", [$subOrderId]);
if (!$subOrder) {
    Response::error('Alt sipariÅŸ bulunamadÄ±.', 404);
}

if ($subOrder['payout_status'] !== 'held') {
    Response::error('Bu sipariÅŸ zaten onaylanmÄ±ÅŸ veya iptal edilmiÅŸ.', 400);
}

try {
    // In a real marketplace, you would call iyzico approval API here
    // IyzicoMarketplace::approvePayment($paymentTransactionId);

    Database::execute("
        UPDATE order_sub_orders SET payout_status = 'released', payout_released_at = CURRENT_TIMESTAMP, 
        status = 'delivered', delivered_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ", [$subOrderId]);

    // Update commission status
    Database::execute("UPDATE commissions SET status = 'confirmed' WHERE sub_order_id = ?", [$subOrderId]);

    // Notify producer
    NotificationService::notifyProducerPayout(
        $subOrder['producer_id'],
        $subOrder['producer_earning'],
        ''
    );

    // Check if all sub-orders are delivered
    $pendingSubOrders = Database::count("
        SELECT COUNT(*) FROM order_sub_orders WHERE order_id = ? AND status != 'delivered'
    ", [$subOrder['order_id']]);

    if ($pendingSubOrders === 0) {
        Database::execute("UPDATE orders SET status = 'delivered' WHERE id = ?", [$subOrder['order_id']]);
    }

    Response::success(null, 'Teslimat onaylandÄ±, Ã¶deme serbest bÄ±rakÄ±ldÄ±.');

} catch (Exception $e) {
    Response::error('Onay hatasÄ±: ' . $e->getMessage(), 500);
}
