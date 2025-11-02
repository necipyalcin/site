<?php
// DOSYA ADI: api.php (Yönetim Paneli ve Güvenlik Eklenmiş Sürüm)
header('Content-Type: application/json');
session_start(); // Güvenlik kontrolleri için session BAŞLATILDI

$resource = $_GET['resource'] ?? '';
$id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$filePath = $resource . '.json';

// ... api.php
// 'settings' kaynağı eklendi
if (!in_array($resource, ['products', 'categories', 'users', 'orders', 'cart', 'settings', 'banners', 'showcase', 'pages', 'vitrinBanners'])) { // <-- GÜNCELLENDİ
     http_response_code(400); echo json_encode(['success' => false, 'message' => 'Geçersiz kaynak.']); exit;
}

// ### YENİ: AYARLAR (SETTINGS) İŞLEMLERİ ###
// Bu bölüm, diğer işlemlerden önce ele alınmalıdır çünkü kendi dosyası (settings.json) var
if ($resource === 'settings') {
    // AYRICALIKLI ERİŞİM: Ayarlar için admin girişi GEREKLİ
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(403); // Yetkisiz
        echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim. Lütfen admin girişi yapın.']);
        exit;
    }
    
    $settingsPath = 'settings.json';
    $settings = json_decode(file_get_contents($settingsPath), true);

    if ($method === 'GET') {
        // Parola hash'ini ASLA client'a (JavaScript'e) gönderme
        unset($settings['adminPassHash']);
        echo json_encode($settings);
        exit;
    }

    if ($method === 'POST' && $action === 'update_info') {
        $newData = json_decode(file_get_contents('php://input'), true);
        $settings['companyInfo'] = array_merge($settings['companyInfo'] ?? [], $newData);
        file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        unset($settings['adminPassHash']); // Hash'i yanıtta gönderme
        echo json_encode(['success' => true, 'data' => $settings]);
        exit;
    }
    
    if ($method === 'POST' && $action === 'change_pass') {
        $passData = json_decode(file_get_contents('php://input'), true);
        
        // 1. Mevcut şifreyi doğrula
        if (!isset($passData['oldPassword']) || !password_verify($passData['oldPassword'], $settings['adminPassHash'])) {
            http_response_code(401); // Yetkisiz (Yanlış şifre)
            echo json_encode(['success' => false, 'message' => 'Mevcut şifre yanlış.']);
            exit;
        }
        // 2. Yeni şifreleri onayla
        if (empty($passData['newPassword']) || $passData['newPassword'] !== $passData['confirmPassword']) {
            http_response_code(400); // Kötü istek
            echo json_encode(['success' => false, 'message' => 'Yeni şifreler eşleşmiyor veya boş.']);
            exit;
        }
        // 3. Yeni şifreyi hash'le ve kaydet
        $settings['adminPassHash'] = password_hash($passData['newPassword'], PASSWORD_DEFAULT);
        file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo json_encode(['success' => true, 'message' => 'Şifre başarıyla değiştirildi.']);
        exit;
    }
    
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz ayar işlemi.']);
    exit;
}
// ### AYARLAR BÖLÜMÜ SONU ###


// ----- Mevcut Kodunuz (Kalanı) -----
if ($resource !== 'cart' && !file_exists($filePath)) { file_put_contents($filePath, '[]'); }

$data = ($resource !== 'cart') ? json_decode(file_get_contents($filePath), true) : ($_SESSION['cart'] ?? []);

if (json_last_error() !== JSON_ERROR_NONE && $resource !== 'cart') {
    $data = [];
}

// ... (Geri kalan tüm case 'GET', 'POST', 'PUT', 'DELETE' ve sipariş kodlarınız aynı kalabilir) ...
// ... (api.php dosyanızın geri kalanını buraya yapıştırın) ...

if ($resource === 'cart' && $action === 'clear' && $method === 'POST') {
    $_SESSION['cart'] = [];
    echo json_encode(['success' => true, 'message' => 'Sepet başarıyla temizlendi.', 'cart' => []]);
    exit;
}

