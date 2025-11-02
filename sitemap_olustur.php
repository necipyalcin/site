<?php
// DOSYA ADI: sitemap_olustur.php (OTOMATİK KAYIT YAPAN GÜNCEL SÜRÜM)

// Ekrana bir şey basmayacağımız için header satırını kaldırıyoruz.

$products_json = file_get_contents('products.json');
$products = json_decode($products_json, true);
$baseURL = "https://akinsoftadana.com.tr/";

$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

// Ana sayfayı ekle
$url = $xml->addChild('url');
$url->addChild('loc', $baseURL . 'index.php');
$url->addChild('lastmod', date('c')); // Geçerli tarihi otomatik alır
$url->addChild('priority', '1.0');

// Her bir ürün için URL ekle
if (is_array($products)) {
    foreach ($products as $product) {
        $url = $xml->addChild('url');
        $url->addChild('loc', $baseURL . 'urun.php?id=' . $product['id']);
        $url->addChild('lastmod', date('c', filemtime('products.json'))); 
        $url->addChild('priority', '0.8');
    }
}

// XML'i düzgün bir şekilde formatla
$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());
$formatted_xml = $dom->saveXML();

// Oluşturulan XML içeriğini sitemap.xml dosyasına kaydet
if (file_put_contents('sitemap.xml', $formatted_xml)) {
    echo "sitemap.xml dosyası başarıyla ve hatasız bir şekilde oluşturuldu. Şimdi Google Search Console'dan tekrar gönderebilirsiniz.";
} else {
    echo "HATA: sitemap.xml dosyası oluşturulamadı. Lütfen sunucunuzdaki dosya yazma izinlerini kontrol edin.";
}
?>