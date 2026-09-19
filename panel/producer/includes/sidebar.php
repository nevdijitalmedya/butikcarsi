<?php
$producerMenu = [
    ['page' => 'index',    'icon' => 'dashboard',    'label' => 'Dashboard'],
    ['page' => 'products', 'icon' => 'package',      'label' => 'Ürünlerim'],
    ['page' => 'orders',   'icon' => 'shopping-cart', 'label' => 'Siparişlerim'],
    ['page' => 'earnings', 'icon' => 'credit-card',  'label' => 'Kazançlarım'],
    ['page' => 'profile',  'icon' => 'user',         'label' => 'Profilim'],
];
?>
<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.5">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>ButikÇarşı</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <?php foreach ($producerMenu as $item): ?>
        <a href="<?php echo $item['page']; ?>.php" class="sidebar-link <?php echo $currentPage === $item['page'] ? 'active' : ''; ?>">
            <span class="sidebar-icon" data-icon="<?php echo $item['icon']; ?>"></span>
            <span class="sidebar-label"><?php echo $item['label']; ?></span>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><?php echo mb_substr($producerBrand, 0, 1); ?></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?php echo htmlspecialchars($producerBrand); ?></span>
                <span class="sidebar-user-role">Üretici</span>
            </div>
        </div>
        <a href="logout.php" class="sidebar-logout" title="Çıkış">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
        </a>
    </div>
</aside>
