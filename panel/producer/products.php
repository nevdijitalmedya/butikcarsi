<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Ürünlerim';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pid = Auth::producerId();
$filterStatus = $_GET['status'] ?? 'all';
$search = trim($_GET['q'] ?? '');

$where = ["p.producer_id = ?"];
$params = [$pid];

if ($filterStatus !== 'all') {
    $where[] = "p.status = ?";
    $params[] = $filterStatus;
}
if ($search !== '') {
    $where[] = "(p.name LIKE ? OR p.slug LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSql = implode(' AND ', $where);

$products = Database::queryAll("
    SELECT p.*, 
           (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) as primary_image,
           (SELECT GROUP_CONCAT(c.name SEPARATOR ', ') FROM product_categories pc JOIN categories c ON pc.category_id = c.id WHERE pc.product_id = p.id) as category_names
    FROM products p
    WHERE $whereSql
    ORDER BY p.id DESC
", $params);

$statusCounts = [
    'all' => Database::count("SELECT COUNT(*) FROM products WHERE producer_id = ?", [$pid]),
    'approved' => Database::count("SELECT COUNT(*) FROM products WHERE producer_id = ? AND status = 'approved'", [$pid]),
    'pending' => Database::count("SELECT COUNT(*) FROM products WHERE producer_id = ? AND status = 'pending'", [$pid]),
    'draft' => Database::count("SELECT COUNT(*) FROM products WHERE producer_id = ? AND status = 'draft'", [$pid]),
    'rejected' => Database::count("SELECT COUNT(*) FROM products WHERE producer_id = ? AND status = 'rejected'", [$pid]),
];
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Ürünlerim</h2>
        <div class="topbar-actions">
            <a href="product-add.php" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Yeni Ürün Ekle
            </a>
        </div>
    </div>

    <div class="admin-content">
        <div class="filter-bar">
            <div class="filter-tabs">
                <a href="?status=all" class="filter-tab <?= $filterStatus === 'all' ? 'active' : '' ?>">Tümü (<?= $statusCounts['all'] ?>)</a>
                <a href="?status=approved" class="filter-tab <?= $filterStatus === 'approved' ? 'active' : '' ?>">Yayında (<?= $statusCounts['approved'] ?>)</a>
                <a href="?status=pending" class="filter-tab <?= $filterStatus === 'pending' ? 'active' : '' ?>">Onay Bekleyen (<?= $statusCounts['pending'] ?>)</a>
                <a href="?status=draft" class="filter-tab <?= $filterStatus === 'draft' ? 'active' : '' ?>">Taslak (<?= $statusCounts['draft'] ?>)</a>
                <a href="?status=rejected" class="filter-tab <?= $filterStatus === 'rejected' ? 'active' : '' ?>">Reddedilen (<?= $statusCounts['rejected'] ?>)</a>
            </div>
            <form method="GET" class="search-form">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
                <input type="text" name="q" placeholder="Ürün adı ara..." value="<?= htmlspecialchars($search) ?>" class="form-control">
            </form>
        </div>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="70">Görsel</th>
                            <th>Ürün Adı</th>
                            <th>Kategoriler</th>
                            <th>Fiyat</th>
                            <th>Stok</th>
                            <th>Durum</th>
                            <th>Kişiselleştirme</th>
                            <th width="120">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="8" class="empty-state">Henüz ürün bulunamadı. <a href="product-add.php">Hemen ilk ürününüzü ekleyin!</a></td></tr>
                        <?php else: ?>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td>
                                        <div class="product-thumb">
                                            <img src="<?= htmlspecialchars($p['primary_image'] ?: '../admin/assets/images/placeholder.svg') ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($p['name']) ?></strong>
                                        <div class="text-muted small"><?= htmlspecialchars($p['slug']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($p['category_names'] ?: '—') ?></td>
                                    <td>
                                        <?php if ($p['sale_price'] && $p['sale_price'] < $p['regular_price']): ?>
                                            <span class="text-danger">₺<?= number_format($p['sale_price'], 2, ',', '.') ?></span>
                                            <del class="text-muted small">₺<?= number_format($p['regular_price'], 2, ',', '.') ?></del>
                                        <?php else: ?>
                                            ₺<?= number_format($p['regular_price'], 2, ',', '.') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $p['stock_status'] === 'instock' ? 'success' : 'danger' ?>">
                                            <?= $p['stock_status'] === 'instock' ? ($p['stock_quantity'] !== null ? $p['stock_quantity'] . ' adet' : 'Stokta') : 'Tükendi' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $badgeMap = [
                                            'approved' => ['success', 'Yayında'],
                                            'pending'  => ['warning', 'Onay Bekliyor'],
                                            'draft'    => ['secondary', 'Taslak'],
                                            'rejected' => ['danger', 'Reddedildi'],
                                            'archived' => ['dark', 'Arşivlendi']
                                        ];
                                        $b = $badgeMap[$p['status']] ?? ['secondary', $p['status']];
                                        ?>
                                        <span class="badge badge-<?= $b[0] ?>"><?= $b[1] ?></span>
                                        <?php if ($p['status'] === 'rejected' && $p['admin_note']): ?>
                                            <div class="text-danger small mt-1"><?= htmlspecialchars($p['admin_note']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $p['customizable'] ? '<span class="badge badge-info">Kişiye Özel</span>' : '<span class="text-muted">—</span>' ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn-icon" title="Düzenle">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            </a>
                                            <a href="product-delete.php?id=<?= $p['id'] ?>" class="btn-icon text-danger" title="Sil" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </a>
                                        </div>
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
