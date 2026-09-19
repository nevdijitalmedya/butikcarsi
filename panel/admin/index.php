<?php
/**
 * Admin Dashboard — ButikÇarşı
 */
require_once __DIR__ . '/../config.php';
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Dashboard Stats
$totalProducers = Database::count("SELECT COUNT(*) FROM producers WHERE status = 'approved'");
$totalProducts = Database::count("SELECT COUNT(*) FROM products WHERE status = 'approved'");
$totalOrders = Database::count("SELECT COUNT(*) FROM orders WHERE payment_status = 'paid'");
$totalRevenue = Database::query("SELECT COALESCE(SUM(grand_total), 0) as total FROM orders WHERE payment_status = 'paid'")['total'] ?? 0;
$totalCommission = Database::query("SELECT COALESCE(SUM(commission_total), 0) as total FROM orders WHERE payment_status = 'paid'")['total'] ?? 0;

// Recent orders
$recentOrders = Database::queryAll("
    SELECT o.*, 
           (SELECT COUNT(*) FROM order_sub_orders WHERE order_id = o.id) as sub_order_count
    FROM orders o 
    ORDER BY o.created_at DESC 
    LIMIT 10
");

// Pending approvals
$pendingProducersList = Database::queryAll("
    SELECT * FROM producers WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5
");

$pendingProductsList = Database::queryAll("
    SELECT p.*, pr.brand_name as producer_name 
    FROM products p 
    JOIN producers pr ON p.producer_id = pr.id 
    WHERE p.status = 'pending' 
    ORDER BY p.created_at DESC LIMIT 5
");
?>

<main class="admin-main">
    <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <h2 class="page-title">Dashboard</h2>
        <div class="topbar-right">
            <span class="topbar-date"><?php echo strftime('%d %B %Y'); ?></span>
        </div>
    </div>

    <div class="admin-content">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-card--purple">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value"><?php echo number_format($totalProducers); ?></span>
                    <span class="stat-label">Aktif Üretici</span>
                </div>
            </div>
            <div class="stat-card stat-card--blue">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value"><?php echo number_format($totalProducts); ?></span>
                    <span class="stat-label">Aktif Ürün</span>
                </div>
            </div>
            <div class="stat-card stat-card--green">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value"><?php echo number_format($totalOrders); ?></span>
                    <span class="stat-label">Toplam Sipariş</span>
                </div>
            </div>
            <div class="stat-card stat-card--amber">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value">₺<?php echo number_format($totalRevenue, 2, ',', '.'); ?></span>
                    <span class="stat-label">Toplam Ciro</span>
                </div>
            </div>
            <div class="stat-card stat-card--rose">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="5" x2="5" y2="19"></line><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg>
                </div>
                <div class="stat-info">
                    <span class="stat-value">₺<?php echo number_format($totalCommission, 2, ',', '.'); ?></span>
                    <span class="stat-label">Komisyon Geliri</span>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Pending Approvals -->
            <?php if (!empty($pendingProducersList)): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Onay Bekleyen Üreticiler</h3>
                    <a href="producers.php?status=pending" class="btn btn-sm btn-outline">Tümünü Gör</a>
                </div>
                <div class="card-body">
                    <div class="approval-list">
                        <?php foreach ($pendingProducersList as $p): ?>
                        <div class="approval-item">
                            <div class="approval-avatar"><?php echo mb_strtoupper(mb_substr($p['brand_name'], 0, 1)); ?></div>
                            <div class="approval-info">
                                <strong><?php echo htmlspecialchars($p['brand_name']); ?></strong>
                                <span><?php echo htmlspecialchars($p['owner_name']); ?> Â· <?php echo htmlspecialchars($p['city'] ?? ''); ?></span>
                            </div>
                            <a href="producer-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">İncele</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($pendingProductsList)): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Onay Bekleyen Ürünler</h3>
                    <a href="products.php?status=pending" class="btn btn-sm btn-outline">Tümünü Gör</a>
                </div>
                <div class="card-body">
                    <div class="approval-list">
                        <?php foreach ($pendingProductsList as $p): ?>
                        <div class="approval-item">
                            <div class="approval-info">
                                <strong><?php echo htmlspecialchars($p['name']); ?></strong>
                                <span><?php echo htmlspecialchars($p['producer_name']); ?> Â· ₺<?php echo number_format($p['regular_price'], 2, ',', '.'); ?></span>
                            </div>
                            <a href="product-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">İncele</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Recent Orders -->
            <div class="card card--full">
                <div class="card-header">
                    <h3>Son Siparişler</h3>
                    <a href="orders.php" class="btn btn-sm btn-outline">Tümünü Gör</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentOrders)): ?>
                        <p class="text-muted">Henüz sipariş bulunmuyor.</p>
                    <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sipariş No</th>
                                <th>Müşteri</th>
                                <th>Tutar</th>
                                <th>Durum</th>
                                <th>Ödeme</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><a href="order-detail.php?id=<?php echo $order['id']; ?>" class="link"><?php echo htmlspecialchars($order['order_number']); ?></a></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td class="text-bold">₺<?php echo number_format($order['grand_total'], 2, ',', '.'); ?></td>
                                <td><span class="badge badge--<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                <td><span class="badge badge--<?php echo $order['payment_status']; ?>"><?php echo ucfirst($order['payment_status']); ?></span></td>
                                <td class="text-muted"><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
