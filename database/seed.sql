-- ===================================================
-- ButikÇarşı — Marketplace Demo Seed Data
-- ===================================================

USE butikcarsi;

-- PLATFORM SETTINGS
INSERT INTO platform_settings (setting_key, setting_value) VALUES
('platform_name', 'ButikÇarşı'),
('platform_commission_rate', '10.00'),
('support_email', 'destek@butikcarsi.com'),
('support_phone', '+90 (212) 555 0199'),
('currency', 'TRY'),
('min_payout_amount', '500.00'),
('iyzico_mode', 'sandbox')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ADMIN USER (admin / admin123)
-- bcrypt hash for 'admin123': $2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm
INSERT INTO admin_users (username, password_hash, email, full_name, role, is_active) VALUES
('admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'admin@butikcarsi.com', 'Platform Yöneticisi', 'superadmin', 1)
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

-- PRODUCERS (Instagram Artisans)
-- Producer 1: lazerci_hediyelik (Password: producer123 -> $2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm)
INSERT INTO producers (id, slug, brand_name, owner_name, email, phone, password_hash, description, logo_url, cover_image_url, city, district, instagram_url, instagram_handle, iban, bank_name, iyzico_sub_merchant_key, status, commission_rate, is_featured, notify_whatsapp, whatsapp_number) VALUES
(1, 'lazerci-hediyelik', 'Lazerci Hediyelik', 'Ahmet Yılmaz', 'lazerci@butikcarsi.com', '+905321112233', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Kişiye özel lazer kazıma, çelik kolye, deri bileklik, Zippo çakmak, ahşap kutulu hediyelik setler. Sevginizi sonsuza dek ölümsüzleştiren el emeği tasarımlar.', 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=300&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=1200&auto=format&fit=crop&q=80', 'İstanbul', 'Kadıköy', 'https://www.instagram.com/lazerci_hediyelik', '@lazerci_hediyelik', 'TR330006200000012345678901', 'Garanti BBVA', 'SUBM_LAZERCI_001', 'approved', 10.00, 1, 1, '+905321112233'),

(2, 'epoksi-sanat', 'Epoksi Sanat Atölyesi', 'Zeynep Kaya', 'epoksi@butikcarsi.com', '+905432223344', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Deniz dalgası desenli epoksi reçine sunumluklar, ceviz kütük masa saatleri ve kişiye özel altın varaklı takı tepsileri.', 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=300&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=1200&auto=format&fit=crop&q=80', 'İzmir', 'Urla', 'https://www.instagram.com/epoksi_sanat', '@epoksi_sanat', 'TR440001500000098765432101', 'Vakıfbank', 'SUBM_EPOKSI_002', 'approved', 10.00, 1, 1, '+905432223344'),

(3, 'ilmek-butik', 'İlmek Butik Örgü', 'Fatma Demir', 'ilmek@butikcarsi.com', '+905553334455', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', '%100 pamuklu kağıt ipten el örgüsü hasır çantalar, makrome duvar süsleri ve organik pamuk bebek battaniyeleri.', 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?w=300&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?w=1200&auto=format&fit=crop&q=80', 'Ankara', 'Çankaya', 'https://www.instagram.com/ilmek_butik', '@ilmek_butik', 'TR550001000000011223344556', 'Ziraat Bankası', 'SUBM_ILMEK_003', 'approved', 10.00, 1, 1, '+905553334455'),

(4, 'deri-zanaat', 'Deri Zanaat Atölyesi', 'Burak Çelik', 'deri@butikcarsi.com', '+905334445566', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Geleneksel saraç dikişi ile tamamen elde dikilen bitkisel tabaklanmış dana derisi cüzdan, kartlık ve kişiselleştirilebilir anahtarlıklar.', 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=300&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=1200&auto=format&fit=crop&q=80', 'Bursa', 'Nilüfer', 'https://www.instagram.com/deri_zanaat', '@deri_zanaat', 'TR660006400000033445566778', 'İş Bankası', 'SUBM_DERI_004', 'approved', 10.00, 1, 1, '+905334445566'),

(5, 'toprak-ates-seramik', 'Toprak & Ateş Seramik', 'Deniz Akın', 'seramik@butikcarsi.com', '+905365556677', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Çömlekçi çarkında elde şekillendirilmiş, 1040 derecede fırınlanmış gıdaya uygun el boyaması seramik kupalar ve tütsülükler.', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300&auto=format&fit=crop&q=80', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=1200&auto=format&fit=crop&q=80', 'Antalya', 'Muratpaşa', 'https://www.instagram.com/toprak_ates_seramik', '@toprak_ates_seramik', 'TR770003200000055667788990', 'TEB', 'SUBM_SERAMIK_005', 'approved', 10.00, 1, 1, '+905365556677')
ON DUPLICATE KEY UPDATE brand_name = VALUES(brand_name);

-- CATEGORIES
INSERT INTO categories (id, name, slug, description, icon_class, image_url, sort_order, is_active) VALUES
(1, 'Kişiye Özel Hediyelik', 'kisiye-ozel-hediyelik', 'İsim, tarih ve fotoğraf baskılı unutulmaz hediyeler', 'gift', 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=500&auto=format&fit=crop&q=80', 1, 1),
(2, 'Lazer Kesim & Baskı', 'lazer-kesim-baski', 'Çelik kolye, künye, Zippo çakmak ve ahşap lazer kazıma', 'zap', 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=500&auto=format&fit=crop&q=80', 2, 1),
(3, 'Epoksi & Reçine Sanatı', 'epoksi-recine-sanati', 'Deniz dalgalı sunumluk, sehpa ve el yapımı reçine objeler', 'disc', 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=500&auto=format&fit=crop&q=80', 3, 1),
(4, 'El Örgüsü & Makrome', 'el-orgusu-makrome', 'El emeği kağıt ip çantalar, hırkalar ve duvar dekorasyonu', 'feather', 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?w=500&auto=format&fit=crop&q=80', 4, 1),
(5, 'Hakiki Deri Tasarımlar', 'hakiki-deri-tasarimlar', 'El dikişi deri cüzdan, kartlık, kemer ve aksesuarlar', 'briefcase', 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=500&auto=format&fit=crop&q=80', 5, 1),
(6, 'Seramik & Çömlek', 'seramik-comlek', 'Elde şekillendirilmiş el boyaması kupa, vazo ve tütsülük', 'coffee', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80', 6, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- PRODUCTS
INSERT INTO products (id, producer_id, slug, name, short_description, description, regular_price, sale_price, stock_quantity, stock_status, customizable, customization_note, production_time, material, free_shipping, shipping_cost, status, is_featured) VALUES
-- Producer 1: Lazerci Hediyelik
(1, 1, 'kisiye-ozel-isimli-celik-kolye', 'Kişiye Özel İsim Yazılı Çelik Plaka Kolye', 'Kararmaz 316L paslanmaz çelik üzerine lazer kazıma isimli kolye.', 'Yüksek kaliteli 316L medikal çelikten üretilmiştir. Kararmaz, paslanmaz, teni tahriş etmez. İstediğiniz isim, tarih veya sembol lazer fiber makinemizde kusursuz olarak işlenir. Özel hediye kutusunda gönderilir.', 349.00, 299.00, 50, 'instock', 1, 'Kolye plakasına yazılacak isim ve tarihi belirtiniz.', '1-2 iş günü', '316L Paslanmaz Çelik', 1, 0.00, 'approved', 1),

(2, 1, 'ozel-baskili-zippo-model-cakmak', 'Kişiye Özel Lazer Kazıma Mat Siyah Çakmak', 'Fotoğraf veya isim kazımalı dayanıklı rüzgar geçirmez çakmak.', 'Mat siyah kaplama gövde üzerine fiber lazer ile fotoğraf, logo veya el yazısı kazınabilir. Sevdikleriniz için ömür boyu kalıcı bir hediye seçeneği.', 450.00, 399.00, 30, 'instock', 1, 'Kazınmasını istediğiniz yazı veya fotoğrafı WhatsApp üzerinden iletebilirsiniz.', '2 iş günü', 'Pirinç & Mat Siyah Kaplama', 0, 49.90, 'approved', 1),

(3, 1, 'ahsap-kutulu-deri-bileklik-ve-kalem-seti', 'Ahşap Kutulu İsimli Deri Bileklik & Roller Kalem Seti', 'Erkekler için mükemmel doğum günü ve yıldönümü hediyesi.', 'İsme özel ahşap ceviz kaplama hediye kutusu içerisinde lazer baskılı deri bileklik ve mat siyah ağır metal roller tükenmez kalem.', 650.00, 579.00, 25, 'instock', 1, 'Bileklik ve kalem üzerine yazılacak isim-soyisimi belirtiniz.', '2-3 iş günü', 'Hakiki Deri, Çelik, Ceviz Ahşap', 1, 0.00, 'approved', 1),

-- Producer 2: Epoksi Sanat
(4, 2, 'deniz-dalgasi-epoksi-ceviz-sunumluk', 'Deniz Dalgası Reçine Ceviz Ağacı Sunumluk', 'Doğal ceviz kütük ve epoksi reçine ile elde yapılan eşsiz servis tepsisi.', 'Her biri doğadaki ağaç desenine göre benzersiz olan ceviz kütüğü üzerinde çok katmanlı deniz dalgası efekti reçine uygulaması. Gıdaya uygun doğal yağ ile cilalanmıştır.', 890.00, 790.00, 12, 'instock', 1, 'Ahşap köşesine pirinç plaka ile isim veya baş harf eklenebilir.', '3-4 iş günü', 'Doğal Ceviz Ağacı, Epoksi Reçine', 1, 0.00, 'approved', 1),

(5, 2, 'altin-varakli-epoksi-taki-tabagi', 'Altın Varaklı Çiçekli Epoksi Takı Tabağı', 'Kurutulmuş gerçek çiçekler ve 24k altın yapraklarla süslenmiş zarif halka tabak.', 'Özel günler, nişan ve alyans sunumları için mükemmel el yapımı şeffaf epoksi takı ve yüzük tabağı.', 290.00, 240.00, 40, 'instock', 0, NULL, '2 iş günü', 'Şeffaf Döküm Reçine, Altın Varak', 0, 39.90, 'approved', 0),

-- Producer 3: İlmek Butik
(6, 3, 'el-orgusu-kagit-ip-hasir-plaj-cantasi', 'El Örgüsü Kağıt İp Hasır Omuz Çantası', 'Yaz kombinlerinin vazgeçilmezi, astarlı ve fermuarlı el emeği çanta.', '%100 doğal kağıt ipten tığ ile örülmüştür. İçinde kaliteli keten astar ve fermuarlı cep mevcuttur. Hafif ve son derece dayanıklıdır.', 750.00, 680.00, 15, 'instock', 0, NULL, '3-5 iş günü', 'Doğal Kağıt İp, Keten Astar', 1, 0.00, 'approved', 1),

(7, 3, 'bohem-makrome-yaprak-duvar-susu', 'Bohem Tarzı 5li Makrome Yaprak Duvar Dekoru', 'Doğal dal üzerine örülmüş taranmış pamuk makrome duvar süsü.', 'Evinize sıcak ve bohem bir hava katacak el yapımı duvar süsü. Doğadan toplanmış fırınlanmış meşe dalı üzerine krem, bej ve hardal renk tonlarında pamuk iplerle işlenmiştir.', 480.00, 420.00, 20, 'instock', 1, 'Farklı renk kombinasyonu talebinizi yazabilirsiniz.', '2-3 iş günü', '%100 Pamuk Makrome İpi, Meşe Dalı', 1, 0.00, 'approved', 0),

-- Producer 4: Deri Zanaat
(8, 4, 'el-dikisi-vintage-deri-kartlik-cuzdan', 'El Dikişi Vintage Dana Derisi Minimal Kartlık', 'Geleneksel mumlu saraç dikişi ile ömür boyu dayanıklı hakiki deri kartlık.', '1. sınıf bitkisel tabaklanmış crazy horse dana derisinden üretilmiştir. 6 kart gözü ve orta kağıt para bölmesi bulunur. Zamanla kullanıldıkça kendine has vintage bir patina kazanır.', 420.00, 360.00, 35, 'instock', 1, 'Cüzdanın sağ alt köşesine sıcak baskı ile baş harfler basılabilir (örn: A.Y).', '1-2 iş günü', 'Crazy Horse Hakiki Deri', 0, 39.90, 'approved', 1),

(9, 4, 'kisisellestirilebilir-deri-anahtarlik', 'Pirinç Tokalı İsim Baskılı Deri Anahtarlık', 'Sağlam pirinç kanca ve hakiki deri askılı şık anahtarlık.', 'Kişiye özel harf ve rakam baskısı yapılabilen pratik ve dayanıklı tasarım.', 160.00, 130.00, 100, 'instock', 1, 'Basılacak ismi yazınız (maksimum 10 karakter).', '1 iş günü', 'Hakiki Deri, Masif Pirinç Aksam', 0, 29.90, 'approved', 0),

-- Producer 5: Toprak & Ateş Seramik
(10, 5, 'el-yapimi-seramik-espresso-kupa-seti', 'El Boyaması Seramik 2li Espresso Fincan Seti', 'Benekli stoneware çamurdan çömlekçi çarkında elde üretilmiş özel seri.', 'Gıdaya uygun mat sır ile kaplanmış, bulaşık makinesinde yıkanabilir. Kahve keyfinize el yapımı zanaat dokunuşu.', 380.00, 330.00, 20, 'instock', 0, NULL, '2 iş günü', 'Stoneware Seramik Çamuru', 0, 39.90, 'approved', 1),

(11, 5, 'seramik-lotus-tutsuluk-ve-palosanto-tabagi', 'El Yapımı Seramik Lotus Tütsülük', 'Meditasyon ve yoga köşeleri için el oyması estetik tütsülük.', 'Palo Santo, adaçayı ve çubuk tütsüler için uygun ısıya dayanıklı fırınlanmış seramik tasarım.', 260.00, 210.00, 30, 'instock', 0, NULL, '1-2 iş günü', 'Beyaz Çamur, Sır', 0, 29.90, 'approved', 0)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- PRODUCT IMAGES
INSERT INTO product_images (product_id, image_url, is_primary, sort_order) VALUES
(1, 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&auto=format&fit=crop&q=80', 1, 0),
(1, 'https://images.unsplash.com/photo-1611591475152-473523dd66c4?w=800&auto=format&fit=crop&q=80', 0, 1),
(2, 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&auto=format&fit=crop&q=80', 1, 0),
(3, 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&auto=format&fit=crop&q=80', 1, 0),
(4, 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=800&auto=format&fit=crop&q=80', 1, 0),
(5, 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=800&auto=format&fit=crop&q=80', 1, 0),
(6, 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?w=800&auto=format&fit=crop&q=80', 1, 0),
(7, 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&auto=format&fit=crop&q=80', 1, 0),
(8, 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=800&auto=format&fit=crop&q=80', 1, 0),
(9, 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop&q=80', 1, 0),
(10, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&auto=format&fit=crop&q=80', 1, 0),
(11, 'https://images.unsplash.com/photo-1607344645866-009c320c5ab8?w=800&auto=format&fit=crop&q=80', 1, 0)
ON DUPLICATE KEY UPDATE image_url = VALUES(image_url);

-- PRODUCT CATEGORIES
INSERT IGNORE INTO product_categories (product_id, category_id) VALUES
(1, 1), (1, 2), (1, 7),
(2, 1), (2, 2),
(3, 1), (3, 2), (3, 5),
(4, 3), (4, 1),
(5, 3), (5, 7),
(6, 4),
(7, 4),
(8, 5), (8, 1),
(9, 5), (9, 1),
(10, 6),
(11, 6);

-- HOMEPAGE BANNERS
INSERT INTO banners (title, subtitle, image_url, link_url, position, producer_id, sort_order, is_active) VALUES
('Instagram Butik Üreticileri Tek Çatı Altında', 'El emeği, kişiye özel hediyeler ve eşsiz tasarım ürünleri güvenle keşfedin.', 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=1600&auto=format&fit=crop&q=80', '/kesfet', 'hero', NULL, 1, 1),
('Kişiye Özel Lazer Kazıma Hediyeler', 'Lazerci Hediyelik atölyesinin en yeni tasarımları ButikÇarşı güvencesiyle!', 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=1600&auto=format&fit=crop&q=80', '/uretici/lazerci-hediyelik', 'hero', 1, 2, 1),
('Deniz Dalgası Reçine Sanatı', 'Doğal ceviz kütük ve epoksi sanat eserleriyle evinize doğallık katın.', 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=1600&auto=format&fit=crop&q=80', '/uretici/epoksi-sanat', 'hero', 2, 3, 1)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- SAMPLE ORDER (Multi-Vendor Demo: Item from Lazerci + Item from Deri Zanaat)
INSERT INTO orders (id, order_number, customer_name, customer_email, customer_phone, shipping_address, shipping_city, shipping_district, subtotal, shipping_total, commission_total, grand_total, iyzico_payment_id, status, payment_status) VALUES
(1, 'BC-2026-00001', 'Canan Özdemir', 'canan@example.com', '+905329998877', 'Bağdat Caddesi No: 142 Daire: 5', 'İstanbul', 'Kadıköy', 659.00, 0.00, 65.90, 659.00, 'IYZ-DEMO-PAY-1001', 'paid', 'paid')
ON DUPLICATE KEY UPDATE order_number = VALUES(order_number);

-- SUB-ORDERS (Escrow Demonstration: 10% Platform, 90% Producer)
INSERT INTO order_sub_orders (id, order_id, producer_id, sub_order_number, subtotal, shipping_cost, commission_amount, producer_earning, shipping_provider, tracking_number, status, payout_status) VALUES
(1, 1, 1, 'BC-2026-00001-A', 299.00, 0.00, 29.90, 269.10, 'Yurtiçi Kargo', 'YK-882910291', 'shipped', 'held'),
(2, 1, 4, 'BC-2026-00001-B', 360.00, 0.00, 36.00, 324.00, 'Aras Kargo', 'AR-192039102', 'shipped', 'held')
ON DUPLICATE KEY UPDATE sub_order_number = VALUES(sub_order_number);

-- ORDER ITEMS
INSERT INTO order_items (order_id, sub_order_id, product_id, product_name, producer_name, quantity, unit_price, total_price, customization_text) VALUES
(1, 1, 1, 'Kişiye Özel İsim Yazılı Çelik Plaka Kolye', 'Lazerci Hediyelik', 1, 299.00, 299.00, 'Ön yüz: "Canan & Barış", Arka yüz: "24.07.2023"'),
(1, 2, 8, 'El Dikişi Vintage Dana Derisi Minimal Kartlık', 'Deri Zanaat Atölyesi', 1, 360.00, 360.00, 'Sağ alt köşe: "B.Ö"')
ON DUPLICATE KEY UPDATE product_name = VALUES(product_name);

-- COMMISSIONS
INSERT INTO commissions (sub_order_id, producer_id, order_total, commission_rate, commission_amount, producer_earning, status) VALUES
(1, 1, 299.00, 10.00, 29.90, 269.10, 'confirmed'),
(2, 4, 360.00, 10.00, 36.00, 324.00, 'confirmed')
ON DUPLICATE KEY UPDATE commission_amount = VALUES(commission_amount);

-- REVIEWS
INSERT INTO reviews (product_id, producer_id, customer_name, customer_email, rating, comment, is_approved) VALUES
(1, 1, 'Merve S.', 'merve@example.com', 5, 'Lazer kazıma kalitesi muazzam, kutusu ve özeni için çok teşekkürler!', 1),
(2, 1, 'Emre T.', 'emre@example.com', 5, 'Eşime doğum günü hediyesi olarak yaptırdım, tam istediğim gibi oldu.', 1),
(4, 2, 'Burcu K.', 'burcu@example.com', 5, 'Epoksi sunumluk tek kelimeyle bir sanat eseri! Masama çok yakıştı.', 1),
(8, 4, 'Oğuz A.', 'oguz@example.com', 5, 'Deri kalitesi ve dikiş işçiliği kusursuz. Harf baskısı da çok şık duruyor.', 1);
