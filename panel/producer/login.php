<?php
/**
 * Producer Login â€” ButikÃ‡arÅŸÄ±
 */
require_once __DIR__ . '/../config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (Auth::producerLogin($email, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'GeÃ§ersiz e-posta veya ÅŸifre.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ãœretici GiriÅŸi â€” ButikÃ‡arÅŸÄ±</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../admin/assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <h1>Ãœretici Paneli</h1>
                <p>ÃœrÃ¼nlerinizi yÃ¶netin, sipariÅŸlerinizi takip edin</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">E-posta</label>
                    <input type="email" id="email" name="email" required autofocus placeholder="uretici@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Åifre</label>
                    <input type="password" id="password" name="password" required placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢">
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">GiriÅŸ Yap</button>
            </form>

            <p style="text-align:center;margin-top:1.5rem;">
                <a href="register.php" class="link" style="font-size:0.85rem;">HenÃ¼z hesabÄ±nÄ±z yok mu? BaÅŸvuru yapÄ±n â†’</a>
            </p>
        </div>
    </div>
</body>
</html>
