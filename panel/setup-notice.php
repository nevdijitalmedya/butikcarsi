<?php
$dbHost = Config::get('DB_HOST', 'localhost');
$dbName = Config::get('DB_NAME', 'butikcarsi_db');
$dbUser = Config::get('DB_USER', 'root');
$errMsg = $dbException ? $dbException->getMessage() : 'Bilinmeyen hata';

// Allow saving DB credentials directly from browser
$saveMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_mysql'])) {
        $newHost = trim($_POST['host'] ?? 'localhost');
        $newName = trim($_POST['name'] ?? 'butikcarsi_db');
        $newUser = trim($_POST['user'] ?? 'root');
        $newPass = trim($_POST['pass'] ?? '');

        $envContent = "DB_CONNECTION=mysql\nDB_HOST={$newHost}\nDB_NAME={$newName}\nDB_USER={$newUser}\nDB_PASS={$newPass}\nAPP_ENV=development\nAPP_DEBUG=true\n";
        file_put_contents(PANEL_PATH . '/.env', $envContent);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
    if (isset($_POST['use_sqlite'])) {
        $envContent = "DB_CONNECTION=sqlite\nDB_HOST=localhost\nDB_NAME=butikcarsi_db\nDB_USER=root\nDB_PASS=\nAPP_ENV=development\nAPP_DEBUG=true\n";
        file_put_contents(PANEL_PATH . '/.env', $envContent);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veritabanı Kurulumu — ButikÇarşı</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-paper: #faf8f5;
            --color-surface: #ffffff;
            --color-ink: #18202f;
            --color-ink-muted: #596780;
            --color-primary: #c85834;
            --color-primary-hover: #b04927;
            --color-border: #e8dfd5;
            --radius-md: 14px;
            --radius-sm: 8px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--color-paper); color: var(--color-ink); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
        .setup-card { background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 2.5rem; max-width: 580px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
        .badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.8rem; background: #fee2e2; color: #991b1b; font-size: 0.8rem; font-weight: 700; border-radius: 999px; margin-bottom: 1rem; }
        h1 { font-family: 'Outfit', sans-serif; font-size: 1.8rem; margin-bottom: 0.6rem; color: var(--color-ink); font-weight: 800; line-height: 1.25; }
        p { color: var(--color-ink-muted); font-size: 0.95rem; margin-bottom: 1.5rem; line-height: 1.6; }
        .error-box { background: #fef2f2; border: 1px solid #fecaca; padding: 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; color: #b91c1c; margin-bottom: 1.5rem; font-family: monospace; word-break: break-all; }
        .form-group { margin-bottom: 1rem; text-align: left; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.3rem; color: var(--color-ink); }
        .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: inherit; font-size: 0.9rem; }
        .form-control:focus { outline: none; border-color: var(--color-primary); }
        .btn { display: inline-flex; align-items: center; justify-content: center; width: 100%; padding: 0.85rem; border-radius: var(--radius-sm); font-weight: 700; font-size: 0.95rem; cursor: pointer; border: none; transition: all 0.2s ease; text-decoration: none; }
        .btn-primary { background: var(--color-primary); color: #fff; margin-bottom: 0.75rem; }
        .btn-primary:hover { background: var(--color-primary-hover); }
        .btn-secondary { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .btn-secondary:hover { background: #e2e8f0; }
        .divider { display: flex; align-items: center; text-align: center; margin: 1.5rem 0; color: #94a3b8; font-size: 0.8rem; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--color-border); }
        .divider span { padding: 0 0.8rem; }
    </style>
</head>
<body>
    <div class="setup-card">
        <span class="badge">Veritabanı Ayarı Gerekli</span>
        <h1>Veritabanı Bağlantısı</h1>
        <p>Hostinger veya yerel sunucunuzdaki veritabanı bilgilerini aşağıdan güncelleyebilir veya tek tıkla SQLite moduna geçebilirsiniz:</p>

        <div class="error-box">
            <?= htmlspecialchars($errMsg) ?>
        </div>

        <form method="POST">
            <input type="hidden" name="save_mysql" value="1">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem;">
                <div class="form-group">
                    <label>MySQL Host</label>
                    <input type="text" name="host" class="form-control" value="<?= htmlspecialchars($dbHost) ?>" placeholder="localhost">
                </div>
                <div class="form-group">
                    <label>Veritabanı Adı (DB_NAME)</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($dbName) ?>" placeholder="u123456_butikcarsi">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem;">
                <div class="form-group">
                    <label>Kullanıcı Adı (DB_USER)</label>
                    <input type="text" name="user" class="form-control" value="<?= htmlspecialchars($dbUser) ?>" placeholder="u123456_user">
                </div>
                <div class="form-group">
                    <label>Şifre (DB_PASS)</label>
                    <input type="password" name="pass" class="form-control" placeholder="Veritabanı şifresi">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Kaydet ve MySQL ile Bağlan</button>
        </form>

        <div class="divider"><span>VEYA</span></div>

        <form method="POST">
            <input type="hidden" name="use_sqlite" value="1">
            <button type="submit" class="btn btn-secondary">
                Sıfır Kurulum: Tek Tıkla SQLite Moduna Geç
            </button>
        </form>
    </div>
</body>
</html>
