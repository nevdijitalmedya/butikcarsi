<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Siparişlerim';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pid = Auth::producerId();
$filterStatus = $_GET['status'] ?? 'all';

$where = ["so.producer_id = ?"];
$params = [$pid];

if ($filterStatus !== 'all') {
    $where[] = "so.status = ?";
    $params[] = $filterStatus;
}
$whereSql = implode(' AND ', $where);

$orders = Database::queryAll("
    SELECT so.*, o.order_number, o.customer_name, o.customer_phone, o.customer_email, o.shipping_city, o.created_at as main_order_date,
           (SELECT COUNT(*) FROM order_items WHERE sub_order_id = so.id) as item_count
    FROM order_sub_orders so
    JOIN orders o ON so.order_id = o.id
    WHERE $whereSql
    ORDER BY so.id DESC
", $params);
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Siparişlerim</h2>
    </div>

    <div class="admin-content">
        <div class="filter-bar">
            <div class="filter-tabs">
                <a href="?status=all" class="filter-tab <?= $filterStatus === 'all' ? 'active' : '' ?>">Tümü</a>
                <a href="?status=pending" class="filter-tab <?= $filterStatus === 'pending' ? 'active' : '' ?>">Hazırlanacak</a>
                <a href="?status=processing" class="filter-tab <?= $filterStatus === 'processing' ? 'active' : '' ?>">İşleniyor</a>
                <a href="?status=shipped" class="filter-tab <?= $filterStatus === 'shipped' ? 'active' : '' ?>">Kargoda</a>
                <a href="?status=delivered" class="filter-tab <?= $filterStatus === 'delivered' ? 'active' : '' ?>">Teslim Edildi</a>
            </div>
        </div>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Alt Sipariş No</th>
                            <th>Müşteri</th>
                            <th>Şehir</th>
                            <th>Ürün Sayısı</th>
                            <th>Toplam</th>
                            <th>Kazancınız (%90)</th>
                            <th>Kargo Takip</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="9" class="empty-state">Henüz bir sipariş bulunmuyor.</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($o['sub_order_number']) ?></strong></td>
                                    <td>
                                        <div><?= htmlspecialchars($o['customer_name']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($o['customer_phone'] ?? '') ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($o['shipping_city'] ?? '—') ?></td>
                                    <td><?= $o['item_count'] ?> adet</td>
                                    <td>₺<?= number_format($o['subtotal'], 2, ',', '.') ?></td>
                                    <td><strong class="text-success">₺<?= number_format($o['producer_earning'], 2, ',', '.') ?></strong></td>
                                    <td>
                                        <?php if ($o['tracking_number']): ?>
                                            <span class="badge badge-info"><?= htmlspecialchars($o['shipping_provider']) ?>: <?= htmlspecialchars($o['tracking_number']) ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Kargo Girilmedi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $map = [
                                            'pending' => ['warning', 'Bekliyor'],
                                            'processing' => ['info', 'Hazırlanıyor'],
                                            'shipped' => ['primary', 'Kargoya Verildi'],
                                            'delivered' => ['success', 'Teslim Edildi'],
                                            'cancelled' => ['danger', 'İptal']
                                        ];
                                        $st = $map[$o['status']] ?? ['secondary', $o['status']];
                                        ?>
                                        <span class="badge badge-<?= $st[0] ?>"><?= $st[1] ?></span>
                                    </td>
                                    <td>
                                        <a href="order-detail.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline">Detay & Kargo</a>
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
