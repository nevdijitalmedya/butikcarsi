<?php
/**
 * Public API: Products
 * GET /api/products.php                     — List approved products
 * GET /api/products.php?slug=xxx            — Single product by slug
 * GET /api/products.php?category=slug       — Filter by category
 * GET /api/products.php?producer=slug       — Filter by producer
 * GET /api/products.php?featured=1          — Featured only
 * GET /api/products.php?q=search            — Search
 */
require_once __DIR__ . '/../config.php';
Response::handleOptions();

$slug = $_GET['slug'] ?? '';
$categorySlug = $_GET['category'] ?? '';
$producerSlug = $_GET['producer'] ?? '';
$featured = $_GET['featured'] ?? '';
$search = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'newest';
$limit = min((int)($_GET['limit'] ?? 24), 100);
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

if ($slug) {
    // Single product
    $product = Database::query("
        SELECT p.*, pr.brand_name as producer_name, pr.slug as producer_slug, 
               pr.instagram_handle as producer_instagram, pr.logo_url as producer_logo
        FROM products p
        JOIN producers pr ON p.producer_id = pr.id
        WHERE p.slug = ? AND p.status = 'approved' AND pr.status = 'approved'
    ", [$slug]);

    if (!$product) {
        Response::error('Ürün bulunamadı.', 404);
    }

    // Images
    $product['images'] = Database::queryAll(
        "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC",
        [$product['id']]
    );

    // Categories
    $product['categories'] = Database::queryAll("
        SELECT c.id, c.name, c.slug FROM categories c
        JOIN product_categories pc ON c.id = pc.category_id
        WHERE pc.product_id = ?
    ", [$product['id']]);

    // Variants
    $product['variants'] = Database::queryAll(
        "SELECT * FROM product_variants WHERE product_id = ? AND is_active = 1",
        [$product['id']]
    );

    // Reviews
    $product['reviews'] = Database::queryAll("
        SELECT customer_name, rating, comment, created_at 
        FROM reviews WHERE product_id = ? AND is_approved = 1 
        ORDER BY created_at DESC LIMIT 10
    ", [$product['id']]);

    Response::success($product);
}

// Build query
$joins = "JOIN producers pr ON p.producer_id = pr.id";
$where = "WHERE p.status = 'approved' AND pr.status = 'approved'";
$params = [];

if ($categorySlug) {
    $joins .= " JOIN product_categories pc ON p.id = pc.product_id JOIN categories c ON pc.category_id = c.id";
    $where .= " AND c.slug = ?";
    $params[] = $categorySlug;
}

if ($producerSlug) {
    $where .= " AND pr.slug = ?";
    $params[] = $producerSlug;
}

if ($featured) {
    $where .= " AND p.is_featured = 1";
}

if ($search) {
    $where .= " AND (p.name LIKE ? OR p.short_description LIKE ? OR pr.brand_name LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term]);
}

$orderBy = match($sort) {
    'price_asc' => 'COALESCE(p.sale_price, p.regular_price) ASC',
    'price_desc' => 'COALESCE(p.sale_price, p.regular_price) DESC',
    'popular' => 'p.sort_order ASC',
    default => 'p.created_at DESC'
};

$total = Database::count("SELECT COUNT(DISTINCT p.id) FROM products p $joins $where", $params);

$products = Database::queryAll("
    SELECT DISTINCT p.id, p.slug, p.name, p.short_description, p.regular_price, p.sale_price, 
           p.stock_status, p.is_featured, p.customizable, p.production_time,
           pr.brand_name as producer_name, pr.slug as producer_slug, pr.instagram_handle as producer_instagram,
           (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image,
           (SELECT image_url_webp FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image_webp,
           (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND is_approved = 1) as avg_rating,
           (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND is_approved = 1) as review_count
    FROM products p $joins $where
    ORDER BY p.is_featured DESC, $orderBy
    LIMIT $limit OFFSET $offset
", $params);

Response::paginated($products, $total, $page, $limit);
