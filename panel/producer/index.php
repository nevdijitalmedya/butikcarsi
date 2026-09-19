<?php
/**
 * Producer Dashboard â€” ButikÃ‡arÅŸÄ±
 */
require_once __DIR__ . '/../config.php';
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pid = Auth::producerId();

$totalProducts = Database::count("SELECT COUNT(*) FROM products WHERE producer_id = ? AND status = 'approved'", [$pid]);
$pendingProducts = Database::count("SELECT COUNT(*) FROM products WHERE producer_id = ? AND status = 'pending'", [$pid]);
$totalOrders = Database::count("SELECT COUNT(*) FROM order_sub_orders WHERE producer_id = ?", [$pid]);
$pendingOrders = Database::count("SELECT COUNT(*) FROM order_sub_orders WHERE producer_id = ? AND status IN ('pending','processing')", [$pid]);
$totalEarnings = Database::query("SELECT COALESCE(SUM(producer_earning), 0) as total FROM order_sub_orders WHERE producer_id = ? AND payout_status IN ('released','paid')", [$pid])['total'] ?? 0;
$heldEarnings = Database::query("SELECT COALESCE(SUM(producer_earning), 0) as total FROM order_sub_orders WHERE producer_id = ? AND payout_status = 'held'", [$pid])['total'] ?? 0;

$recentOrders = Database::queryAll("
    SELECT so.*, o.order_number, o.customer_name, o.created_at as order_date
    FROM order_sub_orders so
    JOIN orders o ON so.order_id = o.id
    WHERE so.producer_id = ?
    ORDER BY so.created_at DESC LIMIT 5
", [$pid]);
?>

<main class="admin-main">
    <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <h2 class="page-title">HoÅŸ Geldiniz, <?php echo htmlspecialchars($producerBrand); ?> ğŸ‘‹</h2>
    </div>

    <div class="admin-content">
        <?php if ($producerStatus === 'pending'): ?>
        <div class="pending-banner">
            â³ HesabÄ±nÄ±z henÃ¼z onay aÅŸamasÄ±ndadÄ±r. ÃœrÃ¼nlerinizi ekleyebilirsiniz ancak onaylanana kadar yayÄ±nlanmayacaktÄ±r.
        </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card stat-card--blue">
                <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg></div>
                <div class="stat-info"><span class="stat-value"><?php echo $totalProducts; ?></span><span class="stat-label">Aktif ÃœrÃ¼n</span></div>
            </div>
            <div class="stat-card stat-card--amber">
                <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div>
                <div class="stat-info"><span class="stat-value"><?php echo $pendingProducts; ?></span><span class="stat-label">Onay Bekleyen</span></div>
            </div>
            <div class="stat-card stat-card--green">
                <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg></div>
                <div class="stat-info"><span class="stat-value"><?php echo $totalOrders; ?></span><span class="stat-label">Toplam SipariÅŸ</span></div>
            </div>
            <div class="stat-card stat-card--purple">
                <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
                <div class="stat-info"><span class="stat-value">â‚º<?php echo number_format($totalEarnings, 2, ',', '.'); ?></span><span class="stat-label">Toplam KazanÃ§</span></div>
            </div>
            <div class="stat-card stat-card--rose">
                <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg></div>
                <div class="stat-info"><span class="stat-value">â‚º<?php echo number_format($heldEarnings, 2, ',', '.'); ?></span><span class="stat-label">Bekleyen Ã–deme</span></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Son SipariÅŸler</h3>
                <a href="orders.php" class="btn btn-sm btn-outline">TÃ¼mÃ¼nÃ¼ GÃ¶r</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentOrders)): ?>
                    <p class="text-muted text-center">HenÃ¼z sipariÅŸ yok. ÃœrÃ¼nlerinizi ekleyerek baÅŸlayÄ±n!</p>
                <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>SipariÅŸ</th><th>MÃ¼ÅŸteri</th><th>Tutar</th><th>KazanÃ§</th><th>Durum</th><th>Tarih</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentOrders as $o): ?>
                        <tr>
                            <td><a href="order-detail.php?id=<?php echo $o['id']; ?>" class="link"><?php echo htmlspecialchars($o['sub_order_number']); ?></a></td>
                            <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                            <td class="text-bold">â‚º<?php echo number_format($o['subtotal'], 2, ',', '.'); ?></td>
                            <td style="color:var(--success)">â‚º<?php echo number_format($o['producer_earning'], 2, ',', '.'); ?></td>
                            <td><span class="badge badge--<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                            <td class="text-muted"><?php echo date('d.m.Y', strtotime($o['order_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../admin/includes/footer.php'; ?>
