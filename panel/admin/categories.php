<?php
/**
 * Categories Management — ButikÇarşı Admin
 */
require_once __DIR__ . '/../config.php';
$pageTitle = 'Kategoriler';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: Validator::makeSlug($name);
    $description = trim($_POST['description'] ?? '');
    $iconClass = trim($_POST['icon_class'] ?? '');
    $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    switch ($action) {
        case 'create':
            Database::execute(
                "INSERT INTO categories (name, slug, description, icon_class, parent_id, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$name, $slug, $description, $iconClass, $parentId, $sortOrder, $isActive]
            );
            break;
        case 'update':
            Database::execute(
                "UPDATE categories SET name=?, slug=?, description=?, icon_class=?, parent_id=?, sort_order=?, is_active=? WHERE id=?",
                [$name, $slug, $description, $iconClass, $parentId, $sortOrder, $isActive, $id]
            );
            break;
        case 'delete':
            Database::execute("DELETE FROM categories WHERE id = ?", [$id]);
            break;
    }
    header('Location: categories.php');
    exit;
}

$categories = Database::queryAll("
    SELECT c.*, 
           p.name as parent_name,
           (SELECT COUNT(*) FROM product_categories pc WHERE pc.category_id = c.id) as product_count
    FROM categories c
    LEFT JOIN categories p ON c.parent_id = p.id
    ORDER BY c.sort_order ASC, c.name ASC
");
$editCategory = null;
if (!empty($_GET['edit'])) {
    $editCategory = Database::query("SELECT * FROM categories WHERE id = ?", [(int)$_GET['edit']]);
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <h2 class="page-title">Kategoriler</h2>
    </div>

    <div class="admin-content">
        <div class="dashboard-grid">
            <!-- Category Form -->
            <div class="card">
                <div class="card-header">
                    <h3><?php echo $editCategory ? 'Kategori Düzenle' : 'Yeni Kategori'; ?></h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="<?php echo $editCategory ? 'update' : 'create'; ?>">
                        <?php if ($editCategory): ?>
                            <input type="hidden" name="id" value="<?php echo $editCategory['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="name">Kategori Adı</label>
                            <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($editCategory['name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($editCategory['slug'] ?? ''); ?>">
                            <p class="form-hint">Boş bırakılırsa otomatik oluşturulur.</p>
                        </div>
                        <div class="form-group">
                            <label for="icon_class">İkon (Lucide)</label>
                            <input type="text" id="icon_class" name="icon_class" placeholder="sparkles, palette, heart..." value="<?php echo htmlspecialchars($editCategory['icon_class'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="parent_id">Üst Kategori</label>
                            <select id="parent_id" name="parent_id">
                                <option value="">— Ana Kategori —</option>
                                <?php foreach ($categories as $cat): ?>
                                    <?php if (($editCategory['id'] ?? 0) !== $cat['id']): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($editCategory['parent_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sort_order">Sıra</label>
                                <input type="number" id="sort_order" name="sort_order" value="<?php echo $editCategory['sort_order'] ?? 0; ?>">
                            </div>
                            <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:0.5rem;">
                                <label>
                                    <input type="checkbox" name="is_active" <?php echo ($editCategory['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                    Aktif
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea id="description" name="description" rows="2"><?php echo htmlspecialchars($editCategory['description'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $editCategory ? 'Güncelle' : 'Ekle'; ?></button>
                        <?php if ($editCategory): ?>
                            <a href="categories.php" class="btn btn-outline">İptal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Categories List -->
            <div class="card">
                <div class="card-header">
                    <h3>Mevcut Kategoriler (<?php echo count($categories); ?>)</h3>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sıra</th>
                                <th>Kategori</th>
                                <th>Slug</th>
                                <th>Üst</th>
                                <th>Ürün</th>
                                <th>Durum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td class="text-muted"><?php echo $cat['sort_order']; ?></td>
                                <td class="text-bold"><?php echo htmlspecialchars($cat['name']); ?></td>
                                <td class="text-muted text-sm"><?php echo htmlspecialchars($cat['slug']); ?></td>
                                <td class="text-muted text-sm"><?php echo htmlspecialchars($cat['parent_name'] ?? '—'); ?></td>
                                <td class="text-center"><?php echo $cat['product_count']; ?></td>
                                <td>
                                    <span class="badge badge--<?php echo $cat['is_active'] ? 'approved' : 'draft'; ?>">
                                        <?php echo $cat['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="btn btn-xs btn-outline">Düzenle</a>
                                        <form method="POST" style="display:inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                            <button type="submit" class="btn btn-xs btn-outline" data-confirm="Bu kategoriyi silmek istediğinize emin misiniz?" style="color:var(--danger)">Sil</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
