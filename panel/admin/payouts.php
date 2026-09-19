<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Üretici Ödemeleri (Payouts)';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pendingPayouts = Database::queryAll("
    SELECT so.*, p.brand_name, p.owner_name, p.bank_name, p.iban, o.order_number
    FROM order_sub_orders so
    JOIN producers p ON so.producer_id = p.id
    JOIN orders o ON so.order_id = o.id
    WHERE so.payout_status = 'released'
    ORDER BY so.id ASC
");

$historyPayouts = Database::queryAll("
    SELECT po.*, p.brand_name, p.owner_name
    FROM payouts po
    JOIN producers p ON po.producer_id = p.id
    ORDER BY po.id DESC LIMIT 50
");

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid'])) {
    $subOrderId = (int)$_POST['sub_order_id'];
    $sub = Database::query("SELECT * FROM order_sub_orders WHERE id = ?", [$subOrderId]);
    if ($sub) {
        $prod = Database::query("SELECT * FROM producers WHERE id = ?", [$sub['producer_id']]);
        $ref = 'TRF-' . strtoupper(substr(md5(uniqid()), 0, 8));
        
        Database::insert("
            INSERT INTO payouts (producer_id, amount, fee, net_amount, iban, bank_name, status, processed_at, reference_code)
            VALUES (?, ?, 0, ?, ?, ?, 'completed', NOW(), ?)
        ", [$sub['producer_id'], $sub['producer_earning'], $sub['producer_earning'], $prod['iban'] ?? '', $prod['bank_name'] ?? '', $ref]);

        Database::execute("
            UPDATE order_sub_orders 
            SET payout_status = 'paid', payout_paid_at = NOW() 
            WHERE id = ?
        ", [$subOrderId]);

        $msg = "Ödeme onaylandı ve referans kodu oluşturuldu ($ref)!";
        // Refresh
        header("Location: payouts.php?msg=success");
        exit;
    }
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Üretici Hak Ediş Ödemeleri</h2>
    </div>

    <div class="admin-content">
        <?php if ($msg || isset($_GET['msg'])): ?><div class="alert alert-success">İşlem başarıyla tamamlandı.</div><?php endif; ?>

        <div class="card table-card mb-4">
            <div class="card-header">
                <h3 class="card-title">Transfer Bekleyen Hak Edişler (Onaylanmış Siparişler)</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Alt Sipariş</th>
                            <th>Üretici / Butik</th>
                            <th>Banka & IBAN</th>
                            <th>Tutar</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingPayouts)): ?>
                            <tr><td colspan="5" class="empty-state">Şu an transfer bekleyen bir hak ediş bulunmuyor.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pendingPayouts as $pp): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($pp['sub_order_number']) ?></strong></td>
                                    <td><?= htmlspecialchars($pp['brand_name']) ?> (<?= htmlspecialchars($pp['owner_name']) ?>)</td>
                                    <td>
                                        <div><strong><?= htmlspecialchars($pp['bank_name'] ?: '—') ?></strong></div>
                                        <div class="text-muted small"><?= htmlspecialchars($pp['iban'] ?: 'IBAN Eksik!') ?></div>
                                    </td>
                                    <td><strong class="text-success">₺<?= number_format($pp['producer_earning'], 2, ',', '.') ?></strong></td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('Bu tutarı üreticinin IBAN adresine havale ettiniz mi?');">
                                            <input type="hidden" name="mark_paid" value="1">
                                            <input type="hidden" name="sub_order_id" value="<?= $pp['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-primary">Ödendi Olarak İşaretle</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-header">
                <h3 class="card-title">Geçmiş Transfer Kayıtları</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Üretici</th>
                            <th>Tutar</th>
                            <th>IBAN</th>
                            <th>Referans</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historyPayouts)): ?>
                            <tr><td colspan="6" class="empty-state">Henüz tamamlanmış transfer kaydı yok.</td></tr>
                        <?php else: ?>
                            <?php foreach ($historyPayouts as $hp): ?>
                                <tr>
                                    <td><?= date('d.m.Y H:i', strtotime($hp['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($hp['brand_name']) ?></td>
                                    <td><strong>₺<?= number_format($hp['net_amount'], 2, ',', '.') ?></strong></td>
                                    <td><?= htmlspecialchars($hp['iban']) ?></td>
                                    <td><code><?= htmlspecialchars($hp['reference_code']) ?></code></td>
                                    <td><span class="badge badge-success"><?= htmlspecialchars($hp['status']) ?></span></td>
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
