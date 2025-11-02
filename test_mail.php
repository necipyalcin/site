<?php
// DOSYA ADI: test_mail.php

$gonderilecek_adres = "yagmurbilgisayaradana@hotmail.com"; // Bildirimin geleceği adres
$konu = "PHP E-posta Gönderme Testi";
$mesaj = "Merhaba, bu e-posta sunucunuzdaki PHP mail() fonksiyonunun çalışıp çalışmadığını test etmek için gönderilmiştir.";

// ÖNEMLİ: Bu adresi hosting panelinizden oluşturduğunuz GERÇEK bir e-posta adresi ile değiştirin.
$gonderen_adresi = 'From: akinsofta@akinsoftadana.com.tr'; 

// E-postayı gönder ve sonucu ekrana yazdır
if (mail($gonderilecek_adres, $konu, $mesaj, $gonderen_adresi)) {
    echo "E-posta başarıyla gönderildi.";
} else {
    echo "E-posta gönderimi BAŞARISIZ OLDU. Sunucu yapılandırmanızı veya hosting firmanızın ayarlarını kontrol edin.";
}
?>