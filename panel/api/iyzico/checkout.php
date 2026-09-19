<?php
/**
 * iyzico Marketplace Checkout Initialization
 * POST /api/iyzico/checkout.php
 */
require_once __DIR__ . '/../../config.php';
Response::handleOptions();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('POST method required.', 405);
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data || empty($data['items']) || empty($data['buyer'])) {
    Response::error('GeÃ§ersiz sepet verisi.', 400);
}

try {
    // Build cart items with producer info
    $cartItems = [];
    foreach ($data['items'] as $item) {
        $product = Database::query("
            SELECT p.*, pr.id as producer_id, pr.brand_name, pr.commission_rate, pr.iyzico_sub_merchant_key
            FROM products p
            JOIN producers pr ON p.producer_id = pr.id
            WHERE p.id = ? AND p.status = 'approved' AND pr.status = 'approved'
        ", [$item['product_id']]);

        if (!$product) {
            Response::error("ÃœrÃ¼n bulunamadÄ±: " . ($item['product_name'] ?? ''), 400);
        }

        $cartItems[] = [
            'product_id' => $product['id'],
            'product_name' => $product['name'],
            'product_image' => $item['product_image'] ?? '',
            'producer_id' => $product['producer_id'],
            'unit_price' => (float)($product['sale_price'] ?? $product['regular_price']),
            'quantity' => (int)($item['quantity'] ?? 1),
            'variant_id' => $item['variant_id'] ?? null,
            'customization_text' => $item['customization_text'] ?? '',
        ];
    }

    // Split by producer
    $subOrderGroups = CommissionEngine::splitByProducer($cartItems);

    // Create order
    $orderData = [
        'customer_name' => $data['buyer']['name'] ?? 'MÃ¼ÅŸteri',
        'customer_email' => $data['buyer']['email'] ?? '',
        'customer_phone' => $data['buyer']['phone'] ?? '',
        'customer_note' => $data['buyer']['note'] ?? '',
        'shipping_address' => $data['buyer']['address'] ?? '',
        'shipping_city' => $data['buyer']['city'] ?? '',
        'shipping_district' => $data['buyer']['district'] ?? '',
        'shipping_zip' => $data['buyer']['zip'] ?? '34000',
        'billing_address' => $data['buyer']['billing_address'] ?? $data['buyer']['address'] ?? '',
    ];

    $orderId = CommissionEngine::createOrder($orderData, $subOrderGroups);
    $order = Database::query("SELECT * FROM orders WHERE id = ?", [$orderId]);

    // Build iyzico basket items with sub-merchant keys
    $basketItems = CommissionEngine::buildIyzicoBasketItems($subOrderGroups);

    // Callback URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'butikcarsi.com';
    $callbackUrl = $protocol . '://' . $host . '/panel/api/iyzico/callback.php';

    // Initialize iyzico checkout
    $result = IyzicoMarketplace::initCheckout($order, $subOrderGroups, $basketItems, $callbackUrl);

    if (isset($result['status']) && $result['status'] === 'success') {
        // Save token to order
        Database::execute("UPDATE orders SET iyzico_token = ?, iyzico_conversation_id = ? WHERE id = ?",
            [$result['token'] ?? '', $order['order_number'], $orderId]);

        Response::success([
            'order_number' => $order['order_number'],
            'checkoutFormContent' => $result['checkoutFormContent'] ?? '',
            'paymentPageUrl' => $result['paymentPageUrl'] ?? '',
            'token' => $result['token'] ?? '',
        ]);
    } else {
        // Cleanup: delete order on payment init failure
        Database::execute("DELETE FROM orders WHERE id = ?", [$orderId]);
        Response::error($result['errorMessage'] ?? 'Ã–deme formu oluÅŸturulamadÄ±.', 500);
    }

} catch (Exception $e) {
    Response::error('SipariÅŸ oluÅŸturulurken hata: ' . $e->getMessage(), 500);
}
