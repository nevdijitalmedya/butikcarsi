<?php
/**
 * Producers Management — ButikÇarşı Admin
 */
require_once __DIR__ . '/../config.php';
$pageTitle = 'Üreticiler';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Handle quick actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        switch ($action) {
            case 'approve':
                Database::execute("UPDATE producers SET status = 'approved', approved_at = CURRENT_TIMESTAMP WHERE id = ?", [$id]);
                break;
            case 'reject':
                $note = trim($_POST['note'] ?? '');
                Database::execute("UPDATE producers SET status = 'rejected', approval_note = ? WHERE id = ?", [$note, $id]);
                break;
            case 'suspend':
                Database::execute("UPDATE producers SET status = 'suspended' WHERE id = ?", [$id]);
                break;
            case 'delete':
                Database::execute("DELETE FROM producers WHERE id = ?", [$id]);
                break;
            case 'feature':
                Database::execute("UPDATE producers SET is_featured = NOT is_featured WHERE id = ?", [$id]);
                break;
        }
        header('Location: producers.php' . ($_GET['status'] ?? '' ? '?status=' . $_GET['status'] : ''));
        exit;
    }
}

// Filters
$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['q'] ?? '');

$where = "WHERE 1=1";
$params = [];

if ($statusFilter) {
    $where .= " AND p.status = ?";
    $params[] = $statusFilter;
}
if ($search) {
    $where .= " AND (p.brand_name LIKE ? OR p.owner_name LIKE ? OR p.email LIKE ? OR p.instagram_handle LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

$total = Database::count("SELECT COUNT(*) FROM producers p $where", $params);
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$producers = Database::queryAll("
    SELECT p.*,
           (SELECT COUNT(*) FROM products WHERE producer_id = p.id AND status = 'approved') as product_count,
           (SELECT COUNT(*) FROM order_sub_orders WHERE producer_id = p.id) as order_count,
           (SELECT COALESCE(SUM(producer_earning), 0) FROM order_sub_orders WHERE producer_id = p.id AND payout_status IN ('released','paid')) as total_earned
    FROM producers p
    $where
    ORDER BY p.created_at DESC
    LIMIT $perPage OFFSET $offset
", $params);

$statusCounts = Database::queryAll("SELECT status, COUNT(*) as cnt FROM producers GROUP BY status");
$statusMap = [];
foreach ($statusCounts as $sc) {
    $statusMap[$sc['status']] = $sc['cnt'];
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <h2 class="page-title">Üreticiler</h2>
        <div class="topbar-right">
            <a href="producer-edit.php" class="btn btn-primary">+ Yeni Üretici</a>
        </div>
    </div>

    <div class="admin-content">
        <!-- Filters -->
        <div class="filter-bar">
            <div class="filter-tabs">
                <a href="producers.php" class="filter-tab <?php echo !$statusFilter ? 'active' : ''; ?>">
                    Tümü <span class="filter-count"><?php echo $total; ?></span>
                </a>
                <?php foreach (['pending' => 'Bekleyen', 'approved' => 'Onaylı', 'suspended' => 'Askıda', 'rejected' => 'Reddedilen'] as $key => $label): ?>
                <a href="producers.php?status=<?php echo $key; ?>" class="filter-tab <?php echo $statusFilter === $key ? 'active' : ''; ?>">
                    <?php echo $label; ?> <span class="filter-count"><?php echo $statusMap[$key] ?? 0; ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <form method="GET" class="search-form">
                <?php if ($statusFilter): ?><input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>"><?php endif; ?>
                <input type="text" name="q" placeholder="Üretici ara..." value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                <button type="submit" class="btn btn-sm btn-outline">Ara</button>
            </form>
        </div>

        <!-- Producers Table -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($producers)): ?>
                    <p class="text-muted text-center">Üretici bulunamadı.</p>
                <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Üretici</th>
                            <th>Instagram</th>
                            <th>Şehir</th>
                            <th>Ürün</th>
                            <th>Sipariş</th>
                            <th>Kazanç</th>
                            <th>Durum</th>
                            <th>Kayıt</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($producers as $p): ?>
                        <tr>
                            <td>
                                <div class="cell-user">
                                    <div class="cell-avatar" style="background: <?php echo $p['is_featured'] ? 'linear-gradient(135deg, #7c3aed, #f59e0b)' : '#374151'; ?>">
                                        <?php echo mb_strtoupper(mb_substr($p['brand_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <a href="producer-edit.php?id=<?php echo $p['id']; ?>" class="link text-bold"><?php echo htmlspecialchars($p['brand_name']); ?></a>
                                        <span class="text-sm text-muted"><?php echo htmlspecialchars($p['owner_name']); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($p['instagram_handle']): ?>
                                    <a href="<?php echo htmlspecialchars($p['instagram_url']); ?>" target="_blank" class="link">@<?php echo htmlspecialchars($p['instagram_handle']); ?></a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($p['city'] ?? '—'); ?></td>
                            <td class="text-center"><?php echo $p['product_count']; ?></td>
                            <td class="text-center"><?php echo $p['order_count']; ?></td>
                            <td class="text-bold">₺<?php echo number_format($p['total_earned'], 2, ',', '.'); ?></td>
                            <td><span class="badge badge--<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                            <td class="text-muted text-sm"><?php echo date('d.m.Y', strtotime($p['created_at'])); ?></td>
                            <td>
                                <div class="action-group">
                                    <a href="producer-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-xs btn-outline" title="Düzenle">Düzenle</a>
                                    <?php if ($p['status'] === 'pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-xs btn-success" title="Onayla">Onayla</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="action" value="feature">
                                        <button type="submit" class="btn btn-xs <?php echo $p['is_featured'] ? 'btn-amber' : 'btn-outline'; ?>" title="Öne Çıkar">Öne Çıkar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total > $perPage): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo $statusFilter ? '&status='.$statusFilter : ''; ?><?php echo $search ? '&q='.urlencode($search) : ''; ?>"
                   class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
