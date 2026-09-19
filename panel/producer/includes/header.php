<?php
Auth::requireProducer();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$producerBrand = $_SESSION['producer_brand'] ?? 'Butik';
$producerStatus = $_SESSION['producer_status'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> â€” Ãœretici Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../admin/assets/css/admin.css">
    <style>
        .admin-sidebar { --sidebar-accent: #f59e0b; }
        .sidebar-link.active { color: var(--sidebar-accent); }
        .sidebar-link.active::before { background: var(--sidebar-accent); }
        .sidebar-user-avatar { background: linear-gradient(135deg, #f59e0b, #ef4444) !important; }
        .pending-banner { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); padding: 0.75rem 1rem; border-radius: var(--radius-sm); color: #fbbf24; font-size: 0.85rem; margin-bottom: 1rem; }
    </style>
</head>
<body class="admin-body">
    <div class="admin-layout">
