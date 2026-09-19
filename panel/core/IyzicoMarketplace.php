<?php
/**
 * IyzicoMarketplace.php â€” iyzico Marketplace API Integration
 * Handles sub-merchant registration, checkout with marketplace split, and approval
 */

class IyzicoMarketplace {
    private static string $apiKey = '';
    private static string $secretKey = '';
    private static string $baseUrl = '';

    private static function init(): void {
        if (empty(self::$apiKey)) {
            self::$apiKey = Config::setting('iyzico_api_key', '');
            self::$secretKey = Config::setting('iyzico_secret_key', '');
            self::$baseUrl = Config::setting('iyzico_base_url', 'https://sandbox-api.iyzipay.com');
        }
    }

    /**
     * Generate PKI String for iyzico signature
     */
    private static function generatePkiString(array $payload): string {
        $pki = "[";
        foreach ($payload as $key => $val) {
            if (is_array($val)) {
                if (array_keys($val) === range(0, count($val) - 1)) {
                    $subPki = "[";
                    foreach ($val as $subItem) {
                        $subPki .= is_array($subItem) ? self::generatePkiString($subItem) . "," : $subItem . ",";
                    }
                    $pki .= $key . "=" . rtrim($subPki, ",") . "],";
                } else {
                    $pki .= $key . "=" . self::generatePkiString($val) . ",";
                }
            } else {
                $pki .= $key . "=" . $val . ",";
            }
        }
        return rtrim($pki, ",") . "]";
    }

    /**
     * Make authenticated request to iyzico
     */
    private static function request(string $endpoint, array $payload): array {
        self::init();
        $jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $rnd = microtime(true) . rand(1000, 9999);
        $pkiString = self::generatePkiString($payload);
        $hashStr = self::$apiKey . $rnd . self::$secretKey . $pkiString;
        $signature = base64_encode(sha1($hashStr, true));
        $authHeader = "IYZWS " . self::$apiKey . ":" . $signature;

        $ch = curl_init(self::$baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-iyzi-rnd: ' . $rnd,
            'Authorization: ' . $authHeader
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('iyzico sunucusuna baÄŸlanÄ±lamadÄ±.');
        }

        return json_decode($response, true) ?: [];
    }

    /**
     * Register a sub-merchant (producer) with iyzico
     */
    public static function createSubMerchant(array $producer): array {
        $payload = [
            'locale' => 'tr',
            'conversationId' => 'BC_SM_' . $producer['id'],
            'subMerchantExternalId' => 'BC_' . $producer['id'],
            'subMerchantType' => $producer['iyzico_sub_merchant_type'] ?? 'PERSONAL',
            'address' => $producer['city'] ?? 'Turkey',
            'email' => $producer['email'],
            'gsmNumber' => $producer['phone'] ?? '+905000000000',
            'name' => $producer['brand_name'],
            'iban' => $producer['iban'] ?? '',
            'identityNumber' => $producer['identity_number'] ?? '11111111111',
            'currency' => 'TRY',
        ];

        if ($producer['iyzico_sub_merchant_type'] === 'PERSONAL') {
            $nameParts = explode(' ', $producer['owner_name'], 2);
            $payload['contactName'] = $nameParts[0] ?? $producer['owner_name'];
            $payload['contactSurname'] = $nameParts[1] ?? 'SoyadÄ±';
        } else {
            $payload['legalCompanyTitle'] = $producer['brand_name'];
            $payload['taxNumber'] = $producer['tax_number'] ?? '';
            $payload['taxOffice'] = $producer['tax_office'] ?? '';
        }

        return self::request('/onboarding/submerchant', $payload);
    }

    /**
     * Initialize checkout form with marketplace split
     * Each basket item includes subMerchantKey and subMerchantPrice
     */
    public static function initCheckout(array $order, array $subOrders, array $basketItems, string $callbackUrl): array {
        $buyer = [
            'id' => 'BY_' . time(),
            'name' => explode(' ', $order['customer_name'])[0] ?? 'MÃ¼ÅŸteri',
            'surname' => explode(' ', $order['customer_name'], 2)[1] ?? 'SoyadÄ±',
            'gsmNumber' => $order['customer_phone'] ?? '+905000000000',
            'email' => $order['customer_email'],
            'identityNumber' => '11111111111',
            'registrationAddress' => $order['shipping_address'],
            'ip' => $order['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'city' => $order['shipping_city'] ?? 'Istanbul',
            'country' => 'Turkey',
            'zipCode' => $order['shipping_zip'] ?? '34000',
        ];

        $formattedTotal = number_format($order['grand_total'], 2, '.', '');

        $payload = [
            'locale' => 'tr',
            'conversationId' => $order['order_number'],
            'price' => $formattedTotal,
            'paidPrice' => $formattedTotal,
            'currency' => 'TRY',
            'basketId' => $order['order_number'],
            'paymentGroup' => 'PRODUCT',
            'callbackUrl' => $callbackUrl,
            'enabledInstallments' => [1, 2, 3, 6, 9, 12],
            'buyer' => $buyer,
            'shippingAddress' => [
                'contactName' => $order['customer_name'],
                'city' => $order['shipping_city'] ?? 'Istanbul',
                'country' => 'Turkey',
                'address' => $order['shipping_address'],
                'zipCode' => $order['shipping_zip'] ?? '34000',
            ],
            'billingAddress' => [
                'contactName' => $order['customer_name'],
                'city' => $order['shipping_city'] ?? 'Istanbul',
                'country' => 'Turkey',
                'address' => $order['billing_address'] ?? $order['shipping_address'],
                'zipCode' => $order['shipping_zip'] ?? '34000',
            ],
            'basketItems' => $basketItems,
        ];

        return self::request('/payment/iyzipay/checkoutform/initialize/auth/ecom', $payload);
    }

    /**
     * Verify payment after callback
     */
    public static function verifyPayment(string $token): array {
        $payload = [
            'locale' => 'tr',
            'conversationId' => 'BC_VERIFY_' . time(),
            'token' => $token,
        ];

        return self::request('/payment/iyzipay/checkoutform/auth/ecom/detail', $payload);
    }

    /**
     * Approve payment for a sub-order (release escrow to sub-merchant)
     */
    public static function approvePayment(string $paymentTransactionId): array {
        $payload = [
            'locale' => 'tr',
            'conversationId' => 'BC_APPROVE_' . time(),
            'paymentTransactionId' => $paymentTransactionId,
        ];

        return self::request('/payment/iyzipos/item/approve', $payload);
    }

    /**
     * Disapprove (hold/dispute) payment
     */
    public static function disapprovePayment(string $paymentTransactionId): array {
        $payload = [
            'locale' => 'tr',
            'conversationId' => 'BC_DISAPPROVE_' . time(),
            'paymentTransactionId' => $paymentTransactionId,
        ];

        return self::request('/payment/iyzipos/item/disapprove', $payload);
    }
}
