<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Banner & Slider Yönetimi';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$banners = Database::queryAll("SELECT b.*, p.brand_name FROM banners b LEFT JOIN producers p ON b.producer_id = p.id ORDER BY b.sort_order ASC");
$producers = Database::queryAll("SELECT id, brand_name FROM producers WHERE status = 'approved'");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_banner'])) {
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');
    $linkUrl = trim($_POST['link_url'] ?? '');
    $position = $_POST['position'] ?? 'hero';
    $producerId = !empty($_POST['producer_id']) ? (int)$_POST['producer_id'] : null;

    if ($imageUrl) {
        Database::insert("
            INSERT INTO banners (title, subtitle, image_url, link_url, position, producer_id, is_active)
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ", [$title, $subtitle, $imageUrl, $linkUrl, $position, $producerId]);
        header("Location: banners.php");
        exit;
    }
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Banner & Kampanya Yönetimi</h2>
    </div>

    <div class="admin-content">
        <div class="grid-3">
            <div class="col-span-2">
                <div class="card table-card">
                    <div class="card-header"><h3 class="card-title">Mevcut Bannerlar</h3></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="100">Önizleme</th>
                                <th>Başlık & Alt Başlık</th>
                                <th>Konum</th>
                                <th>İlgili Butik</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($banners)): ?>
                                <tr><td colspan="5" class="empty-state">Henüz banner eklenmemiş.</td></tr>
                            <?php else: ?>
                                <?php foreach ($banners as $b): ?>
                                    <tr>
                                        <td><img src="<?= htmlspecialchars($b['image_url']) ?>" style="width: 80px; height: 45px; object-fit: cover; border-radius: 4px;"></td>
                                        <td>
                                            <strong><?= htmlspecialchars($b['title']) ?></strong>
                                            <div class="text-muted small"><?= htmlspecialchars($b['subtitle']) ?></div>
                                        </td>
                                        <td><span class="badge badge-secondary"><?= htmlspecialchars($b['position']) ?></span></td>
                                        <td><?= htmlspecialchars($b['brand_name'] ?: 'Genel') ?></td>
                                        <td><span class="badge badge-<?= $b['is_active'] ? 'success' : 'danger' ?>"><?= $b['is_active'] ? 'Aktif' : 'Pasif' ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="card">
                    <h3 class="card-title">Yeni Banner Ekle</h3>
                    <form method="POST">
                        <input type="hidden" name="add_banner" value="1">
                        <div class="form-group">
                            <label class="form-label">Başlık</label>
                            <input type="text" name="title" class="form-control" placeholder="Örn: El Emeği Butik Üreticiler">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alt Başlık</label>
                            <input type="text" name="subtitle" class="form-control" placeholder="Örn: Instagram'ın en sevilen atölyeleri tek çatı altında">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Görsel URL *</label>
                            <input type="url" name="image_url" class="form-control" required placeholder="https://images.unsplash.com/...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Yönlendirme Linki</label>
                            <input type="text" name="link_url" class="form-control" placeholder="/uretici/lazerci-hediyelik">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konum</label>
                            <select name="position" class="form-control">
                                <option value="hero">Hero Anasayfa Slider</option>
                                <option value="producer_spotlight">Üretici Spotlight</option>
                                <option value="promo">Promosyon Bandı</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Öne Çıkarılan Butik (Opsiyonel)</label>
                            <select name="producer_id" class="form-control">
                                <option value="">— Seçilmedi —</option>
                                <?php foreach ($producers as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['brand_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Banner Ekle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
