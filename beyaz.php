<!DOCTYPE html>
<html lang="tr">
<head>
    <meta name="google-site-verification" content="fpvJbOeS7J2lqeUygmRqBhIP7XaPoZGkrl5qYGrHvbU" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Yağmur Bilgisayar | Akınsoft Adana Bayi</title>
    <link rel="icon" type="image/jpeg" href="/Resimler/logo.jpg">
    <meta name="description" content="Akınsoft Adana Bayisi Yağmur Bilgisayar - Profesyonel E-ticaret sitesi. Yazılım, Donanım, Bilgisayar Bileşenleri, Barkod Ürünleri, Teknik Servis ve daha fazlası. Güvenli ödeme, hızlı kargo ve 7/24 destek." />
    <meta name="keywords" content="rulo, termel, kağıt, yazar kasa, Adana, Akinsoft, Akınsoft, adanaakınsoft, akınsoftadana, adanaakinsoft, akinsoftadana, E-ticaret, online alışveriş, bilgisayar parçaları, yazılım, donanım, barkod ürünleri, teknik servis, Akınsoft, Wolvox, ERP, Adana, Yağmur Bilgisayar, güvenli ödeme, hızlı kargo" />
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

        /* Wrapper'ı butonlar arası boşluk için kullanıyoruz */
        .category-btn-wrapper {
            position: relative;
            margin-bottom: 0.35rem; /* Ana butonların arası */
        }

        /* ANA KATEGORİ BUTONU (Kabartı + Düşük Yükseklik + Nokta Nokta) */
        .category-btn {
            background-color: white;
            color: #1e3a8a !important; 
            width: 100%;
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
       

        .lottie-container { position: absolute; right: 5%; bottom: 0; width: 300px; height: 300px; z-index: 1; }
        @media (max-width: 768px) { .lottie-container { display: none; } }
        .slider-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .slider { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 1s ease-in-out; background-size: cover; background-position: center; filter: blur(3px); }
        .slider.active { opacity: 1; }
        .slider-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom right, rgba(17, 24, 89, 0.85), rgba(23, 55, 165, 0.85)); backdrop-filter: blur(1px); }
        .menu-link { position: relative; }
        .menu-link::before, .menu-link::after, .menu-link .bottom-border { content: ''; position: absolute; background: transparent; box-shadow: 0 0 8px rgba(239, 68, 68, 0); transition: all 0.3s ease; }
        .menu-link::before { left: 0; top: 0; bottom: 0; width: 2px; }
        .menu-link::after { right: 0; top: 0; bottom: 0; width: 2px; }
        .menu-link .bottom-border { left: 0; right: 0; bottom: 0; height: 2px; }
        .menu-link:hover::before, .menu-link:hover::after, .menu-link:hover .bottom-border { background: rgb(239, 68, 68); box-shadow: 0 0 8px rgba(239, 68, 68, 0.6); }
        #wishlistBtnContainer { position: relative; }
        #wishlistCount { position: absolute; top: -8px; right: -8px; background-color: #fbbf24; color: #1e3a8a; border-radius: 50%; width: 22px; height: 22px; font-size: 0.75rem; font-weight: bold; display: flex; align-items: center; justify-content: center; border: 2px solid white; }
        #wishlistBtnContainer button .fa-heart { font-size: 2rem; transition: color 0.3s ease, transform 0.3s ease; }
        #wishlistBtnContainer button.active .fa-heart { color: #ef4444; transform: scale(1.1); }
        .banner-slider { position: relative; overflow: hidden; width: 100vw; margin-left: calc(-50vw + 50%); height: 70vh; min-height: 450px; }
        .banner-slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 1.2s ease-in-out; background-size: cover; background-position: center; }
        .banner-slide.active { opacity: 1; }
        .banner-content { position: absolute; top: 50%; left: 10%; transform: translateY(-50%); max-width: 600px; z-index: 10; }
        .banner-title { font-size: 3.5rem; font-weight: 800; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); line-height: 1.1; }
        .banner-subtitle { font-size: 1.5rem; margin-bottom: 2rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); opacity: 0.9; }
        .banner-button { display: inline-block; background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #1e3a8a; font-weight: 700; padding: 1rem 2rem; border-radius: 50px; text-decoration: none; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3); }
        .banner-button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4); }
        .banner-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(30, 58, 138, 0.7), rgba(59, 130, 246, 0.3)); }
        .banner-dot.active { opacity: 1 !important; background-color: #fbbf24 !important; transform: scale(1.2); }
        .banner-slider button { backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); width: 50px; height: 50px; font-size: 1.2rem; }
        .banner-slider button:hover { background-color: rgba(255, 255, 255, 0.3) !important; transform: scale(1.1); }
        @media (max-width: 768px) { .banner-slider { height: 70vh; min-height: 500px; } .banner-content { left: 5%; right: 5%; text-align: center; } .banner-title { font-size: 2.5rem; } .banner-subtitle { font-size: 1.2rem; } }
        .campaign-slider { scrollbar-width: none; -ms-overflow-style: none; }
        .campaign-slider::-webkit-scrollbar { display: none; }
        .campaign-item:hover { transform: translateY(-8px); }
        .campaign-dot.active { background-color: #2563eb !important; transform: scale(1.3); }
        #authModal.flex.items-center { align-items: flex-start; padding-top: 5vh; }
        #registerTabContent form { max-height: 65vh; overflow-y: auto; padding-right: 1rem; }
        #authModalContent { background-color: white !important; }
        #authModal #registerTabContent, #authModal #registerTabContent label, #authModal #registerTabContent a, #authModal #loginTabContent, #authModal #loginTabContent label, #authModal h3 { color: #1e40af; }
        #authModal input, #authModal textarea { color: #1e40af !important; border-color: #93c5fd !important; }
        #authModal input::placeholder, #authModal textarea::placeholder { color: #1e40af !important; opacity: 0.7; }
        #uyelikSozlesmesiModal > div, #aydinlatmaMetniModal > div { display: flex; flex-direction: column; max-height: 85vh; }
        #uyelikSozlesmesiModal .overflow-y-auto, #aydinlatmaMetniModal .overflow-y-auto { flex-grow: 1; }
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
    
    <header class="px-4 flex justify-between items-stretch bg-blue-900 relative h-24">
        <div class="flex items-center space-x-4">
            <a href="/" class="bg-white p-1 rounded self-center hover:scale-105 transition-all duration-300 hover:shadow-lg hover:shadow-yellow-300/50 group">
                <img src="/Resimler/logo.jpg" alt="Yağmur bilgisayar" class="w-16 h-16 cursor-pointer group-hover:brightness-110" />
            </a>
            <a href="https://www.akinsoft.com.tr/" class="bg-white p-1 rounded self-center hover:scale-105 transition-all duration-300 hover:shadow-lg hover:shadow-yellow-300/50 group">
                <img src="/Resimler/bylogo.jpg" alt="Akınsoft Adana Logo" class="w-16 h-16 cursor-pointer group-hover:brightness-100" />
            </a>
            <div class="flex space-x-2" id="authButtons">
                <button id="authModalBtn" onclick="openAuthModal('login')" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-all transform hover:scale-105 shadow-md">
                    <i class="fas fa-sign-in-alt mr-1"></i>Üye Girişi
                </button>
            </div>
            <div class="items-center space-x-4 hidden" id="userButtons">
                <div class="user-dropdown">
                    <button id="profileBtn" class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition-all">
                        <i class="fas fa-user mr-1"></i><span id="userName">Hoşgeldin</span>
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div class="user-dropdown-content" id="userDropdown">
                        <a href="#" onclick="showCustomerReport()"><i class="fas fa-file-invoice-dollar"></i>Hesap Ekstresi</a>
                        <a href="#" onclick="editUserInfo()"><i class="fas fa-user-edit"></i>Bilgileri Güncelle</a>
                        <a href="#" onclick="showOrderHistory()"><i class="fas fa-receipt"></i>Sipariş Geçmişi</a>
                        <a href="#" onclick="handleLogout()"><i class="fas fa-sign-out-alt"></i>Çıkış Yap</a>
                    </div>
                </div>
                <div id="wishlistBtnContainer">
                    <button onclick="window.toggleFavoriteFilter()" title="Favorileri Göster/Tümünü Göster" class="text-gray-400 hover:text-red-400">
                        <i class="fas fa-heart"></i>
                    </button>
                    <span id="wishlistCount" class="hidden">0</span>
                </div>
            </div>
        </div>
        <nav class="space-x-1 hidden md:flex items-stretch">
          <a href="/index.php" class="menu-link px-4 flex items-center hover:text-yellow-300 transition-all duration-300 h-full">
                <i class="fas fa-box mr-2"></i>Anasayfa
                <span class="bottom-border"></span>
            </a>
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
        </nav>
        <button id="mobileMenuBtn" class="mobile-menu-button text-white text-2xl btn-raised rounded-lg p-2 md:hidden self-center">
            <i class="fas fa-bars"></i>
        </button>
       <div class="mobile-menu-content md:hidden">
            <a href="https://abakus.akinsoft.net/" class="mobile-menu-link"><i class="fas fa-box mr-2"></i>Akınsoft Abaküs</a>
            <a href="https://www.akinsoft.com.tr/kampanyalar/guncel-kampanyalar/" class="mobile-menu-link"><i class="fas fa-box mr-2"></i>Kampanyalar</a>
            <a href="/hakkimizde.php" class="mobile-menu-link"><i class="fas fa-info-circle mr-2"></i>Hakkımızda</a>
            <a href="/hizmetler.php" class="mobile-menu-link"><i class="fas fa-cogs mr-2"></i>Hizmetler</a>
            <a href="/iletisim.php" class="mobile-menu-link"><i class="fas fa-envelope mr-2"></i>İletişim</a>
        </div>
    </header>
<section class="py-2 px-1 relative overflow-hidden" data-aos="fade-up">
    <div class="container mx-auto flex flex-col md:flex-row items-start md:items-center gap-6">
        
        <div class="filter-panel flex items-center gap-2">
            
            <div class="w-40 relative">
                <input type="text" id="productSearch" placeholder="Ürün ara..." class="w-full h-8 px-3 pl-8 rounded-full border border-blue-300 focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none text-gray-700 text-sm">
                <i class="fas fa-search absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
            </div>

            <div class="w-40">
                <select id="productSort" class="w-full h-8 px-3 rounded-full border border-blue-300 focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none text-gray-700 text-sm">
                    <option value="name">Fiyata Göre</option>
                    <option value="price-low">Fiyat (Düşük-Yüksek)</option>
                    <option value="price-high">Fiyat (Yüksek-Düşük)</option>
                    <option value="newest">En Yeni</option>
                    <option value="popular">En Popüler</option>
                    <option value="rating">En Yüksek Puan</option>
                </select>
            </div>

        </div>
       

   
            <div class="flex-1 flex flex-col items-center mt-1 md:mt-3 text-center text-blue-900 bg-white-900" style="transform: translateX(0px);">
 <h1 class="text-5xl font-bold mb-1 animate-bounce">Yağmur Bilgisayar</h1>
 <p class="text-xl mb-1">Akınsoft Adana Bayi | Yazılım - Bilgisayar Teknik Destek | E-Ticaret | E-İmza</p>
 <p class="text-xl mb-1">İdealimiz En Kaliteli En İyi Hizmeti En Güzel Şekilde Sunmaktır </p>
 </div>
        </div>
      
    </section>
 </div>
</section>

   <section class="py-0 px-1 bg-gradient-to-r from-white-900 to-white-800" id="urunler">
        <div class="max-w-7xl mx-auto py-8">
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-12">

                <div id="category-column" class="hidden md:block md:col-span-1" data-aos="fade-right">
                    <h2 class="text-xl font-bold text-white mb-4">Kategoriler</h2>
                    <div id="categoryMenu" class="flex flex-col space-y-3 relative">
                        </div>
                </div>

                <div class="md:col-span-4">
                    <div id="productList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" data-aos="fade-up" style="opacity: 1; visibility: visible;">
                       
                    </div>
                </div>
            </div>
            <div class="text-center mt-12" data-aos="fade-up">
                
            </div>
        </div>
    </section>
    <br>
    <br>
    <br>
    <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-800">Bilgileri Düzenle</h3>
                        <button onclick="closeModal('editUserModal')" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <form id="editUserForm" class="p-6 modal-scroll-content">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
                            <input type="text" name="editName" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">E-posta *</label>
                            <input type="email" name="editEmail" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefon *</label>
                            <input type="tel" name="editPhone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teslimat Adresi *</label>
                            <textarea name="editAddress" required rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="editCity" class="block text-sm font-medium text-gray-700 mb-2">Teslimat İli *</label>
                                <input type="text" name="editCity" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="editDistrict" class="block text-sm font-medium text-gray-700 mb-2">Teslimat İlçesi *</label>
                                <input type="text" name="editDistrict" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                            </div>
                        </div>
                        <div class="border-t pt-4 mt-4">
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Fatura Adresi</h4>
                            <p class="text-xs text-gray-500 mb-2">Fatura adresiniz teslimat adresinden farklıysa doldurunuz.</p>
                            <div>
                                <label for="editBillingAddress" class="block text-sm font-medium text-gray-700 mb-2">Fatura Adresi</label>
                                <textarea name="editBillingAddress" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label for="editBillingCity" class="block text-sm font-medium text-gray-700 mb-2">Fatura İli</label>
                                    <input type="text" name="editBillingCity" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                                </div>
                                <div>
                                    <label for="editBillingDistrict" class="block text-sm font-medium text-gray-700 mb-2">Fatura İlçesi</label>
                                    <input type="text" name="editBillingDistrict" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                                </div>
                            </div>
                        </div>
                        <div class="border-t pt-4 mt-4">
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Şifre Değiştir</h4>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre (değiştirmek istemiyorsanız boş bırakın)</label>
                                <input type="password" name="editPassword" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Şifre Tekrar</label>
                                <input type="password" name="editPasswordConfirm" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6">
                        <button type="button" onclick="closeModal('editUserModal')" class="flex-1 bg-gray-500 text-white py-3 rounded-lg font-semibold hover:bg-gray-600 transition-all">
                            <i class="fas fa-times mr-2"></i>İptal
                        </button>
                        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-all">
                            <i class="fas fa-save mr-2"></i>Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="userProfileModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-800">Kullanıcı Profili</h3>
                        <button id="closeProfileBtn" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6 modal-scroll-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Kullanıcı Bilgileri</h4>
                            <div id="userInfo" class="space-y-2">
                            </div>
                            <button id="logoutBtn" class="mt-4 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-all">
                                <i class="fas fa-sign-out-alt mr-2"></i>Çıkış Yap
                            </button>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Geçmiş Siparişler</h4>
                            <div id="orderHistory" class="space-y-4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="customerManagementModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-800">Müşteri Yönetimi</h3>
                        <button id="closeCustomerManagementBtn" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <input type="text" id="customerSearch" placeholder="Müşteri ara..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                    </div>
                    <div id="customerList" class="space-y-4">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="orderManagementModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-800">Sipariş Yönetimi</h3>
                        <button id="closeOrderManagementBtn" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <input type="text" id="orderSearch" placeholder="Sipariş ara..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                    </div>
                    <div id="orderList" class="space-y-4">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="changeAddressModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-800">Teslim Adresi Değiştir</h3>
                        <button id="closeAddressBtn" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <form id="changeAddressForm" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
                            <input type="text" name="addressName" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefon *</label>
                            <input type="tel" name="addressPhone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adres *</label>
                            <textarea name="addressText" required rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şehir *</label>
                            <input type="text" name="addressCity" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Posta Kodu</label>
                            <input type="text" name="addressPostalCode" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6">
                        <button type="button" id="cancelAddressBtn" class="flex-1 bg-gray-500 text-white py-3 rounded-lg font-semibold hover:bg-gray-600 transition-all">
                            <i class="fas fa-times mr-2"></i>İptal
                        </button>
                        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-all">
                            <i class="fas fa-save mr-2"></i>Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="cartModal" class="fixed inset-0 bg-blue-500 bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[85vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-blue-800">Alışveriş Sepeti</h3>
                        <button id="closeCartBtn" class="text-blue-500 hover:text-blue-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div id="cartItems" class="p-6">
                </div>
                <div class="p-6 border-t border-blue-500 bg-blue-50">
                    <div class="coupon-section mb-4">
                        <h4 class="font-semibold text-blue-800 mb-2">Kupon Kodu</h4>
                        <div class="coupon-input">
                            <input type="text" id="couponCode" placeholder="Kupon kodunuzu girin" class="flex-1 px-3 py-2 border border-blue-300 rounded-lg focus:outline-none text-gray-800">
                            <button onclick="applyCoupon()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all">
                                <i class="fas fa-tag mr-1"></i>Uygula
                            </button>
                        </div>
                        <div id="couponMessage" class="mt-2 text-sm"></div>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Ara Toplam:</span>
                        <span id="cartSubtotal" class="text-gray-800">0,00 ₺</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-900">Kargo:</span>
                        <span id="shippingCost" class="text-gray-800">0,00 ₺</span>
                    </div>
                    <div id="discountRow" class="flex justify-between items-center mb-2 hidden">
                        <span class="text-gray-600">İndirim:</span>
                        <span id="discountAmount" class="text-green-600">-0,00 ₺</span>
                    </div>
                    <div class="flex justify-between items-center mb-4 pt-2 border-t border-gray-300">
                        <span class="text-lg font-semibold text-gray-800">Toplam:</span>
                        <span id="cartTotal" class="text-2xl font-bold text-red-600">0,00 ₺</span>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Ödeme Yöntemi</h4>
                        <div class="payment-methods">
                            <div class="payment-method selected" onclick="selectPayment('credit-card')">
                                <i class="fas fa-credit-card"></i>
                                <span class="font-semibold text-yellow-300 mb-2">Kredi Kartı</span>
                            </div>
                            <div class="payment-method" onclick="selectPayment('bank-transfer')">
                                <i class="fas fa-university"></i>
                                <span class="font-semibold text-yellow-300 mb-2">Havale/EFT</span>
                            </div>
                            <div class="payment-method" onclick="selectPayment('cash-on-delivery')">
                                <i class="fas fa-money-bill-wave"></i>
                                <span class="font-semibold text-yellow-300 mb-2">Kapıda Ödeme</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <button id="clearCartBtn" class="flex-1 bg-gray-500 text-white py-3 rounded-lg font-semibold hover:bg-gray-600 transition-all">
                            <i class="fas fa-trash mr-2"></i>Sepeti Temizle
                        </button>
                        <button id="goToCartBtn" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-all">
                            <i class="fas fa-external-link-alt mr-2"></i>Sepete Git
                        </button>
                        <button id="checkoutBtn" class="flex-1 bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition-all">
                            <i class="fas fa-credit-card mr-2"></i>Sipariş Ver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="orderModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-2xl font-bold">Sipariş Bilgileri</h3><button id="closeOrderBtn" onclick="closeModal('orderModal')" class="text-gray-500 hover:text-gray-700 text-2xl">×</button>
            </div>
            <form id="orderForm" class="modal-scroll-content p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <h4 class="md:col-span-2 text-lg font-semibold border-b pb-2 mb-2">Teslimat Bilgileri</h4>
                    <div><label class="block text-sm font-medium mb-1">Ad Soyad *</label><input type="text" name="name" required class="w-full p-2 border rounded-md"></div>
                    <div><label class="block text-sm font-medium mb-1">Telefon *</label><input type="tel" name="phone" required class="w-full p-2 border rounded-md"></div>
                    <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">E-posta</label><input type="email" name="email" class="w-full p-2 border rounded-md"></div>
                    <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">Adres *</label><textarea name="address" required rows="2" class="w-full p-2 border rounded-md"></textarea></div>
                    <div><label class="block text-sm font-medium mb-1">İl (Şehir) *</label><input type="text" name="city" required class="w-full p-2 border rounded-md"></div>
                    <div><label class="block text-sm font-medium mb-1">İlçe *</label><input type="text" name="district" required class="w-full p-2 border rounded-md"></div>
                    <div class="md:col-span-2 mt-2"><label class="flex items-center"><input type="checkbox" id="billingAddressCheck" class="mr-2"> Fatura adresim farklı</label></div>
                    <div id="billingAddressSection" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 hidden mt-2 border-t pt-4">
                        <h4 class="md:col-span-2 text-lg font-semibold border-b pb-2 mb-2">Fatura Adresi</h4>
                        <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">Fatura Adresi *</label><textarea name="billingAddress" rows="2" class="w-full p-2 border rounded-md"></textarea></div>
                        <div><label class="block text-sm font-medium mb-1">İl (Şehir) *</label><input type="text" name="billingCity" class="w-full p-2 border rounded-md"></div>
                        <div><label class="block text-sm font-medium mb-1">İlçe *</label><input type="text" name="billingDistrict" class="w-full p-2 border rounded-md"></div>
                    </div>
                    <h4 class="md:col-span-2 text-lg font-semibold border-b pb-2 mb-2 mt-4">Ödeme Yöntemi</h4>
                    <div class="md:col-span-2 flex gap-4">
                        <label class="flex-1 p-3 border rounded-lg flex items-center cursor-pointer hover:border-blue-500"><input type="radio" name="paymentMethod" value="Kredi Kartı" class="mr-2" checked> Kredi Kartı (Yakında)</label>
                        <label class="flex-1 p-3 border rounded-lg flex items-center cursor-pointer hover:border-blue-500"><input type="radio" name="paymentMethod" value="Havale/EFT" class="mr-2"> Havale/EFT</label>
                    </div>
                    <div id="bankInfo" class="md:col-span-2 mt-4 p-4 bg-blue-50 rounded-lg hidden">
                        <h5 class="font-semibold mb-2">Banka Bilgileri</h5>
                        <p><strong>Alıcı:</strong> Müzeyyen Yalçın</p>
                        <p><strong>Banka:</strong> Akbank</p>
                        <p><strong>IBAN:</strong> TR12 0004 6002 6088 8000 1439 67</p>
                        <p class="text-sm mt-2 text-gray-600">Lütfen açıklama kısmına sipariş numaranızı yazınız.</p>
                    </div>
                </div>
                <div class="mt-6 text-center text-sm text-red-600 font-semibold p-3 bg-red-50 rounded-lg">
                    Ödemesi EFT yada havale yapılmayan siparişler dikkate alınmayacaktır.
                </div>
                <div class="mt-6 flex gap-4">
                    <button type="button" id="cancelOrderBtn" onclick="closeModal('orderModal')" class="flex-1 bg-gray-500 text-white py-3 rounded-lg font-semibold hover:bg-gray-600">İptal</button>
                    <button type="submit" class="flex-1 bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700"><i class="fas fa-paper-plane mr-2"></i>Siparişi Gönder</button>
                </div>
            </form>
        </div>
    </div>
    <div id="adminPanelModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-800">Admin Paneli</h3>
                        <button id="closeAdminBtn" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-blue-800 mb-2">Sipariş Yönetimi</h4>
                            <p class="text-sm text-blue-600 mb-3">Toplam Sipariş: <span id="totalOrders">0</span></p>
                            <button onclick="viewAllOrders()" class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                                Siparişleri Görüntüle
                            </button>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-green-800 mb-2">Stok Yönetimi</h4>
                            <p class="text-sm text-green-600 mb-3">Düşük Stok: <span id="lowStockCount">0</span> ürün</p>
                            <button onclick="manageStock()" class="bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                                Stok Yönet
                            </button>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-purple-800 mb-2">Müşteri Yönetimi</h4>
                            <p class="text-sm text-purple-600 mb-3">Toplam Müşteri: <span id="totalCustomers">0</span></p>
                            <button onclick="manageCustomers()" class="bg-purple-600 text-white px-3 py-2 rounded text-sm hover:bg-purple-700">
                                Müşterileri Görüntüle
                            </button>
                        </div>
                    </div>
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-800 mb-4">Satış Raporları</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                <h5 class="text-sm font-medium text-gray-600">Günlük Satış</h5>
                                <p class="text-2xl font-bold text-green-600"><span id="dailySales">0</span> ₺</p>
                            </div>
                            <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                <h5 class="text-sm font-medium text-gray-600">Haftalık Satış</h5>
                                <p class="text-2xl font-bold text-blue-600"><span id="weeklySales">0</span> ₺</p>
                            </div>
                            <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                <h5 class="text-sm font-medium text-gray-600">Aylık Satış</h5>
                                <p class="text-2xl font-bold text-purple-600"><span id="monthlySales">0</span> ₺</p>
                            </div>
                            <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                <h5 class="text-sm font-medium text-gray-600">Toplam Satış</h5>
                                <p class="text-2xl font-bold text-red-600"><span id="totalSales">0</span> ₺</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="productDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center" style="z-index: 999999 !important;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-4xl w-full relative" style="z-index: 1000000 !important;">
                <div class="p-6 border-b border-gray-200 flex-shrink-0">
                    <div class="flex justify-between items-center">
                        <h3 id="detailProductName" class="text-2xl font-bold text-gray-800">Ürün Detayı</h3>
                        <button onclick="closeModal('productDetailModal')" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div id="productDetailContent" class="modal-scroll-content">
                </div>
            </div>
        </div>
    </div>
    <div id="orderHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh]">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-800">Sipariş Geçmişim</h3>
                        <button onclick="closeModal('orderHistoryModal')" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div id="orderHistoryContent" class="modal-scroll-content">
                    <p class="text-center text-gray-500">Siparişleriniz yükleniyor...</p>
                </div>
            </div>
        </div>
    </div>
    <div id="customer-reportModal" class="fixed inset-0 bg-black bg-opacity-60 z-[100000] hidden items-center justify-center p-4">
        <div class="bg-white text-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col">
            <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white">
                <h3 class="text-xl font-bold" id="customer-reportModalTitle">Hesap Ekstresi</h3>
                <button onclick="closeModal('customer-reportModal')" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">×</button>
            </div>
            <div id="customer-report-content" class="modal-scroll-content">
            </div>
            <div class="mt-auto p-4 border-t text-right bg-gray-50">
                <button id="export-pdf-btn-user" class="bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-all">
                    <i class="fas fa-file-pdf mr-2"></i>PDF Olarak İndir
                </button>
            </div>
        </div>
    </div>
       
    
    
    <div id="cart-button">
        <a href="cart.html" title="Sepeti Görüntüle">
            <i class="fas fa-shopping-cart"></i>
            <div class="cart-count" id="cartCountFixed">0</div>
        </a>
    </div>
    <div id="whatsapp-button">
        <a href="https://wa.me/905054552946" target="_blank" title="WhatsApp ile İletişim">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
    
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
            
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Çalışma Ortamlarımız</a></li>
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Departmanlar</a></li>
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Kişisel Verilerin Korunması Kanunu</a></li>

            
          </ul>
        </div>

        <div class="bg-gray-800 p-4 rounded-lg">
            <h4 class="text-yellow-400 font-semibold mb-3">DESTEK ASİSTANI</h4>
          <ul class="space-y-2 text-sm">
           
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Bilgi Bankası</a></li>
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Sıkça Sorulan Sorular</a></li>
           
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Video Yardım</a></li>
            
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Gizlilik Politikası</a></li>
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Çerez Politikası</a></li>
          </ul>
        </div>

        <div class="bg-gray-800 p-4 rounded-lg">
            <h4 class="text-yellow-400 font-semibold mb-3">İLETİŞİM</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Ücretsiz Deneyin</a></li>
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Robot Kirala/Satın Al</a></li>
            <li><a href="#" class="hover:text-yellow-300 transition-colors">İstek ve Öneri</a></li>
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Etkinlik Daveti</a></li>
            <li><a href="#" class="hover:text-yellow-300 transition-colors">Sponsor Talebi</a></li>
          </ul>
        </div>

        </div>
      </div>
      <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                <p>© Copyright © 2004 - 2025 Yağmur Bilgisayar | Akınsoft Adana Bayi | Her Hakkı Saklıdır.</p>
            </div>
        </div>
    </footer>

    <div id="authModal" class="fixed inset-0 bg-black bg-opacity-60 z-[99999] hidden flex items-center justify-center p-4">
        <div id="authModalContent" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all duration-200 scale-95 opacity-0">
            <div class="p-5 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-800">Hoş Geldiniz</h3>
                <button id="closeAuthModalBtn" class="text-gray-400 hover:text-gray-700 text-2xl w-8 h-8 flex items-center justify-center rounded-full transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex border-b border-gray-200">
                <button id="loginTabBtn" class="flex-1 p-4 font-semibold text-center transition-colors text-blue-700 border-b-4 border-blue-700">
                    Giriş Yap
                </button>
                <button id="registerTabBtn" class="flex-1 p-4 font-semibold text-center transition-colors text-gray-500">
                    Kayıt Ol
                </button>
            </div>
            <div class="p-8">
                <div id="loginTabContent">
                    <form id="loginForm" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">E-posta Adresi</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none text-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Şifre</label>
                            <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none text-gray-900">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-all">
                            Giriş Yap
                        </button>
                    </form>
                </div>
                <div id="registerTabContent" class="hidden">
                    <form id="registerForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Ad *</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Soyad *</label>
                                <input type="text" name="surname" required class="w-full px-3 py-2 border rounded-lg">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">E-posta *</label>
                            <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Telefon *</label>
                            <input type="tel" name="phone" required class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Teslimat Adresi *</label>
                            <textarea name="address" required rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Teslimat İli *</label>
                                <input type="text" name="city" required class="w-full px-3 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Teslimat İlçesi *</label>
                                <input type="text" name="district" required class="w-full px-3 py-2 border rounded-lg">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Fatura Adresi (Farklıysa)</label>
                            <textarea name="billingAddress" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Fatura İli</label>
                                <input type="text" name="billingCity" class="w-full px-3 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Fatura İlçesi</label>
                                <input type="text" name="billingDistrict" class="w-full px-3 py-2 border rounded-lg">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4 mt-2">
                            <div>
                                <label class="block text-sm font-medium mb-1">Şifre *</label>
                                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Şifre Tekrar *</label>
                                <input type="password" name="passwordConfirm" required class="w-full px-3 py-2 border rounded-lg">
                            </div>
                        </div>
                        <div class="pt-2 space-y-2">
                            <div class="flex items-start">
                                <input type="checkbox" id="aydinlatmaOnay" name="aydinlatmaOnay" required class="h-4 w-4 text-blue-600 border-gray-300 rounded mt-0.5">
                                <label for="aydinlatmaOnay" class="ml-2 block text-xs">
                                    <a href="#" onclick="openModal('aydinlatmaMetniModal'); return false;" class="underline hover:text-blue-800">Üyelik Aydınlatma Metni</a>'ni okudum, kabul ediyorum.
                                </label>
                            </div>
                            <div class="flex items-start">
                                <input type="checkbox" id="uyelikOnay" name="uyelikOnay" required class="h-4 w-4 text-blue-600 border-gray-300 rounded mt-0.5">
                                <label for="uyelikOnay" class="ml-2 block text-xs">
                                    <a href="#" onclick="openModal('uyelikSozlesmesiModal'); return false;" class="underline hover:text-blue-800">Kullanım Koşulları ve Üyelik Sözleşmesi</a>'ni okudum, kabul ediyorum.
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-all">
                            Hesap Oluştur
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="uyelikSozlesmesiModal" class="fixed inset-0 bg-black bg-opacity-60 z-[100000] hidden items-center justify-center p-4">
        <div class="bg-white text-gray-800 rounded-lg shadow-xl max-w-3xl w-full max-h-[85vh] flex flex-col">
            <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white">
                <h3 class="text-xl font-bold">Kullanım Koşulları ve Üyelik Sözleşmesi</h3>
                <button onclick="closeModal('uyelikSozlesmesiModal')" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">×</button>
            </div>
            <div class="p-6 overflow-y-auto">
                <h4 class="font-bold mb-2">Konu:</h4>
                <p class="mb-4">İşbu Üyelik Sözleşmesinin Konusu, https://akinsoftadana.com.tr/ Adlı Sitede Sunulan Hizmetlerin ve Bu Hizmetlerden Yararlanma Şartları İle Aşağıda Belirtilen Tarafların Hak ve Yükümlülüklerinin Belirlenmesidir.</p>
                <h4 class="font-bold mb-2">Taraflar:</h4>
                <p class="mb-4">https://akinsoftadana.com.tr/ Adlı İnternet Sitesinden Hizmet Almak Amacıyla Üyelik Talebi İle İmzalamak Olduğunuz İşbu Üyelik Sözleşmesi, YAĞMUR BİLGİSAYAR İle Siteye Üye Olan ve Herhangi Bir Şekilde Site İçeriğine Ulaşan Kullanıcının Sitede Sağlanan Hizmetlerden Yararlanabilmesi Amacıyla Düzenlenmiş Olup, İlgili Sitenin Bulunduğu Elektronik Ortamda, Kullanıcı veya Üyeler Tarafından Onaylanması Anında Hüküm İfade Edecektir.</p>
                <h4 class="font-bold mb-2">Kullanım Koşulları:</h4>
                <ul class="list-disc list-inside space-y-2 mb-4">
                    <li>Kullanıcı veya Üyeler https://akinsoftadana.com.tr/ Sitesini Kullanım Konusunda Bilgilendirme Amacı Taşıyan Aşağıda Yazılı Koşulları Okuduğunu ve Bu Koşullara Peşinen Uyacağını Kabul Etmiş Sayılmaktadır.</li>
                </ul>
            </div>
        </div>
    </div>
    <div id="aydinlatmaMetniModal" class="fixed inset-0 bg-black bg-opacity-60 z-[100000] hidden items-center justify-center p-4">
        <div class="bg-white text-gray-800 rounded-lg shadow-xl max-w-3xl w-full max-h-[85vh] flex flex-col">
            <div class="p-4 border-b flex justify-between items-center sticky top-0 bg-white">
                <h3 class="text-xl font-bold">Üyelik Aydınlatma Metni</h3>
                <button onclick="closeModal('aydinlatmaMetniModal')" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">×</button>
            </div>
            <div class="p-6 overflow-y-auto">
                <p class="mb-4">İşbu Üyelik Sözleşmesinin Konusu, https://akinsoftadana.com.tr/ Adlı Sitede Sunulan Hizmetlerin ve Bu Hizmetlerden Yararlanma Şartları İle Aşağıda Belirtilen Tarafların Hak ve Yükümlülüklerinin Belirlenmesidir.</p>
                <h4 class="font-bold mb-2">Hizmetlerin Kapsamı:</h4>
                <p class="mb-4">YAĞMUR BİLGİSAYAR’ ın, https://akinsoftadana.com.tr/ üzerinden sunacağı hizmetler genel itibariyle Tüketici Hukuku mevzuatında tanımlanan elektronik ticaretten ibarettir.</p>
            </div>
        </div>
    </div>
    <div id="wishlistModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh]">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-800">Favorilerim</h3>
                        <button onclick="closeModal('wishlistModal')" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div id="wishlistContent" class="modal-scroll-content">
                </div>
            </div>
        </div>
    </div>
    <div id="discountNotificationModal" class="bg-white rounded-xl shadow-2xl max-w-sm w-full border-l-4 border-red-500">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-tags text-red-500 text-2xl"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-bold text-gray-900">
                        Fırsatı Kaçırma!
                    </p>
                    <p class="mt-1 text-sm text-gray-600">
                        Favorilerinizdeki bazı ürünlerde indirim başladı!
                    </p>
                    <div class="mt-3 flex space-x-4">
                        <button onclick="window.showWishlist(); closeModal('discountNotificationModal');" class="bg-red-600 text-white px-3 py-2 rounded-md text-sm font-semibold hover:bg-red-700">
                            İncele
                        </button>
                        <button onclick="closeModal('discountNotificationModal')" class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                            Kapat
                        </button>
                    </div>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="closeModal('discountNotificationModal')" class="inline-flex text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
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