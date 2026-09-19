<?php
/**
 * Platform Settings — ButikÇarşı Admin
 */
require_once __DIR__ . '/../config.php';
$pageTitle = 'Ayarlar';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = $_POST['settings'] ?? [];
    foreach ($settings as $key => $value) {
        Config::updateSetting($key, trim($value));
    }
    $success = 'Ayarlar güncellendi.';
}

// Group settings
$settingGroups = [
    'Genel' => ['site_name','tagline','email','phone','logo_url','favicon_url','primary_color','secondary_color','accent_color'],
    'Sosyal Medya' => ['social_instagram','social_tiktok'],
    'Komisyon & Finans' => ['default_commission_rate','currency','auto_approval_days'],
    'iyzico Ödeme' => ['iyzico_api_key','iyzico_secret_key','iyzico_base_url'],
    'E-posta (SMTP)' => ['smtp_host','smtp_port','smtp_user','smtp_pass','smtp_from_name','smtp_from_email'],
];

$allSettings = Database::queryAll("SELECT setting_key, setting_value FROM platform_settings");
$settingsMap = [];
foreach ($allSettings as $s) {
    $settingsMap[$s['setting_key']] = $s['setting_value'];
}

$labels = [
    'site_name' => 'Site Adı', 'tagline' => 'Slogan', 'email' => 'E-posta', 'phone' => 'Telefon',
    'logo_url' => 'Logo URL', 'favicon_url' => 'Favicon URL', 'social_instagram' => 'Instagram',
    'social_tiktok' => 'TikTok', 'default_commission_rate' => 'Varsayılan Komisyon (%)',
    'currency' => 'Para Birimi', 'auto_approval_days' => 'Otomatik Onay Süresi (gün)',
    'iyzico_api_key' => 'API Key', 'iyzico_secret_key' => 'Secret Key', 'iyzico_base_url' => 'Base URL',
    'smtp_host' => 'SMTP Host', 'smtp_port' => 'Port', 'smtp_user' => 'Kullanıcı', 'smtp_pass' => 'Şifre',
    'smtp_from_name' => 'Gönderen Adı', 'smtp_from_email' => 'Gönderen E-posta',
    'primary_color' => 'Ana Renk', 'secondary_color' => 'İkincil Renk', 'accent_color' => 'Vurgu Renk',
];
?>

<main class="admin-main">
    <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <h2 class="page-title">Platform Ayarları</h2>
    </div>
    <div class="admin-content">
        <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
        <form method="POST">
            <?php foreach ($settingGroups as $groupName => $keys): ?>
            <div class="card mb-2">
                <div class="card-header"><h3><?php echo $groupName; ?></h3></div>
                <div class="card-body">
                    <div class="form-row">
                        <?php foreach ($keys as $key): ?>
                        <div class="form-group">
                            <label><?php echo $labels[$key] ?? $key; ?></label>
                            <?php if (str_contains($key, 'color')): ?>
                                <input type="color" name="settings[<?php echo $key; ?>]" value="<?php echo htmlspecialchars($settingsMap[$key] ?? '#000000'); ?>" style="height:40px;padding:4px;">
                            <?php elseif (str_contains($key, 'pass') || str_contains($key, 'secret')): ?>
                                <input type="password" name="settings[<?php echo $key; ?>]" value="<?php echo htmlspecialchars($settingsMap[$key] ?? ''); ?>">
                            <?php else: ?>
                                <input type="text" name="settings[<?php echo $key; ?>]" value="<?php echo htmlspecialchars($settingsMap[$key] ?? ''); ?>">
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Ayarları Kaydet</button>
        </form>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
