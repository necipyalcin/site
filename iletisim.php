<!DOCTYPE html>
<html lang="tr">
<head>
   <meta name="google-site-verification" content="fpvJbOeS7J2lqeUygmRqBhIP7XaPoZGkrl5qYGrHvbU" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>İletişim - Yağmur Bilgisayar | Akınsoft Adana Bayi</title>
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
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
   
<?php
// 1. pages.json dosyasından tüm sayfaları çek
// (public_api.php'nin GET /pages desteği olduğunu varsayarak)
$pages_json = file_get_contents('http://localhost/site/public_api.php?resource=pages');
$pages = json_decode($pages_json, true);

// 2. 'iletisim' sayfasını bul
$iletisim_data = null;
if (is_array($pages)) {
    foreach ($pages as $page) {
        if (isset($page['slug']) && $page['slug'] === 'iletisim') {
            $iletisim_data = $page;
            break;
        }
    }
}

// 3. Verileri değişkenlere ata (yoksa varsayılan değer kullan)
$sayfa_basligi = $iletisim_data['title'] ?? 'İletişim';
$sayfa_icerigi = $iletisim_data['content'] ?? '<p>İçerik yakında eklenecek.</p>';
$adres = $iletisim_data['address'] ?? '';
$telefon = $iletisim_data['phone'] ?? '';
$email = $iletisim_data['contactEmail'] ?? '';
$harita_url = $iletisim_data['mapUrl'] ?? '';

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($sayfa_basligi); ?></title>
    </head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($sayfa_basligi); ?></h1>
        
        <div>
            <?php echo $sayfa_icerigi; // HTML olduğu için escape etmiyoruz ?>
        </div>

        <?php if ($adres || $telefon || $email): ?>
        <div class="iletisim-bilgileri">
            <h3>İş Yeri Bilgileri</h3>
            <?php if ($adres): ?><p><strong>Adres:</strong> <?php echo nl2br(htmlspecialchars($adres)); ?></p><?php endif; ?>
            <?php if ($telefon): ?><p><strong>Telefon:</strong> <?fhp echo htmlspecialchars($telefon); ?></p><?php endif; ?>
            <?php if ($email): ?><p><strong>E-posta:</strong> <?php echo htmlspecialchars($email); ?></p><?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($harita_url): ?>
        <div class="google-harita">
            <h3>Harita</h3>
            <iframe 
                src="<?php echo htmlspecialchars($harita_url); ?>" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
        <?php endif; ?>
    </div>

    </body>
</html>
   
    
                <div id="wishlistContent" class="modal-scroll-content">
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
</body>
</html>