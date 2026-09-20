<?php
/**
 * Producer Registration / Application Form — ButikÇarşı
 * Hallmark Artisan Design — Mobile-First & Responsive
 */
header('Content-Type: text/html; charset=UTF-8');
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
        $error = 'Şifreler eşleşmiyor. Lütfen her iki alana da aynı şifreyi giriniz.';
    } elseif ($v->hasErrors()) {
        $error = $v->firstError();
    } else {
        $existing = Database::query("SELECT id FROM producers WHERE email = ?", [$email]);
        if ($existing) {
            $error = 'Bu e-posta adresi ile kayıtlı bir üretici zaten mevcut.';
        } else {
            $slug = Validator::makeSlug($brandName);
            $slugCheck = Database::query("SELECT id FROM producers WHERE slug = ?", [$slug]);
            if ($slugCheck) {
                $slug .= '-' . time();
            }

            Database::execute("
                INSERT INTO producers (brand_name, owner_name, email, phone, password_hash, slug,
                instagram_handle, instagram_url, city, description, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ", [
                $brandName, $ownerName, $email, $phone,
                password_hash($password, PASSWORD_BCRYPT),
                $slug, $instagramHandle, $instagramUrl, $city, $description
            ]);

            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Üretici Başvurusu — ButikÇarşı</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-paper: #faf8f5;
            --color-surface: #ffffff;
            --color-ink: #18202f;
            --color-ink-muted: #596780;
            --color-ink-faint: #8e9bb0;
            --color-primary: #c85834;
            --color-primary-hover: #b04927;
            --color-primary-soft: #fbf0ec;
            --color-border: #e8dfd5;
            --color-border-focus: #c85834;
            --radius-md: 14px;
            --radius-sm: 8px;
            --radius-full: 999px;
            --shadow-card: 0 10px 25px -5px rgba(24, 32, 47, 0.05), 0 8px 10px -6px rgba(24, 32, 47, 0.03);
            --font-heading: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-body: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { overflow-x: clip; min-height: 100%; }
        body {
            font-family: var(--font-body);
            background-color: var(--color-paper);
            color: var(--color-ink);
            line-height: 1.55;
            padding: 1.5rem 1rem 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .back-nav {
            width: 100%;
            max-width: 580px;
            margin-bottom: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--color-ink-muted);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: color 0.15s ease;
        }
        .back-link:hover { color: var(--color-primary); }
        .register-container { width: 100%; max-width: 580px; }
        .register-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: clamp(1.25rem, 4vw, 2.25rem);
            box-shadow: var(--shadow-card);
        }
        .register-header { text-align: center; margin-bottom: 1.75rem; }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--color-primary);
            color: #ffffff;
            margin-bottom: 0.85rem;
            box-shadow: 0 4px 12px rgba(200, 88, 52, 0.25);
        }
        .register-header h1 {
            font-family: var(--font-heading);
            font-size: clamp(1.5rem, 5vw, 1.85rem);
            font-weight: 700;
            color: var(--color-ink);
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
            overflow-wrap: anywhere;
            min-width: 0;
        }
        .register-header p {
            color: var(--color-ink-muted);
            font-size: 0.92rem;
            max-width: 440px;
            margin: 0 auto 1.1rem;
        }
        .value-pills {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.4rem;
            margin-bottom: 0.5rem;
        }
        .value-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: var(--radius-full);
            background: var(--color-primary-soft);
            color: var(--color-primary);
            font-size: 0.76rem;
            font-weight: 700;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 0.85rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .col-span-2 { grid-column: 1 / -1; }
        .form-group { display: flex; flex-direction: column; gap: 0.35rem; }
        .form-group label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--color-ink);
        }
        .form-group input, .form-group textarea {
            width: 100%;
            min-height: 46px;
            padding: 0.65rem 0.9rem;
            font-family: inherit;
            font-size: 15px;
            color: var(--color-ink);
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: var(--radius-sm);
            transition: all 0.15s ease;
            outline: none;
        }
        .form-group textarea { min-height: 85px; resize: vertical; }
        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--color-border-focus);
            box-shadow: 0 0 0 3px rgba(200, 88, 52, 0.12);
        }
        .btn-submit {
            width: 100%;
            min-height: 48px;
            margin-top: 0.5rem;
            background: var(--color-primary);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.98rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.15s ease;
            box-shadow: 0 4px 12px rgba(200, 88, 52, 0.25);
        }
        .btn-submit:hover {
            background: var(--color-primary-hover);
            transform: translateY(-1px);
        }
        .btn-submit:active { transform: translateY(0); }
        .card-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.88rem;
            color: var(--color-ink-muted);
        }
        .card-footer a { color: var(--color-primary); font-weight: 600; text-decoration: none; }
        .card-footer a:hover { text-decoration: underline; }
        .success-card { text-align: center; padding: 2.5rem 1.5rem; }
        .success-badge {
            width: 64px;
            height: 64px;
            border-radius: var(--radius-full);
            background: #dcfce7;
            color: #15803d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }
        .success-card h2 {
            font-family: var(--font-heading);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-ink);
            margin-bottom: 0.6rem;
        }
        .success-card p {
            color: var(--color-ink-muted);
            font-size: 0.95rem;
            margin-bottom: 1.75rem;
            line-height: 1.6;
        }
        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; gap: 0.85rem; }
            .col-span-2 { grid-column: 1; }
            body { padding: 1rem 0.75rem 2.5rem; }
            .register-card { padding: 1.25rem 1rem; }
        }
    </style>
