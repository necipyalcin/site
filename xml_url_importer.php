<?php
// xml_url_importer.php
header('Content-Type: application/json');

// (Buraya auth/login kontrolü eklemeniz ÖNEMLİDİR)

// Gerekli fonksiyonları ve dosyaları dahil et
include 'xml_processor_logic.php'; // XML işleme mantığını ayrı bir dosyaya taşıyoruz

$xml_url = $_POST['xml_url'] ?? null;

if (empty($xml_url)) {
    echo json_encode(['success' => false, 'message' => 'XML URL\'si gönderilmedi.']);
    exit;
}

// XML'i URL'den yüklemeyi dene
try {
    // allow_url_fopen'in sunucuda açık olması gerekir
    // Veya cURL kullanabilirsiniz
    $xml = @simplexml_load_file($xml_url); 
    if ($xml === false) {
        throw new Exception("XML dosyası yüklenemedi veya bozuk: " . $xml_url);
    }

    // Tedarikçi adını URL'den kabaca çıkarmaya çalışabiliriz (opsiyonel)
    $host = parse_url($xml_url, PHP_URL_HOST);
    $supplierName = str_replace('www.', '', $host);

    // Ana işleme fonksiyonunu çağır
    $result = processXmlData($xml, $supplierName);
    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>