<?php
// xml_processor_logic.php

if (!function_exists('readJsonFile') || !function_exists('writeJsonFile') || !function_exists('getNewId')) {
    // Bu dosya doğrudan çağrılırsa hata ver (sadece api.php içinden çağrılmalı)
    // Ancak, biz api.php içinde read/write fonksiyonlarını tanımladık.
    // Bu dosyanın api.php'ye 'include' edildiğini varsayıyoruz.
    // Eğer bu fonksiyonlar api.php'de tanımlı değilse, buraya da eklenmeleri gerekir.
    // Bizim senaryomuzda api.php'de tanımlılar.
}


/**
 * Bu fonksiyon, products.json dosyasını XML verisiyle günceller.
 * BU FONKSİYONU KENDİ XML YAPINIZA GÖRE DÜZENLEMENİZ GEREKİR.
 *
 * @param SimpleXMLElement $xml Gelen XML objesi
 * @param string $supplierName Tedarikçi adı (örn: Gunes, Manuel)
 * @param string $productsFile Güncellenecek products.json dosyasının yolu
 * @return array İşlem sonucu
 */
function processXmlData($xml, $supplierName, $productsFile) {
    
    // 1. Mevcut ürünleri yükle
    $productsData = readJsonFile($productsFile);
    
    // 2. Hızlı arama için mevcut stok kodlarını haritala
    //    $skuMap[stok_kodu] = index;
    $skuMap = [];
    foreach ($productsData as $index => $product) {
        if (!empty($product['stok_kodu'])) {
            $skuMap[$product['stok_kodu']] = $index;
        }
    }

    $updatedCount = 0;
    $addedCount = 0;

    // 3. XML'i döngüye al
    // !!! BURASI TAMAMEN SİZİN XML YAPINIZA BAĞLIDIR !!!
    // Örnek XML yapısı: <Urunler><Urun>...</Urun></Urunler>
    // Sizinki farklıysa (örn: <products><product>) bu döngüyü değiştirin.
    
    // Yaygın XML kök etiketlerini kontrol et
    $productsList = null;
    if (isset($xml->Urun)) {
        $productsList = $xml->Urun; // <Urunler><Urun>...</Urun></Urunler>
    } elseif (isset($xml->product)) {
        $productsList = $xml->product; // <products><product>...</product>
    } elseif (isset($xml->item)) {
        $productsList = $xml->item; // <items><item>...</item>
    }
    
    if ($productsList === null) {
         return ['success' => false, 'message' => 'XML yapısı tanınamadı. (Urun, product, veya item tagı bulunamadı)'];
    }
    
    foreach ($productsList as $xmlProduct) {
        
        // --- !!! BU ALANLARI KENDİ XML'İNİZE GÖRE EŞLEŞTİRİN !!! ---
        
        // Stok Kodu (ZORUNLU): <StokKodu>, <sku>, <product_code>
        $stok_kodu = (string)($xmlProduct->StokKodu ?? $xmlProduct->sku ?? $xmlProduct->product_code ?? ''); 
        
        // Ürün Adı: <UrunAdi>, <name>, <title>
        $urun_adi = (string)($xmlProduct->UrunAdi ?? $xmlProduct->name ?? $xmlProduct->title ?? 'İsimsiz Ürün');
        
        // Fiyat: <Fiyat>, <price> (KDV DAHİL mi, HARİÇ mi dikkat edin)
        // Eğer KDV'li fiyat varsa ve size KDV'siz lazımsa (KDV %20 ise)
        // $fiyat = (float)($xmlProduct->FiyatKDVli ?? $xmlProduct->price_with_tax ?? 0) / 1.20;
        $fiyat = (float)($xmlProduct->Fiyat ?? $xmlProduct->price ?? 0);
        
        // Stok: <StokAdedi>, <stock>, <quantity>
        $stok = (int)($xmlProduct->StokAdedi ?? $xmlProduct->stock ?? $xmlProduct->quantity ?? 0);
        
        // Resim: <ResimURL>, <image>, <image_url> (Genelde ilk resim)
        $resim = (string)($xmlProduct->ResimURL ?? $xmlProduct->image ?? $xmlProduct->image_url ?? $xmlProduct->images->image[0] ?? '');
        
        // Marka: <Marka>, <brand>
        $marka = (string)($xmlProduct->Marka ?? $xmlProduct->brand ?? '');
        
        // Açıklama: <Aciklama>, <description>
        $aciklama = (string)($xmlProduct->Aciklama ?? $xmlProduct->description ?? '');
        
        // -----------------------------------------------------------------
        
        if (empty($stok_kodu)) {
            continue; // Stok kodu olmayan ürünü atla
        }

        // 4. Ürün mevcut mu diye kontrol et
        if (isset($skuMap[$stok_kodu])) {
            // MEVCUT ÜRÜN: Güncelle
            $index = $skuMap[$stok_kodu];
            
            // Sadece XML'den gelenleri güncelle, manuel girilenleri koru
            $productsData[$index]['name'] = $urun_adi;
            $productsData[$index]['price'] = $fiyat;
            $productsData[$index]['stock'] = $stok;
            $productsData[$index]['image'] = $resim;
            $productsData[$index]['brand'] = $marka;
            $productsData[$index]['description'] = $aciklama;
            // ... diğer alanları güncelle ...
            
            // Kaynağın bu tedarikçi olduğunu teyit et
            $productsData[$index]['ozel_kod'] = $supplierName; 
            
            $updatedCount++;
            
        } else {
            // YENİ ÜRÜN: Ekle
            $yeniUrun = [
                'id' => getNewId($productsData), // Fonksiyondan yeni ID al
                'stok_kodu' => $stok_kodu,  // Eşleştirme için kritik
                'ozel_kod' => $supplierName, // Kaynağı belirtmek için kritik
                'name' => $urun_adi,
                'price' => $fiyat,
                'stock' => $stok,
                'image' => $resim,
                'brand' => $marka,
                'categoryId' => 0, // Kategori eşleştirmesi yapmanız gerekir
                'description' => $aciklama,
                'features' => [],
                'totalStockIn' => $stok,
                'totalSold' => 0,
                'oldPrice' => 0
            ];
            
            $productsData[] = $yeniUrun;
            // Yeni eklenen ürünü de haritaya ekle ki bir sonraki döngüde bulabilsin (XML'de çift kayıt varsa)
            $skuMap[$stok_kodu] = count($productsData) - 1;
            
            $addedCount++;
        }
    }
    
    // 5. Değişiklikleri products.json'a kaydet
    if (writeJsonFile($productsFile, $productsData)) {
        return ['success' => true, 'message' => "$addedCount yeni ürün eklendi, $updatedCount ürün güncellendi."];
    } else {
        return ['success' => false, 'message' => 'products.json dosyası güncellenemedi.'];
    }
}
?>