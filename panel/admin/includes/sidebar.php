<?php
/**
 * Admin Sidebar Navigation
 */
$menuItems = [
    ['page' => 'index',       'icon' => 'dashboard',  'label' => 'Dashboard'],
    ['page' => 'producers',   'icon' => 'store',      'label' => 'Ãœreticiler',   'badge' => $pendingProducers > 0 ? $pendingProducers : null],
    ['page' => 'products',    'icon' => 'package',     'label' => 'ÃœrÃ¼nler',      'badge' => $pendingProducts > 0 ? $pendingProducts : null],
    ['page' => 'orders',      'icon' => 'shopping-cart','label' => 'SipariÅŸler',  'badge' => $newOrders > 0 ? $newOrders : null],
    ['page' => 'categories',  'icon' => 'grid',        'label' => 'Kategoriler'],
    ['page' => 'commissions', 'icon' => 'percent',     'label' => 'Komisyonlar'],
    ['page' => 'payouts',     'icon' => 'credit-card', 'label' => 'Ã–demeler'],
    ['page' => 'banners',     'icon' => 'image',       'label' => 'Bannerlar'],
    ['page' => 'messages',    'icon' => 'mail',        'label' => 'Mesajlar'],
    ['page' => 'settings',    'icon' => 'settings',    'label' => 'Ayarlar'],
];
?>
<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <span>ButikÃ‡arÅŸÄ±</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($menuItems as $item): ?>
            <a href="<?php echo $item['page']; ?>.php"
               class="sidebar-link <?php echo $currentPage === $item['page'] ? 'active' : ''; ?>">
                <span class="sidebar-icon" data-icon="<?php echo $item['icon']; ?>"></span>
                <span class="sidebar-label"><?php echo $item['label']; ?></span>
                <?php if (!empty($item['badge'])): ?>
                    <span class="sidebar-badge"><?php echo $item['badge']; ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><?php echo mb_substr($adminName, 0, 1); ?></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?php echo htmlspecialchars($adminName); ?></span>
                <span class="sidebar-user-role"><?php echo ucfirst($_SESSION['admin_role'] ?? 'admin'); ?></span>
            </div>
        </div>
        <a href="logout.php" class="sidebar-logout" title="Ã‡Ä±kÄ±ÅŸ Yap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </a>
    </div>
</aside>
