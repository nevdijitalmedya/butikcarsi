<?php
/**
 * Admin Login Page â€” ButikÃ‡arÅŸÄ± Platform
 */
require_once __DIR__ . '/../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (Auth::adminLogin($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'GeÃ§ersiz kullanÄ±cÄ± adÄ± veya ÅŸifre.';
    }
}

$siteName = Config::setting('site_name', 'ButikÃ‡arÅŸÄ±');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin GiriÅŸ â€” <?php echo htmlspecialchars($siteName); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                </div>
                <h1>ButikÃ‡arÅŸÄ±</h1>
                <p>Platform YÃ¶netim Paneli</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">KullanÄ±cÄ± AdÄ±</label>
                    <input type="text" id="username" name="username" required autofocus
                           placeholder="admin" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Åifre</label>
                    <input type="password" id="password" name="password" required placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                </div>
                <button type="submit" class="btn btn-primary btn-block">GiriÅŸ Yap</button>
            </form>
        </div>
        <p class="login-footer-text">&copy; <?php echo date('Y'); ?> ButikÃ‡arÅŸÄ± â€” TÃ¼m haklarÄ± saklÄ±dÄ±r.</p>
    </div>
</body>
</html>
