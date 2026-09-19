<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Sipariş Detayı';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$id = (int)($_GET['id'] ?? 0);
$order = Database::query("SELECT * FROM orders WHERE id = ?", [$id]);

if (!$order) {
    echo "<div class='p-4 text-danger'>Sipariş bulunamadı.</div>";
    exit;
}

$subOrders = Database::queryAll("
    SELECT so.*, p.brand_name, p.owner_name, p.phone as producer_phone, p.iban as producer_iban
    FROM order_sub_orders so
    JOIN producers p ON so.producer_id = p.id
    WHERE so.order_id = ?
", [$id]);

$items = Database::queryAll("SELECT * FROM order_items WHERE order_id = ?", [$id]);

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['release_payout'])) {
    $subOrderId = (int)$_POST['sub_order_id'];
    Database::execute("
        UPDATE order_sub_orders 
        SET payout_status = 'released', payout_released_at = NOW() 
        WHERE id = ? AND order_id = ?
    ", [$subOrderId, $id]);
    $msg = 'Üretici hak edişi emanetten serbest bırakıldı!';
    // Refresh
    $subOrders = Database::queryAll("
        SELECT so.*, p.brand_name, p.owner_name, p.phone as producer_phone, p.iban as producer_iban
        FROM order_sub_orders so
        JOIN producers p ON so.producer_id = p.id
        WHERE so.order_id = ?
    ", [$id]);
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Sipariş: <?= htmlspecialchars($order['order_number']) ?></h2>
        <div class="topbar-actions">
            <a href="orders.php" class="btn btn-secondary">Siparişlere Dön</a>
        </div>
    </div>

    <div class="admin-content">
        <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

        <div class="grid-3">
            <div class="col-span-2">
                <div class="card mb-4">
                    <h3 class="card-title">Üretici Bazlı Alt Siparişler (Sub-Orders)</h3>
                    <?php foreach ($subOrders as $so): ?>
                        <div class="p-3 border rounded mb-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h4 class="m-0">Butik: <strong><?= htmlspecialchars($so['brand_name']) ?></strong> (<?= htmlspecialchars($so['sub_order_number']) ?>)</h4>
                                    <span class="text-muted small">İletişim: <?= htmlspecialchars($so['producer_phone'] ?? '—') ?></span>
                                </div>
                                <div>
                                    <span class="badge badge-info">Durum: <?= htmlspecialchars($so['status']) ?></span>
                                    <span class="badge badge-<?= $so['payout_status'] === 'released' ? 'success' : 'warning' ?>">
                                        Emanet: <?= htmlspecialchars($so['payout_status']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid-3 mb-2 small">
                                <div><strong>Alt Toplam:</strong> ₺<?= number_format($so['subtotal'], 2, ',', '.') ?></div>
                                <div><strong>Komisyon (%10):</strong> ₺<?= number_format($so['commission_amount'], 2, ',', '.') ?></div>
                                <div><strong>Üretici Payı:</strong> <strong class="text-success">₺<?= number_format($so['producer_earning'], 2, ',', '.') ?></strong></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                <div class="small">
                                    <strong>Kargo:</strong> <?= htmlspecialchars($so['shipping_provider'] ?: 'Girilmedi') ?> 
                                    (<?= htmlspecialchars($so['tracking_number'] ?: 'Takip No Yok') ?>)
                                </div>
                                <?php if ($so['payout_status'] === 'held'): ?>
                                    <form method="POST" onsubmit="return confirm('Bu alt siparişin ödemesini serbest bırakmak istiyor musunuz?');">
                                        <input type="hidden" name="release_payout" value="1">
                                        <input type="hidden" name="sub_order_id" value="<?= $so['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success">Emanet Ödemeyi Serbest Bırak</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card">
                    <h3 class="card-title">Sepetteki Ürünler</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Üretici</th>
                                <th>Adet</th>
                                <th>Birim</th>
                                <th>Toplam</th>
                                <th>Kişiselleştirme</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($it['product_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($it['producer_name'] ?? '—') ?></td>
                                    <td><?= $it['quantity'] ?></td>
                                    <td>₺<?= number_format($it['unit_price'], 2, ',', '.') ?></td>
                                    <td>₺<?= number_format($it['total_price'], 2, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($it['customization_text'] ?: '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="card">
                    <h3 class="card-title">Müşteri & Teslimat</h3>
                    <p><strong>Ad Soyad:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><strong>E-posta:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                    <p><strong>Telefon:</strong> <?= htmlspecialchars($order['customer_phone'] ?? '—') ?></p>
                    <hr>
                    <p><strong>Teslimat Adresi:</strong><br><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                    <p><strong>Şehir / İlçe:</strong> <?= htmlspecialchars($order['shipping_city'] ?? '') ?> / <?= htmlspecialchars($order['shipping_district'] ?? '') ?></p>
                    <?php if ($order['customer_note']): ?>
                        <hr>
                        <p><strong>Müşteri Notu:</strong><br><?= nl2br(htmlspecialchars($order['customer_note'])) ?></p>
                    <?php endif; ?>
                </div>

                <div class="card mt-3">
                    <h3 class="card-title">iyzico & Ödeme Özeti</h3>
                    <p><strong>Toplam Tutar:</strong> ₺<?= number_format($order['grand_total'], 2, ',', '.') ?></p>
                    <p><strong>Platform Geliri:</strong> <strong class="text-success">₺<?= number_format($order['commission_total'], 2, ',', '.') ?></strong></p>
                    <p><strong>iyzico Payment ID:</strong> <?= htmlspecialchars($order['iyzico_payment_id'] ?: 'Simüle Edildi') ?></p>
                    <p><strong>Ödeme Durumu:</strong> <span class="badge badge-success"><?= htmlspecialchars($order['payment_status']) ?></span></p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
