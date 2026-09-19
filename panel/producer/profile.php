<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Profilim & Butik Bilgileri';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pid = Auth::producerId();
$producer = Database::query("SELECT * FROM producers WHERE id = ?", [$pid]);
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = trim($_POST['brand_name'] ?? '');
    $owner = trim($_POST['owner_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $insta = trim($_POST['instagram_handle'] ?? '');
    $instaUrl = trim($_POST['instagram_url'] ?? '');
    $whatsapp = trim($_POST['whatsapp_number'] ?? '');
    $iban = trim($_POST['iban'] ?? '');
    $bank = trim($_POST['bank_name'] ?? '');
    $logoUrl = trim($_POST['logo_url'] ?? '');
    $coverUrl = trim($_POST['cover_image_url'] ?? '');

    if (empty($brand) || empty($owner)) {
        $error = 'Butik adı ve yetkili adı zorunludur.';
    } else {
        Database::execute("
            UPDATE producers SET 
                brand_name = ?, owner_name = ?, description = ?, city = ?, district = ?,
                instagram_handle = ?, instagram_url = ?, whatsapp_number = ?,
                iban = ?, bank_name = ?, logo_url = ?, cover_image_url = ?
            WHERE id = ?
        ", [$brand, $owner, $desc, $city, $district, $insta, $instaUrl, $whatsapp, $iban, $bank, $logoUrl, $coverUrl, $pid]);

        $msg = 'Profil bilgileriniz başarıyla güncellendi!';
        $producer = Database::query("SELECT * FROM producers WHERE id = ?", [$pid]);
        $_SESSION['producer_brand'] = $brand;
    }
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Butik & Profil Ayarları</h2>
    </div>

    <div class="admin-content">
        <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST" class="form-grid">
            <div class="form-main">
                <div class="card">
                    <h3 class="card-title">Butik Bilgileri</h3>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Butik Marka Adı *</label>
                            <input type="text" name="brand_name" class="form-control" value="<?= htmlspecialchars($producer['brand_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Yetkili Adı Soyadı *</label>
                            <input type="text" name="owner_name" class="form-control" value="<?= htmlspecialchars($producer['owner_name']) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Butik Hikayesi / Tanıtım Yazısı</label>
                        <textarea name="description" rows="4" class="form-control"><?= htmlspecialchars($producer['description'] ?? '') ?></textarea>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Şehir</label>
                            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($producer['city'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">İlçe</label>
                            <input type="text" name="district" class="form-control" value="<?= htmlspecialchars($producer['district'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Instagram & Sosyal Medya</h3>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Instagram Kullanıcı Adı</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="instagram_handle" class="form-control" value="<?= htmlspecialchars(str_replace('@', '', $producer['instagram_handle'] ?? '')) ?>" placeholder="lazerci_hediyelik">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Instagram Linki</label>
                            <input type="url" name="instagram_url" class="form-control" value="<?= htmlspecialchars($producer['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp Sipariş & Destek Numarası</label>
                        <input type="text" name="whatsapp_number" class="form-control" value="<?= htmlspecialchars($producer['whatsapp_number'] ?? '') ?>" placeholder="+905551234567">
                    </div>
                </div>

                <div class="card">
                    <h3 class="card-title">Görseller (Logo & Kapak)</h3>
                    <div class="form-group">
                        <label class="form-label">Logo / Profil Fotoğrafı Linki</label>
                        <input type="text" name="logo_url" class="form-control" value="<?= htmlspecialchars($producer['logo_url'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kapak Görseli Linki</label>
                        <input type="text" name="cover_image_url" class="form-control" value="<?= htmlspecialchars($producer['cover_image_url'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="form-sidebar">
                <div class="card">
                    <h3 class="card-title">Banka Bilgileri (Ödeme İçin)</h3>
                    <div class="form-group">
                        <label class="form-label">Banka Adı</label>
                        <input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($producer['bank_name'] ?? '') ?>" placeholder="Garanti, Ziraat vb.">
                    </div>
                    <div class="form-group">
                        <label class="form-label">IBAN Numarası</label>
                        <input type="text" name="iban" class="form-control" value="<?= htmlspecialchars($producer['iban'] ?? '') ?>" placeholder="TR00 0000 0000...">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-3">Profili Kaydet</button>
                </div>
            </div>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
