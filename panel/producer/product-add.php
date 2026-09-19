<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Yeni Ürün Ekle';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pid = Auth::producerId();
$categories = Database::queryAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC");
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = preg_replace('/[^a-z0-9-]+/', '-', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $name)));
        $slug = trim($slug, '-');
    }
    // ensure unique
    $check = Database::query("SELECT id FROM products WHERE slug = ?", [$slug]);
    if ($check) {
        $slug .= '-' . rand(100, 999);
    }

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
    $isDraft = isset($_POST['save_draft']);
    $status = $isDraft ? 'draft' : 'pending';

    if (empty($name) || $regularPrice <= 0) {
        $error = 'Ürün adı ve geçerli bir fiyat zorunludur.';
    } else {
        $sql = "INSERT INTO products (producer_id, slug, name, short_description, description, regular_price, sale_price, stock_quantity, stock_status, customizable, customization_note, production_time, material, free_shipping, shipping_cost, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $prodId = Database::insert($sql, [
            $pid, $slug, $name, $shortDesc, $desc, $regularPrice, $salePrice, $stockQuantity, $stockStatus, $customizable, $customizationNote, $productionTime, $material, $freeShipping, $shippingCost, $status
        ]);

        if ($prodId) {
            // categories
            foreach ($selectedCats as $catId) {
                Database::execute("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)", [$prodId, $catId]);
            }

            // Image URL upload or direct link
            if (!empty($_POST['image_urls'])) {
                $urls = explode("
", str_replace("", "", $_POST['image_urls']));
                $first = 1;
                foreach ($urls as $url) {
                    $u = trim($url);
                    if ($u) {
                        Database::execute("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, ?)", [$prodId, $u, $first]);
                        $first = 0;
                    }
                }
            }

            header("Location: products.php?msg=added");
            exit;
        } else {
            $error = 'Ürün eklenirken bir hata oluştu.';
        }
    }
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Yeni Ürün Ekle</h2>
        <div class="topbar-actions">
            <a href="products.php" class="btn btn-secondary">İptal</a>
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
                        <input type="text" name="name" class="form-control" required placeholder="Örn: Kişiye Özel Ahşap Fotoğraf Çerçevesi">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kısa Açıklama</label>
                        <input type="text" name="short_description" class="form-control" placeholder="Arama ve listelerde görünecek tek cümlelik özet">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Detaylı Açıklama</label>
                        <textarea name="description" rows="6" class="form-control" placeholder="Ürünün hikayesi, yapım süreci, boyutları ve teknik detayları..."></textarea>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Fiyat & Stok</h3>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Satış Fiyatı (₺) *</label>
                            <input type="number" step="0.01" name="regular_price" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label">İndirimli Fiyat (₺)</label>
                            <input type="number" step="0.01" name="sale_price" class="form-control" placeholder="Opsiyonel">
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Stok Adedi</label>
                            <input type="number" name="stock_quantity" class="form-control" placeholder="Boş bırakılırsa sınırsız">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stok Durumu</label>
                            <select name="stock_status" class="form-control">
                                <option value="instock">Stokta Var</option>
                                <option value="outofstock">Stokta Yok</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Butik & Kişiselleştirme Seçenekleri</h3>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="customizable" id="customizable" value="1">
                        <label for="customizable"><strong>Bu ürün kişiye özel sipariş alabilir</strong> (İsim, tarih, yazı, fotoğraf baskısı vb.)</label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Müşteriye Kişiselleştirme Yönergesi</label>
                        <textarea name="customization_note" rows="2" class="form-control" placeholder="Örn: Sipariş verirken bilekliğe yazılacak ismi ve yazı fontunu belirtiniz."></textarea>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Hazırlanma / Üretim Süresi</label>
                            <input type="text" name="production_time" class="form-control" placeholder="Örn: 2-3 iş günü">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kullanılan Malzeme</label>
                            <input type="text" name="material" class="form-control" placeholder="Örn: Hakiki Deri, Çam Ağacı, Epoksi Reçine">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Ürün Görselleri</h3>
                    <p class="text-muted small mb-2">Her satıra bir görsel linki giriniz (ilk satır ana görsel olacaktır):</p>
                    <textarea name="image_urls" rows="4" class="form-control" placeholder="https://images.unsplash.com/...&#10;https://images.unsplash.com/..."></textarea>
                </div>
            </div>

            <div class="form-sidebar">
                <div class="card">
                    <h3 class="card-title">Yayınla</h3>
                    <p class="text-muted small mb-3">Ürün admin onayına gönderilecektir. Admin onayladıktan sonra anasayfada ve butik sayfanızda yayına girer.</p>
                    <button type="submit" class="btn btn-primary btn-block mb-2">Onaya Gönder</button>
                    <button type="submit" name="save_draft" value="1" class="btn btn-outline btn-block">Taslak Olarak Kaydet</button>
                </div>

                <div class="card">
                    <h3 class="card-title">Kategoriler</h3>
                    <div class="category-checkboxes">
                        <?php foreach ($categories as $cat): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>">
                                <span><?= htmlspecialchars($cat['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Kargo Ayarları</h3>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="free_shipping" id="free_shipping" value="1">
                        <label for="free_shipping">Ücretsiz Kargo</label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kargo Ücreti (₺)</label>
                        <input type="number" step="0.01" name="shipping_cost" class="form-control" value="0.00">
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
