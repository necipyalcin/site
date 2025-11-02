<?php
// DOSYA ADI: test_xml_output.php
// Bu script sadece Güneş Bilgisayar'dan gelen XML verisini ekrana basar.

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. xml_import.php DOSYASINDA KULLANDIĞINIZ GERÇEK XML LİNKİNİ BURAYA DA YAPIŞTIRIN
$xml_url = 'https://api.gunes.net/api/Urunler/XmlUrunListesi/14335'; // !!! BU SATIRI KENDİ LİNKİNİZLE DEĞİŞTİRİN !!!

echo "<pre>"; // Düz metin olarak göstermek için <pre> etiketi
echo "XML Linki: " . htmlspecialchars($xml_url) . "\n\n";
echo "Veri çekiliyor...\n\n";

// Zaman aşımını uzat
set_time_limit(300); // 5 dakika

// 2. XML VERİSİNİ ÇEKME (cURL ile)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $xml_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 120); // 2 dakika bekleme süresi
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0'); // Basit bir user agent
$xml_data = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "HTTP Durum Kodu: " . $http_code . "\n";
if ($curl_error) {
    echo "cURL Hatası: " . htmlspecialchars($curl_error) . "\n";
}
echo "-------------------- GELEN VERİ BAŞLANGICI --------------------\n\n";

if ($xml_data === false) {
    echo "HATA: XML verisi çekilemedi. Sunucuya bağlanılamadı veya zaman aşımı.";
} elseif ($http_code !== 200) {
    echo "HATA: Tedarikçi sunucusu hata verdi (HTTP $http_code).\n\n";
    echo "Alınan Ham Veri (ilk 500 karakter):\n";
    echo htmlspecialchars(substr($xml_data, 0, 500)); // Hata mesajını göster
} else {
    // 3. GELEN VERİYİ EKRANA BASMA (HTML olarak yorumlamadan)
    echo htmlspecialchars($xml_data); // Tüm XML içeriğini güvenli bir şekilde ekrana basar
}

echo "\n\n-------------------- GELEN VERİ SONU --------------------";
echo "</pre>";

?>