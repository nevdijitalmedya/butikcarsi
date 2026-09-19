<?php
/**
 * iyzico Payment Callback — ButikÇarşı
 */
require_once __DIR__ . '/../../config.php';

$token = $_POST['token'] ?? $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: /?payment=error&msg=token_missing');
    exit;
}

try {
    $result = IyzicoMarketplace::verifyPayment($token);
    $isSuccess = isset($result['status']) && $result['status'] === 'success'
                 && isset($result['paymentStatus']) && $result['paymentStatus'] === 'SUCCESS';

    // Find order by token
    $order = Database::query("SELECT * FROM orders WHERE iyzico_token = ?", [$token]);

    if ($order) {
        if ($isSuccess) {
            Database::execute("
                UPDATE orders SET status = 'paid', payment_status = 'paid',
                iyzico_payment_id = ?, iyzico_fraud_status = ?
                WHERE id = ?
            ", [$result['paymentId'] ?? '', $result['fraudStatus'] ?? '', $order['id']]);

            // Update all sub-orders
            Database::execute("UPDATE order_sub_orders SET status = 'processing' WHERE order_id = ?", [$order['id']]);

            // Notify all producers
            $subOrders = Database::queryAll("SELECT id FROM order_sub_orders WHERE order_id = ?", [$order['id']]);
            foreach ($subOrders as $so) {
                NotificationService::notifyProducerNewOrder($so['id']);
            }

            // Redirect to success page
            header('Location: /?payment=success&order=' . urlencode($order['order_number']));
        } else {
            Database::execute("UPDATE orders SET payment_status = 'failed' WHERE id = ?", [$order['id']]);
            header('Location: /?payment=failed&order=' . urlencode($order['order_number']));
        }
    } else {
        header('Location: /?payment=error&msg=order_not_found');
    }
} catch (Exception $e) {
    error_log("Payment callback error: " . $e->getMessage());
    header('Location: /?payment=error');
}
exit;
