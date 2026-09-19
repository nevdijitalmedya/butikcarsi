<?php
/**
 * Producer Registration / Application Form — ButikÇarşı
 */
require_once __DIR__ . '/../config.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = new Validator();
    $brandName = trim($_POST['brand_name'] ?? '');
    $ownerName = trim($_POST['owner_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $instagramHandle = trim($_POST['instagram_handle'] ?? '');
    $instagramUrl = trim($_POST['instagram_url'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $v->required('brand_name', $brandName, 'Butik Adı')
      ->required('owner_name', $ownerName, 'Ad Soyad')
      ->required('email', $email, 'E-posta')
      ->email('email', $email, 'E-posta')
      ->required('password', $password, 'Şifre')
      ->minLength('password', $password, 6, 'Şifre');

    if ($password !== $passwordConfirm) {
        $error = 'Şifreler eşleşmiyor.';
    } elseif ($v->hasErrors()) {
        $error = $v->firstError();
    } else {
        // Check existing
        $existing = Database::query("SELECT id FROM producers WHERE email = ?", [$email]);
        if ($existing) {
            $error = 'Bu e-posta adresi zaten kayıtlı.';
        } else {
            $slug = Validator::makeSlug($brandName);
            // Ensure unique slug
            $slugCheck = Database::query("SELECT id FROM producers WHERE slug = ?", [$slug]);
            if ($slugCheck) {
                $slug .= '-' . time();
            }

            Database::execute("
                INSERT INTO producers (brand_name, owner_name, email, phone, password_hash, slug,
                instagram_handle, instagram_url, city, description, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ", [$brandName, $ownerName, $email, $phone, password_hash($password, PASSWORD_BCRYPT),
                $slug, $instagramHandle, $instagramUrl, $city, $description]);

            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üretici Başvurusu — ButikÇarşı</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../admin/assets/css/admin.css">
    <style>
        .register-container { max-width: 560px; margin: 2rem auto; padding: 0 1rem; }
        .register-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 2rem; }
        .register-header { text-align: center; margin-bottom: 2rem; }
        .register-header h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
        .register-header p { color: var(--text-muted); font-size: 0.85rem; }
        .success-card { text-align: center; padding: 3rem 2rem; }
        .success-icon { width: 64px; height: 64px; border-radius: 50%; background: rgba(16,185,129,0.15); color: var(--success); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
    </style>
</head>
<body class="login-page">
    <div class="register-container">
        <?php if ($success): ?>
        <div class="register-card success-card">
            <div class="success-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <h2>Başvurunuz Alındı!</h2>
            <p style="color:var(--text-muted);margin:1rem 0;">Başvurunuz incelendikten sonra e-posta ile bilgilendirileceksiniz. Bu süreç genellikle 1-2 iş günü sürmektedir.</p>
            <a href="login.php" class="btn btn-primary">Giriş Sayfasına Dön</a>
        </div>
        <?php else: ?>
        <div class="register-card">
            <div class="register-header">
                <h1>ğŸª Üretici Başvurusu</h1>
                <p>ButikÇarşı'da ürünlerinizi satmaya başlayın</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Butik / Marka Adı *</label>
                        <input type="text" name="brand_name" required value="<?php echo htmlspecialchars($_POST['brand_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Ad Soyad *</label>
                        <input type="text" name="owner_name" required value="<?php echo htmlspecialchars($_POST['owner_name'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>E-posta *</label>
                        <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Telefon</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Şifre *</label>
                        <input type="password" name="password" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Şifre Tekrar *</label>
                        <input type="password" name="password_confirm" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Instagram @kullanıcı</label>
                        <input type="text" name="instagram_handle" placeholder="@butikadi" value="<?php echo htmlspecialchars($_POST['instagram_handle'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Instagram Profil URL</label>
                        <input type="url" name="instagram_url" value="<?php echo htmlspecialchars($_POST['instagram_url'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Şehir</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Butik Tanıtımı</label>
                    <textarea name="description" rows="3" placeholder="Ne üretiyorsunuz? Hangi malzemeleri kullanıyorsunuz?"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="background:linear-gradient(135deg,#f59e0b,#ef4444);">Başvuru Yap</button>
                <p style="text-align:center;margin-top:1rem;">
                    <a href="login.php" class="link" style="font-size:0.85rem;">Zaten hesabınız var mı? Giriş yapın</a>
                </p>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
