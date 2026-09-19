export interface Producer {
    id: number;
    slug: string;
    brand_name: string;
    owner_name: string;
    email: string;
    phone: string;
    description: string;
    logo_url: string;
    cover_image_url: string;
    city: string;
    district: string;
    instagram_url: string;
    instagram_handle: string;
    whatsapp_number: string;
    commission_rate: number;
    is_featured: boolean;
    rating: number;
    review_count: number;
    sales_count: number;
    category: string;
}

export interface Product {
    id: number;
    producer_id: number;
    producer_slug: string;
    producer_name: string;
    producer_instagram: string;
    producer_city: string;
    slug: string;
    name: string;
    short_description: string;
    description: string;
    regular_price: number;
    sale_price: number | null;
    stock_status: 'instock' | 'outofstock';
    customizable: boolean;
    customization_note: string | null;
    production_time: string;
    material: string;
    free_shipping: boolean;
    shipping_cost: number;
    primary_image: string;
    images: string[];
    category_slugs: string[];
    is_featured: boolean;
    rating: number;
    review_count: number;
}

export interface Category {
    id: number;
    name: string;
    slug: string;
    description: string;
    icon: string;
    image_url: string;
}

export interface Banner {
    id: number;
    title: string;
    subtitle: string;
    image_url: string;
    link_url: string;
    position: string;
}

export const CATEGORIES: Category[] = [
    {
        id: 1,
        name: 'Kişiye Özel Hediyelik',
        slug: 'kisiye-ozel-hediyelik',
        description: 'İsim, tarih ve fotoğraf baskılı unutulmaz hediyeler',
        icon: '🎁',
        image_url: 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=600&auto=format&fit=crop&q=80'
    },
    {
        id: 2,
        name: 'Lazer Kesim & Baskı',
        slug: 'lazer-kesim-baski',
        description: 'Çelik kolye, künye, Zippo çakmak ve ahşap lazer kazıma',
        icon: '⚡',
        image_url: 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=600&auto=format&fit=crop&q=80'
    },
    {
        id: 3,
        name: 'Epoksi & Reçine Sanatı',
        slug: 'epoksi-recine-sanati',
        description: 'Deniz dalgalı sunumluk, sehpa ve el yapımı reçine objeler',
        icon: '🌊',
        image_url: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=600&auto=format&fit=crop&q=80'
    },
    {
        id: 4,
        name: 'El Örgüsü & Makrome',
        slug: 'el-orgusu-makrome',
        description: 'El emeği kağıt ip çantalar, hırkalar ve duvar dekorasyonu',
        icon: '🧶',
        image_url: 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?w=600&auto=format&fit=crop&q=80'
    },
    {
        id: 5,
        name: 'Hakiki Deri Tasarımlar',
        slug: 'hakiki-deri-tasarimlar',
        description: 'El dikişi deri cüzdan, kartlık, kemer ve aksesuarlar',
        icon: '💼',
        image_url: 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=600&auto=format&fit=crop&q=80'
    },
    {
        id: 6,
        name: 'Seramik & Çömlek',
        slug: 'seramik-comlek',
        description: 'Elde şekillendirilmiş el boyaması kupa, vazo ve tütsülük',
        icon: '☕',
        image_url: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=600&auto=format&fit=crop&q=80'
    }
];

