<?php
require_once __DIR__ . '/config.php';

if (Auth::checkAdmin()) {
    header("Location: admin/index.php");
    exit;
}
if (Auth::checkProducer()) {
    header("Location: producer/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Portalı — ButikÇarşı</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
        .portal-card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 2.5rem; max-width: 520px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.4); text-align: center; }
        .logo { font-size: 2.5rem; margin-bottom: 0.5rem; }
        h1 { font-size: 1.8rem; margin-bottom: 0.5rem; color: #fff; font-weight: 800; }
        p { color: #94a3b8; font-size: 0.95rem; margin-bottom: 2rem; }
        .btn-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media(max-width: 480px) { .btn-grid { grid-template-columns: 1fr; } }
        .portal-btn { display: flex; flex-direction: column; align-items: center; gap: 0.6rem; padding: 1.5rem 1rem; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1rem; transition: all 0.25s ease; border: 1px solid transparent; }
        .btn-producer { background: rgba(245, 158, 11, 0.12); color: #fbbf24; border-color: rgba(245, 158, 11, 0.3); }
        .btn-producer:hover { background: #f59e0b; color: #000; transform: translateY(-3px); }
        .btn-admin { background: rgba(224, 99, 59, 0.12); color: #ff8a65; border-color: rgba(224, 99, 59, 0.3); }
        .btn-admin:hover { background: #e0633b; color: #fff; transform: translateY(-3px); }
        .portal-btn span.icon { font-size: 2rem; }
        .portal-btn span.desc { font-size: 0.78rem; font-weight: 400; opacity: 0.85; }
        .back-link { display: inline-block; margin-top: 1.8rem; color: #64748b; font-size: 0.85rem; text-decoration: none; }
        .back-link:hover { color: #cbd5e1; }
    </style>
</head>
<body>
    <div class="portal-card">
        <div class="logo">🛍️</div>
        <h1>ButikÇarşı Portalı</h1>
        <p>Giriş yapmak istediğiniz yönetim panelini seçiniz:</p>

        <div class="btn-grid">
            <a href="producer/login.php" class="portal-btn btn-producer">
                <span class="icon">🏪</span>
                <span>Üretici Paneli</span>
                <span class="desc">Atölye ve ürün yönetimi</span>
            </a>
            <a href="admin/login.php" class="portal-btn btn-admin">
                <span class="icon">🛡️</span>
                <span>Platform Admin</span>
                <span class="desc">Süper yönetici girişi</span>
            </a>
        </div>

        <a href="/" class="back-link">← ButikÇarşı Web Sitesine Dön</a>
    </div>
</body>
</html>
