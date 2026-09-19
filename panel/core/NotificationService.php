<?php
/**
 * NotificationService.php — Email + WhatsApp notification system
 */

class NotificationService {

    /**
     * Send order notification to producer
     */
    public static function notifyProducerNewOrder(int $subOrderId): bool {
        $subOrder = Database::query("
            SELECT so.*, o.order_number, o.customer_name, o.customer_phone, o.shipping_city,
                   p.brand_name, p.email as producer_email, p.whatsapp_number, p.notify_email, p.notify_whatsapp
            FROM order_sub_orders so
            JOIN orders o ON so.order_id = o.id
            JOIN producers p ON so.producer_id = p.id
            WHERE so.id = ?
        ", [$subOrderId]);

        if (!$subOrder) return false;

        $items = Database::queryAll("SELECT * FROM order_items WHERE sub_order_id = ?", [$subOrderId]);
        $itemsList = '';
        foreach ($items as $item) {
            $itemsList .= "- {$item['product_name']} x{$item['quantity']} (₺" . number_format($item['total_price'], 2, ',', '.') . ")\n";
        }

        // Email notification
        if ($subOrder['notify_email']) {
            $subject = "ğŸ›’ Yeni Sipariş #{$subOrder['sub_order_number']} — ButikÇarşı";
            $body = "Merhaba {$subOrder['brand_name']},\n\n"
                  . "Yeni bir sipariş aldınız!\n\n"
                  . "Sipariş No: {$subOrder['sub_order_number']}\n"
                  . "Müşteri: {$subOrder['customer_name']}\n"
                  . "Şehir: {$subOrder['shipping_city']}\n\n"
                  . "Ürünler:\n{$itemsList}\n"
                  . "Toplam: ₺" . number_format($subOrder['subtotal'], 2, ',', '.') . "\n"
                  . "Komisyon (%10): ₺" . number_format($subOrder['commission_amount'], 2, ',', '.') . "\n"
                  . "Net Kazanç: ₺" . number_format($subOrder['producer_earning'], 2, ',', '.') . "\n\n"
                  . "Lütfen siparişi en kısa sürede hazırlayıp kargoya verin.\n"
                  . "Panel: " . self::getSiteUrl() . "/panel/producer/orders.php\n\n"
                  . "ButikÇarşı Ekibi";

            self::sendEmail($subOrder['producer_email'], $subject, $body, 'order', $subOrderId);
        }

        // WhatsApp notification (deep link)
        if ($subOrder['notify_whatsapp'] && !empty($subOrder['whatsapp_number'])) {
            $waMessage = "ğŸ›’ *Yeni Sipariş!*\n\n"
                       . "Sipariş: {$subOrder['sub_order_number']}\n"
                       . "Müşteri: {$subOrder['customer_name']}\n"
                       . "Tutar: ₺" . number_format($subOrder['subtotal'], 2, ',', '.') . "\n\n"
                       . "Detaylar için panele girin.";

            self::logNotification('whatsapp', $subOrder['whatsapp_number'], 'Yeni Sipariş', $waMessage, 'order', $subOrderId);
        }

        // Update notification timestamp
        Database::execute("UPDATE order_sub_orders SET producer_notified_at = CURRENT_TIMESTAMP WHERE id = ?", [$subOrderId]);

        return true;
    }

    /**
     * Notify customer about shipment
     */
    public static function notifyCustomerShipment(int $subOrderId): bool {
        $subOrder = Database::query("
            SELECT so.*, o.customer_name, o.customer_email, o.customer_phone, o.order_number,
                   p.brand_name
            FROM order_sub_orders so
            JOIN orders o ON so.order_id = o.id
            JOIN producers p ON so.producer_id = p.id
            WHERE so.id = ?
        ", [$subOrderId]);

        if (!$subOrder) return false;

        $subject = "ğŸ“¦ Siparişiniz Kargoya Verildi — #{$subOrder['sub_order_number']}";
        $body = "Merhaba {$subOrder['customer_name']},\n\n"
              . "{$subOrder['brand_name']} tarafından hazırlanan siparişiniz kargoya verildi!\n\n"
              . "Kargo Firması: {$subOrder['shipping_provider']}\n"
              . "Takip No: {$subOrder['tracking_number']}\n\n"
              . "Siparişiniz tahminen 2-5 iş günü içinde elinize ulaşacaktır.\n\n"
              . "ButikÇarşı Ekibi";

        self::sendEmail($subOrder['customer_email'], $subject, $body, 'shipment', $subOrderId);

        Database::execute("UPDATE order_sub_orders SET customer_notified_at = CURRENT_TIMESTAMP WHERE id = ?", [$subOrderId]);

        return true;
    }

    /**
     * Notify producer about payout
     */
    public static function notifyProducerPayout(int $producerId, float $amount, string $iban): bool {
        $producer = Database::query("SELECT * FROM producers WHERE id = ?", [$producerId]);
        if (!$producer) return false;

        $subject = "ğŸ’° Ödemeniz Aktarıldı — ButikÇarşı";
        $body = "Merhaba {$producer['brand_name']},\n\n"
              . "₺" . number_format($amount, 2, ',', '.') . " tutarındaki ödemeniz IBAN hesabınıza aktarıldı.\n\n"
              . "IBAN: {$iban}\n\n"
              . "Kazançlarınızı panelden takip edebilirsiniz.\n\n"
              . "ButikÇarşı Ekibi";

        return self::sendEmail($producer['email'], $subject, $body, 'payout', $producerId);
    }

    /**
     * Send email via SMTP or PHP mail()
     */
    private static function sendEmail(string $to, string $subject, string $body, string $relatedType = '', int $relatedId = 0): bool {
        $smtpHost = Config::setting('smtp_host', '');
        $fromName = Config::setting('smtp_from_name', 'ButikÇarşı');
        $fromEmail = Config::setting('smtp_from_email', 'noreply@butikcarsi.com');

        $success = false;

        if (!empty($smtpHost)) {
            // Use PHPMailer if available (via composer)
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                try {
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $smtpHost;
                    $mail->SMTPAuth = true;
                    $mail->Username = Config::setting('smtp_user', '');
                    $mail->Password = Config::setting('smtp_pass', '');
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = (int)Config::setting('smtp_port', '587');
                    $mail->CharSet = 'UTF-8';
                    $mail->setFrom($fromEmail, $fromName);
                    $mail->addAddress($to);
                    $mail->Subject = $subject;
                    $mail->Body = $body;
                    $mail->send();
                    $success = true;
                } catch (\Exception $e) {
                    error_log("Email send failed: " . $e->getMessage());
                }
            }
        } else {
            // Fallback to PHP mail()
            $headers = "From: {$fromName} <{$fromEmail}>\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $success = @mail($to, $subject, $body, $headers);
        }

        self::logNotification('email', $to, $subject, $body, $relatedType, $relatedId, $success);
        return $success;
    }

    /**
     * Log notification to database
     */
    private static function logNotification(string $type, string $recipient, string $subject, string $body, string $relatedType, int $relatedId, bool $success = true): void {
        try {
            Database::execute("
                INSERT INTO notification_logs (type, recipient, subject, body, related_type, related_id, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ", [$type, $recipient, $subject, $body, $relatedType, $relatedId, $success ? 'sent' : 'failed']);
        } catch (\Exception $e) {
            error_log("Notification log failed: " . $e->getMessage());
        }
    }

    /**
     * Generate WhatsApp deep link
     */
    public static function whatsappLink(string $phone, string $message): string {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    private static function getSiteUrl(): string {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'butikcarsi.com';
        return $protocol . '://' . $host;
    }
}
