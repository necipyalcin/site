<?php
// DOSYA ADI: hash_olustur.php

// Şifremiz (isterseniz 'admin123456' yerine başka bir şey de yazabilirsiniz)
$sifre = 'admin123456';

$hash = password_hash($sifre, PASSWORD_DEFAULT);

echo 'Kullanılacak Şifre: ' . $sifre . '<br><br>';
echo 'Lütfen bu kodun tamamını kopyalayın:<br><br>';
echo '<textarea rows="3" style="width: 100%; font-family: monospace; font-size: 16px;">' . $hash . '</textarea>';
echo '<br><br>Bu kodu kopyalayıp settings.json dosyasındaki "adminPassHash" değerine yapıştırın.';
?>