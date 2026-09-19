<?php
/**
 * Products Management — ButikÇarşı Admin
 */
require_once __DIR__ . '/../config.php';
$pageTitle = 'Ürünler';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Handle quick actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        switch ($action) {
            case 'approve':
                Database::execute("UPDATE products SET status = 'approved' WHERE id = ?", [$id]);
                break;
            case 'reject':
                $note = trim($_POST['note'] ?? '');
                Database::execute("UPDATE products SET status = 'rejected', admin_note = ? WHERE id = ?", [$note, $id]);
                break;
            case 'feature':
                Database::execute("UPDATE products SET is_featured = NOT is_featured WHERE id = ?", [$id]);
                break;
            case 'delete':
                Database::execute("DELETE FROM products WHERE id = ?", [$id]);
                break;
        }
        header('Location: products.php' . (!empty($_GET['status']) ? '?status='.$_GET['status'] : ''));
        exit;
    }
}

$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['q'] ?? '');
$where = "WHERE 1=1";
$params = [];

if ($statusFilter) { $where .= " AND p.status = ?"; $params[] = $statusFilter; }
if ($search) {
    $where .= " AND (p.name LIKE ? OR pr.brand_name LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%"]);
}

$total = Database::count("SELECT COUNT(*) FROM products p JOIN producers pr ON p.producer_id = pr.id $where", $params);
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$products = Database::queryAll("
    SELECT p.*, pr.brand_name as producer_name, pr.slug as producer_slug,
           (SELECT image_url_webp FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as thumb
    FROM products p
    JOIN producers pr ON p.producer_id = pr.id
    $where
    ORDER BY p.created_at DESC
    LIMIT $perPage OFFSET $offset
", $params);

$statusCounts = Database::queryAll("SELECT status, COUNT(*) as cnt FROM products GROUP BY status");
$statusMap = [];
foreach ($statusCounts as $sc) { $statusMap[$sc['status']] = $sc['cnt']; }
?>

<main class="admin-main">
    <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <h2 class="page-title">Ürünler</h2>
    </div>
    <div class="admin-content">
        <div class="filter-bar">
            <div class="filter-tabs">
                <a href="products.php" class="filter-tab <?php echo !$statusFilter ? 'active' : ''; ?>">Tümü <span class="filter-count"><?php echo $total; ?></span></a>
                <?php foreach (['pending'=>'Bekleyen','approved'=>'Onaylı','draft'=>'Taslak','rejected'=>'Reddedilen'] as $k=>$l): ?>
                <a href="products.php?status=<?php echo $k; ?>" class="filter-tab <?php echo $statusFilter===$k?'active':''; ?>"><?php echo $l; ?> <span class="filter-count"><?php echo $statusMap[$k]??0; ?></span></a>
                <?php endforeach; ?>
            </div>
            <form method="GET" class="search-form">
                <?php if($statusFilter): ?><input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>"><?php endif; ?>
                <input type="text" name="q" placeholder="Ürün veya üretici ara..." value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                <button type="submit" class="btn btn-sm btn-outline">Ara</button>
            </form>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if(empty($products)): ?>
                    <p class="text-muted text-center">Ürün bulunamadı.</p>
                <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>Ürün</th><th>Üretici</th><th>Fiyat</th><th>Stok</th><th>Durum</th><th>Tarih</th><th>İşlem</th></tr></thead>
                    <tbody>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td>
                            <div class="cell-user">
                                <?php if($p['thumb']): ?>
                                <img src="../uploads/products/<?php echo basename($p['thumb']); ?>" style="width:40px;height:40px;border-radius:6px;object-fit:cover;">
                                <?php else: ?>
                                <div class="cell-avatar" style="background:#374151;">ğŸ“¦</div>
                                <?php endif; ?>
                                <div>
                                    <span class="text-bold"><?php echo htmlspecialchars(mb_substr($p['name'],0,40)); ?></span>
                                    <span class="text-sm text-muted"><?php echo htmlspecialchars($p['slug']); ?></span>
                                </div>
                            </div>
                        </td>
                        <td><a href="producer-edit.php?id=<?php echo $p['producer_id']; ?>" class="link"><?php echo htmlspecialchars($p['producer_name']); ?></a></td>
                        <td>
                            <?php if($p['sale_price']): ?>
                                <span style="text-decoration:line-through;color:var(--text-dim);">₺<?php echo number_format($p['regular_price'],2,',','.'); ?></span><br>
                                <span class="text-bold" style="color:var(--success);">₺<?php echo number_format($p['sale_price'],2,',','.'); ?></span>
                            <?php else: ?>
                                <span class="text-bold">₺<?php echo number_format($p['regular_price'],2,',','.'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge--<?php echo $p['stock_status']==='instock'?'approved':'rejected'; ?>"><?php echo $p['stock_status']; ?></span></td>
                        <td><span class="badge badge--<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                        <td class="text-muted text-sm"><?php echo date('d.m.Y', strtotime($p['created_at'])); ?></td>
                        <td>
                            <div class="action-group">
                                <?php if($p['status']==='pending'): ?>
                                <form method="POST" style="display:inline"><input type="hidden" name="id" value="<?php echo $p['id']; ?>"><input type="hidden" name="action" value="approve"><button class="btn btn-xs btn-success" title="Onayla">Onayla</button></form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline"><input type="hidden" name="id" value="<?php echo $p['id']; ?>"><input type="hidden" name="action" value="feature"><button class="btn btn-xs <?php echo $p['is_featured']?'btn-amber':'btn-outline'; ?>" title="Öne Çıkar">Öne Çıkar</button></form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
