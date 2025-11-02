<?php
session_start();
// Giriş yapılmamışsa, login sayfasına yönlendir
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: yonetim-giris-a4b8c2.php'); // Giriş sayfanızın adını düzelttim
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesyonel E-Ticaret Paneli</title>
    <script src="https://cdn.tiny.cloud/1/ztmf8dqmghuo7jqpq95lv4ojhe4kvz6exkjjnld213iopb3v/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <link rel="icon" type="image/jpeg" href="/Resimler/logo.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .nav-item.active { background-color: #eef2ff; color: #4f46e5; font-weight: 600; }
        .tab-content, .modal { display: none; }
        .tab-content.active, .modal.active { display: block; }
        .modal.active { display: flex; }
        #toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.75rem; }
        .toast { padding: 1rem; border-radius: 0.5rem; color: white; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; opacity: 0; transform: translateX(100%); animation: slideIn 0.5s forwards, fadeOut 0.5s 4.5s forwards; }
        .toast.success { background-color: #28a745; }
        .toast.error { background-color: #dc3545; }
        .toast.info { background-color: #17a2b8; }
        @keyframes slideIn { to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeOut { to { opacity: 0; transform: translateX(100%); } }
    </style>
</head>
<body class="bg-gray-100">

    <div id="toast-container"></div>

    <div id="adminPanel">
        <div class="flex h-screen bg-gray-100">
            <aside class="w-64 bg-white shadow-md flex flex-col">
                <div class="p-4 border-b"><h1 class="text-xl font-bold text-indigo-600">E-Ticaret Paneli</h1></div>
                <nav class="flex-1 p-2 space-y-1 overflow-y-auto">
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="dashboard"><i class="fas fa-tachometer-alt w-6 mr-3"></i>Dashboard</button>
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="products"><i class="fas fa-box w-6 mr-3"></i>Ürünler</button>
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="categories"><i class="fas fa-tags w-6 mr-3"></i>Kategoriler</button>
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="orders"><i class="fas fa-shopping-cart w-6 mr-3"></i>Siparişler</button>
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="users"><i class="fas fa-users w-6 mr-3"></i>Müşteriler</button>
                    
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="management">
                        <i class="fas fa-cog w-6 mr-3"></i>Yönetim
                    </button>
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="banners">
                        <i class="fas fa-images w-6 mr-3"></i>Banner Yönetimi
                    </button>
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="showcase">
                        <i class="fas fa-star w-6 mr-3"></i>Vitrin Ürünleri
                    </button>
                    
                    
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="vitrinBanners">
                        <i class="fas fa-grip-horizontal w-6 mr-3"></i>Vitrin Bannerları
                    </button>
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="pages">
                        <i class="fas fa-file-alt w-6 mr-3"></i>Dinamik Sayfalar
                    </button>
                    
                    <button class="nav-item w-full text-left p-3 rounded-lg flex items-center" data-tab="xmlIntegration">
                   
                        <i class="fas fa-sync-alt w-6 mr-3"></i>XML Entegrasyonu
                    </button>
                </nav>
                <div class="p-2 border-t">
                    <button id="logoutBtn" class="w-full text-left p-3 rounded-lg hover:bg-gray-100 flex items-center">
                        <i class="fas fa-sign-out-alt w-6 mr-3"></i>Çıkış Yap
                    </button>
                </div>
            </aside>
            <main class="flex-1 p-8 overflow-y-auto">
                <div id="dashboard" class="tab-content"></div>
                <div id="products" class="tab-content"></div>
                <div id="categories" class="tab-content"></div>
                <div id="orders" class="tab-content"></div>
                <div id="users" class="tab-content"></div>
                <div id="management" class="tab-content"></div>
                <div id="banners" class="tab-content"></div>
                <div id="showcase" class="tab-content"></div>
                <div id="xmlIntegration" class="tab-content"></div>
                <div id="vitrinBanners" class="tab-content"></div>
                <div id="pages" class="tab-content"></div>
            </main>
            </main>
            </div>
    </div>

    <div id="productModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50 overflow-y-auto p-4"><div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-xl max-h-full overflow-y-auto"><h3 class="text-xl font-bold mb-4" id="productModalTitle"></h3><form id="productForm" class="space-y-4"><input type="hidden" id="productId"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label>Ürün Adı</label><input type="text" id="productName" class="w-full p-2 border rounded" required></div><div><label>Marka</label><input type="text" id="productBrand" class="w-full p-2 border rounded"></div><div><label>Ana Kategori</label><select id="productCategoryId" class="w-full p-2 border rounded" required><option value="">Seçin...</option></select></div><div><label>Alt Kategori</label><select id="productSubCategoryId" class="w-full p-2 border rounded"><option value="">Seçin...</option></select></div><div><label>Fiyat (₺)</label><input type="number" id="productPrice" step="0.01" class="w-full p-2 border rounded" required></div><div><label>Eski Fiyat (₺)</label><input type="number" id="productOldPrice" step="0.01" class="w-full p-2 border rounded"></div><div><label>Stok</label><input type="number" id="productStock" class="w-full p-2 border rounded" required></div><div><label>Resim URL</label><input type="url" id="productImage" class="w-full p-2 border rounded"></div></div><div><label>Açıklama</label><textarea id="productDescription" rows="3" class="w-full p-2 border rounded"></textarea></div><div><label>Özellikler (Her satıra bir özellik)</label><textarea id="productFeatures" rows="4" class="w-full p-2 border rounded"></textarea></div><div class="flex justify-end space-x-2 pt-2"><button type="button" onclick="adminPanel.hideModal('product')" class="bg-gray-200 px-4 py-2 rounded">İptal</button><button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Kaydet</button></div></form></div></div>
    
    <div id="categoryModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50"><div class="bg-white rounded-lg p-6 w-full max-w-lg shadow-xl"><h3 class="text-xl font-bold mb-4" id="categoryModalTitle"></h3><form id="categoryForm" class="space-y-3"><input type="hidden" id="categoryId"><input type="text" id="categoryName" placeholder="Kategori Adı" class="w-full p-2 border rounded" required><select id="categoryParentId" class="w-full p-2 border rounded"><option value="">Ana Kategori (Boş Bırakın)</option></select><div class="flex justify-end space-x-2 pt-2"><button type="button" onclick="adminPanel.hideModal('category')" class="bg-gray-200 px-4 py-2 rounded">İptal</button><button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Kaydet</button></div></form></div></div>
    
    <div id="userModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50 overflow-y-auto p-4"><div class="bg-white rounded-lg p-6 w-full max-w-3xl shadow-xl max-h-full overflow-y-auto"><h3 class="text-xl font-bold mb-6 border-b pb-2" id="userModalTitle"></h3><form id="userForm" class="space-y-6"><input type="hidden" id="userId"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="font-medium">Ad</label><input type="text" id="userName" class="w-full p-2 border rounded mt-1" required></div><div><label class="font-medium">Soyad</label><input type="text" id="userSurname" class="w-full p-2 border rounded mt-1" required></div><div><label class="font-medium">E-posta</label><input type="email" id="userEmail" class="w-full p-2 border rounded mt-1" required></div><div><label class="font-medium">Telefon</label><input type="tel" id="userPhone" class="w-full p-2 border rounded mt-1"></div><div><label class="font-medium">Yeni Şifre</label><input type="password" id="userPassword" class="w-full p-2 border rounded mt-1"><p class="text-xs text-gray-500 mt-1">Değiştirmek istemiyorsanız boş bırakın</p></div></div><div class="space-y-2 p-4 border rounded-md"><h4 class="font-semibold text-lg">Teslimat Adresi</h4><div><label>Adres</label><textarea id="userAddress" rows="2" class="w-full p-2 border rounded mt-1"></textarea></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label>İl (Şehir)</label><input type="text" id="userCity" class="w-full p-2 border rounded mt-1"></div><div><label>İlçe</label><input type="text" id="userDistrict" class="w-full p-2 border rounded mt-1"></div></div></div><div class="space-y-2 p-4 border rounded-md"><h4 class="font-semibold text-lg">Fatura Adresi</h4><div><label>Fatura Adresi</label><textarea id="userBillingAddress" rows="2" class="w-full p-2 border rounded mt-1"></textarea></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label>Fatura İli (Şehir)</label><input type="text" id="userBillingCity" class="w-full p-2 border rounded mt-1"></div><div><label>Fatura İlçesi</label><input type="text"id="userBillingDistrict" class="w-full p-2 border rounded mt-1"></div></div></div><div class="flex justify-end space-x-2 pt-4"><button type="button" onclick="adminPanel.hideModal('user')" class="bg-gray-200 px-4 py-2 rounded">İptal</button><button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Kaydet</button></div></form></div></div>
    
    <div id="orderModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50 overflow-y-auto p-4"><div class="bg-white rounded-lg p-6 w-full max-w-4xl shadow-xl max-h-full overflow-y-auto"><div class="flex justify-between items-center mb-4"><h3 class="text-xl font-bold" id="orderModalTitle">Sipariş Detayı</h3><button type="button" onclick="adminPanel.hideModal('order')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button></div><div id="orderDetailsContent"></div></div></div>
    
    <div id="bannerModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50 overflow-y-auto p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-xl max-h-full overflow-y-auto">
            <h3 class="text-xl font-bold mb-4" id="bannerModalTitle">Banner Ekle/Düzenle</h3>
            <form id="bannerForm" class="space-y-4">
                <input type="hidden" id="bannerId">
                <div>
                    <label for="bannerSlot" class="block text-sm font-medium text-gray-700">Banner Slot (Sıra No)</label>
                    <input type="number" id="bannerSlot" min="1" max="10" class="w-full p-2 border rounded mt-1" placeholder="1-10 arası bir sayı" required>
                </div>
                <div>
                    <label for="bannerImage" class="block text-sm font-medium text-gray-700">Resim URL</label>
                    <input type="url" id="bannerImage" class="w-full p-2 border rounded mt-1" placeholder="https://ornek.com/resim.jpg" required>
                </div>
                <div>
                    <label for="bannerDescription" class="block text-sm font-medium text-gray-700">Açıklama (Başlık)</label>
                    <input type="text" id="bannerDescription" class="w-full p-2 border rounded mt-1" placeholder="Harika Fırsatlar!">
                </div>
                
                <div>
                    <label for="bannerLinkType" class="block text-sm font-medium text-gray-700">Link Türü</label>
                    <select id="bannerLinkType" class="w-full p-2 border rounded mt-1">
                        <option value="custom">Özel Link</option>
                        <option value="product">Mevcut Üründen Seç</option>
                    </select>
                </div>
                
                <div id="bannerCustomLinkGroup">
                    <label for="bannerLink" class="block text-sm font-medium text-gray-700">Yönlendirme Linki (URL)</label>
                    <input type="url" id="bannerLink" class="w-full p-2 border rounded mt-1" placeholder="https://akinsoftadana.com.tr/urunler">
                </div>
                
                <div id="bannerProductLinkGroup" class="hidden space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Ürün Seç</label>
                    <input type="text" id="bannerProductSearch" class="w-full p-2 border rounded" placeholder="Bağlanacak ürünü ara...">
                    <div id="bannerProductList" class="max-h-40 overflow-y-auto border rounded divide-y"></div>
                    <input type="hidden" id="bannerProductId">
                    <p id="bannerSelectedProduct" class="text-sm font-medium text-green-700"></p>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="adminPanel.hideModal('banner')" class="bg-gray-200 px-4 py-2 rounded">İptal</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="bulk-deleteModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-70 items-center justify-center z-[60]">
        <div class="bg-white rounded-lg p-8 w-full max-w-md shadow-2xl">
            <h3 class="text-xl font-bold text-red-700 mb-4"><i class="fas fa-exclamation-triangle mr-2"></i>Emin misiniz?</h3>
            <p class="text-gray-600 mb-4">Bu işlem geri alınamaz. Seçili tüm öğeleri kalıcı olarak silmek istediğinizden emin misiniz?</p>
            <p class="text-sm text-gray-700 mb-2">Devam etmek için aşağıdaki alana <strong class="text-red-700">SİL</strong> yazın.</p>
            <input type="text" id="bulk-delete-input" placeholder="SİL" class="w-full p-2 border border-gray-300 rounded mb-4 uppercase">
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="adminPanel.hideModal('bulk-delete')" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">İptal</button>
                <button id="confirm-bulk-delete-btn" class="bg-red-300 text-white px-4 py-2 rounded-lg cursor-not-allowed" disabled>Onayla ve Sil</button>
            </div>
        </div>
    </div>
    
    <div id="bulk-price-updateModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg shadow-xl">
            <h3 class="text-xl font-bold mb-4" id="bulk-price-updateModalTitle">Toplu Fiyat Güncelleme</h3>
            <form id="bulk-price-update-form">
                <div class="space-y-4">
                    <div>
                        <label for="price-update-type" class="block text-sm font-medium text-gray-700">Güncelleme Türü</label>
                        <select id="price-update-type" class="w-full p-2 border rounded mt-1">
                            <option value="percent_increase">Yüzdesel Artış (%)</option>
                            <option value="percent_decrease">Yüzdesel Azalış (%)</option>
                            <option value="fixed_increase">Sabit Artış (₺)</option>
                            <option value="fixed_decrease">Sabit Azalış (₺)</option>
                        </select>
                    </div>
                    <div>
                        <label for="price-update-amount" class="block text-sm font-medium text-gray-700">Değer</label>
                        <input type="number" id="price-update-amount" step="0.01" class="w-full p-2 border rounded mt-1" required>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-4 mt-2">
                    <button type="button" onclick="adminPanel.hideModal('bulk-price-update')" class="bg-gray-200 px-4 py-2 rounded">İptal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Güncelle</button>
                </div>
            </form>
        </div>
    </div>

    <div id="bulk-stock-updateModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg shadow-xl">
            <h3 class="text-xl font-bold mb-4" id="bulk-stock-updateModalTitle">Toplu Stok Güncelle</h3>
            <form id="bulk-stock-update-form" class="space-y-4">
                <p class="text-sm text-gray-600">Seçili olan ürünler için yeni stok miktarını girin.</p>
                <div>
                    <label for="stock-update-amount" class="block text-sm font-medium text-gray-700">Yeni Stok Miktarı</label>
                    <input type="number" id="stock-update-amount" class="w-full p-2 border rounded mt-1" required>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="adminPanel.hideModal('bulk-stock-update')" class="bg-gray-200 px-4 py-2 rounded">İptal</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Stokları Güncelle</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="customer-reportModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl shadow-xl max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-xl font-bold" id="customer-reportModalTitle">Müşteri Raporu</h3>
                <button onclick="adminPanel.hideModal('customer-report')" class="text-gray-500">&times;</button>
            </div>
            <div id="customer-report-content" class="overflow-y-auto">
                </div>
            <div class="mt-4 pt-4 border-t text-right">
                 <button id="export-pdf-btn" class="bg-red-600 text-white px-4 py-2 rounded">PDF Olarak İndir</button>
            </div>
        </div>
    </div>
    <div id="vitrinBannerModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50 overflow-y-auto p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-xl max-h-full overflow-y-auto">
            <h3 class="text-xl font-bold mb-4" id="vitrinBannerModalTitle">Vitrin Banner Ekle/Düzenle</h3>
            <form id="vitrinBannerForm" class="space-y-4">
                <input type="hidden" id="vitrinBannerId">
                <div>
                    <label for="vitrinBannerSlot" class="block text-sm font-medium text-gray-700">Banner Slot (Sıra No)</label>
                    <input type="number" id="vitrinBannerSlot" min="1" max="20" class="w-full p-2 border rounded mt-1" placeholder="1-20 arası bir sayı" required>
                </div>
                <div>
                    <label for="vitrinBannerImage" class="block text-sm font-medium text-gray-700">Resim URL</label>
                    <input type="url" id="vitrinBannerImage" class="w-full p-2 border rounded mt-1" placeholder="https://ornek.com/resim.jpg" required>
                </div>
                <div>
                    <label for="vitrinBannerDescription" class="block text-sm font-medium text-gray-700">Açıklama (Başlık)</label>
                    <input type="text" id="vitrinBannerDescription" class="w-full p-2 border rounded mt-1" placeholder="Öne Çıkan Ürün">
                </div>
                
                <div>
                    <label for="vitrinBannerLinkType" class="block text-sm font-medium text-gray-700">Link Türü</label>
                    <select id="vitrinBannerLinkType" class="w-full p-2 border rounded mt-1">
                        <option value="custom">Özel Link</option>
                        <option value="product">Mevcut Üründen Seç</option>
                    </select>
                </div>
                
                <div id="vitrinBannerCustomLinkGroup">
                    <label for="vitrinBannerLink" class="block text-sm font-medium text-gray-700">Yönlendirme Linki (URL)</label>
                    <input type="url" id="vitrinBannerLink" class="w-full p-2 border rounded mt-1" placeholder="https://akinsoftadana.com.tr/urunler">
                </div>
                
                <div id="vitrinBannerProductLinkGroup" class="hidden space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Ürün Seç</label>
                    <input type="text" id="vitrinBannerProductSearch" class="w-full p-2 border rounded" placeholder="Bağlanacak ürünü ara...">
                    <div id="vitrinBannerProductList" class="max-h-40 overflow-y-auto border rounded divide-y"></div>
                    <input type="hidden" id="vitrinBannerProductId">
                    <p id="vitrinBannerSelectedProduct" class="text-sm font-medium text-green-700"></p>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="adminPanel.hideModal('vitrinBanner')" class="bg-gray-200 px-4 py-2 rounded">İptal</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Kaydet</button>
                </div>
            </form>
        </div>
    </div>

<div id="pageModal" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50 overflow-y-auto p-4">
    <div class="bg-white rounded-lg p-6 w-full max-w-5xl shadow-xl max-h-full overflow-y-auto"> <h3 class="text-xl font-bold mb-4" id="pageModalTitle">Sayfa Düzenle</h3>
        <form id="pageForm" class="space-y-4">
            <input type="hidden" id="pageId">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="pageTitle" class="block text-sm font-medium text-gray-700">Sayfa Başlığı</label>
                    <input type="text" id="pageTitle" class="w-full p-2 border rounded mt-1" required>
                </div>
                <div>
                    <label for="pageSlug" class="block text-sm font-medium text-gray-700">Sayfa URL (Slug)</label>
                    <input type="text" id="pageSlug" class="w-full p-2 border rounded mt-1" placeholder="hakkimizda" required>
                </div>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="pageShowInMenu" name="pageShowInMenu" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="pageShowInMenu" class="ml-2 block text-sm font-medium text-gray-700">Ana Menüde Göster</label>
            </div>
            
            <div id="contactPageFields" class="space-y-4 p-4 border border-blue-200 rounded-lg hidden">
                <h4 class="text-lg font-semibold text-blue-700">İletişim Sayfası Ek Bilgileri</h4>
                <div>
                    <label for="pageAddress" class="block text-sm font-medium text-gray-700">Adres</label>
                    <textarea id="pageAddress" rows="2" class="w-full p-2 border rounded mt-1"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pagePhone" class="block text-sm font-medium text-gray-700">Telefon</label>
                        <input type="text" id="pagePhone" class="w-full p-2 border rounded mt-1">
                    </div>
                    <div>
                        <label for="pageContactEmail" class="block text-sm font-medium text-gray-700">E-posta</label>
                        <input type="email" id="pageContactEmail" class="w-full p-2 border rounded mt-1">
                    </div>
                </div>
                <div>
                    <label for="pageMapUrl" class="block text-sm font-medium text-gray-700">Google Harita Gömme (Embed) URL</label>
                    <input type="url" id="pageMapUrl" class="w-full p-2 border rounded mt-1" placeholder="http://googleusercontent.com/maps/google.com/0?...">
                </div>
            </div>
            
            <div>
                <label for="pageContent" class="block text-sm font-medium text-gray-700">Sayfa İçeriği</label>
                <textarea id="pageContent" rows="15"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="adminPanel.hideModal('page')" class="bg-gray-200 px-4 py-2 rounded">İptal</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Kaydet</button>
            </div>
        </form>
    </div>
</div>
    
    <script src="admin.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</body>
</html>