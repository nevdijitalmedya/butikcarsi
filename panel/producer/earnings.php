<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Kazançlarım';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pid = Auth::producerId();
$producer = Database::query("SELECT * FROM producers WHERE id = ?", [$pid]);

$stats = Database::query("
    SELECT 
        COALESCE(SUM(subtotal), 0) as total_volume,
        COALESCE(SUM(commission_amount), 0) as total_commission,
        COALESCE(SUM(producer_earning), 0) as total_earning,
        COALESCE(SUM(CASE WHEN payout_status = 'held' THEN producer_earning ELSE 0 END), 0) as held_amount,
        COALESCE(SUM(CASE WHEN payout_status = 'released' THEN producer_earning ELSE 0 END), 0) as released_amount,
        COALESCE(SUM(CASE WHEN payout_status = 'paid' THEN producer_earning ELSE 0 END), 0) as paid_amount
    FROM order_sub_orders 
    WHERE producer_id = ?
", [$pid]);

$payouts = Database::queryAll("SELECT * FROM payouts WHERE producer_id = ? ORDER BY id DESC", [$pid]);
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Kazançlarım & Hak Edişler</h2>
    </div>

    <div class="admin-content">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Toplam Satış Hacminiz</div>
                <div class="stat-value">₺<?= number_format($stats['total_volume'], 2, ',', '.') ?></div>
                <div class="stat-meta">Tüm siparişlerin brüt tutarı</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Platform Komisyonu (%10)</div>
                <div class="stat-value text-muted">₺<?= number_format($stats['total_commission'], 2, ',', '.') ?></div>
                <div class="stat-meta">Platform aracılık kesintisi</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Net Toplam Kazancınız</div>
                <div class="stat-value text-success">₺<?= number_format($stats['total_earning'], 2, ',', '.') ?></div>
                <div class="stat-meta">Size tahsis edilen %90 pay</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Emanet (Escrow) Bekleyen</div>
                <div class="stat-value text-warning">₺<?= number_format($stats['held_amount'], 2, ',', '.') ?></div>
                <div class="stat-meta">Teslimat onayı bekleyen bakiye</div>
            </div>
        </div>

        <div class="card mt-4">
            <h3 class="card-title">Kayıtlı Banka & IBAN Bilgisi</h3>
            <p>Ödemeleriniz alıcı teslimat onayı sonrasında aşağıdaki IBAN adresinize aktarılır:</p>
            <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center">
                <div>
                    <div><strong>Banka:</strong> <?= htmlspecialchars($producer['bank_name'] ?: 'Belirtilmedi') ?></div>
                    <div><strong>IBAN:</strong> <?= htmlspecialchars($producer['iban'] ?: 'Henüz IBAN girilmedi') ?></div>
                </div>
                <a href="profile.php" class="btn btn-sm btn-outline">Bilgileri Güncelle</a>
            </div>
        </div>

        <div class="card table-card mt-4">
            <div class="card-header">
                <h3 class="card-title">Ödeme Geçmişi (Transferler)</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Tutar</th>
                            <th>Banka / IBAN</th>
                            <th>Referans Kodu</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payouts)): ?>
                            <tr><td colspan="5" class="empty-state">Henüz hesabınıza aktarılmış bir ödeme bulunmamaktadır.</td></tr>
                        <?php else: ?>
                            <?php foreach ($payouts as $po): ?>
                                <tr>
                                    <td><?= date('d.m.Y H:i', strtotime($po['created_at'])) ?></td>
                                    <td><strong>₺<?= number_format($po['net_amount'], 2, ',', '.') ?></strong></td>
                                    <td><?= htmlspecialchars($po['bank_name']) ?> (<?= htmlspecialchars($po['iban']) ?>)</td>
                                    <td><?= htmlspecialchars($po['reference_code'] ?: '—') ?></td>
                                    <td><span class="badge badge-success"><?= htmlspecialchars($po['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
