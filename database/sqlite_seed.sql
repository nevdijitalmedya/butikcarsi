INSERT OR IGNORE INTO platform_settings (setting_key, setting_value) VALUES
('platform_name', 'ButikÇarşı'),
('platform_commission_rate', '10.00'),
('support_email', 'destek@butikcarsi.com'),
('currency', 'TRY');

INSERT OR IGNORE INTO admin_users (username, password_hash, email, full_name, role, is_active) VALUES
('admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'admin@butikcarsi.com', 'Platform Yöneticisi', 'superadmin', 1);

INSERT OR IGNORE INTO producers (id, slug, brand_name, owner_name, email, phone, password_hash, description, logo_url, cover_image_url, city, district, instagram_url, instagram_handle, iban, bank_name, status, commission_rate, is_featured, whatsapp_number) VALUES
(1, 'lazerci-hediyelik', 'Lazerci Hediyelik', 'Ahmet Yılmaz', 'lazerci@butikcarsi.com', '+905321112233', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Kişiye özel lazer kazıma, çelik kolye, deri bileklik, Zippo çakmak, ahşap kutulu hediyelik setler. Sevginizi sonsuza dek ölümsüzleştiren el emeği tasarımlar.', 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=300&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=1200&auto=format&fit=crop&q=80', 'İstanbul', 'Kadıköy', 'https://www.instagram.com/lazerci_hediyelik', '@lazerci_hediyelik', 'TR330006200000012345678901', 'Garanti BBVA', 'approved', 10.00, 1, '+905321112233'),
(2, 'epoksi-sanat', 'Epoksi Sanat Atölyesi', 'Zeynep Kaya', 'epoksi@butikcarsi.com', '+905432223344', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Deniz dalgası desenli epoksi reçine sunumluklar, ceviz kütük masa saatleri.', 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=300&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=1200&auto=format&fit=crop&q=80', 'İzmir', 'Urla', 'https://www.instagram.com/epoksi_sanat', '@epoksi_sanat', 'TR440001500000098765432101', 'Vakıfbank', 'approved', 10.00, 1, '+905432223344');

INSERT OR IGNORE INTO categories (id, name, slug, description, sort_order, is_active) VALUES
(1, 'Kişiye Özel Hediyelik', 'kisiye-ozel-hediyelik', 'İsim, tarih ve fotoğraf baskılı unutulmaz hediyeler', 1, 1),
(2, 'Lazer Kesim & Baskı', 'lazer-kesim-baski', 'Çelik kolye, künye, Zippo çakmak ve ahşap lazer kazıma', 2, 1),
(3, 'Epoksi & Reçine Sanatı', 'epoksi-recine-sanati', 'Deniz dalgalı sunumluk, sehpa ve el yapımı reçine objeler', 3, 1),
(4, 'El Örgüsü & Makrome', 'el-orgusu-makrome', 'El emeği kağıt ip çantalar, hırkalar ve duvar dekorasyonu', 4, 1),
(5, 'Hakiki Deri Tasarımlar', 'hakiki-deri-tasarimlar', 'El dikişi deri cüzdan, kartlık, kemer ve aksesuarlar', 5, 1),
(6, 'Seramik & Çömlek', 'seramik-comlek', 'Elde şekillendirilmiş el boyaması kupa, vazo ve tütsülük', 6, 1);

INSERT OR IGNORE INTO products (id, producer_id, slug, name, short_description, description, regular_price, sale_price, stock_quantity, stock_status, customizable, customization_note, production_time, material, free_shipping, shipping_cost, status, is_featured) VALUES
(1, 1, 'kisiye-ozel-isimli-celik-kolye', 'Kişiye Özel İsim Yazılı Çelik Plaka Kolye', 'Kararmaz 316L paslanmaz çelik üzerine lazer kazıma isimli kolye.', 'Yüksek kaliteli 316L medikal çelikten üretilmiştir. Kararmaz, paslanmaz, teni tahriş etmez.', 349.00, 299.00, 50, 'instock', 1, 'Kolye plakasına yazılacak isim ve tarihi belirtiniz.', '1-2 iş günü', '316L Paslanmaz Çelik', 1, 0.00, 'approved', 1),
(2, 1, 'ozel-baskili-zippo-model-cakmak', 'Kişiye Özel Lazer Kazıma Mat Siyah Çakmak', 'Fotoğraf veya isim kazımalı dayanıklı rüzgar geçirmez çakmak.', 'Mat siyah kaplama gövde üzerine fiber lazer kazıma.', 450.00, 399.00, 30, 'instock', 1, 'Yazı veya fotoğraf detaylarını yazınız.', '2 iş günü', 'Pirinç & Mat Kaplama', 0, 49.90, 'approved', 1);

INSERT OR IGNORE INTO product_images (product_id, image_url, is_primary, sort_order) VALUES
(1, 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&auto=format&fit=crop&q=80', 1, 0),
(2, 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&auto=format&fit=crop&q=80', 1, 0);

INSERT OR IGNORE INTO product_categories (product_id, category_id) VALUES (1, 1), (1, 2), (2, 1), (2, 2);