export const PRODUCERS: Producer[] = [
    {
        id: 1,
        slug: 'lazerci-hediyelik',
        brand_name: 'Lazerci Hediyelik',
        owner_name: 'Ahmet Yılmaz',
        email: 'lazerci@butikcarsi.com',
        phone: '+90 532 111 22 33',
        description: 'Kişiye özel lazer kazıma, kararmaz çelik kolye, deri bileklik, Zippo çakmak ve ahşap kutulu hediyelik setler. Sevginizi ve hatıralarınızı ölümsüzleştiren yüksek hassasiyetli zanaat.',
        logo_url: 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=300&auto=format&fit=crop&q=80',
        cover_image_url: 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=1600&auto=format&fit=crop&q=80',
        city: 'İstanbul',
        district: 'Kadıköy',
        instagram_url: 'https://www.instagram.com/lazerci_hediyelik',
        instagram_handle: '@lazerci_hediyelik',
        whatsapp_number: '+905321112233',
        commission_rate: 10,
        is_featured: true,
        rating: 4.9,
        review_count: 142,
        sales_count: 520,
        category: 'Kişiye Özel & Lazer Baskı'
    },
    {
        id: 2,
        slug: 'epoksi-sanat',
        brand_name: 'Epoksi Sanat Atölyesi',
        owner_name: 'Zeynep Kaya',
        email: 'epoksi@butikcarsi.com',
        phone: '+90 543 222 33 44',
        description: 'Deniz dalgası desenli epoksi reçine sunumluklar, ceviz kütük masa saatleri ve kişiye özel altın varaklı takı tepsileri. Her parça doğanın desenine göre tek ve benzersizdir.',
        logo_url: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=300&auto=format&fit=crop&q=80',
        cover_image_url: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=1600&auto=format&fit=crop&q=80',
        city: 'İzmir',
        district: 'Urla',
        instagram_url: 'https://www.instagram.com/epoksi_sanat',
        instagram_handle: '@epoksi_sanat',
        whatsapp_number: '+905432223344',
        commission_rate: 10,
        is_featured: true,
        rating: 5.0,
        review_count: 98,
        sales_count: 310,
        category: 'Epoksi & Reçine Sanatı'
    },
    {
        id: 3,
        slug: 'ilmek-butik',
        brand_name: 'İlmek Butik Örgü',
        owner_name: 'Fatma Demir',
        email: 'ilmek@butikcarsi.com',
        phone: '+90 555 333 44 55',
        description: '%100 pamuklu kağıt ipten tığ işi hasır çantalar, bohem makrome duvar süsleri ve bebek tasarımları. Doğallık ve zarafet evinize gelsin.',
        logo_url: 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?w=300&auto=format&fit=crop&q=80',
        cover_image_url: 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?w=1600&auto=format&fit=crop&q=80',
        city: 'Ankara',
        district: 'Çankaya',
        instagram_url: 'https://www.instagram.com/ilmek_butik',
        instagram_handle: '@ilmek_butik',
        whatsapp_number: '+905553334455',
        commission_rate: 10,
        is_featured: true,
        rating: 4.8,
        review_count: 76,
        sales_count: 240,
        category: 'El Örgüsü & Makrome'
    },
    {
        id: 4,
        slug: 'deri-zanaat',
        brand_name: 'Deri Zanaat Atölyesi',
        owner_name: 'Burak Çelik',
        email: 'deri@butikcarsi.com',
        phone: '+90 533 444 55 66',
        description: 'Geleneksel saraç dikişi ile tamamen elde dikilen bitkisel tabaklanmış dana derisi cüzdan, kartlık ve kişiselleştirilebilir anahtarlıklar. Zamanla değerlenen hakiki zanaat.',
        logo_url: 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=300&auto=format&fit=crop&q=80',
        cover_image_url: 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=1600&auto=format&fit=crop&q=80',
        city: 'Bursa',
        district: 'Nilüfer',
        instagram_url: 'https://www.instagram.com/deri_zanaat',
        instagram_handle: '@deri_zanaat',
        whatsapp_number: '+905334445566',
        commission_rate: 10,
        is_featured: true,
        rating: 4.9,
        review_count: 114,
        sales_count: 480,
        category: 'Hakiki Deri'
    },
    {
        id: 5,
        slug: 'toprak-ates-seramik',
        brand_name: 'Toprak & Ateş Seramik',
        owner_name: 'Deniz Akın',
        email: 'seramik@butikcarsi.com',
        phone: '+90 536 555 66 77',
        description: 'Çömlekçi çarkında elde şekillendirilmiş, gıdaya uygun mat sır ile sırlanmış ve 1040 derecede pişirilmiş eşsiz seramik kupalar, tabaklar ve tütsülükler.',
        logo_url: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=300&auto=format&fit=crop&q=80',
        cover_image_url: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=1600&auto=format&fit=crop&q=80',
        city: 'Antalya',
        district: 'Muratpaşa',
        instagram_url: 'https://www.instagram.com/toprak_ates_seramik',
        instagram_handle: '@toprak_ates_seramik',
        whatsapp_number: '+905365556677',
        commission_rate: 10,
        is_featured: true,
        rating: 4.9,
        review_count: 85,
        sales_count: 290,
        category: 'Seramik & Çömlek'
    }
];

