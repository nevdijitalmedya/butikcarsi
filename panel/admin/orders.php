<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Siparişler';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$filterStatus = $_GET['status'] ?? 'all';
$search = trim($_GET['q'] ?? '');

$where = ["1=1"];
$params = [];

if ($filterStatus !== 'all') {
    $where[] = "o.status = ?";
    $params[] = $filterStatus;
}
if ($search !== '') {
    $where[] = "(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSql = implode(' AND ', $where);

$orders = Database::queryAll("
    SELECT o.*, 
           (SELECT COUNT(*) FROM order_sub_orders WHERE order_id = o.id) as suborder_count,
           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
    FROM orders o
    WHERE $whereSql
    ORDER BY o.id DESC
", $params);
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Marketplace Siparişleri</h2>
    </div>

    <div class="admin-content">
        <div class="filter-bar">
            <div class="filter-tabs">
                <a href="?status=all" class="filter-tab <?= $filterStatus === 'all' ? 'active' : '' ?>">Tümü</a>
                <a href="?status=paid" class="filter-tab <?= $filterStatus === 'paid' ? 'active' : '' ?>">Ödendi (İşlemde)</a>
                <a href="?status=shipped" class="filter-tab <?= $filterStatus === 'shipped' ? 'active' : '' ?>">Kargoda</a>
                <a href="?status=delivered" class="filter-tab <?= $filterStatus === 'delivered' ? 'active' : '' ?>">Teslim Edildi</a>
                <a href="?status=cancelled" class="filter-tab <?= $filterStatus === 'cancelled' ? 'active' : '' ?>">İptal/İade</a>
            </div>
            <form method="GET" class="search-form">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
                <input type="text" name="q" placeholder="Sipariş no, müşteri adı..." value="<?= htmlspecialchars($search) ?>" class="form-control">
            </form>
        </div>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sipariş No</th>
                            <th>Müşteri</th>
                            <th>Tarih</th>
                            <th>Üretici Sayısı</th>
                            <th>Toplam Tutar</th>
                            <th>Platform Komisyonu</th>
                            <th>Ödeme Durumu</th>
                            <th>Sipariş Durumu</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="9" class="empty-state">Kayıtlı sipariş bulunamadı.</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($o['order_number']) ?></strong></td>
                                    <td>
                                        <div><?= htmlspecialchars($o['customer_name']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($o['customer_email']) ?></div>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                                    <td><span class="badge badge-secondary"><?= $o['suborder_count'] ?> Üretici</span></td>
                                    <td><strong>₺<?= number_format($o['grand_total'], 2, ',', '.') ?></strong></td>
                                    <td><span class="text-success font-weight-bold">₺<?= number_format($o['commission_total'], 2, ',', '.') ?></span></td>
                                    <td>
                                        <span class="badge badge-<?= $o['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                            <?= htmlspecialchars($o['payment_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?= htmlspecialchars($o['status']) ?></span>
                                    </td>
                                    <td>
                                        <a href="order-detail.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline">İncele</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