</head>
<body>
    <nav class="back-nav" aria-label="Geri Dön">
        <a href="/" class="back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>
            ButikÇarşı Vitrinine Dön
        </a>
        <a href="login.php" class="back-link">
            Atölye Girişi
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </a>
    </nav>

    <main class="register-container">
        <?php if ($success): ?>
        <div class="register-card success-card">
            <div class="success-badge">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <h2>Başvurunuz Alındı!</h2>
            <p>Butik başvurunuz başarıyla sisteme kaydedildi. Ekibimiz atölyenizi ve Instagram profilinizi inceleyip <strong>24 saat içinde</strong> e-posta yoluyla onay bildirecektir.</p>
            <a href="login.php" class="btn-submit" style="text-decoration:none;">
                Atölye Girişine Git
            </a>
        </div>
        <?php else: ?>
        <div class="register-card">
            <header class="register-header">
                <div class="brand-badge">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </div>
                <h1>Üretici Başvurusu</h1>
                <p>Instagram sayfanızdaki el emeği ve butik ürünleri ButikÇarşı vitrininde binlerce müşteriyle buluşturun.</p>
                
                <div class="value-pills">
                    <span class="value-pill">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        Sabit Ücret Yok
                    </span>
                    <span class="value-pill">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        Sadece %10 Komisyon
                    </span>
                    <span class="value-pill">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        Ücretsiz Vitrin
                    </span>
                </div>
            </header>

            <?php if ($error): ?>
                <div class="alert-error" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="form-grid" novalidate>
                <div class="form-group">
                    <label for="brand_name">Butik / Marka Adı *</label>
                    <input type="text" id="brand_name" name="brand_name" required placeholder="Örn: Lazerci Hediyelik" value="<?php echo htmlspecialchars($_POST['brand_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="owner_name">Yetkili Ad Soyad *</label>
                    <input type="text" id="owner_name" name="owner_name" required placeholder="Adınız ve Soyadınız" value="<?php echo htmlspecialchars($_POST['owner_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">E-posta *</label>
                    <input type="email" id="email" name="email" required placeholder="iletisim@butiginiz.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Telefon</label>
                    <input type="tel" id="phone" name="phone" placeholder="05XX XXX XX XX" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Giriş Şifresi *</label>
                    <input type="password" id="password" name="password" required minlength="6" placeholder="En az 6 karakter">
                </div>
                <div class="form-group">
                    <label for="password_confirm">Şifre Tekrar *</label>
                    <input type="password" id="password_confirm" name="password_confirm" required placeholder="Şifrenizi tekrar yazın">
                </div>

                <div class="form-group">
                    <label for="instagram_handle">Instagram Kullanıcı Adı</label>
                    <input type="text" id="instagram_handle" name="instagram_handle" placeholder="@lazerci_hediyelik" value="<?php echo htmlspecialchars($_POST['instagram_handle'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="instagram_url">Instagram Profil Linki</label>
                    <input type="url" id="instagram_url" name="instagram_url" placeholder="https://instagram.com/..." value="<?php echo htmlspecialchars($_POST['instagram_url'] ?? ''); ?>">
                </div>

                <div class="form-group col-span-2">
                    <label for="city">Bulunduğunuz Şehir</label>
                    <input type="text" id="city" name="city" placeholder="Örn: İstanbul, İzmir, Ankara" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                </div>

                <div class="form-group col-span-2">
                    <label for="description">Atölye ve Ürün Tanıtımı</label>
                    <textarea id="description" name="description" placeholder="Hangi el emeği ürünleri üretiyorsunuz? Lazer kesim, epoksi, deri vb. kısaca anlatın..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="col-span-2">
                    <button type="submit" class="btn-submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>
                        </svg>
                        Üretici Başvurusunu Tamamla
                    </button>
                </div>
            </form>

            <footer class="card-footer">
                Zaten bir atölye hesabınız var mı? <a href="login.php">Giriş Yapın →</a>
            </footer>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
