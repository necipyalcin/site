<?php
// xml_file_importer.php
header('Content-Type: application/json');

// (Buraya auth/login kontrolü eklemeniz ÖNEMLİDİR)

// Gerekli fonksiyonları ve dosyaları dahil et
include 'xml_processor_logic.php'; // XML işleme mantığını ayrı bir dosyaya taşıyoruz

$supplierName = $_POST['supplier_name'] ?? 'Manuel';

if (!isset($_FILES['xml_file']) || $_FILES['xml_file']['error'] != UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'XML dosyası yüklenemedi.']);
    exit;
}

$tmp_name = $_FILES['xml_file']['tmp_name'];

// XML'i yüklenen geçici dosyadan yüklemeyi dene
try {
    $xml = @simplexml_load_file($tmp_name);
    if ($xml === false) {
        throw new Exception("Yüklenen XML dosyası bozuk veya okunamıyor.");
    }

    // Ana işleme fonksiyonunu çağır
    $result = processXmlData($xml, $supplierName);
    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>