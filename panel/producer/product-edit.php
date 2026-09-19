<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Ürün Düzenle';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pid = Auth::producerId();
$id = (int)($_GET['id'] ?? 0);

$product = Database::query("SELECT * FROM products WHERE id = ? AND producer_id = ?", [$id, $pid]);
if (!$product) {
    echo "<div class='p-4 text-danger'>Ürün bulunamadı veya yetkiniz yok.</div>";
    exit;
}

$categories = Database::queryAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC");
$prodCats = Database::queryAll("SELECT category_id FROM product_categories WHERE product_id = ?", [$id]);
$currentCatIds = array_column($prodCats, 'category_id');

$images = Database::queryAll("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC", [$id]);
$imageUrlsText = implode("
", array_column($images, 'image_url'));

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $shortDesc = trim($_POST['short_description'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $regularPrice = (float)($_POST['regular_price'] ?? 0);
    $salePrice = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stockQuantity = isset($_POST['stock_quantity']) && $_POST['stock_quantity'] !== '' ? (int)$_POST['stock_quantity'] : null;
    $stockStatus = $_POST['stock_status'] ?? 'instock';
    $customizable = isset($_POST['customizable']) ? 1 : 0;
    $customizationNote = trim($_POST['customization_note'] ?? '');
    $productionTime = trim($_POST['production_time'] ?? '');
    $material = trim($_POST['material'] ?? '');
    $freeShipping = isset($_POST['free_shipping']) ? 1 : 0;
    $shippingCost = (float)($_POST['shipping_cost'] ?? 0);
    $selectedCats = $_POST['categories'] ?? [];

    if (empty($name) || $regularPrice <= 0) {
        $error = 'Ürün adı ve fiyat zorunludur.';
    } else {
        Database::execute("
            UPDATE products SET 
                name = ?, short_description = ?, description = ?,
                regular_price = ?, sale_price = ?, stock_quantity = ?, stock_status = ?,
                customizable = ?, customization_note = ?, production_time = ?, material = ?,
                free_shipping = ?, shipping_cost = ?
            WHERE id = ? AND producer_id = ?
        ", [
            $name, $shortDesc, $desc, $regularPrice, $salePrice, $stockQuantity, $stockStatus,
            $customizable, $customizationNote, $productionTime, $material, $freeShipping, $shippingCost,
            $id, $pid
        ]);

        // Re-assign categories
        Database::execute("DELETE FROM product_categories WHERE product_id = ?", [$id]);
        foreach ($selectedCats as $catId) {
            Database::execute("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)", [$id, $catId]);
        }

        // Update images if provided
        if (isset($_POST['image_urls'])) {
            Database::execute("DELETE FROM product_images WHERE product_id = ?", [$id]);
            $urls = explode("
", str_replace("", "", $_POST['image_urls']));
            $first = 1;
            foreach ($urls as $url) {
                $u = trim($url);
                if ($u) {
                    Database::execute("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, ?)", [$id, $u, $first]);
                    $first = 0;
                }
            }
        }

        header("Location: products.php?msg=updated");
        exit;
    }
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Ürün Düzenle: <?= htmlspecialchars($product['name']) ?></h2>
        <div class="topbar-actions">
            <a href="products.php" class="btn btn-secondary">Geri Dön</a>
        </div>
    </div>

    <div class="admin-content">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="form-grid">
            <div class="form-main">
                <div class="card">
                    <h3 class="card-title">Temel Bilgiler</h3>
                    <div class="form-group">
                        <label class="form-label">Ürün Adı *</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kısa Açıklama</label>
                        <input type="text" name="short_description" class="form-control" value="<?= htmlspecialchars($product['short_description'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Detaylı Açıklama</label>
                        <textarea name="description" rows="6" class="form-control"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Fiyat & Stok</h3>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Satış Fiyatı (₺) *</label>
                            <input type="number" step="0.01" name="regular_price" class="form-control" value="<?= $product['regular_price'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">İndirimli Fiyat (₺)</label>
                            <input type="number" step="0.01" name="sale_price" class="form-control" value="<?= $product['sale_price'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Stok Adedi</label>
                            <input type="number" name="stock_quantity" class="form-control" value="<?= $product['stock_quantity'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stok Durumu</label>
                            <select name="stock_status" class="form-control">
                                <option value="instock" <?= $product['stock_status'] === 'instock' ? 'selected' : '' ?>>Stokta Var</option>
                                <option value="outofstock" <?= $product['stock_status'] === 'outofstock' ? 'selected' : '' ?>>Stokta Yok</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Kişiselleştirme</h3>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="customizable" id="customizable" value="1" <?= $product['customizable'] ? 'checked' : '' ?>>
                        <label for="customizable">Bu ürün kişiye özel üretilebilir</label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kişiselleştirme Yönergesi</label>
                        <textarea name="customization_note" rows="2" class="form-control"><?= htmlspecialchars($product['customization_note'] ?? '') ?></textarea>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Hazırlanma Süresi</label>
                            <input type="text" name="production_time" class="form-control" value="<?= htmlspecialchars($product['production_time'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Malzeme</label>
                            <input type="text" name="material" class="form-control" value="<?= htmlspecialchars($product['material'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Görseller</h3>
                    <textarea name="image_urls" rows="4" class="form-control"><?= htmlspecialchars($imageUrlsText) ?></textarea>
                </div>
            </div>

            <div class="form-sidebar">
                <div class="card">
                    <h3 class="card-title">Durum</h3>
                    <p>Mevcut Durum: <span class="badge badge-info"><?= htmlspecialchars($product['status']) ?></span></p>
                    <button type="submit" class="btn btn-primary btn-block mt-3">Değişiklikleri Kaydet</button>
                </div>

                <div class="card">
                    <h3 class="card-title">Kategoriler</h3>
                    <div class="category-checkboxes">
                        <?php foreach ($categories as $cat): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" <?= in_array($cat['id'], $currentCatIds) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($cat['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
