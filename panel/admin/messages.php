<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'İletişim Mesajları';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$messages = Database::queryAll("SELECT * FROM contact_messages ORDER BY id DESC");
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">İletişim & Destek Mesajları</h2>
    </div>

    <div class="admin-content">
        <div class="card table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Gönderen</th>
                        <th>İletişim</th>
                        <th>Konu</th>
                        <th>Mesaj</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr><td colspan="5" class="empty-state">Henüz bir mesaj bulunmuyor.</td></tr>
                    <?php else: ?>
                        <?php foreach ($messages as $m): ?>
                            <tr>
                                <td><?= date('d.m.Y H:i', strtotime($m['created_at'])) ?></td>
                                <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                                <td>
                                    <div><?= htmlspecialchars($m['email']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($m['phone'] ?? '') ?></div>
                                </td>
                                <td><?= htmlspecialchars($m['subject']) ?></td>
                                <td><?= nl2br(htmlspecialchars($m['message'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