export const PRODUCTS: Product[] = [
    {
        id: 1,
        producer_id: 1,
        producer_slug: 'lazerci-hediyelik',
        producer_name: 'Lazerci Hediyelik',
        producer_instagram: '@lazerci_hediyelik',
        producer_city: 'İstanbul',
        slug: 'kisiye-ozel-isimli-celik-kolye',
        name: 'Kişiye Özel İsim Yazılı Çelik Plaka Kolye',
        short_description: 'Kararmaz 316L paslanmaz çelik üzerine lazer kazıma isimli kolye.',
        description: 'Yüksek kaliteli 316L medikal çelikten üretilmiştir. Kararmaz, paslanmaz, teni tahriş etmez. İstediğiniz isim, tarih veya koordinat lazer fiber makinemizde kusursuz olarak işlenir. Şık kadife hediye kutusunda gönderilir.',
        regular_price: 349.00,
        sale_price: 299.00,
        stock_status: 'instock',
        customizable: true,
        customization_note: 'Kolye plakasına yazılacak isim, tarih veya anlamlı mesajınızı giriniz.',
        production_time: '1-2 iş günü',
        material: '316L Paslanmaz Çelik',
        free_shipping: true,
        shipping_cost: 0,
        primary_image: 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&auto=format&fit=crop&q=80',
        images: [
            'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1611591475152-473523dd66c4?w=800&auto=format&fit=crop&q=80'
        ],
        category_slugs: ['kisiye-ozel-hediyelik', 'lazer-kesim-baski'],
        is_featured: true,
        rating: 5.0,
        review_count: 48
    },
    {
        id: 2,
        producer_id: 1,
        producer_slug: 'lazerci-hediyelik',
        producer_name: 'Lazerci Hediyelik',
        producer_instagram: '@lazerci_hediyelik',
        producer_city: 'İstanbul',
        slug: 'ozel-baskili-zippo-model-cakmak',
        name: 'Kişiye Özel Lazer Kazıma Mat Siyah Çakmak',
        short_description: 'Fotoğraf veya isim kazımalı dayanıklı rüzgar geçirmez çakmak.',
        description: 'Mat siyah kaplama gövde üzerine yüksek hassasiyetli fiber lazer teknolojisi ile fotoğraf, logo veya el yazısı kazınabilir. Sevdikleriniz için ömür boyu kalıcı bir anı.',
        regular_price: 450.00,
        sale_price: 399.00,
        stock_status: 'instock',
        customizable: true,
        customization_note: 'Kazınmasını istediğiniz yazı veya fotoğraf detaylarını yazınız.',
        production_time: '2 iş günü',
        material: 'Pirinç & Mat Siyah Kaplama',
        free_shipping: false,
        shipping_cost: 49.90,
        primary_image: 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&auto=format&fit=crop&q=80',
        images: [
            'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&auto=format&fit=crop&q=80'
        ],
        category_slugs: ['kisiye-ozel-hediyelik', 'lazer-kesim-baski'],
        is_featured: true,
        rating: 4.9,
        review_count: 32
    },
    {
        id: 3,
        producer_id: 1,
        producer_slug: 'lazerci-hediyelik',
        producer_name: 'Lazerci Hediyelik',
        producer_instagram: '@lazerci_hediyelik',
        producer_city: 'İstanbul',
        slug: 'ahsap-kutulu-deri-bileklik-ve-kalem-seti',
        name: 'Ahşap Kutulu İsimli Deri Bileklik & Roller Kalem Seti',
        short_description: 'Erkekler için mükemmel doğum günü ve yıldönümü hediyesi.',
        description: 'İsme özel ahşap ceviz kaplama hediye kutusu içerisinde lazer baskılı deri bileklik ve mat siyah metal roller tükenmez kalem.',
        regular_price: 650.00,
        sale_price: 579.00,
        stock_status: 'instock',
        customizable: true,
        customization_note: 'Bileklik, kalem ve ahşap kutu üzerine yazılacak isim-soyismi giriniz.',
        production_time: '2-3 iş günü',
        material: 'Hakiki Deri, Çelik, Ceviz Ahşap',
        free_shipping: true,
        shipping_cost: 0,
        primary_image: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&auto=format&fit=crop&q=80',
        images: ['https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&auto=format&fit=crop&q=80'],
        category_slugs: ['kisiye-ozel-hediyelik', 'lazer-kesim-baski'],
        is_featured: true,
        rating: 4.9,
        review_count: 27
    },
    {
        id: 4,
        producer_id: 2,
        producer_slug: 'epoksi-sanat',
        producer_name: 'Epoksi Sanat Atölyesi',
        producer_instagram: '@epoksi_sanat',
        producer_city: 'İzmir',
        slug: 'deniz-dalgasi-epoksi-ceviz-sunumluk',
        name: 'Deniz Dalgası Reçine Ceviz Ağacı Sunumluk',
        short_description: 'Doğal ceviz kütük ve epoksi reçine ile elde yapılan eşsiz servis tepsisi.',
        description: 'Her biri doğadaki ağaç harelerine göre benzersiz olan ceviz kütüğü üzerinde çok katmanlı deniz dalgası efekti reçine uygulaması. Gıdaya uygun sertifikalı doğal yağ ile cilalanmıştır.',
        regular_price: 890.00,
        sale_price: 790.00,
        stock_status: 'instock',
        customizable: true,
        customization_note: 'Ahşap köşesine pirinç plaka ile isim veya tarih eklenebilir.',
        production_time: '3-4 iş günü',
        material: 'Doğal Ceviz Ağacı, Epoksi Reçine',
        free_shipping: true,
        shipping_cost: 0,
        primary_image: 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=800&auto=format&fit=crop&q=80',
        images: ['https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=800&auto=format&fit=crop&q=80'],
        category_slugs: ['epoksi-recine-sanati', 'kisiye-ozel-hediyelik'],
        is_featured: true,
        rating: 5.0,
        review_count: 36
    },
    {
        id: 5,
        producer_id: 2,
        producer_slug: 'epoksi-sanat',
        producer_name: 'Epoksi Sanat Atölyesi',
        producer_instagram: '@epoksi_sanat',
        producer_city: 'İzmir',
        slug: 'altin-varakli-epoksi-taki-tabagi',
        name: 'Altın Varaklı Çiçekli Epoksi Takı Tabağı',
        short_description: 'Kurutulmuş gerçek çiçekler ve 24k altın yapraklarla süslenmiş zarif halka tabak.',
        description: 'Özel günler, nişan ve alyans sunumları için mükemmel el yapımı şeffaf epoksi takı ve yüzük tabağı.',
        regular_price: 290.00,
        sale_price: 240.00,
        stock_status: 'instock',
        customizable: false,
        customization_note: null,
        production_time: '2 iş günü',
        material: 'Döküm Reçine, Kurutulmuş Çiçek, Altın Varak',
        free_shipping: false,
        shipping_cost: 39.90,
        primary_image: 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=800&auto=format&fit=crop&q=80',
        images: ['https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=800&auto=format&fit=crop&q=80'],
        category_slugs: ['epoksi-recine-sanati'],
        is_featured: false,
        rating: 4.8,
        review_count: 19
    },
    {
        id: 6,
        producer_id: 3,
        producer_slug: 'ilmek-butik',
        producer_name: 'İlmek Butik Örgü',
        producer_instagram: '@ilmek_butik',
        producer_city: 'Ankara',
        slug: 'el-orgusu-kagit-ip-hasir-plaj-cantasi',
        name: 'El Örgüsü Kağıt İp Hasır Omuz Çantası',
        short_description: 'Yaz kombinlerinin vazgeçilmezi, astarlı ve fermuarlı el emeği çanta.',
        description: '%100 doğal kağıt ipten tığ ile ilmek ilmek örülmüştür. İçinde kaliteli keten astar ve fermuarlı cep mevcuttur. Hafif ve son derece dayanıklıdır.',
        regular_price: 750.00,
        sale_price: 680.00,
        stock_status: 'instock',
        customizable: false,
        customization_note: null,
        production_time: '3-5 iş günü',
        material: 'Doğal Kağıt İp, Keten Astar',
        free_shipping: true,
        shipping_cost: 0,
        primary_image: 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?w=800&auto=format&fit=crop&q=80',
        images: ['https://images.unsplash.com/photo-1584992236310-6edddc08acff?w=800&auto=format&fit=crop&q=80'],
        category_slugs: ['el-orgusu-makrome'],
        is_featured: true,
        rating: 4.9,
        review_count: 22
    },
    {
        id: 7,
        producer_id: 3,
        producer_slug: 'ilmek-butik',
        producer_name: 'İlmek Butik Örgü',
        producer_instagram: '@ilmek_butik',
        producer_city: 'Ankara',
        slug: 'bohem-makrome-yaprak-duvar-susu',
        name: 'Bohem Tarzı 5li Makrome Yaprak Duvar Dekoru',
        short_description: 'Doğal dal üzerine örülmüş taranmış pamuk makrome duvar süsü.',
        description: 'Evinize sıcak ve bohem bir hava katacak el yapımı duvar süsü. Doğadan toplanmış fırınlanmış meşe dalı üzerine krem, bej ve hardal renk tonlarında pamuk iplerle işlenmiştir.',
        regular_price: 480.00,
        sale_price: 420.00,
        stock_status: 'instock',
        customizable: true,
        customization_note: 'Farklı renk tonu talebinizi belirtebilirsiniz.',
        production_time: '2-3 iş günü',
        material: '%100 Pamuk Makrome İpi, Meşe Dalı',
        free_shipping: true,
        shipping_cost: 0,
        primary_image: 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&auto=format&fit=crop&q=80',
        images: ['https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=800&auto=format&fit=crop&q=80'],
        category_slugs: ['el-orgusu-makrome'],
        is_featured: false,
        rating: 4.8,
        review_count: 15
    },
    {
        id: 8,
        producer_id: 4,
        producer_slug: 'deri-zanaat',
        producer_name: 'Deri Zanaat Atölyesi',
        producer_instagram: '@deri_zanaat',
        producer_city: 'Bursa',
        slug: 'el-dikisi-vintage-deri-kartlik-cuzdan',
        name: 'El Dikişi Vintage Dana Derisi Minimal Kartlık',
        short_description: 'Geleneksel mumlu saraç dikişi ile ömür boyu dayanıklı hakiki deri kartlık.',
        description: '1. sınıf bitkisel tabaklanmış crazy horse dana derisinden üretilmiştir. 6 kart gözü ve orta kağıt para bölmesi bulunur. Zamanla kullanıldıkça kendine has vintage bir patina kazanır.',
        regular_price: 420.00,
        sale_price: 360.00,
        stock_status: 'instock',
        customizable: true,
        customization_note: 'Sağ alt köşeye sıcak baskı ile basılmasını istediğiniz baş harfleri yazınız (Örn: A.Y).',
        production_time: '1-2 iş günü',
        material: 'Crazy Horse Hakiki Dana Derisi',
        free_shipping: false,
        shipping_cost: 39.90,
        primary_image: 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=800&auto=format&fit=crop&q=80',
        images: ['https://images.unsplash.com/photo-1627123424574-724758594e93?w=800&auto=format&fit=crop&q=80'],
        category_slugs: ['hakiki-deri-tasarimlar', 'kisiye-ozel-hediyelik'],
        is_featured: true,
        rating: 5.0,
        review_count: 38
    },
    {
        id: 9,
        producer_id: 4,
        producer_slug: 'deri-zanaat',
        producer_name: 'Deri Zanaat Atölyesi',
        producer_instagram: '@deri_zanaat',
        producer_city: 'Bursa',
        slug: 'kisisellestirilebilir-deri-anahtarlik',
        name: 'Pirinç Tokalı İsim Baskılı Deri Anahtarlık',
        short_description: 'Sağlam pirinç kanca ve hakiki deri askılı şık anahtarlık.',
        description: 'Kişiye özel harf ve rakam baskısı yapılabilen pratik ve ömür boyu dayanıklı tasarım.',
        regular_price: 160.00,
        sale_price: 130.00,
        stock_status: 'instock',
        customizable: true,
        customization_note: 'Deri şerit üzerine yazılacak ismi giriniz (maksimum 10 harf).',
        production_time: '1 iş günü',
        material: 'Hakiki Deri, Masif Pirinç Aksam',
        free_shipping: false,
        shipping_cost: 29.90,
        primary_image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop&q=80',
        images: ['https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop&q=80'],
        category_slugs: ['hakiki-deri-tasarimlar', 'kisiye-ozel-hediyelik'],
        is_featured: false,
        rating: 4.8,
        review_count: 29
    },
    {
        id: 10,
        producer_id: 5,
        producer_slug: 'toprak-ates-seramik',
        producer_name: 'Toprak & Ateş Seramik',
        producer_instagram: '@toprak_ates_seramik',
        producer_city: 'Antalya',
        slug: 'el-yapimi-seramik-espresso-kupa-seti',
        name: 'El Boyaması Seramik 2li Espresso Fincan Seti',
        short_description: 'Benekli stoneware çamurdan çömlekçi çarkında elde üretilmiş özel seri.',
        description: 'Gıdaya uygun mat sır ile kaplanmış, bulaşık makinesinde yıkanabilir. Kahve keyfinize el yapımı zanaat dokunuşu katar.',
        regular_price: 380.00,
        sale_price: 330.00,
        stock_status: 'instock',
        customizable: false,
        customization_note: null,
        production_time: '2 iş günü',
        material: 'Stoneware Doğal Seramik Çamuru',
        free_shipping: false,
        shipping_cost: 39.90,
        primary_image: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&auto=format&fit=crop&q=80',
        images: ['https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800&auto=format&fit=crop&q=80'],
        category_slugs: ['seramik-comlek'],
        is_featured: true,
        rating: 4.9,
        review_count: 18
    }
];

