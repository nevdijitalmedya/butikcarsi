<?php
/**
 * Public API: Producers
 * GET /api/producers.php              â€” List approved producers
 * GET /api/producers.php?slug=xxx     â€” Single producer by slug
 * GET /api/producers.php?featured=1   â€” Featured producers only
 */
require_once __DIR__ . '/../config.php';
Response::handleOptions();

$slug = $_GET['slug'] ?? '';
$featured = $_GET['featured'] ?? '';
$limit = min((int)($_GET['limit'] ?? 50), 100);
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

if ($slug) {
    // Single producer
    $producer = Database::query("
        SELECT id, slug, brand_name, owner_name, description, logo_url, cover_image_url,
               city, district, instagram_url, instagram_handle, tiktok_url, website_url,
               commission_rate, is_featured, created_at
        FROM producers 
        WHERE slug = ? AND status = 'approved'
    ", [$slug]);

    if (!$producer) {
        Response::error('Ãœretici bulunamadÄ±.', 404);
    }

    // Get producer products
    $products = Database::queryAll("
        SELECT p.*, 
               (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image,
               (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND is_approved = 1) as avg_rating,
               (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND is_approved = 1) as review_count
        FROM products p
        WHERE p.producer_id = ? AND p.status = 'approved'
        ORDER BY p.is_featured DESC, p.sort_order ASC, p.created_at DESC
    ", [$producer['id']]);

    $producer['products'] = $products;
    Response::success($producer);
}

// List producers
$whereExtra = "";
if ($featured) {
    $whereExtra = " AND is_featured = 1";
}

$total = Database::count("SELECT COUNT(*) FROM producers WHERE status = 'approved' $whereExtra");
$producers = Database::queryAll("
    SELECT id, slug, brand_name, owner_name, description, logo_url, cover_image_url,
           city, instagram_url, instagram_handle, is_featured,
           (SELECT COUNT(*) FROM products WHERE producer_id = producers.id AND status = 'approved') as product_count
    FROM producers 
    WHERE status = 'approved' $whereExtra
    ORDER BY is_featured DESC, sort_order ASC, brand_name ASC
    LIMIT $limit OFFSET $offset
", []);

Response::paginated($producers, $total, $page, $limit);
