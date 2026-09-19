CREATE TABLE IF NOT EXISTS platform_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key TEXT NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    email TEXT,
    full_name TEXT,
    role TEXT DEFAULT 'admin',
    is_active INTEGER DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS producers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    brand_name TEXT NOT NULL,
    owner_name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    phone TEXT,
    password_hash TEXT NOT NULL,
    description TEXT,
    logo_url TEXT,
    cover_image_url TEXT,
    city TEXT,
    district TEXT,
    instagram_url TEXT,
    instagram_handle TEXT,
    tiktok_url TEXT,
    youtube_url TEXT,
    website_url TEXT,
    iban TEXT,
    bank_name TEXT,
    tax_number TEXT,
    tax_office TEXT,
    identity_number TEXT,
    iyzico_sub_merchant_key TEXT,
    iyzico_sub_merchant_type TEXT DEFAULT 'PERSONAL',
    status TEXT DEFAULT 'pending',
    approval_note TEXT,
    commission_rate NUMERIC DEFAULT 10.00,
    is_featured INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    notify_email INTEGER DEFAULT 1,
    notify_whatsapp INTEGER DEFAULT 1,
    whatsapp_number TEXT,
    approved_at DATETIME,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    parent_id INTEGER,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT,
    icon_class TEXT,
    image_url TEXT,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    producer_id INTEGER NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    short_description TEXT,
    description TEXT,
    regular_price NUMERIC NOT NULL,
    sale_price NUMERIC,
    currency TEXT DEFAULT 'TRY',
    stock_status TEXT DEFAULT 'instock',
    stock_quantity INTEGER,
    shipping_weight TEXT,
    shipping_dimensions TEXT,
    free_shipping INTEGER DEFAULT 0,
    shipping_cost NUMERIC DEFAULT 0,
    material TEXT,
    color TEXT,
    size TEXT,
    customizable INTEGER DEFAULT 0,
    customization_note TEXT,
    production_time TEXT,
    seo_title TEXT,
    seo_description TEXT,
    status TEXT DEFAULT 'pending',
    admin_note TEXT,
    is_featured INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS product_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    image_url TEXT NOT NULL,
    image_url_webp TEXT,
    image_url_thumb TEXT,
    alt_text TEXT,
    is_primary INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS product_categories (
    product_id INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    PRIMARY KEY (product_id, category_id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_number TEXT NOT NULL UNIQUE,
    customer_name TEXT NOT NULL,
    customer_email TEXT NOT NULL,
    customer_phone TEXT,
    customer_note TEXT,
    shipping_address TEXT NOT NULL,
    shipping_city TEXT,
    shipping_district TEXT,
    shipping_zip TEXT,
    billing_address TEXT,
    subtotal NUMERIC NOT NULL,
    shipping_total NUMERIC DEFAULT 0,
    commission_total NUMERIC DEFAULT 0,
    discount_total NUMERIC DEFAULT 0,
    grand_total NUMERIC NOT NULL,
    currency TEXT DEFAULT 'TRY',
    iyzico_payment_id TEXT,
    iyzico_conversation_id TEXT,
    iyzico_fraud_status TEXT,
    iyzico_token TEXT,
    status TEXT DEFAULT 'pending',
    payment_status TEXT DEFAULT 'pending',
    ip_address TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_sub_orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    producer_id INTEGER NOT NULL,
    sub_order_number TEXT NOT NULL,
    subtotal NUMERIC NOT NULL,
    shipping_cost NUMERIC DEFAULT 0,
    commission_amount NUMERIC NOT NULL,
    producer_earning NUMERIC NOT NULL,
    shipping_provider TEXT,
    tracking_number TEXT,
    shipped_at DATETIME,
    delivered_at DATETIME,
    status TEXT DEFAULT 'pending',
    payout_status TEXT DEFAULT 'held',
    payout_released_at DATETIME,
    payout_paid_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    sub_order_id INTEGER NOT NULL,
    product_id INTEGER,
    variant_id INTEGER,
    product_name TEXT,
    product_image TEXT,
    producer_name TEXT,
    quantity INTEGER DEFAULT 1,
    unit_price NUMERIC,
    total_price NUMERIC,
    customization_text TEXT
);

CREATE TABLE IF NOT EXISTS commissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sub_order_id INTEGER NOT NULL,
    producer_id INTEGER NOT NULL,
    order_total NUMERIC NOT NULL,
    commission_rate NUMERIC NOT NULL,
    commission_amount NUMERIC NOT NULL,
    producer_earning NUMERIC NOT NULL,
    status TEXT DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS payouts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    producer_id INTEGER NOT NULL,
    amount NUMERIC NOT NULL,
    fee NUMERIC DEFAULT 0,
    net_amount NUMERIC NOT NULL,
    iban TEXT,
    bank_name TEXT,
    sub_order_ids TEXT,
    status TEXT DEFAULT 'pending',
    processed_at DATETIME,
    reference_code TEXT,
    note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS banners (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    position TEXT DEFAULT 'hero',
    title TEXT,
    subtitle TEXT,
    image_url TEXT NOT NULL,
    image_url_mobile TEXT,
    link_url TEXT,
    producer_id INTEGER,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT,
    subject TEXT,
    message TEXT NOT NULL,
    is_read INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
