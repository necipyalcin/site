<?php
// Gelen 'slug' parametresini al
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    // Slug yoksa anasayfaya yönlendir
    header('Location: index.php');
    exit;
}

// 1. public_api.php'den tüm sayfaları çek
// Not: Siteniz canlıdaysa 'http://localhost/site/' kısmını kendi alan adınızla değiştirin.
$api_url = 'http://localhost/site/public_api.php?resource=pages';
$pages_json = @file_get_contents($api_url);
$pages = json_decode($pages_json, true);

// 2. İstenen 'slug' ile eşleşen sayfayı bul
$sayfa_data = null;
if (is_array($pages)) {
    foreach ($pages as $page) {
        if (isset($page['slug']) && $page['slug'] === $slug) {
            $sayfa_data = $page;
            break;
        }
    }
}

// 3. Sayfa bulunamadıysa 404 hatası ver
if ($sayfa_data === null) {
    http_response_code(404);
    echo "<h1>404 - Sayfa Bulunamadı</h1>";
    echo "<p>Aradığınız sayfa mevcut değil.</p>";
    echo '<a href="index.php">Anasayfaya Dön</a>';
    exit;
}

// Verileri değişkenlere ata
$sayfa_basligi = $sayfa_data['title'] ?? 'Sayfa';
$sayfa_icerigi = $sayfa_data['content'] ?? '<p>İçerik yakında eklenecek.</p>';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($sayfa_basligi); ?> - Sitenizin Adı</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="css/main.css"> <style>
        /* TinyMCE'den gelen içeriğin düzgün görünmesi için temel stiller */
        .page-content {
            padding: 2rem;
            max-width: 900px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .page-content h1, .page-content h2, .page-content h3 {
            font-weight: bold;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }
        .page-content h1 { font-size: 2em; }
        .page-content h2 { font-size: 1.5em; }
        .page-content p { margin-bottom: 1em; line-height: 1.7; }
        .page-content ul, .page-content ol { margin-left: 1.5rem; margin-bottom: 1em; }
        .page-content a { color: #3b82f6; text-decoration: underline; }
        .page-content img { max-width: 100%; height: auto; border-radius: 8px; }
        .page-content table { width: 100%; border-collapse: collapse; margin-bottom: 1em; }
        .page-content th, .page-content td { border: 1px solid #ddd; padding: 8px; }
        .page-content th { background-color: #f4f4f4; }
        .page-content blockquote { border-left: 4px solid #ccc; padding-left: 1rem; margin-left: 0; font-style: italic; }
        .page-content iframe { max-width: 100%; aspect-ratio: 16 / 9; } /* Google Haritalar için */
    </style>
</head>
<body class="bg-gray-100">

    <main>
        <div class="page-content">
            <h1><?php echo htmlspecialchars($sayfa_basligi); ?></h1>
            
            <div>
                <?php echo $sayfa_icerigi; // TinyMCE içeriği (HTML içerir, escape edilmemeli) ?>
            </div>
        </div>
    </main>

    <script src="main.js"></script>
</body>
</html>