if ($method === 'POST' && $resource === 'orders') {
    $orderData = json_decode(file_get_contents('php://input'), true);
    $cart = $_SESSION['cart'] ?? [];
    
    if (empty($cart)) {
        http_response_code(400); echo json_encode(['success' => false, 'message' => 'Sipariş oluşturmak için sepetinizde ürün bulunmalıdır.']); exit;
    }
    
    $productsFilePath = 'products.json';
    $products = json_decode(file_get_contents($productsFilePath), true);
    
    foreach ($cart as $cartItem) {
        $productFound = false;
        foreach ($products as $index => $product) {
            if ($product['id'] == $cartItem['id']) {
                if ($products[$index]['stock'] < $cartItem['quantity']) {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => 'Stok yetersiz: ' . $cartItem['name']]);
                    exit;
                }
                $products[$index]['stock'] -= $cartItem['quantity'];
                $productFound = true;
                break;
            }
        }
        if (!$productFound) {
             http_response_code(404);
             echo json_encode(['success' => false, 'message' => 'Sepetteki ürün bulunamadı: ' . $cartItem['name']]);
             exit;
        }
    }
    
    file_put_contents($productsFilePath, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    $orders = json_decode(file_get_contents('orders.json'), true);
    $lastId = empty($orders) ? 0 : max(array_column($orders, 'id'));
    
    $total = array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
    
    $newOrder = [
        'id' => $lastId + 1,
        'userId' => $_SESSION['user']['id'] ?? null,
        'customerInfo' => $orderData['customer'],
        'items' => array_map(function($item) {
            return ['productId' => $item['id'], 'name' => $item['name'], 'quantity' => $item['quantity'], 'price' => $item['price']];
        }, $cart),
        'total' => $total,
        'status' => 'Beklemede',
        'date' => date('c'),
    ];
    
    $orders[] = $newOrder;
    file_put_contents('orders.json', json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    $_SESSION['cart'] = [];
    
    // --- E-POSTA BİLDİRİM KODU ---
    $adminEmail = "yagmurbilgisayaradana@hotmail.com"; // DEĞİŞTİR: KENDİ E-POSTA ADRESİNİZİ YAZIN
    $siparisNo = $newOrder['id'];
    $musteriAdi = $newOrder['customerInfo']['name'] ?? 'Bilinmiyor';
    $toplamTutar = number_format($newOrder['total'], 2, ',', '.') . ' TL';
    $konu = "Yeni Sipariş Alındı! - Sipariş No: #$siparisNo";
    $mesaj = "<html><body><h2>Yeni Sipariş Bildirimi</h2><p>Siteniz üzerinden yeni bir sipariş aldınız.</p><p><strong>Sipariş Numarası:</strong> #$siparisNo</p><p><strong>Müşteri Adı:</strong> $musteriAdi</p><p><strong>Toplam Tutar:</strong> $toplamTutar</p><p>Lütfen siparişi kontrol etmek için admin panelinize giriş yapın.</p></body></html>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <akinsofta@akinsoftadana.com.tr>' . "\r\n";
    @mail($adminEmail, $konu, $mesaj, $headers);
    // --- E-POSTA BİLDİRİM KODU SONU ---
    
    echo json_encode(['success' => true, 'message' => 'Siparişiniz başarıyla alındı.', 'order' => $newOrder]);
    exit;
}

if ($method === 'POST' && $action === 'bulk-import' && $resource === 'products') {
    $newProducts = json_decode(file_get_contents('php://input'), true);
    $lastId = empty($data) ? 0 : max(array_column($data, 'id'));
    
    foreach ($newProducts as $product) {
        $lastId++;
        $product['id'] = $lastId;
        $data[] = $product;
    }
    
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['success' => true, 'message' => count($newProducts) . ' ürün başarıyla eklendi.']);
    exit;
}

switch ($method) {
    case 'GET': echo json_encode($data); break;
    case 'POST':
        $newData = json_decode(file_get_contents('php://input'), true);
        $lastId = empty($data) ? 0 : max(array_column($data, 'id'));
        $newData['id'] = $lastId + 1;
        if ($resource === 'users' && isset($newData['password'])) {
            $newData['hashedPassword'] = password_hash($newData['password'], PASSWORD_DEFAULT);
            unset($newData['password']);
        }
        $data[] = $newData;
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true, 'data' => $newData]);
        break;
    case 'PUT':
        $updateData = json_decode(file_get_contents('php://input'), true);
        $updateId = $updateData['id'] ?? 0; $found = false;
        foreach ($data as $index => $item) {
            if ($item['id'] == $updateId) {
                if ($resource === 'users' && !empty($updateData['password'])) {
                    $updateData['hashedPassword'] = password_hash($updateData['password'], PASSWORD_DEFAULT);
                }
                unset($updateData['password']);
                $data[$index] = array_merge($item, $updateData);
                $found = true;
                break;
            }
        }
        if ($found) {
            file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo json_encode(['success' => true, 'data' => $data[$index]]);
        }
        break;
    case 'DELETE':
        if ($id) {
            $data = array_values(array_filter($data, fn($item) => $item['id'] != $id));
            file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            echo json_encode(['success' => true]);
        }
        break;
}
?>