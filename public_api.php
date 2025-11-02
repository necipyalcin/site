<?php
// DOSYA ADI: public_api.php (FAVORİ SİSTEMİ EKLENMİŞ VE SEPET MİKTAR HATASI DÜZELTİLMİŞ TAM SÜRÜM)

session_start();
header('Content-Type: application/json; charset=utf-8');

// --- YARDIMCI FONKSİYONLAR ---

function getJsonData($filename) {
    if (!file_exists($filename)) {
        file_put_contents($filename, '[]');
        return [];
    }
    $content = file_get_contents($filename);
    if (empty(trim($content))) return [];
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Hatası: $filename - " . json_last_error_msg());
        return [];
    }
    return $data;
}

function saveJsonData($filename, $data) {
    // Veriyi array_values ile sarmalayarak anahtarların sıfırlanmasını sağla (JSON array formatını korumak için)
    file_put_contents($filename, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// --- ANA API YÖNLENDİRİCİSİ ---

$resource = $_GET['resource'] ?? '';
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch ($resource) {
    case 'products':
        handleProductActions('products.json', $method, $id, $action);
        break;
    case 'categories':
        handleResourceActions('categories.json', $method, $id);
        break;
        case 'banners':
        handleResourceActions('banners.json', $method, $id);
        break;
    case 'showcase':
        handleResourceActions('showcase.json', $method, $id);
        break;
        case 'vitrinBanners': // ### YENİ ###
        if ($method === 'GET') {
            handleResourceActions('vitrin_banners.json', $method, $id);
        } else {
            http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
        }
        break;
    case 'pages': // ### YENİ ###
        if ($method === 'GET') {
            handleResourceActions('pages.json', $method, $id);
        } else {
            http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
        }
        break;
     case 'users':
         handleUserActions($action, $method, $id);
         break;
    case 'users':
        handleUserActions($action, $method, $id);
        break;
    case 'cart':
        handleCartActions($action, $method);
        break;
    case 'orders':
        handleOrderActions($action, $method, $id);
        break;
    case 'favorites': // YENİ EKLENDİ
        handleFavoriteActions($action, $method);
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Geçersiz kaynak.']);
        break;
}

// --- İŞLEM FONKSİYONLARI ---

function handleFavoriteActions($action, $method) {
    // Sadece giriş yapmış kullanıcılar bu işlemi yapabilir
    if (!isset($_SESSION['user']['id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Favori eklemek için giriş yapmalısınız.']);
        exit;
    }

    $userId = $_SESSION['user']['id'];
    $filename = 'favorites.json';
    $input = json_decode(file_get_contents('php://input'), true);

    // favorites.json dosyasını daha dikkatli oku (Artık user ID anahtarlı)
    $favoritesData = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];
    if (!is_array($favoritesData)) {
        $favoritesData = []; // Hatalı veya boş dosyaya karşı koruma
    }

    // Kullanıcının favori listesi yoksa oluşturalım (anahtar olarak user ID ile)
    if (!isset($favoritesData[$userId]) || !is_array($favoritesData[$userId])) {
        $favoritesData[$userId] = [];
    }

    // Kullanıcının favorilerini getir (GET isteği)
    if ($method === 'GET' && $action === 'get') {
        echo json_encode(['success' => true, 'data' => array_values($favoritesData[$userId])]); // Sadece ID listesini döndür
        return;
    }

    // Bir ürünü favorilere ekle/kaldır (POST isteği)
    if ($method === 'POST' && $action === 'toggle') {
        if (!isset($input['productId'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ürün ID eksik.']);
            return;
        }
        $productId = intval($input['productId']);

        // Ürün zaten favorilerde mi kontrol et
        $key = array_search($productId, $favoritesData[$userId]);

        if ($key !== false) {
            // Evet, favorilerde var, o zaman çıkaralım.
            array_splice($favoritesData[$userId], $key, 1);
            $message = 'Ürün favorilerden kaldırıldı.';
        } else {
            // Hayır, favorilerde yok, o zaman ekleyelim.
            $favoritesData[$userId][] = $productId;
            $message = 'Ürün favorilere eklendi.';
        }

        // Değiştirilmiş veriyi dosyaya yaz (anahtar-değer yapısını koru)
        // DİKKAT: favorites.json dosyası artık { "userId": [productId1, productId2], ... } formatında olacak.
        file_put_contents($filename, json_encode($favoritesData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Kullanıcının güncel favori ID listesini geri döndür
        echo json_encode(['success' => true, 'message' => $message, 'data' => array_values($favoritesData[$userId])]);
        return;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz favori işlemi.']);
}


function handleProductActions($filename, $method, $id, $action) {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($method === 'POST' && $action === 'bulk_update_price') {
        $products = getJsonData($filename);
        $ids = $input['ids'] ?? [];
        $type = $input['type'] ?? '';
        $amount = floatval($input['amount'] ?? 0);
        if(empty($ids) || empty($type) || $amount <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Eksik veya geçersiz parametreler.']);
            return;
        }
        foreach ($products as &$product) {
            if (in_array($product['id'], $ids)) {
                $price = floatval($product['price']);
                switch ($type) {
                    case 'percent_increase': $product['price'] = $price * (1 + $amount / 100); break;
                    case 'percent_decrease': $product['price'] = $price * (1 - $amount / 100); break;
                    case 'fixed_increase': $product['price'] = $price + $amount; break;
                    case 'fixed_decrease': $product['price'] = $price - $amount; break;
                }
                $product['price'] = max(0, round($product['price'], 2));
            }
        }
        unset($product); // Referansı kaldır
        saveJsonData($filename, $products);
        echo json_encode(['success' => true, 'message' => count($ids) . ' ürünün fiyatı güncellendi.']);
        return;
    }
    // Diğer ürün işlemleri (GET, POST, PUT, DELETE) için handleResourceActions'ı çağır
    handleResourceActions($filename, $method, $id);
}

function handleUserActions($action, $method, $id) {
    $users = getJsonData('users.json');
    $input = json_decode(file_get_contents('php://input'), true);

    // Toplu Silme (DELETE isteği ve body'de 'ids' array'i ile)
    if ($method === 'DELETE' && isset($input['ids']) && is_array($input['ids'])) {
        $idsToDelete = array_map('intval', $input['ids']);
        $initialCount = count($users);
        $users = array_filter($users, fn($user) => !in_array($user['id'], $idsToDelete, true));
        if (count($users) < $initialCount) {
            saveJsonData('users.json', $users);
            http_response_code(204); // Başarılı, içerik yok
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Silinecek kullanıcı bulunamadı.']);
        }
        return;
    }

    // Tüm Kullanıcıları Listele (GET /users) - Şifreleri kaldır
    if ($method === 'GET' && empty($action) && empty($id)) {
        foreach ($users as &$user) { unset($user['password'], $user['hashedPassword']); }
        unset($user); // Referansı kaldır
        echo json_encode(array_values($users)); // array_values eklenmeli
        return;
    }

    // Yeni Kullanıcı Ekle (POST /users)
    if ($method === 'POST' && empty($action)) {
        // E-posta kontrolü
        foreach ($users as $user) { if (isset($user['email']) && $user['email'] === $input['email']) { http_response_code(409); echo json_encode(['success' => false, 'message' => 'Bu e-posta adresi zaten kayıtlı.']); exit; } }

        $lastId = empty($users) ? 0 : max(array_column($users, 'id'));
        $newUser = $input;
        $newUser['id'] = $lastId + 1;
        if (!empty($input['password'])) { $newUser['password'] = password_hash($input['password'], PASSWORD_DEFAULT); }
        $newUser['registeredAt'] = date('c'); // Kayıt tarihi ekle
        $users[] = $newUser;
        saveJsonData('users.json', $users);
        unset($newUser['password']); // Yanıttan şifreyi kaldır
        echo json_encode(['success' => true, 'message' => 'Müşteri başarıyla eklendi.', 'data' => $newUser]);
        return;
    }

    // Kullanıcı Güncelle (PUT /users/{id})
    if ($method === 'PUT' && $id) {
        $foundIndex = -1;
        foreach($users as $index => $user) { if ($user['id'] == $id) { $foundIndex = $index; break; } }
        if ($foundIndex !== -1) {
            // E-posta değiştiriliyorsa ve zaten varsa hata ver
            if (isset($input['email']) && $input['email'] !== $users[$foundIndex]['email']) {
                foreach ($users as $indexCheck => $userCheck) {
                    if ($indexCheck !== $foundIndex && isset($userCheck['email']) && $userCheck['email'] === $input['email']) {
                        http_response_code(409);
                        echo json_encode(['success' => false, 'message' => 'Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.']);
                        exit;
                    }
                }
            }

            // Şifre boş gönderildiyse güncelleme
            if (isset($input['password']) && empty($input['password'])) { unset($input['password']); }

            $updatedUser = array_merge($users[$foundIndex], $input);
            // Yeni şifre varsa hashle
            if (!empty($input['password'])) { $updatedUser['password'] = password_hash($input['password'], PASSWORD_DEFAULT); }

            $users[$foundIndex] = $updatedUser;
            saveJsonData('users.json', $users);
            unset($updatedUser['password']); // Yanıttan şifreyi kaldır
            echo json_encode(['success' => true, 'message' => 'Kullanıcı bilgileri güncellendi.', 'data' => $updatedUser]);
        } else { http_response_code(404); echo json_encode(['success' => false, 'message' => 'Kullanıcı bulunamadı.']); }
        return;
    }

    // Oturum Açmış Kullanıcının Bilgilerini Güncelle (POST /users/update_details)
    if ($method === 'POST' && $action === 'update_details') {
        if (!isset($_SESSION['user']['id'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Bu işlem için giriş yapmalısınız.']); exit; }
        $userId = $_SESSION['user']['id'];
        $foundIndex = -1;
        foreach ($users as $index => $user) { if ($user['id'] == $userId) { $foundIndex = $index; break; } }
        if ($foundIndex !== -1) {
             // E-posta değiştiriliyorsa ve zaten varsa hata ver (yukarıdaki PUT ile aynı kontrol)
             if (isset($input['email']) && $input['email'] !== $users[$foundIndex]['email']) {
                foreach ($users as $indexCheck => $userCheck) {
                    if ($indexCheck !== $foundIndex && isset($userCheck['email']) && $userCheck['email'] === $input['email']) {
                        http_response_code(409);
                        echo json_encode(['success' => false, 'message' => 'Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.']);
                        exit;
                    }
                }
            }

            // Güvenlik: id ve registeredAt değiştirilemez
            unset($input['id'], $input['registeredAt']);

            // Şifre boş gönderildiyse güncelleme
            if (isset($input['password']) && empty($input['password'])) { unset($input['password']); }

            $updatedUser = array_merge($users[$foundIndex], $input);
            if (!empty($input['password'])) { $updatedUser['password'] = password_hash($input['password'], PASSWORD_DEFAULT); }

            $users[$foundIndex] = $updatedUser;
            saveJsonData('users.json', $users);
            unset($updatedUser['password']); // Yanıttan şifreyi kaldır
            // Session'daki ismi de güncelle (eğer değiştiyse)
            if (isset($updatedUser['name'])) { $_SESSION['user']['name'] = $updatedUser['name']; }

            echo json_encode(['success' => true, 'message' => 'Bilgileriniz başarıyla güncellendi.', 'data' => $updatedUser]);
        } else { http_response_code(404); echo json_encode(['success' => false, 'message' => 'Kullanıcı bulunamadı.']); }
        return;
    }

    // Diğer Kullanıcı Aksiyonları (register, login, logout, status, get_details)
    switch ($action) {
        case 'register':
            if ($method !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method Not Allowed']); return; }
            // E-posta kontrolü
            foreach ($users as $user) { if (isset($user['email']) && $user['email'] === $input['email']) { http_response_code(409); echo json_encode(['success' => false, 'message' => 'Bu e-posta adresi zaten kayıtlı.']); exit; } }

            // Şifre eşleşme kontrolü
            if (!isset($input['password']) || !isset($input['passwordConfirm']) || $input['password'] !== $input['passwordConfirm']) {
                http_response_code(400); echo json_encode(['success' => false, 'message' => 'Şifreler eşleşmiyor.']); exit;
            }

            $lastId = empty($users) ? 0 : max(array_column($users, 'id'));
            $newUser = [
                'id' => $lastId + 1,
                'name' => $input['name'] ?? '',
                'surname' => $input['surname'] ?? '',
                'firstName' => $input['name'] ?? '', // Eski uyumluluk için
                'lastName' => $input['surname'] ?? '', // Eski uyumluluk için
                'email' => $input['email'],
                'phone' => $input['phone'] ?? '',
                'address' => $input['address'] ?? '',
                'city' => $input['city'] ?? '',
                'district' => $input['district'] ?? '',
                'billingAddress' => $input['billingAddress'] ?? '',
                'billingCity' => $input['billingCity'] ?? '',
                'billingDistrict' => $input['billingDistrict'] ?? '',
                'password' => password_hash($input['password'], PASSWORD_DEFAULT),
                'registeredAt' => date('c')
            ];
            $users[] = $newUser;
            saveJsonData('users.json', $users);
            $_SESSION['user'] = ['id' => $newUser['id'], 'name' => $newUser['name']]; // Oturum aç
            echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
            break;

        case 'login':
            if ($method !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method Not Allowed']); return; }
            foreach ($users as $user) {
                // Hem 'password' hem de eski 'hashedPassword' anahtarını kontrol et
                $password_key = isset($user['password']) ? 'password' : (isset($user['hashedPassword']) ? 'hashedPassword' : null);
                if (isset($user['email']) && $user['email'] === $input['email'] && $password_key && password_verify($input['password'], $user[$password_key])) {
                    $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'] ?? ($user['firstName'] ?? '')];
                    echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
                    exit;
                }
            }
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'E-posta veya şifre hatalı.']);
            break;

        case 'logout':
            session_destroy();
            echo json_encode(['success' => true]);
            break;

        case 'status':
            if (isset($_SESSION['user'])) {
                echo json_encode(['loggedIn' => true, 'user' => $_SESSION['user']]);
            } else {
                echo json_encode(['loggedIn' => false]);
            }
            break;

        case 'get_details':
            // Admin panelinden ID ile veya oturumdaki kullanıcının kendi ID'si ile detay al
            $userIdToFetch = $id ? (int)$id : ($_SESSION['user']['id'] ?? null);

            if (!$userIdToFetch) {
                http_response_code(401); // Giriş yapılmamışsa veya ID yoksa yetkisiz
                echo json_encode(['success' => false, 'message' => 'Kullanıcı ID belirtilmedi veya oturum açılmamış.']);
                exit;
            }

            $foundUser = null;
            foreach ($users as $user) {
                if ($user['id'] == $userIdToFetch) {
                    $foundUser = $user;
                    break;
                }
            }

            if ($foundUser) {
                unset($foundUser['password'], $foundUser['hashedPassword']); // Şifreyi yanıttan çıkar
                echo json_encode($foundUser);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Kullanıcı bulunamadı.']);
            }
            break;
        default:
             // Eğer action belirtilmemişse ve GET/POST/PUT değilse veya ID varsa, handleResourceActions'a devret (örn: DELETE /users/{id})
            if ($method === 'DELETE' && $id) {
                handleResourceActions('users.json', $method, $id);
            } else if (!in_array($action, ['register', 'login', 'logout', 'status', 'get_details', 'update_details']) && empty($action)) {
                 // Bilinmeyen bir action veya metod ise hata ver
                 http_response_code(400);
                 echo json_encode(['success' => false, 'message' => 'Geçersiz kullanıcı işlemi.']);
            }
            break;
    }
}


function handleCartActions($action, $method) {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    // Sepeti referansla alalım ki doğrudan güncelleyebilelim
    $cart = &$_SESSION['cart'];
    $input = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
        case 'get':
            // Sepetteki ürünlerin güncel fiyat ve stoklarını kontrol et
            $products = getJsonData('products.json');
            $updatedCart = [];
            $changed = false;
            foreach ($cart as $productId => $item) {
                $found = false;
                foreach ($products as $p) {
                    if ($p['id'] == $productId) {
                        $found = true;
                        // Fiyat değişmişse güncelle
                        if ($item['price'] != $p['price']) {
                            $item['price'] = $p['price'];
                            $changed = true;
                        }
                        // Stok yetersizse miktarı düşür
                        if ($item['quantity'] > $p['stock']) {
                            $item['quantity'] = $p['stock'];
                            $changed = true;
                            // Stok 0 ise ürünü sepetten çıkar da diyebiliriz
                            // if ($p['stock'] <= 0) continue 2; // Bu ürünü atla
                        }
                        // Stok 0'dan büyükse güncel sepet listesine ekle
                        if ($item['quantity'] > 0) {
                             $updatedCart[$productId] = $item;
                        } else {
                             $changed = true; // Stok 0 olduğu için çıkarıldı
                        }
                        break;
                    }
                }
                 // Ürün artık products.json'da yoksa sepetten çıkar
                if (!$found) {
                    $changed = true;
                }
            }
            // Eğer sepet değiştiyse session'ı güncelle
            if ($changed) {
                $_SESSION['cart'] = $updatedCart;
                $cart = &$_SESSION['cart']; // Referansı yeniden ayarla
            }
            echo json_encode(array_values($updatedCart));
            break;

        // ### BAŞLANGIÇ: MİKTAR HATASI DÜZELTİLDİ ###
        case 'add':
            if (!isset($input['productId'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ürün ID eksik.']);
                exit;
            }
            $productId = (int)$input['productId'];
            // main.js'den gelen miktarı al, gelmezse veya geçersizse 1 kabul et
            $quantityToAdd = isset($input['quantity']) ? max(1, (int)$input['quantity']) : 1;

            $products = getJsonData('products.json');
            $productToAdd = null;
            foreach($products as $p) {
                if ($p['id'] == $productId) {
                    $productToAdd = $p;
                    break;
                }
            }

            if (!$productToAdd) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı.']);
                exit;
            }

            // Ürünün stok durumunu kontrol et
            if ($productToAdd['stock'] <= 0) {
                 http_response_code(400);
                 echo json_encode(['success' => false, 'message' => 'Ürün stokta yok.']);
                 exit;
            }

            $quantityInCart = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;

            // Stok kontrolü (Eklenecek Miktara Göre)
            if ($quantityInCart + $quantityToAdd > $productToAdd['stock']) {
                http_response_code(400);
                $canAdd = $productToAdd['stock'] - $quantityInCart;
                $message = 'Stok miktarını aştınız! Sepetinizde ' . $quantityInCart . ' adet var.';
                if ($canAdd > 0) {
                    $message .= ' En fazla ' . $canAdd . ' adet daha ekleyebilirsiniz.';
                } else {
                    $message .= ' Daha fazla ekleyemezsiniz.';
                }
                echo json_encode(['success' => false, 'message' => $message]);
                exit;
            }

            // Sepete Ekleme/Güncelleme (Gelen Miktarı Kullan)
            if (isset($cart[$productId])) {
                // Ürün zaten sepette varsa, gönderilen miktarı ekle
                $cart[$productId]['quantity'] += $quantityToAdd;
            } else {
                // Ürün sepette yoksa, gönderilen miktarla ekle
                $cart[$productId] = [
                    'id' => $productToAdd['id'],
                    'name' => $productToAdd['name'],
                    'price' => $productToAdd['price'],
                    'image' => $productToAdd['image'],
                    'quantity' => $quantityToAdd // <<< Miktar hatası buradaydı, düzeltildi
                ];
            }
            echo json_encode(array_values($cart));
            break;
        // ### BİTİŞ: MİKTAR HATASI DÜZELTİLDİ ###

        case 'update':
            if (!isset($input['productId']) || !isset($input['quantity'])) {
                 http_response_code(400);
                 echo json_encode(['success' => false, 'message' => 'Eksik parametre.']);
                 exit;
            }
            $productId = (int)$input['productId'];
            $quantity = (int)$input['quantity'];

            if ($quantity <= 0) {
                // Miktar 0 veya daha az ise ürünü sepetten çıkar
                unset($cart[$productId]);
            } else {
                // Ürün sepette mi kontrol et
                if(isset($cart[$productId])) {
                    // Ürünün stok bilgisini al
                    $products = getJsonData('products.json');
                    $productToUpdate = null;
                    foreach($products as $p) {
                        if ($p['id'] == $productId) {
                            $productToUpdate = $p;
                            break;
                        }
                    }

                    // Stok kontrolü
                    if ($productToUpdate && $quantity > $productToUpdate['stock']) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Stok miktarını aştınız! En fazla ' . $productToUpdate['stock'] . ' adet ekleyebilirsiniz.']);
                        exit;
                    }
                    // Miktarı güncelle
                    $cart[$productId]['quantity'] = $quantity;
                } else {
                    // Güncellenmek istenen ürün sepette yoksa hata ver (veya ekleme yap?)
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Güncellenecek ürün sepette bulunamadı.']);
                    exit;
                }
            }
            echo json_encode(array_values($cart));
            break;

        case 'remove':
            if (!isset($input['productId'])) {
                 http_response_code(400);
                 echo json_encode(['success' => false, 'message' => 'Ürün ID eksik.']);
                 exit;
            }
            $productId = (int)$input['productId'];
            unset($cart[$productId]);
            echo json_encode(array_values($cart));
            break;

        case 'clear':
            $_SESSION['cart'] = [];
            echo json_encode([]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Geçersiz sepet işlemi.']);
            break;
    }
}


function handleOrderActions($action, $method, $id) {
    $orders = getJsonData('orders.json');
    $input = json_decode(file_get_contents('php://input'), true);

    // Tüm Siparişleri Listele (GET /orders)
    if ($method === 'GET' && empty($id) && empty($action)) {
        echo json_encode(array_values($orders)); // array_values eklenmeli
        return;
    }

    // Tek Sipariş Detayı Getir (GET /orders/{id})
    if ($method === 'GET' && $id && empty($action)) {
        $foundOrder = null;
        foreach ($orders as $order) {
            if ($order['id'] == $id) {
                $foundOrder = $order;
                break;
            }
        }
        if ($foundOrder) {
            echo json_encode($foundOrder);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Sipariş bulunamadı.']);
        }
        return;
    }

    // Siparişten Ürün Silme (POST /orders/remove_item)
    if ($method === 'POST' && $action === 'remove_item') {
        $orderId = $input['orderId'] ?? null;
        $itemIndex = $input['itemIndex'] ?? null; // VEYA itemId gönderilebilir
        $orderIndex = -1;

        if ($orderId === null || $itemIndex === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Eksik parametre (orderId ve itemIndex gerekli).']);
            return;
        }

        // Siparişi bul
        foreach ($orders as $idx => $order) {
            if ($order['id'] == $orderId) {
                $orderIndex = $idx;
                break;
            }
        }

        if ($orderIndex !== -1 && isset($orders[$orderIndex]['items'][$itemIndex])) {
            // Silinecek ürünü al
            $itemToRemove = $orders[$orderIndex]['items'][$itemIndex];
            $productId = $itemToRemove['id'] ?? ($itemToRemove['productId'] ?? null);
            $quantity = $itemToRemove['quantity'];

            // Stokları geri yükle (eğer ürün ID'si ve miktar geçerliyse)
            if ($productId && $quantity > 0) {
                $products = getJsonData('products.json');
                $productFound = false;
                foreach ($products as &$product) {
                    if ($product['id'] == $productId) {
                        $product['stock'] += $quantity;
                        $productFound = true;
                        break;
                    }
                }
                unset($product); // Referansı kaldır
                if ($productFound) {
                    saveJsonData('products.json', $products);
                }
            }

            // Ürünü siparişten çıkar
            array_splice($orders[$orderIndex]['items'], $itemIndex, 1);

            // Sipariş toplamını yeniden hesapla
            $newTotal = array_reduce($orders[$orderIndex]['items'], fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
            $orders[$orderIndex]['total'] = $newTotal;

            // Eğer siparişte hiç ürün kalmadıysa siparişi silmeyi düşünebiliriz
            // VEYA sipariş durumunu "İptal Edildi" yapabiliriz. Şimdilik sadece ürünü siliyoruz.

            saveJsonData('orders.json', $orders);
            echo json_encode(['success' => true, 'message' => 'Ürün siparişten silindi ve stok güncellendi.']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Sipariş veya belirtilen index\'teki ürün bulunamadı.']);
        }
        return;
    }

    // Yeni Sipariş Oluştur (POST /orders)
    if ($method === 'POST' && empty($id) && empty($action)) {
        // Oturum kontrolü
        if (!isset($_SESSION['user']['id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Sipariş vermek için giriş yapmalısınız.']);
            exit;
        }
        // Sepet kontrolü
        if (empty($_SESSION['cart'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Sepetiniz boş.']);
            exit;
        }

        // Gelen veriyi kontrol et (customer bilgileri var mı?)
        if (!isset($input['customer']) || !is_array($input['customer']) || empty($input['customer']['name']) || empty($input['customer']['address'])) {
             http_response_code(400);
             echo json_encode(['success' => false, 'message' => 'Eksik müşteri bilgisi.']);
             exit;
        }
         if (!isset($input['paymentMethod']) || empty($input['paymentMethod'])) {
             http_response_code(400);
             echo json_encode(['success' => false, 'message' => 'Ödeme yöntemi belirtilmedi.']);
             exit;
        }

        $products = getJsonData('products.json');
        $cartItems = $_SESSION['cart']; // Kopyasını alalım, stok kontrolü için

        // Stok kontrolü ve düşme işlemi
        $insufficientStock = [];
        foreach ($cartItems as $cartItemId => $cartItem) {
            $foundProduct = false;
            foreach ($products as $index => $product) {
                if ($product['id'] == $cartItem['id']) {
                    $foundProduct = true;
                    if ($product['stock'] < $cartItem['quantity']) {
                        // Stok yetersiz
                        $insufficientStock[] = $product['name'] . ' (Stok: ' . $product['stock'] . ', İstenen: ' . $cartItem['quantity'] . ')';
                    } else {
                        // Stoğu düş
                        $products[$index]['stock'] -= $cartItem['quantity'];
                        // Toplam satış adedini artır (varsa)
                        $products[$index]['totalSold'] = ($products[$index]['totalSold'] ?? 0) + $cartItem['quantity'];
                    }
                    break;
                }
            }
            if (!$foundProduct) {
                 // Sepetteki ürün products.json'da bulunamadı (nadiren olmalı)
                 $insufficientStock[] = $cartItem['name'] . ' (Ürün bulunamadı)';
            }
        }

        // Eğer stokta olmayan ürün varsa hata ver
        if (!empty($insufficientStock)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Aşağıdaki ürünler için stok yetersiz: ' . implode(', ', $insufficientStock)
            ]);
            exit;
        }

        // Stoklar yeterliyse ve düşüldüyse:
        // Yeni sipariş ID'si oluştur
        $lastId = empty($orders) ? 100 : max(array_column($orders, 'id')); // Sipariş no 101'den başlasın
        // Toplam tutarı hesapla (güvenlik için sunucuda tekrar hesapla)
        $total = 0;
        $orderItems = [];
         foreach ($cartItems as $cartItemId => $cartItem) {
             // Ürünün güncel fiyatını products.json'dan al
             $currentPrice = $cartItem['price']; // Varsayılan olarak sepetteki fiyat
             foreach($products as $p) { if ($p['id'] == $cartItemId) { $currentPrice = $p['price']; break; } }
             $total += $currentPrice * $cartItem['quantity'];
             $orderItems[] = [ // Siparişe eklenecek item formatı
                 'id' => $cartItem['id'],
                 'name' => $cartItem['name'],
                 'price' => $currentPrice, // Güncel fiyatı kaydet
                 'quantity' => $cartItem['quantity']
             ];
         }


        // Yeni sipariş objesini oluştur
        $newOrder = [
            'id' => $lastId + 1,
            'userId' => $_SESSION['user']['id'],
            'customerInfo' => $input['customer'], // Gelen tüm müşteri bilgisini sakla
            'paymentMethod' => $input['paymentMethod'],
            'items' => $orderItems, // array_values($_SESSION['cart']) yerine hesaplanmış items
            'total' => round($total, 2), // Hesaplanan toplam
            'date' => date('c'), // ISO 8601 formatında tarih
            'status' => 'Beklemede', // Varsayılan durum
            'shippingCarrier' => '', // Boş kargo bilgileri
            'shippingTracking' => ''
        ];

        // Siparişi ve güncellenmiş ürünleri kaydet
        $orders[] = $newOrder;
        saveJsonData('orders.json', $orders);
        saveJsonData('products.json', $products); // Stokları düşülmüş haliyle kaydet

        // Sepeti temizle
        $_SESSION['cart'] = [];

        echo json_encode(['success' => true, 'message' => 'Siparişiniz başarıyla alındı.', 'orderId' => $newOrder['id']]);
        return;
    }

    // Sipariş Güncelle (PUT /orders/{id}) - Genellikle admin panelinden durum veya kargo bilgisi için
    if ($method === 'PUT' && $id) {
        $foundIndex = -1;
        foreach ($orders as $index => $order) {
            if ($order['id'] == $id) {
                $foundIndex = $index;
                break;
            }
        }
        if ($foundIndex !== -1) {
            // Güvenlik: Siparişin kritik verilerini (items, total, userId, date) PUT ile değiştirmeyi engelle
            unset($input['items'], $input['total'], $input['userId'], $input['date'], $input['id']);
            // Sadece izin verilen alanları (status, customerInfo, shippingCarrier, shippingTracking vb.) güncelle
            $orders[$foundIndex] = array_merge($orders[$foundIndex], $input);
            saveJsonData('orders.json', $orders);
            echo json_encode(['success' => true, 'message' => 'Sipariş güncellendi.', 'data' => $orders[$foundIndex]]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Sipariş bulunamadı.']);
        }
        return;
    }

    // Sipariş Sil (DELETE /orders/{id})
    if ($method === 'DELETE' && $id) {
        $orderToDelete = null;
        $orderIndex = null;
        foreach ($orders as $index => $order) {
            if ($order['id'] == $id) {
                $orderToDelete = $order;
                $orderIndex = $index;
                break;
            }
        }

        if ($orderToDelete) {
            // İptal edilebilir durumdaysa stokları geri yükle
            // Örneğin: 'Beklemede', 'Ödeme Bekleniyor', 'İptal Edildi' durumları
            // 'Kargoda' veya 'Teslim Edildi' ise stokları geri yüklememek daha mantıklı olabilir.
            if (in_array($orderToDelete['status'], ['Beklemede', 'Ödeme Bekleniyor', 'İptal Edildi'])) {
                $products = getJsonData('products.json');
                $productUpdated = false;
                foreach ($orderToDelete['items'] as $item) {
                    $productId = $item['id'] ?? ($item['productId'] ?? null);
                    if ($productId && $item['quantity'] > 0) {
                        foreach ($products as &$product) {
                            if ($product['id'] == $productId) {
                                $product['stock'] += $item['quantity'];
                                $productUpdated = true;
                                break;
                            }
                        }
                        unset($product); // Referansı kaldır
                    }
                }
                if ($productUpdated) {
                    saveJsonData('products.json', $products);
                }
            }

            // Siparişi sil
            array_splice($orders, $orderIndex, 1);
            saveJsonData('orders.json', $orders);
            echo json_encode(['success' => true, 'message' => 'Sipariş silindi. Gerekliyse stoklar güncellendi.']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Sipariş bulunamadı.']);
        }
        return;
    }

     // Eğer action belirtilmiş ama yukarıdaki koşullara uymuyorsa (örn: GET /orders/some_action)
    if (!empty($action)){
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Geçersiz sipariş işlemi veya parametre.']);
    }
}


// Genel Kaynak İşlemleri (GET, POST, PUT, DELETE by ID, DELETE by IDs)
function handleResourceActions($filename, $method, $id) {
    $data = getJsonData($filename);
    $input = json_decode(file_get_contents('php://input'), true);

    // Tüm Kayıtları Listele (GET /resource)
    if ($method === 'GET' && empty($id)) {
        // Kullanıcı listeleniyorsa şifreleri kaldır (handleUserActions içinde zaten yapılıyor ama burada da olabilir)
        if ($filename === 'users.json') {
             foreach ($data as &$item) { unset($item['password'], $item['hashedPassword']); }
             unset($item);
        }
        echo json_encode(array_values($data)); // array_values eklenmeli
        return;
    }

    // Tek Kayıt Getir (GET /resource/{id})
    if ($method === 'GET' && $id) {
        $foundItem = null;
        foreach($data as $item) { if ($item['id'] == $id) { $foundItem = $item; break; } }
        if ($foundItem) {
             if ($filename === 'users.json') { unset($foundItem['password'], $foundItem['hashedPassword']); }
            echo json_encode($foundItem);
        } else { http_response_code(404); echo json_encode(['success' => false, 'message' => 'Kayıt bulunamadı.']); }
        return;
    }

    // Yeni Kayıt Ekle (POST /resource)
    if ($method === 'POST') {
        $lastId = empty($data) ? 0 : max(array_column($data, 'id'));
        $newItem = $input;
        $newItem['id'] = $lastId + 1;
        // Eğer kullanıcı ekleniyorsa ve şifre varsa hashle (handleUserActions içinde zaten yapılıyor)
        if ($filename === 'users.json' && !empty($newItem['password'])) { $newItem['password'] = password_hash($newItem['password'], PASSWORD_DEFAULT); }
        // Kayıt tarihi ekle (isteğe bağlı)
        // $newItem['createdAt'] = date('c');
        $data[] = $newItem;
        saveJsonData($filename, $data);
        if ($filename === 'users.json') unset($newItem['password']); // Yanıttan şifreyi kaldır
        echo json_encode(['success' => true, 'message' => 'Kayıt eklendi.', 'data' => $newItem]);
        return;
    }

    // Kayıt Güncelle (PUT /resource/{id})
    if ($method === 'PUT' && $id) {
        $foundIndex = -1;
        foreach ($data as $index => $item) { if ($item['id'] == $id) { $foundIndex = $index; break; } }
        if ($foundIndex !== -1) {
             // Güvenlik: ID değiştirilemez
             unset($input['id']);
             // Şifre boşsa güncelleme (handleUserActions içinde zaten yapılıyor)
             if ($filename === 'users.json' && isset($input['password']) && empty($input['password'])) { unset($input['password']); }

            $updatedItem = array_merge($data[$foundIndex], $input);
            // Yeni şifre varsa hashle (handleUserActions içinde zaten yapılıyor)
            if ($filename === 'users.json' && !empty($input['password'])) { $updatedItem['password'] = password_hash($input['password'], PASSWORD_DEFAULT); }

            $data[$foundIndex] = $updatedItem;
            saveJsonData($filename, $data);
            if ($filename === 'users.json') unset($updatedItem['password']); // Yanıttan şifreyi kaldır
            echo json_encode(['success' => true, 'message' => 'Kayıt güncellendi.', 'data' => $updatedItem]);
        } else { http_response_code(404); echo json_encode(['success' => false, 'message' => 'Kayıt bulunamadı.']); }
        return;
    }

    // Kayıt Sil (DELETE /resource/{id} veya DELETE /resource body:{ids:[...]})
    if ($method === 'DELETE') {
        $idsToDelete = [];
        if ($id) { // Tek ID URL'den geliyorsa
            $idsToDelete[] = (int)$id;
        } elseif (isset($input['ids']) && is_array($input['ids'])) { // Toplu ID body'den geliyorsa
            $idsToDelete = array_map('intval', $input['ids']);
        }

        if (empty($idsToDelete)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Silinecek ID belirtilmedi.']);
            return;
        }

        $initialCount = count($data);
        // Belirtilen ID'leri içermeyenleri filtrele
        $data = array_filter($data, fn($item) => !in_array($item['id'], $idsToDelete, true));

        // Eğer en az bir kayıt silindiyse dosyayı kaydet
        if (count($data) < $initialCount) {
            saveJsonData($filename, $data);
            http_response_code(204); // Başarılı, içerik yok
        } else {
            // Hiçbir kayıt silinmediyse (ID'ler bulunamadı)
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Silinecek kayıt bulunamadı.']);
        }
        return;
    }

    // Diğer metodlar desteklenmiyorsa
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Desteklenmeyen metod: ' . $method]);
}

// Favorites için handleFavoriteActions fonksiyonunu tekrar eklemeye gerek yok, en başta tanımlı.
// Eğer dosyanın sonunda fazladan bir handleFavoriteActions tanımı varsa silinmeli.

?>