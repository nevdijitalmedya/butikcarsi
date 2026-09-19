<?php
/**
 * Public API: Categories
 */
require_once __DIR__ . '/../config.php';
Response::handleOptions();

$categories = Database::queryAll("
    SELECT c.*, 
           (SELECT COUNT(DISTINCT pc.product_id) FROM product_categories pc 
            JOIN products p ON pc.product_id = p.id 
            WHERE pc.category_id = c.id AND p.status = 'approved') as product_count
    FROM categories c
    WHERE c.is_active = 1
    ORDER BY c.sort_order ASC, c.name ASC
");

Response::success($categories);
