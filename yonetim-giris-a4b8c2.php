<?php
session_start();
$error = '';

// Eğer zaten giriş yapılmışsa, panele yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: panel.php');
    exit;
}

// Form gönderilmiş mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settingsPath = 'settings.json';
    if (!file_exists($settingsPath)) {
        $error = 'Sistem hatası: Ayar dosyası (settings.json) bulunamadı.';
    } else {
        $settings_content = file_get_contents($settingsPath);
        $settings = json_decode($settings_content, true);
        
        // ### YENİ GÜVENLİK KONTROLÜ ###
        // settings.json bozuk mu veya gerekli anahtarlar eksik mi diye kontrol et
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($settings) || !isset($settings['adminUser']) || !isset($settings['adminPassHash'])) {
            $error = 'Sistem hatası: settings.json dosyası bozuk veya eksik. Lütfen dosya içeriğini kontrol edin.';
        } else {
            // Kontrol başarılı, normal devam et
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Kullanıcı adı ve şifrelenmiş parolayı doğrula
            if ($username === $settings['adminUser'] && password_verify($password, $settings['adminPassHash'])) {
                // Giriş başarılı
                $_SESSION['admin_logged_in'] = true;
                header('Location: panel.php');
                exit;
            } else {
                // Giriş başarısız
                $error = 'Kullanıcı adı veya şifre yanlış.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli Girişi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/jpeg" href="/Resimler/logo.jpg">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg p-8 w-96 shadow-xl">
        <h2 class="text-2xl font-bold text-center mb-6">Admin Girişi</h2>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST"> 
            <input type="text" id="username" name="username" placeholder="Kullanıcı Adı" class="w-full p-2 border rounded mb-4" value="admin" required>
            <input type="password" id="password" name="password" placeholder="Şifre" class="w-full p-2 border rounded mb-4" required>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Giriş Yap</button>
        </form>
    </div>
</body>
</html>