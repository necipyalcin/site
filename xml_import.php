<?php
// DOSYA ADI: xml_import.php (KDV EKLENMİŞ + MANUEL KUR + ALT KATEGORİ + YAPISAL ÖZELLİKLER)

ini_set('display_errors', 1); // Geliştirme sırasında hataları görmek için açık bırakın
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1); // Hataları log dosyasına yaz
// error_log("XML Import Başladı: " . date('Y-m-d H:i:s')); // Loglama Başlangıcı

header('Content-Type: application/json');

// --- AYARLAR ---
$xml_url = 'https://api.gunes.net/api/Urunler/XmlUrunListesi/14335';
$tcmb_url = 'https://www.tcmb.gov.tr/kurlar/today.xml';
define('KAR_ORANI', 1.35); // 1.35 = %35 kar
$settings_file = 'settings.json'; // Admin panel ayarlarının okunacağı dosya
// --- AYARLAR SONU ---

set_time_limit(1200); // Süreyi daha da artıralım, XML büyük olabilir
ini_set('memory_limit', '512M'); // Bellek limitini artıralım

// --- YARDIMCI FONKSİYONLAR ---
function getJsonData($filename) {
    if (!file_exists($filename)) {
        if ($filename === 'settings.json') {
            $defaultSettings = ['manualRate' => 0, 'autoRate' => 0, 'lastAutoUpdate' => ''];
            // Dosya oluşturulamazsa hata ver
            if (file_put_contents($filename, json_encode($defaultSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
                 throw new Exception($filename . ' dosyası oluşturulamadı veya yazılamadı.');
            }
            return $defaultSettings;
        }
        if (file_put_contents($filename, '[]') === false) {
             throw new Exception($filename . ' dosyası oluşturulamadı veya yazılamadı.');
        }
        return [];
    }
    $content = @file_get_contents($filename);
     if ($content === false) {
         throw new Exception($filename . ' dosyası okunamadı.');
     }
    if (empty(trim($content))) {
         if ($filename === 'settings.json') return ['manualRate' => 0, 'autoRate' => 0, 'lastAutoUpdate' => ''];
         return [];
    }
    $data = json_decode($content, true);
     if (json_last_error() !== JSON_ERROR_NONE) {
          throw new Exception($filename . ' dosyasında JSON parse hatası: ' . json_last_error_msg());
     }
    return $data;
}

function saveJsonData($filename, $data) {
    $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
     if ($jsonData === false) {
          throw new Exception('JSON encode hatası: ' . json_last_error_msg());
     }
    if ($filename !== 'settings.json') {
        // products.json için array_values kullanmaya gerek yok, anahtarlı array olarak kalsın
        // $jsonData = json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    if (file_put_contents($filename, $jsonData) === false) {
         throw new Exception($filename . ' dosyasına yazılamadı. Dosya izinlerini kontrol edin.');
    }
}


function getUsdRate($tcmb_url, $settings_file) {
    $settings = getJsonData($settings_file);
    $manualRate = floatval($settings['manualRate'] ?? 0);

    if ($manualRate > 0) {
        return ['rate' => $manualRate, 'type' => 'Manuel'];
    }

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tcmb_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // TCMB için daha kısa timeout
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Gerekliyse
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Gerekliyse
        $xml_data = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($xml_data === false) {
            throw new Exception('TCMB kur verisi çekilemedi (cURL). Hata: ' . $curl_error);
        }
         if ($http_code !== 200) {
            throw new Exception("TCMB sunucusu hata verdi (HTTP $http_code).");
        }
        if (empty(trim($xml_data))) {
            throw new Exception('TCMB\'den boş veri alındı.');
        }

        // XML verisinin geçerli olup olmadığını kontrol edelim
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xml_data);
        if ($xml === false) {
            $errors = libxml_get_errors();
            $error_message = 'TCMB XML verisi ayrıştırılamadı. Hatalar: ';
            foreach ($errors as $error) { $error_message .= trim($error->message) . '; '; }
            libxml_clear_errors();
            throw new Exception($error_message);
        }
        libxml_clear_errors(); // Hataları temizle
        libxml_use_internal_errors(false);

        $usd_kur_obj = $xml->xpath("//Currency[@Kod='USD']");

        if (!empty($usd_kur_obj) && isset($usd_kur_obj[0]->ForexSelling)) {
            $kur_str = (string)$usd_kur_obj[0]->ForexSelling;
             // Virgülü noktaya çevir
             $kur_str_formatted = str_replace(',', '.', $kur_str);
            $kur = (float) $kur_str_formatted;
            if ($kur > 0) {
                $settings['autoRate'] = $kur;
                $settings['lastAutoUpdate'] = date('c');
                saveJsonData($settings_file, $settings);
                return ['rate' => $kur, 'type' => 'TCMB'];
            } else {
                 throw new Exception('TCMB XML içinden USD kuru (ForexSelling) sıfır veya negatif döndü. Okunan değer: ' . $kur_str);
            }
        } else {
            $currencyCodes = [];
            if ($xml instanceof SimpleXMLElement) {
                 foreach($xml->Currency as $currency) {
                      if(isset($currency['Kod'])) { $currencyCodes[] = (string)$currency['Kod']; }
                 }
            }
             throw new Exception('TCMB XML içinden USD kuru (ForexSelling) okunamadı. Bulunan kodlar: ' . implode(', ', $currencyCodes));
        }

    } catch (Exception $e) {
        // error_log("TCMB Hatası: " . $e->getMessage()); // Hataları logla
        $lastAutoRate = floatval($settings['autoRate'] ?? 0);
        if ($lastAutoRate > 0) {
            return ['rate' => $lastAutoRate, 'type' => 'TCMB (Eski - Hata: ' . substr($e->getMessage(), 0, 100) . ')']; // Hata mesajını kısalt
        }
        throw new Exception('Ne manuel ne de TCMB (yeni/eski) kur bulundu. TCMB Hatası: ' . $e->getMessage());
    }
}


