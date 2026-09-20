<?php
/**
 * Admin Login Page — ButikÇarşı Platform
 * Hallmark Artisan Design — Mobile-First & Responsive
 */
header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (Auth::adminLogin($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Geçersiz kullanıcı adı veya şifre.';
    }
}

$siteName = Config::setting('site_name', 'ButikÇarşı');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Yönetim Girişi — <?php echo htmlspecialchars($siteName); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-paper: #faf8f5;
            --color-surface: #ffffff;
            --color-ink: #18202f;
            --color-ink-muted: #596780;
            --color-primary: #18202f;
            --color-primary-hover: #0f141f;
            --color-accent: #c85834;
            --color-border: #e8dfd5;
            --radius-md: 14px;
            --radius-sm: 8px;
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
        .login-container { width: 100%; max-width: 420px; }
        .login-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: clamp(1.5rem, 5vw, 2.5rem);
            box-shadow: var(--shadow-card);
        }
        .login-header { text-align: center; margin-bottom: 2rem; }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: var(--color-ink);
            color: #ffffff;
            margin-bottom: 0.85rem;
            box-shadow: 0 4px 12px rgba(24, 32, 47, 0.2);
        }
        .login-header h1 {
            font-family: var(--font-heading);
            font-size: clamp(1.5rem, 5vw, 1.85rem);
            font-weight: 700;
            color: var(--color-ink);
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
        }
        .login-header p {
            color: var(--color-ink-muted);
            font-size: 0.92rem;
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
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            margin-bottom: 1.15rem;
        }
        .form-group label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--color-ink);
        }
        .form-group input {
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
        .form-group input:focus {
            border-color: var(--color-accent);
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
        }
        .btn-submit:hover {
            background: var(--color-primary-hover);
            transform: translateY(-1px);
        }
        .card-footer {
            margin-top: 1.75rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--color-ink-muted);
        }
        .card-footer a { color: var(--color-accent); font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
    <main class="login-container">
        <div class="login-card">
            <header class="login-header">
                <div class="brand-badge">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h1>Yönetim Paneli</h1>
                <p><?php echo htmlspecialchars($siteName); ?> Platform Denetimi</p>
            </header>

            <?php if ($error): ?>
                <div class="alert-error" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" required autofocus placeholder="admin" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn-submit">
                    Yönetici Girişi Yap
                </button>
            </form>

            <footer class="card-footer">
                <a href="/">← ButikÇarşı Vitrinine Dön</a>
            </footer>
        </div>
    </main>
</body>
</html>
