<?php
require_once __DIR__ . '/../config.php';
$pageTitle = 'Komisyon Takibi';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$totals = Database::query("
    SELECT 
        COALESCE(SUM(grand_total), 0) as total_volume,
        COALESCE(SUM(commission_total), 0) as total_commission
    FROM orders 
    WHERE payment_status = 'paid'
");

$producerStats = Database::queryAll("
    SELECT p.id, p.brand_name, p.owner_name, p.commission_rate,
           COUNT(so.id) as order_count,
           COALESCE(SUM(so.subtotal), 0) as total_sales,
           COALESCE(SUM(so.commission_amount), 0) as platform_commission,
           COALESCE(SUM(so.producer_earning), 0) as net_producer_earning
    FROM producers p
    LEFT JOIN order_sub_orders so ON p.id = so.producer_id
    GROUP BY p.id
    ORDER BY total_sales DESC
");
?>

<main class="admin-main">
    <div class="admin-topbar">
        <h2 class="page-title">Marketplace Komisyon & Gelir Takibi</h2>
    </div>

    <div class="admin-content">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Toplam İşlem Hacmi (GMV)</div>
                <div class="stat-value">₺<?= number_format($totals['total_volume'], 2, ',', '.') ?></div>
                <div class="stat-meta">Ödenmiş tüm siparişler</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Toplam Platform Komisyonu (%10)</div>
                <div class="stat-value text-success">₺<?= number_format($totals['total_commission'], 2, ',', '.') ?></div>
                <div class="stat-meta">Kazanılan aracılık komisyonu</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Ortalama Komisyon Oranı</div>
                <div class="stat-value text-primary">%10.00</div>
                <div class="stat-meta">Standart marketplace oranı</div>
            </div>
        </div>

        <div class="card table-card mt-4">
            <div class="card-header">
                <h3 class="card-title">Üretici Bazlı Komisyon Dağılımı</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Butik Adı</th>
                            <th>Yetkili</th>
                            <th>Sipariş Sayısı</th>
                            <th>Brüt Satış</th>
                            <th>Uygulanan Oran</th>
                            <th>Platform Komisyonu</th>
                            <th>Üretici Net Kazancı</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($producerStats as $ps): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($ps['brand_name']) ?></strong></td>
                                <td><?= htmlspecialchars($ps['owner_name']) ?></td>
                                <td><?= $ps['order_count'] ?> sipariş</td>
                                <td>₺<?= number_format($ps['total_sales'], 2, ',', '.') ?></td>
                                <td>%<?= number_format($ps['commission_rate'], 1) ?></td>
                                <td><strong class="text-success">₺<?= number_format($ps['platform_commission'], 2, ',', '.') ?></strong></td>
                                <td>₺<?= number_format($ps['net_producer_earning'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
