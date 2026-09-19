<?php
/**
 * CommissionEngine.php â€” Commission calculation and sub-order splitting
 */

class CommissionEngine {

    /**
     * Split cart items into sub-orders by producer and calculate commissions
     * @param array $cartItems â€” [{product_id, quantity, unit_price, producer_id, ...}]
     * @return array of sub-order groups
     */
    public static function splitByProducer(array $cartItems): array {
        $groups = [];

        foreach ($cartItems as $item) {
            $producerId = $item['producer_id'];
            if (!isset($groups[$producerId])) {
                $producer = Database::query("SELECT id, brand_name, commission_rate, iyzico_sub_merchant_key FROM producers WHERE id = ?", [$producerId]);
                $groups[$producerId] = [
                    'producer_id' => $producerId,
                    'producer_name' => $producer['brand_name'] ?? 'Unknown',
                    'commission_rate' => (float)($producer['commission_rate'] ?? Config::setting('default_commission_rate', 10)),
                    'sub_merchant_key' => $producer['iyzico_sub_merchant_key'] ?? '',
                    'items' => [],
                    'subtotal' => 0,
                    'commission_amount' => 0,
                    'producer_earning' => 0,
                ];
            }

            $totalPrice = $item['unit_price'] * $item['quantity'];
            $item['total_price'] = $totalPrice;
            $groups[$producerId]['items'][] = $item;
            $groups[$producerId]['subtotal'] += $totalPrice;
        }

        // Calculate commission for each group
        foreach ($groups as &$group) {
            $group['commission_amount'] = round($group['subtotal'] * ($group['commission_rate'] / 100), 2);
            $group['producer_earning'] = round($group['subtotal'] - $group['commission_amount'], 2);
        }

        return array_values($groups);
    }

    /**
     * Create order with sub-orders in the database
     */
    public static function createOrder(array $orderData, array $subOrderGroups): int {
        Database::beginTransaction();
        try {
            // Calculate totals
            $subtotal = array_sum(array_column($subOrderGroups, 'subtotal'));
            $commissionTotal = array_sum(array_column($subOrderGroups, 'commission_amount'));
            $shippingTotal = 0; // Can be calculated per sub-order
            $grandTotal = $subtotal + $shippingTotal;

            // Generate order number
            $year = date('Y');
            $lastOrder = Database::query("SELECT MAX(id) as max_id FROM orders");
            $nextNum = ($lastOrder['max_id'] ?? 0) + 1;
            $orderNumber = "BC-{$year}-" . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

            // Insert main order
            Database::execute("
                INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, customer_note,
                shipping_address, shipping_city, shipping_district, shipping_zip, billing_address,
                subtotal, shipping_total, commission_total, discount_total, grand_total, currency, ip_address)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,0,?,?,?)
            ", [
                $orderNumber, $orderData['customer_name'], $orderData['customer_email'],
                $orderData['customer_phone'] ?? '', $orderData['customer_note'] ?? '',
                $orderData['shipping_address'], $orderData['shipping_city'] ?? '',
                $orderData['shipping_district'] ?? '', $orderData['shipping_zip'] ?? '',
                $orderData['billing_address'] ?? $orderData['shipping_address'],
                $subtotal, $shippingTotal, $commissionTotal, $grandTotal, 'TRY',
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            $orderId = (int)Database::lastInsertId();
            $subIndex = 'A';

            foreach ($subOrderGroups as $group) {
                $subOrderNumber = "{$orderNumber}-{$subIndex}";

                Database::execute("
                    INSERT INTO order_sub_orders (order_id, producer_id, sub_order_number, subtotal, shipping_cost, commission_amount, producer_earning)
                    VALUES (?,?,?,?,0,?,?)
                ", [$orderId, $group['producer_id'], $subOrderNumber, $group['subtotal'], $group['commission_amount'], $group['producer_earning']]);

                $subOrderId = (int)Database::lastInsertId();

                // Insert order items
                foreach ($group['items'] as $item) {
                    Database::execute("
                        INSERT INTO order_items (order_id, sub_order_id, product_id, variant_id, product_name, product_image, producer_name, quantity, unit_price, total_price, customization_text)
                        VALUES (?,?,?,?,?,?,?,?,?,?,?)
                    ", [
                        $orderId, $subOrderId, $item['product_id'], $item['variant_id'] ?? null,
                        $item['product_name'], $item['product_image'] ?? '', $group['producer_name'],
                        $item['quantity'], $item['unit_price'], $item['total_price'],
                        $item['customization_text'] ?? ''
                    ]);
                }

                // Create commission record
                Database::execute("
                    INSERT INTO commissions (sub_order_id, producer_id, order_total, commission_rate, commission_amount, producer_earning)
                    VALUES (?,?,?,?,?,?)
                ", [$subOrderId, $group['producer_id'], $group['subtotal'], $group['commission_rate'], $group['commission_amount'], $group['producer_earning']]);

                $subIndex++;
            }

            Database::commit();
            return $orderId;

        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    /**
     * Build iyzico basket items with sub-merchant splits
     */
    public static function buildIyzicoBasketItems(array $subOrderGroups): array {
        $basketItems = [];
        $itemIndex = 1;

        foreach ($subOrderGroups as $group) {
            foreach ($group['items'] as $item) {
                for ($q = 0; $q < $item['quantity']; $q++) {
                    $itemPrice = number_format($item['unit_price'], 2, '.', '');
                    $commissionPerItem = number_format($item['unit_price'] * ($group['commission_rate'] / 100), 2, '.', '');
                    $subMerchantPrice = number_format($item['unit_price'] - ($item['unit_price'] * ($group['commission_rate'] / 100)), 2, '.', '');

                    $basketItems[] = [
                        'id' => 'BI_' . $itemIndex,
                        'name' => mb_substr($item['product_name'], 0, 50),
                        'category1' => 'El YapÄ±mÄ±',
                        'category2' => 'Butik ÃœrÃ¼n',
                        'itemType' => 'PHYSICAL',
                        'price' => $itemPrice,
                        'subMerchantKey' => $group['sub_merchant_key'],
                        'subMerchantPrice' => $subMerchantPrice,
                    ];
                    $itemIndex++;
                }
            }
        }

        return $basketItems;
    }
}