function getGunesXml($xml_url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $xml_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600); // Süreyi artırdık
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $xml_data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($xml_data === false) throw new Exception('XML verisi çekilemedi. cURL Hatası: ' . $curl_error);
    if ($http_code !== 200) throw new Exception("Tedarikçi sunucusu hata verdi (HTTP $http_code).");

    $bom = pack('H*','EFBBBF');
    $cleaned_data = preg_replace("/^$bom/", '', $xml_data);
    $trimmed_data = trim($cleaned_data);

    if (empty($trimmed_data)) {
        throw new Exception("XML verisi işlenemedi. Hata: Tedarikçiden boş veri alındı.");
    }
     if (strpos($trimmed_data, '<') !== 0) {
          $preview = substr($trimmed_data, 0, 200);
          throw new Exception("XML verisi işlenemedi. Hata: Alınan veri XML formatında değil. Veri başlangıcı: " . htmlspecialchars($preview));
     }

    libxml_use_internal_errors(true);
    $xml_check = simplexml_load_string($trimmed_data);
    if ($xml_check === false) {
        $errors = libxml_get_errors();
        $error_message = 'Alınan XML verisi yapısal olarak bozuk. Hatalar: ';
        foreach ($errors as $error) { $error_message .= trim($error->message) . '; '; }
        libxml_clear_errors();
        throw new Exception($error_message);
    }
     libxml_clear_errors();
     libxml_use_internal_errors(false);

    return $trimmed_data;
}

