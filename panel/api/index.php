<?php
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'ok',
    'platform' => 'ButikÇarşı REST API',
    'version' => '1.0.0',
    'endpoints' => [
        '/api/producers.php' => 'List and filter producers',
        '/api/products.php' => 'List and filter products',
        '/api/categories.php' => 'List active categories',
        '/api/banners.php' => 'Active homepage banners',
        '/api/iyzico/checkout.php' => 'Initialize marketplace checkout form',
        '/api/iyzico/callback.php' => 'Payment callback handler',
        '/api/iyzico/approval.php' => 'Release escrow funds'
    ]
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
