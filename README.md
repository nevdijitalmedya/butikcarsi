# 🛍️ ButikÇarşı — Instagram Butik Üreticileri Pazaryeri Platformu

> Instagram üzerinden üretim yapıp satan butik zanaatkarları ve üreticileri tek bir platformda toplayan, her biri için özel landing page oluşturan ve yapılan satışlardan %10 komisyon alan modern marketplace platformu.

---

## 📌 Proje Özeti ve Vizyon

Instagram'da hediyelik, lazer kazıma, epoksi sanat, el örgüsü, el yapımı deri ve seramik gibi alanlarda üretim yapan binlerce butik üretici bulunmaktadır. Bu üreticilerin çoğu kendi e-ticaret sitesine ve ödeme altyapısına sahip değildir; satışlarını doğrudan mesaj (DM) ve IBAN havalesi ile yürütmektedir. 

**ButikÇarşı**, bu üreticileri tek bir çatıda buluşturarak şu avantajları sağlar:
1. **Özel Landing Page**: Her butik üreticiye `butikcarsi.com/uretici/[slug]` şeklinde şık bir profil ve vitrin sayfası sağlanır (Instagram biyosuna ekleyebilirler).
2. **Instagram & Sosyal Medya Tanıtımı**: Butiklerin Instagram profilleri (`@lazerci_hediyelik` vb.) öne çıkarılır, doğrudan takip ve WhatsApp iletişim butonları sunulur.
3. **Kişiye Özel Üretim Notları**: İsim, tarih, fotoğraf baskısı veya özel sipariş istekleri için entegre not sistemi.
4. **%10 Komisyon Modeli**: Sabit aidat veya listeleme ücreti yoktur. Sadece gerçekleşen satış üzerinden %10 platform komisyonu alınır, %90 tutar üreticinin hesabına aktarılır.
5. **Emanet (Escrow) ve iyzico Korumalı Ödeme**: Alıcı sipariş verdiğinde ödeme havuzda bekletilir. Kargo teslim edilip onaylandığında veya 7 gün sonra üreticinin IBAN'ına aktarılır.

---

## 🏗️ Mimari ve Teknoloji Yığını

- **Frontend**: [Astro 5 (SSG)](https://astro.build/) — Ultra hızlı statik sayfa üretimi, modern artisan tasarım sistemi (Outfit & Plus Jakarta Sans tipografisi), mobil uyumlu responsive vitrin.
- **Backend & API**: PHP 8+ REST API — Autoloading, MVC mimarisi, JSON yanıt motoru, iyzico Marketplace API entegrasyonu, bildirim servisi.
- **Veritabanı**: MySQL 8.0 — İlişkisel şema (üreticiler, ürünler, sub-order mimarisi, komisyonlar, payouts, bannerlar).
- **Yönetim Panelleri**:
  - **Platform Admin Paneli** (`/panel/admin/`): Süper yönetici; üretici onayları, komisyon gelir raporları, payout transfer onayları, kategori & banner yönetimi.
  - **Üretici Self-Service Paneli** (`/panel/producer/`): Butik üreticilerin ürün ekleme/düzenleme, kendilerine gelen alt siparişleri (sub-orders) görüntüleme, kargo takip numarası girme ve kazançlarını izleme paneli.

---

## 📂 Dizin Yapısı

```
E:/PROJECT/web/butikcarsi/
├── frontend/                     # Astro SSG Web Sitesi
│   ├── src/
│   │   ├── layouts/              # BaseLayout
│   │   ├── components/           # Header, Footer, ProductCard, ProducerCard
│   │   ├── pages/                # Anasayfa, Üretici Landing, Ürün Detay, Sepet, Ödeme vb.
│   │   ├── styles/               # global.css (Design tokens & micro-interactions)
│   │   └── lib/                  # Veri katmanı (data.ts)
│   ├── astro.config.mjs
│   └── package.json
│
├── panel/                        # PHP Backend & Paneller
│   ├── admin/                    # Platform Admin Paneli
│   │   ├── index.php             # Dashboard
│   │   ├── producers.php         # Üretici onay ve listesi
│   │   ├── products.php          # Ürün yönetimi & onay
│   │   ├── orders.php            # Marketplace siparişleri
│   │   ├── commissions.php       # %10 komisyon gelir takibi
│   │   ├── payouts.php           # Üretici hak ediş transferleri
│   │   └── banners.php           # Anasayfa bannerları
│   ├── producer/                 # Üretici Self-Service Paneli
│   │   ├── index.php             # Atölye Dashboard
│   │   ├── products.php          # Ürünlerim & Stok
│   │   ├── product-add.php       # Yeni ürün & kişiselleştirme formu
│   │   ├── orders.php            # Gelen siparişler
│   │   ├── order-detail.php      # Kargo takip no girme
│   │   ├── earnings.php          # Kazanç & IBAN takibi
│   │   └── profile.php           # Butik profili & Instagram bilgileri
│   ├── api/                      # REST API Endpoints
│   │   ├── producers.php
│   │   ├── products.php
│   │   ├── categories.php
│   │   └── iyzico/
│   │       ├── checkout.php      # Marketplace CheckoutForm init
│   │       ├── callback.php      # 3D Secure callback
│   │       └── approval.php      # Escrow teslimat onayı
│   ├── core/                     # Çekirdek Sınıflar
│   │   ├── Database.php          # PDO Veritabanı motoru
│   │   ├── Auth.php              # Session & Yetkilendirme
│   │   ├── CommissionEngine.php  # %10 komisyon ve hak ediş hesaplama
│   │   ├── IyzicoMarketplace.php # iyzico Sub-Merchant & Escrow API
│   │   ├── NotificationService.php # E-posta ve WhatsApp bildirimleri
│   │   └── Config.php            # .env yükleyici
│   └── uploads/                  # Üretici logoları ve ürün görselleri
│
└── database/
    ├── schema.sql                # Tüm tablolar ve ilişkiler
    └── seed.sql                  # Gerçekçi demo verileri (5 butik, ürünler, siparişler)
```

---

## 🚀 Kurulum ve Çalıştırma

### 1. Veritabanı Kurulumu
1. MySQL'de bir veritabanı oluşturun:
   ```sql
   CREATE DATABASE butikcarsi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Şema ve başlangıç verilerini yükleyin:
   ```bash
   mysql -u root -p butikcarsi < database/schema.sql
   mysql -u root -p butikcarsi < database/seed.sql
   ```

### 2. Panel Yapılandırması (.env)
`panel/.env` dosyasını veritabanı ve iyzico bilgilerinizle güncelleyin:
```env
DB_HOST=localhost
DB_NAME=butikcarsi
DB_USER=root
DB_PASS=
SITE_URL=http://localhost/butikcarsi
IYZICO_API_KEY=sandbox-xxx
IYZICO_SECRET_KEY=sandbox-yyy
```

### 3. Frontend Geliştirme (Astro)
```bash
cd frontend
npm install
npm run dev
```
Canlı derleme için:
```bash
npm run build
```

---

## 👥 Demo Giriş Bilgileri

- **Admin Paneli**: `/panel/admin/login.php`
  - Kullanıcı Adı: `admin`
  - Şifre: `admin123`
- **Üretici Paneli (Lazerci Hediyelik)**: `/panel/producer/login.php`
  - E-Posta: `lazerci@butikcarsi.com`
  - Şifre: `producer123`

---

## 📄 Lisans
Bu proje Nev Dijital Medya bünyesinde geliştirilmiştir.
