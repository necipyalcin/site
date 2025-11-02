<?php
// DOSYA ADI: urunleri_kaydet.php (Açıklama satırı kaldırıldı + Kart küçültüldü)

ini_set('display_errors', 1);
error_reporting(E_ALL);

$json_data = file_get_contents('php://input');
$products = json_decode($json_data, true);

if (!is_array($products)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz veri formatı.']);
    exit;
}

$html_content = '';

foreach ($products as $p) {
    $p_id = htmlspecialchars($p['id']);
    $p_name = htmlspecialchars($p['name']);
    $p_brand = htmlspecialchars($p['brand'] ?? '');
    $p_image = htmlspecialchars($p['image'] ?? './Resimler/pc.jpg');
    $p_features = $p['features'] ?? [];
    $fiyat = number_format($p['price'] ?? 0, 2, ',', '.');
    $eski_fiyat_html = '';
    if (!empty($p['oldPrice']) && $p['oldPrice'] > $p['price']) {
        $eski_fiyat = number_format($p['oldPrice'], 2, ',', '.');
        $eski_fiyat_html = "<span class='text-sm text-gray-400 line-through mr-2'>{$eski_fiyat} ₺</span>";
    }

    $stock_class = 'stock-out';
    $stock_text = 'Tükendi';
    $disabled_attr = 'disabled';
    if (($p['stock'] ?? 0) > 10) {
        $stock_class = 'stock-in';
        $stock_text = 'Stokta Var';
        $disabled_attr = '';
    } elseif (($p['stock'] ?? 0) > 0) {
        $stock_class = 'stock-low';
        $stock_text = "Son {$p['stock']} adet";
        $disabled_attr = '';
    }

    $features_html = ''; // Ana kartta özellik göstermiyoruz.
    
    // HTML kartını oluştur
    $html_content .= "
    <div class='product-card group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden flex flex-col h-full'>
        
        <div class='p-4 border-b border-gray-200'>
            <h3 class='text-sm font-bold text-gray-800 leading-snug h-20 overflow-hidden line-clamp-3'>{$p_name}</h3>
        </div>

        <div class='relative flex-shrink-0'>
            <a href='urun.php?id={$p_id}' class='block'>
                <img src='{$p_image}' alt='{$p_name}' class='w-full h-32 object-contain p-3 transition-transform duration-300 group-hover:scale-105'>
            </a>

            <button onclick='window.toggleWishlist({$p_id})' class='absolute top-3 right-3 wishlist-btn bg-white rounded-full p-2 shadow-md hover:scale-110 transition-transform' title='Favorilere Ekle'>
                <i class='fas fa-heart text-red-500 text-lg'></i>
            </button>

            </div>

        <div class='p-4 flex flex-col flex-grow'>
            <div class='mt-auto'>
                <div class='flex items-center justify-between mb-3'>
                    <div>
                        {$eski_fiyat_html}
                        <span class='text-xl font-bold " . ($eski_fiyat_html ? 'price-pulse-color' : 'text-blue-800') . "'>{$fiyat} ₺</span>
                    </div>
                    <div class='stock-status {$stock_class} text-xs px-2 py-1 rounded-full font-semibold whitespace-nowrap'>
                       {$stock_text}
                    </div>
                </div>
    
                <div class='flex gap-2'>
                    <a href='urun.php?id={$p_id}' class='flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 text-center transition-colors'>Detaylar</a>
                    <button {$disabled_attr} onclick='window.addToCart({$p_id})' class='flex-1 bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 disabled:bg-gray-400 transition-colors'>Sepete Ekle</button>
                </div>
            </div>
        </div>
    </div>";
}

if (file_put_contents('urunler.html', $html_content) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'urunler.html dosyasına yazılamadı. Dosya yazma izinlerini kontrol edin.']);
} else {
    echo json_encode(['success' => true, 'message' => count($products) . ' ürün ana sayfaya başarıyla yazıldı.']);
}
?>