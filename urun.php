<?php
// URL'den ürün ID'sini al, eğer yoksa veya sayı değilse ana sayfaya yönlendir
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$urun_id = intval($_GET['id']);

// Tüm ürünlerin verisini products.json'dan oku
$products_json = file_get_contents('products.json');
$products = json_decode($products_json, true);

// İstenen ID'ye sahip ürünü bul
$bulunan_urun = null;
foreach ($products as $product) {
    if ($product['id'] == $urun_id) {
        $bulunan_urun = $product;
        break;
    }
}

// Eğer ürün bulunamadıysa, 404 Not Found hatası ver ve işlemi durdur
if ($bulunan_urun === null) {
    http_response_code(404);
    echo "<h1>404 - Ürün Bulunamadı</h1><p>Aradığınız ürün mevcut değil.</p><a href='index.php'>Ana Sayfaya Dön</a>";
    exit;
}

// Sayfada kullanılacak değişkenleri hazırla ve güvenlik için temizle
$name = htmlspecialchars($bulunan_urun['name']);
$image = htmlspecialchars($bulunan_urun['image']);
$description = $bulunan_urun['description'];
$brand = htmlspecialchars($bulunan_urun['brand']);
$model = htmlspecialchars($bulunan_urun['model'] ?? '');
$price = number_format($bulunan_urun['price'], 2, ',', '.');
$oldPrice_html = '';
if (!empty($bulunan_urun['oldPrice']) && $bulunan_urun['oldPrice'] > $bulunan_urun['price']) {
    $oldPrice = number_format($bulunan_urun['oldPrice'], 2, ',', '.');
    $oldPrice_html = "<span class='text-xl text-gray-400 line-through ml-2'>{$oldPrice} ₺</span>";
}
$stock = intval($bulunan_urun['stock']);
$features = $bulunan_urun['features'] ?? [];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $name; ?> | Yağmur Bilgisayar</title>
    <meta name="description" content="<?php echo substr(str_replace('"', '', $description), 0, 160); ?>">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": "<?php echo addslashes($name); ?>",
      "image": "<?php echo $image; ?>",
      "description": "<?php echo addslashes($description); ?>",
      "brand": { "@type": "Brand", "name": "<?php echo addslashes($brand); ?>" },
      "sku": "<?php echo $bulunan_urun['id']; ?>",
      "offers": {
        "@type": "Offer",
        "url": "https://akinsoftadana.com.tr/urun.php?id=<?php echo $bulunan_urun['id']; ?>",
        "priceCurrency": "TRY",
        "price": "<?php echo $bulunan_urun['price']; ?>",
        "itemCondition": "https://schema.org/NewCondition",
        "availability": "<?php echo $stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'; ?>"
      }
    }
    </script>
</head>
<body class="bg-gray-100">
    <header class="bg-blue-900 text-white p-4 text-center">
        <a href="index.php" class="text-2xl font-bold">Yağmur Bilgisayar | Akınsoft Adana Bayi</a> <br>
        <a href="index.php" class="text-2xl font-bold">ANASAYFA</a>
    </header>

    <main class="container mx-auto p-4 md:p-8">
        <nav class="text-sm mb-4 text-gray-600">
            <a href="index.php" class="text-blue-600 hover:underline">Ana Sayfa</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800"><?php echo $name; ?></span>
        </nav>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" class="w-full rounded-lg shadow-md">
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800"><?php echo $name; ?></h1>
                    <p class="text-gray-500 mt-2">Marka: <strong><?php echo $brand; ?></strong> | Model: <strong><?php echo $model; ?></strong></p>
                    
                    <div class="my-4">
                        <span class="text-4xl font-bold text-red-600"><?php echo $price; ?> ₺</span>
                        <?php echo $oldPrice_html; ?>
                    </div>

                    <?php if ($stock > 0): ?>
                        <div class="bg-green-100 text-green-800 p-2 rounded-md inline-flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Stokta Var (<?php echo $stock; ?> Adet)
                        </div>
                    <?php else: ?>
                        <div class="bg-red-100 text-red-800 p-2 rounded-md inline-flex items-center gap-2">
                            <i class="fas fa-times-circle"></i> Stokta Tükendi
                        </div>
                    <?php endif; ?>

                   

                    <?php if (!empty($features)): ?>
                    <div class="mt-6">
                        <h2 class="text-xl font-semibold mb-2 text-gray-700">Özellikler</h2>
                        <ul class="list-disc list-inside text-gray-700 space-y-2 bg-gray-50 p-4 rounded-md">
                            <?php foreach ($features as $feature): ?>
                                <li><?php echo htmlspecialchars($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                   <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button 
                            onclick="addToCartAndGoHome(<?php echo $urun_id; ?>)" 
                            class="w-full text-center bg-red-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-red-700 transition-all text-lg shadow-lg disabled:bg-gray-400 disabled:cursor-not-allowed"
                            <?php echo $stock > 0 ? '' : 'disabled'; ?>>
                            <i class="fas fa-shopping-cart"></i> 
                            <?php echo $stock > 0 ? 'Sepete Ekle ve Devam Et' : 'Stok Tükendi'; ?>
                        </button>
                         <a href="index.php" class="w-full text-center bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-all text-lg shadow-lg">
                            <i class="fas fa-home"></i> 
                            Anasayfaya Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-900 text-white text-center p-4 mt-8">
        <p>© <?php echo date("Y"); ?> Copyright © 2004 - 2025 Yağmur Bilgisayar | Akınsoft Adana Bayi | Her Hakkı Saklıdır.</p>
    </footer>

 <script>
    // Bu fonksiyon sadece bu sayfada çalışacak ve global API'yi kullanacak
    // main.js'deki fonksiyonlara erişimimiz olmadığı için basit bir API çağrısı yapıyoruz.
    async function addToCartAndGoHome(productId) {
        try {
            // Sepete ekleme işlemi için API'ye istek gönder
            const response = await fetch(`public_api.php?resource=cart&action=add`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ productId: productId })
            });

            if (!response.ok) {
                const errorResult = await response.json();
                throw new Error(errorResult.message || 'Sepete eklenemedi.');
            }

            // Başarılı olursa ana sayfaya yönlendir
            window.location.href = 'index.php?open=checkout'; // Sepetim açık şekilde anasayfaya dön
        } catch (error) {
            console.error('Hata:', error);
            alert(error.message); // Kullanıcıya hata mesajı göster
        }
    }
</script>
</body>
</html>