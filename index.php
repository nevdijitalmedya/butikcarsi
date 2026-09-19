<?php
// Root router fallback
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
if (strpos($requestUri, '/panel') === 0) {
    header("Location: /panel/");
    exit;
}

$distIndex = __DIR__ . '/frontend/dist/index.html';
if (file_exists($distIndex)) {
    readfile($distIndex);
    exit;
}

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>ButikÇarşı</title></head><body style='font-family:sans-serif;text-align:center;padding:50px;'><h1>ButikÇarşı</h1><p><a href='/panel/admin/login.php'>Admin Paneli</a> | <a href='/panel/producer/login.php'>Üretici Paneli</a></p></body></html>";
