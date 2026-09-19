<?php
/**
 * Producer Edit/Create â€” ButikÃ‡arÅŸÄ± Admin
 */
require_once __DIR__ . '/../config.php';
$pageTitle = 'Ãœretici DÃ¼zenle';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$id = (int)($_GET['id'] ?? 0);
$producer = null;
$success = '';
$error = '';

if ($id > 0) {
    $producer = Database::query("SELECT * FROM producers WHERE id = ?", [$id]);
    if (!$producer) {
        header('Location: producers.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = new Validator();
    $brandName = trim($_POST['brand_name'] ?? '');
    $ownerName = trim($_POST['owner_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: Validator::makeSlug($brandName);
    $description = trim($_POST['description'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $instagramUrl = trim($_POST['instagram_url'] ?? '');
    $instagramHandle = trim($_POST['instagram_handle'] ?? '');
    $tiktokUrl = trim($_POST['tiktok_url'] ?? '');
    $websiteUrl = trim($_POST['website_url'] ?? '');
    $iban = trim($_POST['iban'] ?? '');
    $bankName = trim($_POST['bank_name'] ?? '');
    $taxNumber = trim($_POST['tax_number'] ?? '');
    $identityNumber = trim($_POST['identity_number'] ?? '');
    $commissionRate = (float)($_POST['commission_rate'] ?? 10);
    $status = $_POST['status'] ?? 'pending';
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $whatsappNumber = trim($_POST['whatsapp_number'] ?? '');
    $approvalNote = trim($_POST['approval_note'] ?? '');

    $v->required('brand_name', $brandName, 'Butik AdÄ±')
      ->required('owner_name', $ownerName, 'Sahip AdÄ±')
      ->required('email', $email, 'E-posta')
      ->email('email', $email, 'E-posta');

    if ($v->hasErrors()) {
        $error = $v->firstError();
    } else {
        try {
            // Handle logo upload
            $logoUrl = $producer['logo_url'] ?? null;
            if (!empty($_FILES['logo']['name'])) {
                $result = ImageProcessor::upload($_FILES['logo'], 'producers');
                $logoUrl = $result['webp'] ?? $result['original'];
            }

            $coverUrl = $producer['cover_image_url'] ?? null;
            if (!empty($_FILES['cover_image']['name'])) {
                $result = ImageProcessor::upload($_FILES['cover_image'], 'producers', 1600);
                $coverUrl = $result['webp'] ?? $result['original'];
            }

            if ($id > 0) {
                // Update
                Database::execute("
                    UPDATE producers SET brand_name=?, owner_name=?, email=?, phone=?, slug=?, description=?,
                    city=?, district=?, instagram_url=?, instagram_handle=?, tiktok_url=?, website_url=?,
                    iban=?, bank_name=?, tax_number=?, identity_number=?, commission_rate=?,
                    status=?, is_featured=?, whatsapp_number=?, approval_note=?,
                    logo_url=?, cover_image_url=?,
                    approved_at = CASE WHEN ? = 'approved' AND status != 'approved' THEN CURRENT_TIMESTAMP ELSE approved_at END
                    WHERE id=?
                ", [$brandName, $ownerName, $email, $phone, $slug, $description,
                    $city, $district, $instagramUrl, $instagramHandle, $tiktokUrl, $websiteUrl,
                    $iban, $bankName, $taxNumber, $identityNumber, $commissionRate,
                    $status, $isFeatured, $whatsappNumber, $approvalNote,
                    $logoUrl, $coverUrl, $status, $id]);
                $success = 'Ãœretici gÃ¼ncellendi.';
                $producer = Database::query("SELECT * FROM producers WHERE id = ?", [$id]);
            } else {
                // Create new
                $password = $_POST['password'] ?? '';
                if (empty($password)) {
                    $password = bin2hex(random_bytes(4)); // Random 8-char password
                }
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                Database::execute("
                    INSERT INTO producers (brand_name, owner_name, email, phone, slug, description,
                    city, district, instagram_url, instagram_handle, tiktok_url, website_url,
                    iban, bank_name, tax_number, identity_number, commission_rate,
                    status, is_featured, whatsapp_number, approval_note, logo_url, cover_image_url,
                    password_hash, approved_at)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,
                    CASE WHEN ? = 'approved' THEN CURRENT_TIMESTAMP ELSE NULL END)
                ", [$brandName, $ownerName, $email, $phone, $slug, $description,
                    $city, $district, $instagramUrl, $instagramHandle, $tiktokUrl, $websiteUrl,
                    $iban, $bankName, $taxNumber, $identityNumber, $commissionRate,
                    $status, $isFeatured, $whatsappNumber, $approvalNote, $logoUrl, $coverUrl,
                    $passwordHash, $status]);
                $newId = Database::lastInsertId();
                $success = "Ãœretici oluÅŸturuldu. GeÃ§ici ÅŸifre: <strong>$password</strong>";
                $producer = Database::query("SELECT * FROM producers WHERE id = ?", [$newId]);
                $id = $newId;
            }
        } catch (Exception $e) {
            $error = 'Hata: ' . $e->getMessage();
        }
    }
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <h2 class="page-title"><?php echo $producer ? htmlspecialchars($producer['brand_name']) : 'Yeni Ãœretici'; ?></h2>
        <div class="topbar-right">
            <a href="producers.php" class="btn btn-outline">â† Listeye DÃ¶n</a>
        </div>
    </div>

    <div class="admin-content">
        <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="dashboard-grid">
                <!-- Temel Bilgiler -->
                <div class="card">
                    <div class="card-header"><h3>Temel Bilgiler</h3></div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="brand_name">Butik AdÄ± *</label>
                                <input type="text" id="brand_name" name="brand_name" required value="<?php echo htmlspecialchars($producer['brand_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="slug">Slug</label>
                                <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($producer['slug'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="owner_name">Sahip AdÄ± SoyadÄ± *</label>
                                <input type="text" id="owner_name" name="owner_name" required value="<?php echo htmlspecialchars($producer['owner_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">E-posta *</label>
                                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($producer['email'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Telefon</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($producer['phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="whatsapp_number">WhatsApp No</label>
                                <input type="text" id="whatsapp_number" name="whatsapp_number" placeholder="+905xxxxxxxxx" value="<?php echo htmlspecialchars($producer['whatsapp_number'] ?? ''); ?>">
                            </div>
                        </div>
                        <?php if (!$id): ?>
                        <div class="form-group">
                            <label for="password">GiriÅŸ Åifresi</label>
                            <input type="text" id="password" name="password" placeholder="BoÅŸ bÄ±rakÄ±lÄ±rsa otomatik oluÅŸturulur">
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="description">Butik TanÄ±tÄ±mÄ±</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($producer['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">Åehir</label>
                                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($producer['city'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="district">Ä°lÃ§e</label>
                                <input type="text" id="district" name="district" value="<?php echo htmlspecialchars($producer['district'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sosyal Medya & GÃ¶rseller -->
                <div class="card">
                    <div class="card-header"><h3>Sosyal Medya & GÃ¶rseller</h3></div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="instagram_handle">Instagram @kullanÄ±cÄ±</label>
                                <input type="text" id="instagram_handle" name="instagram_handle" placeholder="@butikadi" value="<?php echo htmlspecialchars($producer['instagram_handle'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="instagram_url">Instagram URL</label>
                                <input type="url" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($producer['instagram_url'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tiktok_url">TikTok URL</label>
                                <input type="url" id="tiktok_url" name="tiktok_url" value="<?php echo htmlspecialchars($producer['tiktok_url'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="website_url">Website</label>
                                <input type="url" id="website_url" name="website_url" value="<?php echo htmlspecialchars($producer['website_url'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="logo">Logo / Profil FotoÄŸrafÄ±</label>
                                <input type="file" id="logo" name="logo" accept="image/*" data-preview="logoPreview">
                                <?php if ($producer['logo_url'] ?? null): ?>
                                    <img id="logoPreview" src="<?php echo '../uploads/' . basename(dirname($producer['logo_url'])) . '/' . basename($producer['logo_url']); ?>" style="max-width:120px;border-radius:8px;margin-top:0.5rem;">
                                <?php else: ?>
                                    <img id="logoPreview" style="display:none;max-width:120px;border-radius:8px;margin-top:0.5rem;">
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="cover_image">Kapak GÃ¶rseli</label>
                                <input type="file" id="cover_image" name="cover_image" accept="image/*" data-preview="coverPreview">
                                <?php if ($producer['cover_image_url'] ?? null): ?>
                                    <img id="coverPreview" src="<?php echo '../uploads/' . basename(dirname($producer['cover_image_url'])) . '/' . basename($producer['cover_image_url']); ?>" style="max-width:200px;border-radius:8px;margin-top:0.5rem;">
                                <?php else: ?>
                                    <img id="coverPreview" style="display:none;max-width:200px;border-radius:8px;margin-top:0.5rem;">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Finansal Bilgiler -->
                <div class="card">
                    <div class="card-header"><h3>Finansal Bilgiler</h3></div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="iban">IBAN</label>
                                <input type="text" id="iban" name="iban" placeholder="TR..." value="<?php echo htmlspecialchars($producer['iban'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="bank_name">Banka</label>
                                <input type="text" id="bank_name" name="bank_name" value="<?php echo htmlspecialchars($producer['bank_name'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tax_number">Vergi No</label>
                                <input type="text" id="tax_number" name="tax_number" value="<?php echo htmlspecialchars($producer['tax_number'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="identity_number">TC Kimlik No</label>
                                <input type="text" id="identity_number" name="identity_number" value="<?php echo htmlspecialchars($producer['identity_number'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="commission_rate">Komisyon OranÄ± (%)</label>
                            <input type="number" id="commission_rate" name="commission_rate" step="0.01" min="0" max="50" value="<?php echo $producer['commission_rate'] ?? '10.00'; ?>">
                        </div>
                    </div>
                </div>

                <!-- Durum & Onay -->
                <div class="card">
                    <div class="card-header"><h3>Durum & Onay</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status">Durum</label>
                            <select id="status" name="status">
                                <?php foreach (['pending'=>'Beklemede','approved'=>'OnaylÄ±','suspended'=>'AskÄ±ya AlÄ±ndÄ±','rejected'=>'Reddedildi'] as $val => $lbl): ?>
                                <option value="<?php echo $val; ?>" <?php echo ($producer['status'] ?? 'pending') === $val ? 'selected' : ''; ?>><?php echo $lbl; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="approval_note">Admin Notu</label>
                            <textarea id="approval_note" name="approval_note" rows="3" placeholder="Onay/Red sebebi..."><?php echo htmlspecialchars($producer['approval_note'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_featured" <?php echo ($producer['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                                â­ Ã–ne Ã‡Ä±kan Ãœretici (Anasayfada gÃ¶ster)
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block"><?php echo $id ? 'GÃ¼ncelle' : 'Ãœretici OluÅŸtur'; ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
