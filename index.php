<!DOCTYPE html>
<html lang="tr">
<head>
    <meta name="google-site-verification" content="fpvJbOeS7J2lqeUygmRqBhIP7XaPoZGkrl5qYGrHvbU" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Yağmur Bilgisayar | Akınsoft Adana Bayi</title>
    <link rel="icon" type="image/jpeg" href="/Resimler/logo.jpg">
    <meta name="description" content="Yağmur Bilgisayar, 2004'ten beri Akınsoft Adana Bayisi olarak ERP, e-Fatura ve sektörel yazılım çözümleri sunar. Adana'da bilgisayar teknik servis, ikinci el bilgisayar alım satım ve e-ticaret hizmetleri için bize ulaşın, Akınsoft Adana Bayisi Yağmur Bilgisayar - Profesyonel E-ticaret sitesi. Yazılım, Donanım, Bilgisayar Bileşenleri, Barkod Ürünleri, Teknik Servis ve daha fazlası. Güvenli ödeme, hızlı kargo ve 7/24 destek." />
    <meta name="keywords" content="Akınsoft Adana Bayi, Yağmur Bilgisayar, Akınsoft Adana, Adana bilgisayar teknik servis, Adana ikinci el bilgisayar, Adana bilgisayar satışı, Adana e-fatura, Adana e-arşiv, Adana ERP çözümleri, Akınsoft Wolvox Adana, Adana yazılım firmaları, Adana e-ticaret, Adana web tasarım, Adana bilgisayar firmaları, Adana bilgisayar tamiri, ikinci el bilgisayar alım satım Adana, Adana barkod sistemleri, Adana pos sistemleri, Adana restoran otomasyonu, Akınsoft destek Adana, Adana muhasebe programı, rulo, termel, kağıt, yazar kasa, Adana, Akinsoft, Akınsoft, adanaakınsoft, akınsoftadana, adanaakinsoft, akinsoftadana, E-ticaret, online alışveriş, bilgisayar parçaları, yazılım, donanım, barkod ürünleri, teknik servis, Akınsoft, Wolvox, ERP, Adana, Yağmur Bilgisayar, güvenli ödeme, hızlı kargo" />
    <meta name="author" content="Yağmur Bilgisayar - Akınsoft Adana Bayisi" />
    <meta name="robots" content="index, follow" />
    <meta name="language" content="Turkish" />
    <meta name="revisit-after" content="7 days" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <meta property="og:title" content="Yağmur Bilgisayar - Akınsoft Adana Bayisi | E-Ticaret" />
    <meta property="og:description" content="Profesyonel e-ticaret sitesi. Yazılım, donanım, bilgisayar bileşenleri ve daha fazlası." />
    <meta property="og:type" content="E-ticaret, website" />
    <meta property="og:url" content="https://akinsoftadana.com.tr" />
    <meta property="og:image" content="/Resimler/logo.jpg" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Yağmur Bilgisayar - Akınsoft Adana Bayisi" />
    <meta name="twitter:description" content="Profesyonel e-ticaret sitesi" />
    <meta name="twitter:image" content="/Resimler/logo.jpg" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    

   <style>
        /* --- TEMEL AYARLAR VE KRİTİK DÜZELTMELER --- */
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            max-width: 100vw;
        }
        .slider-container { z-index: -1; }
        body.modal-open { overflow: hidden; }

        /* --- ÜRÜN KARTI BOYUT VE BUTON DÜZELTMESİ --- */
        .product-card {
            min-height: auto !important;
            display: flex;
            flex-direction: column;
        }
        .product-card .p-4.flex-grow {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .product-card h3 {
            height: 3rem; 
            line-height: 1.5rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .product-card .description-clamp {
            height: 2.5rem; 
            line-height: 1.25rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .product-card .mt-auto {
            margin-top: auto;
        }
        .product-card .flex.gap-2 a,
        .product-card .flex.gap-2 button {
            font-weight: 500;
        }
        
        /* --- MOBİL MENÜ KONUM DÜZELTMESİ --- */
        .mobile-menu-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            width: 90vw;
            max-width: 350px;
            background: rgba(30, 58, 138, 0.98);
            padding: 1rem;
            z-index: 2000;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0 0 12px 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            backdrop-filter: blur(5px);
        }
        .mobile-menu-content.active { display: block; }
        .mobile-menu-content a {
            display: block;
            padding: 0.75rem 0;
            text-align: center;
            margin: 0.5rem 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .mobile-menu-content a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fbbf24;
        }

        /* --- "YUKARI ÇIK" OK İŞARETİ DÜZELTMESİ --- */
        #scrollTopBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background-color: red;
            color: white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        
        /* --- DİĞER ORİJİNAL STİLLERİNİZ --- */
        .modal-scroll-content { overflow-y: auto; max-height: 70vh; padding: 1.5rem; background-color: #f8fafc; border-radius: 0.5rem; box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0; scrollbar-width: thin; scrollbar-color: #94a3b8 #e2e8f0; }
        .modal-scroll-content::-webkit-scrollbar { width: 14px; }
        .modal-scroll-content::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 10px; }
        .modal-scroll-content::-webkit-scrollbar-thumb { background-color: #94a3b8; border-radius: 10px; border: 3px solid #e2e8f0; }
        .price-pulse-color { display: inline-block; animation: pulsePriceColor 2s ease-in-out infinite; }
        @keyframes pulsePriceColor { 0%, 100% { transform: scale(1); color: #1e40af; } 50% { transform: scale(1.1); color: #dc2626; } }
        .price-parliament-blue { color: #003366 !important; }
        .container { max-width: 100%; overflow-x: hidden; }
        .glass { background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .btn-raised { box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: all 0.3s ease-in-out; transform: translateY(0); }
        .btn-raised:hover { box-shadow: 0 6px 12px rgba(0,0,0,0.3); transform: translateY(-2px); }
        #discountNotificationModal { position: fixed; top: 5rem; right: 1rem; z-index: 100001; transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1); transform: translateX(120%); }
        #discountNotificationModal.show { transform: translateX(0); }
        #whatsapp-button { position: fixed; bottom: 100px; right: 20px; z-index: 50; }
        .user-dropdown { position: relative; display: inline-block; }
        .user-dropdown-content { display: none; position: absolute; right: 0; top: 100%; background-color: white; min-width: 200px; box-shadow: 0 8px 16px rgba(0,0,0,0.2); z-index: 1000; border-radius: 8px; margin-top: 5px; }
        .user-dropdown-content.show { display: block; }
        .user-dropdown-content a { color: #1e3a8a; padding: 12px 16px; text-decoration: none; display: flex; align-items: center; transition: background-color 0.3s; }
        .user-dropdown-content a:hover { background-color: #1e3a8a; color: white; transform: translateX(5px); }
        .user-dropdown-content a i { margin-right: 8px; width: 16px; }
        form label, form input, form textarea, form select { color: #1e3a8a !important; }
        form input::placeholder, form textarea::placeholder { color: #1e3a8a !important; opacity: 0.7; }
        #whatsapp-button a { display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background-color: #25D366; border-radius: 50%; box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: all 0.3s ease; }
        #whatsapp-button a:hover { transform: scale(1.1); box-shadow: 0 6px 12px rgba(0,0,0,0.3); }
        #whatsapp-button i { color: white; font-size: 1.5rem; }
        #cart-button { position: fixed; bottom: 160px; right: 20px; z-index: 50; }
        #cart-button a { display: flex; align-items: center; justify-content: center; width: 50px; height: 50px; background-color: #dc2626; border-radius: 50%; box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: all 0.3s ease; }
        #cart-button a:hover { transform: scale(1.1); box-shadow: 0 6px 12px rgba(0,0,0,0.3); }
        #cart-button i { color: white; font-size: 1.5rem; }
        #cart-button .cart-count { position: absolute; top: -5px; right: -5px; background-color: #fbbf24; color: #1e3a8a; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold; }
        .stock-status { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.875rem; font-weight: 500; }
        .stock-in { background-color: #dcfce7; color: #166534; }
        .stock-low { background-color: #fef3c7; color: #92400e; }
        .stock-out { background-color: #fee2e2; color: #991b1b; }
        .wishlist-btn { position: absolute; top: 0.75rem; right: 0.75rem; background: rgba(255, 255, 255, 0.7); border: none; border-radius: 50%; width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; backdrop-filter: blur(2px); }
        .wishlist-btn:hover { background: rgba(255, 255, 255, 1); transform: scale(1.1); }
        .wishlist-btn i { color: #9ca3af; transition: all 0.2s ease-in-out; font-size: 1.25rem; }
        .wishlist-btn:hover i { color: #f87171; }
        .wishlist-btn.active i { color: #ef4444; }
        .filter-panel { background: white; border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        
     /* --- YENİ VE TAM KATEGORİ MENÜ STİLLERİ (z-index Düzeltildi) --- */
        
        /* * Z-INDEX DÜZELTMESİ: 
         * Ana menüye position:relative ve z-index:20 vererek, 
         * animasyonlu ürün listesinden (z-index: auto/0) daha üstte kalmasını sağlıyoruz.
         */
      /* --- YENİ VE TAM KATEGORİ MENÜ STİLLERİ (z-index + Alt Buton Estetiği Düzeltildi) --- */
        
        /* Sol sütunun tamamını ürünlerin üzerine çıkarır */
        #category-column {
            position: relative;
            z-index: 20;
        }

        /* Wrapper'ı butonlar arası boşluk için kullanıyoruz! */
        .category-btn-wrapper {
            position: relative;
            margin-bottom: 0.35rem; /* Ana butonların arası */
        }
        

        /* ANA KATEGORİ BUTONU (Kabartı + Düşük Yükseklik + Nokta Nokta) */
        .category-btn {
            background-color: white;
            color: #1e3a8a !important; 
            width: 80%;
            text-align: left;
            padding: 0.4rem 0.75rem; /* Yükseklik */
            font-size: 0.875rem;    /* Font boyutu */
            border-radius: 0.375rem; 
            transition: all 0.2s ease;
            font-weight: 600;
            border: 1px solid #e2e8f0; /* Düğme sınırı */
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); /* Kabartı */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ANA KATEGORİ HOVER (Kırmızı zemin, Beyaz yazı) */
        .category-btn:hover {
            background-color: #dc2626;
            color: white !important;
            transform: translateX(3px);
            border-color: #b91c1c;
            box-shadow: 0 2px 5px rgba(220, 38, 38, 0.3);
        }
        
        /* Aktif kategori (Tıklandığında) */
        .category-btn.active {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
            border-color: #b91c1c;
        }
        
        /* ALT KATEGORİ MENÜ KABI */
        .subcategory-menu {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            min-width: 220px;
            background-color: white;
            border-radius: 0.375rem; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            padding: 0.5rem; 
            z-index: 21; /* Ana sütundan (20) bir üstte */
            border: 1px solid #e2e8f0;
        }

        /* Ana kategori üzerine gelince alt menüyü göster */
        .category-btn-wrapper:hover .subcategory-menu {
            display: block;
        }

        /* ### DEĞİŞTİ ###: ALT KATEGORİ BUTONU (Kabartılı + Ayrı + Düşük Yükseklik) */
        .subcategory-btn {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.35rem 0.75rem; /* Yükseklik düşürüldü */
            font-size: 0.825rem;     /* Font küçültüldü */
            background-color: white;
            color: #1e3a8a !important;
            font-weight: 500;
            border-radius: 0.25rem;
            transition: all 0.2s ease;
            cursor: pointer;
            
            /* ### YENİ: Kabartı efekti */
            border: 1px solid #e0e0e0; 
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            
            /* ### YENİ: Alttaki ile ayırmak için boşluk */
            margin-bottom: 4px; 

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .subcategory-btn:last-child {
            margin-bottom: 0; /* Son butonun alt boşluğu olmasın */
        }

        /* ### DEĞİŞTİ ###: ALT KATEGORİ HOVER (Kırmızı zemin, Beyaz yazı) */
        .subcategory-btn:hover {
            background-color: #dc2626; /* Kırmızı */
            color: white !important;
            border-color: #b91c1c; /* Sınır da kırmızı olsun */
            box-shadow: 0 1px 3px rgba(220, 38, 38, 0.3); /* Hafif kırmızı gölge */
        }
       

        
       .slider-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .slider { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 1s ease-in-out; background-size: cover; background-position: center; filter: blur(3px); }
        .slider.active { opacity: 1; }
        .slider-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom right, rgba(17, 24, 89, 0.85), rgba(23, 55, 165, 0.85)); backdrop-filter: blur(1px); }
       
    /* --- YENİ EKLENDİ: YATAY BANNER CAROUSEL (Stilmoto gibi) --- */
        .banner-carousel-container {
            position: relative;
        }
        .banner-carousel-track-container {
            overflow-x: auto; /* Yatay kaydırmayı sağlar */
            scroll-snap-type: x mandatory; /* Kaydırmayı pürüzsüzce slaytlara kilitler */
            -webkit-overflow-scrolling: touch; /* iOS için akıcı kaydırma */
            scrollbar-width: none; /* Firefox scrollbar'ı gizle */
        }
        .banner-carousel-track-container::-webkit-scrollbar {
            display: none; /* Chrome/Safari scrollbar'ı gizle */
        }
        .banner-carousel-track {
            display: flex;
            gap: 16px; /* Kartlar arası boşluk */
            padding: 8px; /* Konteynerin kenarlarından hafif boşluk */
        }
        .banner-carousel-slide {
            flex: 0 0 auto; /* Slaytların büzülmesini engeller */
            scroll-snap-align: start; /* Her slaytın başına kilitler */
            background-color: white;
            border-radius: 0.5rem; /* 8px */
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .banner-carousel-slide:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        .banner-carousel-slide a {
            display: block;
            text-decoration: none;
            color: #1e3a8a; /* Koyu mavi */
        }
        .banner-carousel-slide img {
            width: 100%;
            height: 150px; /* Resim yüksekliği (sabit) */
            object-fit: cover;
        }
        .banner-carousel-slide p {
            font-size: 0.875rem; /* 14px */
            font-weight: 600;
            padding: 12px;
            text-align: center;
            height: 50px; /* Başlık için 2 satırlık yer */
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* --- İSTEĞİNİZ: Fırsat Banner'ı (BÜYÜK KARTLAR) --- */
        #firsat-banner-container .banner-carousel-slide {
            width: 280px; /* Fırsat kartı genişliği */
        }
        /* Telefondaki görünümü (ekranın %80'i) */
        @media (max-width: 767px) {
            #firsat-banner-container .banner-carousel-slide { width: 80vw; }
        }

        /* --- İSTEĞİNİZ: Vitrin Banner'ı (KÜÇÜK KARTLAR) --- */
        #vitrin-banner-container .banner-carousel-slide {
            width: 230px; /* Vitrin kartı genişliği (daha küçük) */
        }
        #vitrin-banner-container .banner-carousel-slide img {
             height: 120px; /* Resmi de biraz daha küçük */
        }
        /* Telefondaki görünümü (ekranın %65'i) */
        @media (max-width: 767px) {
             #vitrin-banner-container .banner-carousel-slide { width: 65vw; }
        }

        /* Carousel İleri/Geri Butonları */
        .banner-carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .banner-carousel-btn:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }
        .banner-carousel-btn.prev { left: -10px; }
        .banner-carousel-btn.next { right: -10px; }
        /* --- YATAY CAROUSEL CSS SONU --- */
    /* --- YENİ: MOBİL KATEGORİ MENÜSÜ --- */
        
        /* Panelin kendisi (Gizli hali) */
        #mobileMenu {
            color: #1e3a8a; /* Metin rengi (beyaz zeminde) */
            transform: translateX(-100%); /* Başlangıçta ekranın solunda gizli */
            /* HTML'deki -translate-x-full sınıfı yerine bunu kullanıyoruz */
        }

        /* Panelin 'active' sınıfı aldığındaki (Görünür) hali */
        #mobileMenu.active {
            transform: translateX(0); /* Ekrana kayarak gelir */
        }

        /* Arka plan karartması (Gizli hali) */
        #mobileMenuOverlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }

        /* Arka plan karartması (Görünür) hali */
        #mobileMenuOverlay.active {
            opacity: 1;
            pointer-events: auto;
            display: block; /* 'hidden' sınıfını ezer */
        }

        /* Panelin içindeki scroll alanı */
        #mobileCategoryList {
            height: calc(100vh - 65px); /* Başlık yüksekliği kadar boşluk bırak */
        }

        /* Kategori Linkleri (Ana Kategori) */
        .mobile-category-item {
            border-bottom: 1px solid #e5e7eb; /* Ayırıcı */
        }
        .mobile-category-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem; /* 16px */
            font-weight: 600; /* Kalın */
            color: #1e40af; /* Koyu mavi */
            transition: background-color 0.2s, color 0.2s;
            width: 100%;
        }
        .mobile-category-link:hover {
            background-color: #f3f4f6; /* Hafif gri */
        }
        .mobile-category-link > i {
            transition: transform 0.3s ease; /* Ok dönüş animasyonu */
        }
        .mobile-category-link.open > i {
            transform: rotate(180deg); /* Oku döndür */
        }

        /* Alt Kategori Listesi (Gizli) */
        .mobile-subcategory-list {
            display: none;
            background-color: #f9fafb; /* Çok hafif gri zemin */
            padding-left: 1rem; /* İçeri girinti */
            border-top: 1px solid #e5e7eb;
        }

        /* Alt Kategori Linkleri */
        .mobile-subcategory-link {
            display: block;
            padding: 0.75rem 1rem 0.75rem 1.5rem; /* Biraz daha az padding, daha çok girinti*/
            font-weight: 500;
            color: #374151; /* Koyu gri */
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s, color 0.2s;
            width: 100%;
        }
        .mobile-subcategory-link:last-child {
            border-bottom: none;
        }
        .mobile-subcategory-link:hover {
            background-color: #e5e7eb;
            color: #1e40af;
        }
    </style>
</head>

<body class="text-white">
    <div class="slider-container">
        <div class="slider active" style="background-image: url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80')"></div>
        <div class="slider" style="background-image: url('https://images.unsplash.com/photo-1550751827-4bd374c3f58b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80')"></div>
        <div class="slider" style="background-image: url('https://images.unsplash.com/photo-1517694712202-14dd9538aa97?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80')"></div>
        <div class="slider-overlay"></div>
    </div>
    <header class="px-4 flex justify-between items-stretch bg-blue-900 relative h-24">
        <div class="flex items-center space-x-4">
            <a href="/" class="bg-white p-1 rounded self-center hover:scale-105 transition-all duration-300 hover:shadow-lg hover:shadow-yellow-300/50 group">
                <img src="/Resimler/logo.jpg" alt="Yağmur bilgisayar" class="w-16 h-16 cursor-pointer group-hover:brightness-110" />
            </a>
            <a href="https://www.akinsoft.com.tr/" class="bg-white p-1 rounded self-center hover:scale-105 transition-all duration-300 hover:shadow-lg hover:shadow-yellow-300/50 group">
                <img src="/Resimler/bylogo.jpg" alt="Akınsoft Adana Logo" class="w-16 h-16 cursor-pointer group-hover:brightness-100" />
            </a>
            </div>
            
            
                
           
                
           <div class="flex items-center space-x-4"> 
       
        <nav class="space-x-1 hidden md:flex items-stretch">
            <a href="https://abakus.akinsoft.net/" class="menu-link px-4 flex items-center hover:text-yellow-300 transition-all duration-300 h-full">
                <i class="fas fa-box mr-2"></i>Abaküs
                <span class="bottom-border"></span>
            </a>
            <a href="https://www.akinsoft.com.tr/kampanyalar/guncel-kampanyalar/" class="menu-link px-4 flex items-center hover:text-yellow-300 transition-all duration-300 h-full">
                <i class="fas fa-box mr-2"></i>Kampanyalar
                <span class="bottom-border"></span>
            </a>
            <a href="/hakkimizde.php" class="menu-link px-4 flex items-center hover:text-yellow-300 transition-all duration-300 h-full">
                <i class="fas fa-info-circle mr-2"></i>Hakkımızda
                <span class="bottom-border"></span>
            </a>
            <a href="/hizmetler.php" class="menu-link px-4 flex items-center hover:text-yellow-300 transition-all duration-300 h-full">
                <i class="fas fa-cogs mr-2"></i>Hizmetler
                <span class="bottom-border"></span>
            </a>
            <a href="/iletisim.php" class="menu-link px-4 flex items-center hover:text-yellow-300 transition-all duration-300 h-full">
                <i class="fas fa-envelope mr-2"></i>İletişim
                <span class="bottom-border"></span>
            </a>
            <a href="/beyaz.php" class="menu-link px-4 flex items-center hover:text-yellow-300 transition-all duration-300 h-full">
                <i class="fas fa-envelope mr-2"></i>Beyaz
                <span class="bottom-border"></span>
            </a>
            
          <div id="dynamic-pages-menu-placeholder"></div>


        </nav>
        <button id="mobileMenuBtn" class="mobile-menu-button text-white text-2xl btn-raised rounded-lg p-2 md:hidden">
                <i class="fas fa-bars"></i>
            </button>
        </div>
       <div class="mobile-menu-content md:hidden">
            <a href="https://abakus.akinsoft.net/" class="mobile-menu-link"><i class="fas fa-box mr-2"></i>Akınsoft Abaküs</a>
            <a href="https://www.akinsoft.com.tr/kampanyalar/guncel-kampanyalar/" class="mobile-menu-link"><i class="fas fa-box mr-2"></i>Kampanyalar</a>
            <a href="/hakkimizde.php" class="mobile-menu-link"><i class="fas fa-info-circle mr-2"></i>Hakkımızda</a>
            <a href="/hizmetler.php" class="mobile-menu-link"><i class="fas fa-cogs mr-2"></i>Hizmetler</a>
            <a href="/iletisim.php" class="mobile-menu-link"><i class="fas fa-envelope mr-2"></i>İletişim</a>
        </div>
    </header>

    <section class="py-2 px-1 relative overflow-hidden" data-aos="fade-up">
        
                    </select>
                </div>
            </div>
            <div class="flex-1 flex flex-col items-center mt-1 md:mt-0 text-center" style="transform: translateX(0px);">
                <h1 class="text-5xl font-bold mb-1 animate-bounce">Yağmur Bilgisayar</h1>
                <p class="text-xl mb-1">Akınsoft Adana Bayi | Yazılım - Bilgisayar Teknik Destek | E-Ticaret | E-İmza</p>
                <p class="text-xl mb-1">İdealimiz En Kaliteli En İyi Hizmeti En Güzel Şekilde Sunmaktır </p>
            </div>
        </div>
      
    </section>

</section>

   
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-12">

            <div id="category-column" class="hidden md:block md:col-span-1" data-aos="fade-right">
                <h2 class="text-xl font-bold text-white mb-4">Kategoriler</h2>
                <div id="categoryMenu" class="flex flex-col space-y-3 relative">
                    </div>
            </div>
            
            <div class="md:col-span-4" data-aos="fade-up">
            
                <div id="firsat-banner-container" class="mb-8">
                    </div>

                <div id="vitrin-banner-container">
                    </div>
                    <br><br><br>

                <div id="best-selling-container" class="mt-12">
                    <h2 class="text-3xl font-bold text-white mb-6 text-center">En Çok Satanlar</h2>
                    <div id="best-selling-products" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        </div>
                </div>

                <div id="showcase-container" class="mt-12">
                    <h2 class="text-3xl font-bold text-white mb-6 text-center">Vitrin Ürünleri</h2>
                    <div id="showcase-products" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        </div>
                </div>
                
            </div> </div> <div class="text-center mt-12" data-aos="fade-up">
             </div>

    </div> </section> ```
      
    
    
    
   
    <button id="scrollTopBtn" onclick="scrollToTop()" class="btn-raised">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4">
      <div class="mb-8">
        <h3 class="text-lg font-bold mb-6 text-yellow-400 text-center">TÜM ÇÖZÜMLER</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        

        <div class="bg-gray-800 p-4 rounded-lg">
            <h4 class="text-yellow-400 font-semibold mb-3">İNSAN KAYNAKLARI</h4>
          <ul class="space-y-2 text-sm">
            
        
        
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Kişisel Verilerin Korunması Kanunu</a></li>

            
          </ul>
        </div>

        <div class="bg-gray-800 p-4 rounded-lg">
            <h4 class="text-yellow-400 font-semibold mb-3">DESTEK ASİSTANI</h4>
          <ul class="space-y-2 text-sm">
           
        
            <li><a href="/cerez.html" class="hover:text-yellow-300 transition-colors">Çerez Politikası</a></li>
          </ul>
        </div>
         <div class="bg-gray-800 p-4 rounded-lg">
            <h4 class="text-yellow-400 font-semibold mb-3">ASİSTANI</h4>
          <ul class="space-y-2 text-sm">
           
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Bilgi Bankası</a></li>
            
           
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Video Yardım</a></li>
            
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Gizlilik Politikası</a></li>
            <li><a href="/cerez.html" class="hover:text-yellow-300 transition-colors">Çerez Politikası</a></li>
          </ul>
        </div>

        <div class="bg-gray-800 p-4 rounded-lg">
            <h4 class="text-yellow-400 font-semibold mb-3">İLETİŞİM</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Ücretsiz Deneyin</a></li>
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Robot Kirala/Satın Al</a></li>
           
            
            
          </ul>
        </div>

        </div>
      </div>
      <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                <p>© Copyright © 2004 - 2025 Yağmur Bilgisayar | Akınsoft Adana Bayi | Her Hakkı Saklıdır.</p>
            </div>
        </div>
    </footer>

   

    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    
    <script src="//unpkg.com/alpinejs" defer></script>
  
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="main.js"></script>
    <div id="mobileMenu" 
     class="fixed inset-0 bg-white z-50 transform -translate-x-full transition-transform duration-300 md:hidden">
    
    <div class="flex items-center justify-between p-2 border-b">
            
            <h2 class="text-lg font-bold text-gray-800 flex-shrink-0 pr-2">Kategoriler</h2>
            
            <nav class="flex-1 flex items-center justify-between text-blue-800 px-1">
                
                <a href="https://abakus.akinsoft.net/" title="Abaküs" class="flex flex-col items-center text-center p-1 rounded hover:bg-gray-100">
                    <i class="fas fa-box text-base"></i>
                    <span class="font-medium" style="font-size: 10px; line-height: 1.2;">Abaküs</span>
                </a>
                
                <a href="https://www.akinsoft.com.tr/kampanyalar/guncel-kampanyalar/" title="Kampanyalar" class="flex flex-col items-center text-center p-1 rounded hover:bg-gray-100">
                    <i class="fas fa-tags text-base"></i> <span class="font-medium" style="font-size: 10px; line-height: 1.2;">Kampanya</span> 
                </a>
                
                <a href="/hakkimizde.php" title="Hakkımızda" class="flex flex-col items-center text-center p-1 rounded hover:bg-gray-100">
                    <i class="fas fa-info-circle text-base"></i>
                    <span class="font-medium" style="font-size: 10px; line-height: 1.2;">Hakkımızda</span>
                </a>
                
                <a href="/hizmetler.php" title="Hizmetler" class="flex flex-col items-center text-center p-1 rounded hover:bg-gray-100">
                    <i class="fas fa-cogs text-base"></i>
                    <span class="font-medium" style="font-size: 10px; line-height: 1.2;">Hizmetler</span>
                </a>
                
                <a href="/iletisim.php" title="İletişim" class="flex flex-col items-center text-center p-1 rounded hover:bg-gray-100">
                    <i class="fas fa-envelope text-base"></i>
                    <span class="font-medium" style="font-size: 10px; line-height: 1.2;">İletişim</span>
                </a>
                
            </nav>

            <button id="closeMobileMenuBtn" class="text-2xl text-gray-700 flex-shrink-0 pl-2">&times;</button>
        </div>

    <div id="mobileCategoryList" class="p-4 overflow-y-auto">
        </div>
</div>
<div id="mobileMenuOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

</body>
</html>