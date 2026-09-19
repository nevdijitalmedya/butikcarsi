<?php
/**
 * Admin Panel Header Include
 */
Auth::requireAdmin();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$adminName = $_SESSION['admin_full_name'] ?? 'Admin';

// Stats for header badges
$pendingProducers = Database::count("SELECT COUNT(*) FROM producers WHERE status = 'pending'");
$pendingProducts = Database::count("SELECT COUNT(*) FROM products WHERE status = 'pending'");
$newOrders = Database::count("SELECT COUNT(*) FROM orders WHERE status = 'paid' AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> — ButikÇarşı Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-layout">