// --- ANA İŞLEM BAŞLANGICI ---
try {
    // error_log("Kur bilgisi alınıyor...");
    $kur_bilgisi = getUsdRate($tcmb_url, $settings_file);
    $usd_kur = $kur_bilgisi['rate'];
    $kur_tipi = $kur_bilgisi['type'];
    // error_log("Kur alındı: " . $usd_kur . " (" . $kur_tipi . ")");

    // error_log("XML verisi çekiliyor...");
    $xml_data = getGunesXml($xml_url);
    // error_log("XML verisi çekildi. Boyut: " . strlen($xml_data));

    // error_log("XML parse ediliyor...");
    $xml = new SimpleXMLElement($xml_data);
    // error_log("XML parse edildi.");

    $products = [];
    $id_counter = 1;
    // error_log("Kategoriler okunuyor...");
    $categories = getJsonData('categories.json');
    // error_log("Kategoriler okundu.");

     $categoryMapById = [];
     $categoryMapByName = [];
     if ($categories && is_array($categories)) {
         foreach($categories as $cat) {
              if (isset($cat['id']) && isset($cat['name'])) {
                    $categoryMapById[$cat['id']] = $cat;
                    $trimmedNameLower = mb_strtolower(trim($cat['name']), 'UTF-8'); // Türkçe karakter desteği
                    if (!isset($categoryMapByName[$trimmedNameLower])) {
                        $categoryMapByName[$trimmedNameLower] = [];
                    }
                    $categoryMapByName[$trimmedNameLower][] = $cat;
              }
         }
     }
     // error_log("Kategori haritası oluşturuldu.");

    if (!isset($xml->XMLUrunView)) {
         throw new Exception("XML yapısı tanınamadı. '<XMLUrunView>' etiketleri bulunamadı.");
    }

    $basari_sayac = 0;
    $hata_sayac = 0;
    $hatali_urunler = [];

    // error_log("Ürün döngüsü başlıyor...");
    $itemCount = 0;
    foreach ($xml->XMLUrunView as $item) {
        $itemCount++;
        // if ($itemCount % 100 == 0) { error_log($itemCount . " ürün işlendi..."); } // Her 100 üründe bir log yaz

        try { // Ürün bazında hata yakalama
            $name = trim((string) $item->Ad);
            $stock = (int) $item->Miktar;
            $brand = trim((string) $item->Marka);
            $image = trim((string) $item->AnaResim);
            $description = trim((string) $item->Detay);

            $categoryName = trim((string) $item->AnaGrup_Ad);
            $subCategoryName = trim((string) $item->AltGrup_Ad);

            $base_price_ozel = (float) str_replace(',', '.', (string)$item->Fiyat_Ozel); // Virgül->Nokta
            $base_price_sk = (float) str_replace(',', '.', (string)$item->Fiyat_SK); // Virgül->Nokta
            $currency = (string) $item->Doviz;
            $kdv_orani = (float) str_replace(',', '.', (string)$item->Kdv); // Virgül->Nokta
            $kdv_carpani = 1 + ($kdv_orani / 100);

            $final_tl_price = 0;
            $final_old_price = 0;

            if ($currency === 'USD' && $usd_kur > 0) {
                $final_tl_price = (($base_price_ozel * $usd_kur) * KAR_ORANI) * $kdv_carpani;
                $final_old_price = (($base_price_sk * $usd_kur) * KAR_ORANI) * $kdv_carpani;
            } elseif ($currency === 'TL' || $currency === 'TRY') { // TL Fiyat Kontrolü
                $final_tl_price = ($base_price_ozel * KAR_ORANI) * $kdv_carpani;
                $final_old_price = ($base_price_sk * KAR_ORANI) * $kdv_carpani;
            } else {
                 // Diğer dövizler veya kur yoksa fiyat 0 kalacak veya hata loglanabilir
                  // error_log("Desteklenmeyen para birimi veya kur eksik: Ürün=" . $name . ", Birim=" . $currency);
                  continue; // Bu ürünü atla
            }

            if ($final_old_price <= $final_tl_price + 0.01) $final_old_price = null;

            // --- Kategori Eşleştirmesi ---
            $mainCategoryId = null;
            $subCategoryId = null;
            $finalCategoryId = null;

             $categoryNameLower = mb_strtolower($categoryName, 'UTF-8');
             $subCategoryNameLower = mb_strtolower($subCategoryName, 'UTF-8');

             if (!empty($categoryNameLower) && isset($categoryMapByName[$categoryNameLower])) {
                 foreach ($categoryMapByName[$categoryNameLower] as $potentialMainCat) {
                     if (empty($potentialMainCat['parentId'])) {
                         $mainCategoryId = $potentialMainCat['id'];
                         break;
                     }
                 }
             }

             if ($mainCategoryId !== null && !empty($subCategoryNameLower) && isset($categoryMapByName[$subCategoryNameLower])) {
                 foreach ($categoryMapByName[$subCategoryNameLower] as $potentialSubCat) {
                     if (isset($potentialSubCat['parentId']) && $potentialSubCat['parentId'] == $mainCategoryId) {
                         $subCategoryId = $potentialSubCat['id'];
                         break;
                     }
                 }
             }
             $finalCategoryId = $subCategoryId ?? $mainCategoryId;

              if ($finalCategoryId === null && (!empty($categoryName) || !empty($subCategoryName)) ) {
                  $hata_sayac++;
                  $hatali_urunler[] = [ 'urun_adi' => $name, 'xml_ana_kategori' => $categoryName, 'xml_alt_kategori' => $subCategoryName ];
              } else {
                  $basari_sayac++;
              }
            // --- Kategori Sonu ---

            // === YENİ: Yapısal Özellikleri Çekme ===
            $attributes = [];
            // Markayı her zaman ekle
            if (!empty($brand)) {
                $attributes['Marka'] = $brand;
            }

            // Teknik özellikleri işle
            if (isset($item->TeknikOzellikler)) {
                foreach($item->TeknikOzellikler->UrunTeknikOzellikler as $ozellik) {
                     $featureName = trim((string)$ozellik->Ozellik);
                     $featureValue = trim((string)$ozellik->Deger);
                     // Boş özellikleri veya anlamsız değerleri atla
                     if(!empty($featureName) && !empty($featureValue) && $featureValue !== '-' && $featureValue !== '.' && mb_strlen($featureName) < 50 && mb_strlen($featureValue) < 150) { // Limitler ekleyelim
                          // Anahtar ismini temizleyelim (Türkçe karakterleri ve özel karakterleri değiştirebiliriz)
                          $attributeKey = preg_replace('/[^A-Za-z0-9_ÇçĞğİıÖöŞşÜü ]/u', '', $featureName);
                          $attributeKey = str_replace(' ', '_', $attributeKey); // Boşlukları alt çizgi yap
                          // Çok uzun anahtarları atla
                          if (mb_strlen($attributeKey) > 0 && mb_strlen($attributeKey) < 50) {
                              $attributes[$attributeKey] = $featureValue;
                          }
                     }
                }
            }
             // Model Numarasını veya Ürün Kodunu attributes'e ekleyebiliriz (Örnek)
             if(isset($attributes['Ürün_Kodu'])) {
                 $attributes['Model'] = $attributes['Ürün_Kodu'];
             } elseif(isset($attributes['Model'])) {
                 // Zaten Model varsa dokunma
             } elseif (isset($item->Kod)) { // XML'deki ana ürün kodu
                 $attributes['Model'] = (string)$item->Kod;
             }


             // Ek Bilgi'yi özelliklere ekleyelim (eğer varsa)
             $ekBilgi = trim((string)$item->Aciklama);
             if (!empty($ekBilgi)) {
                 $attributes['Ek_Bilgi'] = $ekBilgi;
             }
            // === Yapısal Özellikler Sonu ===


            $newProduct = [
                'id'             => $id_counter++,
                'name'           => $name,
                'categoryId'     => $finalCategoryId,
                // 'brand'          => $brand, // Markayı attributes içine taşıdık
                'price'          => round($final_tl_price, 2),
                'oldPrice'       => $final_old_price ? round($final_old_price, 2) : null,
                'stock'          => $stock > 0 ? $stock : 0,
                'totalStockIn'   => $stock > 0 ? $stock : 0,
                'totalSold'      => 0,
                'description'    => $description, // HTML Tablo
                // 'features'       => $features, // Bunu attributes ile değiştirdik
                'attributes'     => $attributes, // YENİ EKLEDİK
                'image'          => $image,
                'createdAt'      => date('c')
            ];

            $products[] = $newProduct;

         } catch (Exception $productEx) {
              // Belirli bir ürünü işlerken hata olursa logla ve devam et
              error_log("Ürün işleme hatası (Ürün Adı: " . (isset($item->Ad) ? (string)$item->Ad : 'Bilinmiyor') . "): " . $productEx->getMessage());
              $hata_sayac++; // Hatalı ürün sayacını artır
         }

    }
    // error_log("Ürün döngüsü bitti. Toplam: " . $itemCount);

    // error_log("products.json dosyasına yazılıyor...");
    saveJsonData('products.json', $products);
    // error_log("products.json dosyasına yazıldı.");

     $message = $basari_sayac + $hata_sayac . ' ürün XML\'den okundu. ' . $basari_sayac . ' ürün için kategori eşleşti. ';
     $message .= 'Kur (' . $kur_tipi . '): ' . $usd_kur . ' | Fiyatlara %' . ((KAR_ORANI - 1) * 100) . ' kar ve KDV eklendi.';
     if ($hata_sayac > 0) {
          $message .= ' UYARI: ' . $hata_sayac . ' ürün için kategori eşleştirilemedi veya işlenirken hata oluştu. Detaylar için error_log dosyasını kontrol edin.';
          if (!empty($hatali_urunler)) {
              error_log("--- XML Kategori Eşleştirme Hataları (" . date('Y-m-d H:i:s') . ") ---");
              $limit = 5; // İlk 5 hatayı logla
              foreach($hatali_urunler as $i => $hata) {
                   if ($i >= $limit) break;
                   error_log("Ürün: " . $hata['urun_adi'] . " | XML Ana: " . $hata['xml_ana_kategori'] . " | XML Alt: " . $hata['xml_alt_kategori']);
              }
              error_log("--- Hata Raporu Sonu ---");
          }
     }

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);

} catch (Exception $e) {
    http_response_code(500);
    $lineNumber = $e->getLine();
    $errorMessage = 'Genel Hata (Satır ' . $lineNumber . '): ' . $e->getMessage();
    echo json_encode([
        'success' => false,
        'message' => $errorMessage
    ]);
     error_log('XML Import Hatası: ' . $errorMessage . ' | Dosya: ' . $e->getFile());
     // error_log("Trace: " . $e->getTraceAsString()); // Detaylı trace loglama (gerekirse)
}
// error_log("XML Import Bitti: " . date('Y-m-d H:i:s')); // Loglama Sonu
?>