<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Sipariş Detayı';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$pid = Auth::producerId();
$subId = (int)($_GET['id'] ?? 0);

$subOrder = Database::query("
    SELECT so.*, o.order_number, o.customer_name, o.customer_email, o.customer_phone, o.customer_note,
           o.shipping_address, o.shipping_city, o.shipping_district, o.shipping_zip, o.created_at as order_date
    FROM order_sub_orders so
    JOIN orders o ON so.order_id = o.id
    WHERE so.id = ? AND so.producer_id = ?
", [$subId, $pid]);

if (!$subOrder) {
    echo "<div class='p-4 text-danger'>Sipariş bulunamadı.</div>";
    exit;
}

$items = Database::queryAll("SELECT * FROM order_items WHERE sub_order_id = ?", [$subId]);

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_shipping'])) {
    $provider = trim($_POST['shipping_provider'] ?? '');
    $tracking = trim($_POST['tracking_number'] ?? '');
    $status = $_POST['status'] ?? $subOrder['status'];

    Database::execute("
        UPDATE order_sub_orders 
        SET shipping_provider = ?, tracking_number = ?, status = ?, shipped_at = IF(status = 'shipped' AND shipped_at IS NULL, NOW(), shipped_at)
        WHERE id = ? AND producer_id = ?
    ", [$provider, $tracking, $status, $subId, $pid]);

    // Send shipment notification if marked as shipped
    if ($status === 'shipped') {
        NotificationService::sendShipmentNotification($subOrder['order_id'], $provider, $tracking);
    }

    $msg = 'Kargo ve durum bilgisi güncellendi!';
    $subOrder['shipping_provider'] = $provider;
    $subOrder['tracking_number'] = $tracking;
    $subOrder['status'] = $status;
}
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Alt Sipariş #<?= htmlspecialchars($subOrder['sub_order_number']) ?></h2>
        <div class="topbar-actions">
            <a href="orders.php" class="btn btn-secondary">Siparişlere Dön</a>
        </div>
    </div>

    <div class="admin-content">
        <?php if ($msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <div class="grid-3">
            <div class="col-span-2">
                <div class="card">
                    <h3 class="card-title">Sipariş Edilen Ürünler</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Adet</th>
                                <th>Birim Fiyat</th>
                                <th>Toplam</th>
                                <th>Kişiselleştirme Notu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($it['product_name']) ?></strong>
                                    </td>
                                    <td><?= $it['quantity'] ?></td>
                                    <td>₺<?= number_format($it['unit_price'], 2, ',', '.') ?></td>
                                    <td>₺<?= number_format($it['total_price'], 2, ',', '.') ?></td>
                                    <td>
                                        <?php if ($it['customization_text']): ?>
                                            <div class="p-2 bg-warning-subtle text-dark rounded border border-warning">
                                                <strong>Müşteri Notu:</strong> <?= htmlspecialchars($it['customization_text']) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="order-summary mt-4 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Ürünler Ara Toplam:</span>
                            <span>₺<?= number_format($subOrder['subtotal'], 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-danger">
                            <span>Platform Komisyonu (%10):</span>
                            <span>-₺<?= number_format($subOrder['commission_amount'], 2, ',', '.') ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between font-weight-bold">
                            <span class="h5">Net Hak Edişiniz:</span>
                            <span class="h5 text-success">₺<?= number_format($subOrder['producer_earning'], 2, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="card">
                    <h3 class="card-title">Kargo & Durum Güncelle</h3>
                    <form method="POST">
                        <input type="hidden" name="update_shipping" value="1">
                        <div class="form-group">
                            <label class="form-label">Sipariş Durumu</label>
                            <select name="status" class="form-control">
                                <option value="pending" <?= $subOrder['status'] === 'pending' ? 'selected' : '' ?>>Hazırlanıyor (Beklemede)</option>
                                <option value="processing" <?= $subOrder['status'] === 'processing' ? 'selected' : '' ?>>İşleniyor / Üretimde</option>
                                <option value="shipped" <?= $subOrder['status'] === 'shipped' ? 'selected' : '' ?>>Kargoya Verildi</option>
                                <option value="delivered" <?= $subOrder['status'] === 'delivered' ? 'selected' : '' ?>>Teslim Edildi</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kargo Firması</label>
                            <input type="text" name="shipping_provider" class="form-control" value="<?= htmlspecialchars($subOrder['shipping_provider'] ?? '') ?>" placeholder="Örn: Yurtiçi Kargo, Aras, MNG">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kargo Takip No</label>
                            <input type="text" name="tracking_number" class="form-control" value="<?= htmlspecialchars($subOrder['tracking_number'] ?? '') ?>" placeholder="Takip barkod numarası">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Bilgileri Güncelle</button>
                    </form>
                </div>

                <div class="card mt-3">
                    <h3 class="card-title">Teslimat Adresi</h3>
                    <p><strong>Alıcı:</strong> <?= htmlspecialchars($subOrder['customer_name']) ?></p>
                    <p><strong>Telefon:</strong> <?= htmlspecialchars($subOrder['customer_phone'] ?? '—') ?></p>
                    <p><strong>E-posta:</strong> <?= htmlspecialchars($subOrder['customer_email']) ?></p>
                    <p class="mt-2"><strong>Adres:</strong><br><?= nl2br(htmlspecialchars($subOrder['shipping_address'])) ?></p>
                    <p><strong>Şehir / İlçe:</strong> <?= htmlspecialchars($subOrder['shipping_city'] ?? '') ?> / <?= htmlspecialchars($subOrder['shipping_district'] ?? '') ?></p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