export const BANNERS: Banner[] = [
    {
        id: 1,
        title: 'Instagram Butik Üreticileri Tek Çatı Altında',
        subtitle: 'El emeği, kişiye özel hediyeler ve eşsiz tasarım ürünleri %100 güvenli ödeme ile keşfedin.',
        image_url: 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?w=1600&auto=format&fit=crop&q=80',
        link_url: '/kesfet',
        position: 'hero'
    },
    {
        id: 2,
        title: 'Kişiye Özel Lazer Kazıma Hediyeler',
        subtitle: 'Lazerci Hediyelik atölyesinin en yeni tasarımları ButikÇarşı güvencesiyle!',
        image_url: 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=1600&auto=format&fit=crop&q=80',
        link_url: '/uretici/lazerci-hediyelik',
        position: 'hero'
    }
];

export function getProducerBySlug(slug: string): Producer | undefined {
    return PRODUCERS.find(p => p.slug === slug);
}

export function getProductsByProducer(slug: string): Product[] {
    return PRODUCTS.filter(p => p.producer_slug === slug);
}

export function getProductBySlug(slug: string): Product | undefined {
    return PRODUCTS.find(p => p.slug === slug);
}

export function getProductsByCategory(categorySlug: string): Product[] {
    return PRODUCTS.filter(p => p.category_slugs.includes(categorySlug));
}

export function getCategoryBySlug(slug: string): Category | undefined {
    return CATEGORIES.find(c => c.slug === slug);
}
