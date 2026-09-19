<?php
/**
 * Public API: Banners
 */
require_once __DIR__ . '/../config.php';
Response::handleOptions();

$position = $_GET['position'] ?? '';
$where = "WHERE b.is_active = 1 AND (b.starts_at IS NULL OR b.starts_at <= NOW()) AND (b.ends_at IS NULL OR b.ends_at >= NOW())";
$params = [];

if ($position) {
    $where .= " AND b.position = ?";
    $params[] = $position;
}

$banners = Database::queryAll("
    SELECT b.*, p.brand_name as producer_name, p.slug as producer_slug
    FROM banners b
    LEFT JOIN producers p ON b.producer_id = p.id
    $where
    ORDER BY b.sort_order ASC
", $params);

Response::success($banners);
