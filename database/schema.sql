-- ═══════════════════════════════════════════════════════════
-- ButikÇarşı — Marketplace Veritabanı Şeması
-- Compatible with: MySQL 5.7+ / MariaDB 10.3+
-- Charset: utf8mb4
-- ═══════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- PLATFORM AYARLARI
CREATE TABLE IF NOT EXISTS platform_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ADMIN KULLANICILARI
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    full_name VARCHAR(255),
    role ENUM('superadmin','admin','support') DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ÜRETİCİLER (Butik Satıcılar)
CREATE TABLE IF NOT EXISTS producers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL UNIQUE,
    brand_name VARCHAR(255) NOT NULL,
    owner_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(50),
    password_hash VARCHAR(255) NOT NULL,
    description TEXT,
    logo_url VARCHAR(500),
    cover_image_url VARCHAR(500),
    city VARCHAR(100),
    district VARCHAR(100),
    instagram_url VARCHAR(500),
    instagram_handle VARCHAR(100),
    tiktok_url VARCHAR(500),
    youtube_url VARCHAR(500),
    website_url VARCHAR(500),
    iban VARCHAR(50),
    bank_name VARCHAR(100),
    tax_number VARCHAR(20),
    tax_office VARCHAR(100),
    identity_number VARCHAR(11),
    iyzico_sub_merchant_key VARCHAR(255),
    iyzico_sub_merchant_type ENUM('PERSONAL','PRIVATE_COMPANY','LIMITED_OR_JOINT_STOCK_COMPANY') DEFAULT 'PERSONAL',
    status ENUM('pending','approved','suspended','rejected') DEFAULT 'pending',
    approval_note TEXT,
    commission_rate DECIMAL(5,2) DEFAULT 10.00,
    is_featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    notify_email TINYINT(1) DEFAULT 1,
    notify_whatsapp TINYINT(1) DEFAULT 1,
    whatsapp_number VARCHAR(50),
    approved_at TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_producer_status (status),
    INDEX idx_producer_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- KATEGORİLER
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    icon_class VARCHAR(100),
    image_url VARCHAR(500),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_cat_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ÜRÜNLER
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producer_id INT NOT NULL,
    slug VARCHAR(500) NOT NULL UNIQUE,
    name VARCHAR(500) NOT NULL,
    short_description TEXT,
    description LONGTEXT,
    regular_price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) NULL,
    currency VARCHAR(3) DEFAULT 'TRY',
    stock_status ENUM('instock','outofstock','onbackorder') DEFAULT 'instock',
    stock_quantity INT DEFAULT NULL,
    shipping_weight VARCHAR(50),
    shipping_dimensions VARCHAR(100),
    free_shipping TINYINT(1) DEFAULT 0,
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    material VARCHAR(255),
    color VARCHAR(100),
    size VARCHAR(100),
    customizable TINYINT(1) DEFAULT 0,
    customization_note TEXT,
    production_time VARCHAR(100),
    seo_title VARCHAR(500),
    seo_description TEXT,
    status ENUM('draft','pending','approved','rejected','archived') DEFAULT 'draft',
    admin_note TEXT,
    is_featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (producer_id) REFERENCES producers(id) ON DELETE CASCADE,
    INDEX idx_prod_producer (producer_id),
    INDEX idx_prod_status (status),
    INDEX idx_prod_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    image_url_webp VARCHAR(500),
    image_url_thumb VARCHAR(500),
    alt_text VARCHAR(500),
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_pimg_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_categories (
    product_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    variant_name VARCHAR(255),
    sku VARCHAR(100),
    price_adjustment DECIMAL(10,2) DEFAULT 0,
    stock_quantity INT DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_pvar_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SİPARİŞLER
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50),
    customer_note TEXT,
    shipping_address TEXT NOT NULL,
    shipping_city VARCHAR(100),
    shipping_district VARCHAR(100),
    shipping_zip VARCHAR(20),
    billing_address TEXT,
    subtotal DECIMAL(10,2) NOT NULL,
    shipping_total DECIMAL(10,2) DEFAULT 0,
    commission_total DECIMAL(10,2) DEFAULT 0,
    discount_total DECIMAL(10,2) DEFAULT 0,
    grand_total DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'TRY',
    iyzico_payment_id VARCHAR(100),
    iyzico_conversation_id VARCHAR(100),
    iyzico_fraud_status VARCHAR(50),
    iyzico_token VARCHAR(255),
    status ENUM('pending','paid','processing','partially_shipped','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
    payment_status ENUM('pending','paid','failed','refunded','held') DEFAULT 'pending',
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_status (status),
    INDEX idx_order_payment (payment_status),
    INDEX idx_order_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ALT SİPARİŞLER (Üretici bazında)
CREATE TABLE IF NOT EXISTS order_sub_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    producer_id INT NOT NULL,
    sub_order_number VARCHAR(50) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    shipping_cost DECIMAL(10,2) DEFAULT 0,
    commission_amount DECIMAL(10,2) NOT NULL,
    producer_earning DECIMAL(10,2) NOT NULL,
    shipping_provider VARCHAR(100),
    tracking_number VARCHAR(100),
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    status ENUM('pending','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
    payout_status ENUM('held','released','paid','disputed') DEFAULT 'held',
    payout_released_at TIMESTAMP NULL,
    payout_paid_at TIMESTAMP NULL,
    producer_notified_at TIMESTAMP NULL,
    customer_notified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (producer_id) REFERENCES producers(id) ON DELETE CASCADE,
    UNIQUE KEY uq_sub_order (order_id, producer_id),
    INDEX idx_suborder_producer (producer_id),
    INDEX idx_suborder_payout (payout_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    sub_order_id INT NOT NULL,
    product_id INT,
    variant_id INT NULL,
    product_name VARCHAR(500),
    product_image VARCHAR(500),
    producer_name VARCHAR(255),
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2),
    total_price DECIMAL(10,2),
    customization_text TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (sub_order_id) REFERENCES order_sub_orders(id) ON DELETE CASCADE,
    INDEX idx_oi_order (order_id),
    INDEX idx_oi_suborder (sub_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- KOMİSYON TAKİP
CREATE TABLE IF NOT EXISTS commissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sub_order_id INT NOT NULL,
    producer_id INT NOT NULL,
    order_total DECIMAL(10,2) NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,
    producer_earning DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','paid') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sub_order_id) REFERENCES order_sub_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (producer_id) REFERENCES producers(id) ON DELETE CASCADE,
    INDEX idx_comm_producer (producer_id),
    INDEX idx_comm_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producer_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    fee DECIMAL(10,2) DEFAULT 0,
    net_amount DECIMAL(10,2) NOT NULL,
    iban VARCHAR(50),
    bank_name VARCHAR(100),
    sub_order_ids JSON,
    status ENUM('pending','processing','completed','failed') DEFAULT 'pending',
    processed_at TIMESTAMP NULL,
    reference_code VARCHAR(100),
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producer_id) REFERENCES producers(id) ON DELETE CASCADE,
    INDEX idx_payout_producer (producer_id),
    INDEX idx_payout_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BİLDİRİM LOGLARI
CREATE TABLE IF NOT EXISTS notification_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('email','whatsapp','sms') NOT NULL,
    recipient VARCHAR(255),
    subject VARCHAR(500),
    body TEXT,
    related_type VARCHAR(50),
    related_id INT,
    status ENUM('sent','failed','pending') DEFAULT 'pending',
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notif_type (type, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BANNERLAR
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position ENUM('hero','promo','category','popup','producer_spotlight') DEFAULT 'hero',
    title VARCHAR(255),
    subtitle VARCHAR(500),
    button_text VARCHAR(100),
    image_url VARCHAR(500) NOT NULL,
    image_url_mobile VARCHAR(500),
    link_url VARCHAR(500),
    producer_id INT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    starts_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producer_id) REFERENCES producers(id) ON DELETE SET NULL,
    INDEX idx_banner_active (is_active, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- STATİK SAYFALAR
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(500) NOT NULL UNIQUE,
    title VARCHAR(500) NOT NULL,
    content LONGTEXT,
    seo_title VARCHAR(500),
    seo_description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- İLETİŞİM MESAJLARI
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(500),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_msg_unread (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DEĞERLENDİRMELER
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    producer_id INT NOT NULL,
    order_item_id INT NULL,
    customer_name VARCHAR(255),
    customer_email VARCHAR(255),
    rating TINYINT UNSIGNED NOT NULL,
    comment TEXT,
    is_approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (producer_id) REFERENCES producers(id) ON DELETE CASCADE,
    INDEX idx_review_product (product_id),
    INDEX idx_review_producer (producer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEED DATA
INSERT INTO admin_users (username, password_hash, email, full_name, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'info@butikcarsi.com', 'Platform Admin', 'superadmin');

INSERT INTO platform_settings (setting_key, setting_value) VALUES
('site_name', 'ButikÇarşı'),
('tagline', 'El Yapımı Butik Ürünler Pazarı'),
('email', 'info@butikcarsi.com'),
('phone', ''),
('logo_url', '/images/logo.svg'),
('favicon_url', '/images/favicon.svg'),
('social_instagram', 'https://www.instagram.com/butikcarsi/'),
('social_tiktok', ''),
('default_commission_rate', '10.00'),
('currency', 'TRY'),
('iyzico_api_key', ''),
('iyzico_secret_key', ''),
('iyzico_base_url', 'https://sandbox-api.iyzipay.com'),
('smtp_host', ''),
('smtp_port', '587'),
('smtp_user', ''),
('smtp_pass', ''),
('smtp_from_name', 'ButikÇarşı'),
('smtp_from_email', 'noreply@butikcarsi.com'),
('auto_approval_days', '7'),
('primary_color', '#7c3aed'),
('secondary_color', '#1e1b4b'),
('accent_color', '#f59e0b');

INSERT INTO categories (name, slug, icon_class, sort_order) VALUES
('Lazer Kesim', 'lazer-kesim', 'sparkles', 1),
('Reçine Sanat', 'recine-sanat', 'palette', 2),
('El Örgüsü', 'el-orgusu', 'heart', 3),
('Takı & Aksesuar', 'taki-aksesuar', 'gem', 4),
('Ahşap Ürünler', 'ahsap-urunler', 'tree-pine', 5),
('Kişiye Özel', 'kisiye-ozel', 'gift', 6),
('Mum & Aromaterapi', 'mum-aromaterapi', 'flame', 7),
('Seramik & Çömlekçilik', 'seramik-comlekcilik', 'flower-2', 8),
('Kumaş & Tekstil', 'kumas-tekstil', 'scissors', 9),
('Doğal Kozmetik', 'dogal-kozmetik', 'leaf', 10),
('Bebek & Çocuk', 'bebek-cocuk', 'baby', 11),
('Ev Dekorasyon', 'ev-dekorasyon', 'home', 12);
