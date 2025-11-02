// DOSYA ADI: main.js (Stilmoto Tipi Yatay Carousel Eklendi + Banner Filtreleme Hatası Düzeltildi)
// *** KART DÜZENLEMESİ (Resim büyütme, Stok+Miktar aynı sıra, Butonlar aynı sıra) DÜZELTİLDİ ***

document.addEventListener('DOMContentLoaded', function () {
    let products = [];
    let wishlist = [];
    let categories = [];
    let cart = [];
    let currentUser = null;
    let currentCategoryId = 'all';
    let isFavoritesFiltered = false;
    let searchTerm = '';
    let sortBy = 'name';

    let currentFilteredList = []; 
    let renderedProductCount = 0; 
    const INITIAL_LOAD_COUNT = 8; 
    const LOAD_MORE_COUNT = 8; 
    let isLoadingMore = false; 
    
    // ### YENİ: Filtreleme için ###
    let activeFilters = {}; // Örn: { Marka: ['ASUS', 'HP'], Bellek: ['16GB'] }
    let allFilterOptions = {}; // Tüm ürünlerden çekilen ham filtre verileri

    const productList = document.getElementById('productList');
    const categoryMenu = document.getElementById('categoryMenu');
    const cartItemsEl = document.getElementById('cartItems');
    const cartTotalEl = document.getElementById('cartTotal');
    const cartCountEl = document.getElementById('cartCount');
    const cartCountFixedEl = document.getElementById('cartCountFixed');
    const wishlistCountEl = document.getElementById('wishlistCount');

    async function api(resource, action = '', method = 'GET', data = null) {
        let url = `public_api.php?resource=${resource}`;
        if (action) url += `&action=${action}`;
        const options = { method, headers: { 'Content-Type': 'application/json' } };
        if (data) options.body = JSON.stringify(data);
        
        try {
            const response = await fetch(url, options);
            if (response.status === 404) {
                 throw new Error(`API dosyası bulunamadı: ${url}.`);
            }
            const textResponse = await response.text();
            if (!textResponse) return []; 
            const result = JSON.parse(textResponse);
            if (!response.ok) throw new Error(result.message || 'Bir hata oluştu.');
            return result;
        } catch (error) {
            console.error(`API Hatası [${resource}/${action}]:`, error);
            showNotification(error.message || 'API ile iletişimde bir sorun oluştu.', 'error');
            throw error;
        }
    }
    
    async function fetchUserFavorites() {
        if (currentUser) {
            try {
                const result = await api('favorites', 'get');
                if (result && result.success && Array.isArray(result.data)) {
                    wishlist = result.data;
                } else {
                    console.error("Hatalı favori verisi alındı:", result);
                    wishlist = [];
                }
            } catch (e) {
                console.error("fetchUserFavorites HATA:", e);
                wishlist = []; 
            }
        } else {
            wishlist = [];
        }
        updateWishlistDisplay();
    }
     
async function initializePage() {
        AOS.init({ duration: 1000 });

        // 'pagesData' eklendi
        const [productsData, categoriesData, cartData, authStatus, bannersData, showcaseData, pagesData] = await Promise.all([
            api('products').catch(e => { console.error("Ürünler alınamadı:", e); return []; }), 
            api('categories').catch(e => { console.error("Kategoriler alınamadı:", e); return []; }), 
            api('cart', 'get').catch(e => { console.error("Sepet alınamadı:", e); return []; }), 
            api('users', 'status').catch(e => { console.error("Auth status alınamadı:", e); return { loggedIn: false }; }), 
            api('banners').catch(e => { console.error("Bannerlar alınamadı:", e); return []; }), 
            api('showcase').catch(e => { console.error("Vitrin alınamadı:", e); return []; }),
            api('pages').catch(e => { console.error("Dinamik sayfalar alınamadı:", e); return []; }) // YENİ EKLENDİ
        ]);

        products = productsData; 
        categories = categoriesData; 
        cart = cartData;

    // ### YENİ EKLENEN KOD BAŞLANGICI ###
    // URL'den gelen 'kategori' parametresini oku
    const urlParams = new URLSearchParams(window.location.search);
    const kategoriFromUrl = urlParams.get('kategori');
    
    // Eğer URL'de bir kategori varsa, global 'currentCategoryId' değişkenini güncelle
    if (kategoriFromUrl) {
        currentCategoryId = kategoriFromUrl; 
    }
    // ### YENİ EKLENEN KOD SONU ###

    window.allBanners = bannersData; 
        window.allShowcaseItems = showcaseData; 
        window.allPages = pagesData; // YENİ: Sayfaları globale kaydet

        renderBanners();      
        renderShowcase();     
        renderBestSellers(); 
        renderDynamicPagesMenu(); // YENİ: Menüyü oluşturmak için çağır

        // ... (initializePage fonksiyonunun geri kalanı) ...
// ... fonksiyonun kalanı aynı ...
        // ... main.js (initializePage fonksiyonu içi)
products = productsData; // Zaten var
categories = categoriesData; // Zaten var
cart = cartData; // Zaten var

renderBestSellers(); // ### YENİ EKLENEN SATIR ###

// ### YENİ: Filtreleri oluştur ###
// ... (diğer kodlar) ...
        
        // ### YENİ: Filtreleri oluştur ###
        // (Bu fonksiyonun çalışması için ürün verinizin "specs" objesi içermesi gerekir)
        generateFilterOptions(products);
        renderFilters();
        // ### YENİ SONU ###

        if (authStatus && authStatus.loggedIn) {
            currentUser = authStatus.user;
            await fetchUserFavorites();
            showUserInterface();
            checkForFavoriteDiscounts();
        } else {
            showAuthInterface();
        }

        renderCategories(categories); // Bu artık mobil menüyü de dolduracak
        updateCartDisplay();
        renderProducts(); 
        setupEventListeners(); // Bu artık mobil menü butonlarını da çalıştıracak
        setupSliders();
        checkUrlForCheckout();
    }

// ### YENİ FONKSİYON: Ürünlerden filtre seçeneklerini oluşturur ###
// ÖNEMLİ: Bu fonksiyon, her ürünün içinde product.specs.Marka, product.specs.Bellek gibi bir yapı olduğunu varsayar.
function generateFilterOptions(products) {
    // Filtrelemek istediğiniz başlıkları buraya yazın (Görsellerinizdekiler)
    const specKeys = [
        'Marka', 'İşlemci Ailesi', 'İşlemci', 'Bellek', 'Sabit Disk', 
        'Ekran Kartı', 'Ekran Kartı Chipset', 'Yazılım', 
        'Ekran Kartı Hafızası', 'Monitör', 'Dokunmatik', 'Renk', 'Model',
        'Yazıcı Tipi', 'Baskı Boyutu', 'Fax', 'Bağlantı Arabirimi'
    ];
    const filters = {};

    specKeys.forEach(key => {
        filters[key] = new Set();
    });

    for (const product of products) {
        if (product.specs) { // 'specs' objesinin var olduğunu varsayıyoruz
            for (const key of specKeys) {
                if (product.specs[key]) {
                    // Değer bir array ise (örn: ['2TB', '1TB SSD'])
                    if (Array.isArray(product.specs[key])) {
                        product.specs[key].forEach(val => filters[key].add(val));
                    } 
                    // Değer tek bir string ise
                    else if (typeof product.specs[key] === 'string' && product.specs[key].trim() !== '') {
                        filters[key].add(product.specs[key].trim());
                    }
                }
            }
        }
    }

    // Set'leri sıralanmış array'lere çevir
    allFilterOptions = {};
    for (const key in filters) {
        if (filters[key].size > 0) { // Sadece en az 1 seçeneği olan filtreleri ekle
            allFilterOptions[key] = Array.from(filters[key]).sort((a, b) => {
                // '16GB', '8GB' gibi değerleri doğru sıralamak için
                const numA = parseFloat(a);
                const numB = parseFloat(b);
                if (!isNaN(numA) && !isNaN(numB)) {
                    return numA - numB;
                }
                return a.localeCompare(b, 'tr');
            });
        }
    }
}

// ### YENİ FONKSİYON: Filtre akordiyonunu HTML olarak render eder ###
function renderFilters() {
    const container = document.getElementById('filter-accordion');
    if (!container) return;

    let html = '';
    for (const key in allFilterOptions) {
        const options = allFilterOptions[key];
        const optionsHTML = options.map(option => `
            <label class="flex items-center space-x-2 text-sm text-gray-600 p-1 rounded hover:bg-gray-100">
                <input type="checkbox" class="filter-checkbox rounded text-blue-600 focus:ring-blue-500" data-filter-key="${key}" value="${option}">
                <span>${option}</span>
            </label>
        `).join('');

        html += `
        <div class="filter-group">
            <button class="filter-group-toggle flex justify-between items-center w-full p-3 text-left font-semibold text-gray-700">
                <span>${key}</span>
                <i class="fas fa-chevron-down text-xs transition-transform"></i>
            </button>
            <div class="filter-group-content p-3 border-t border-gray-200" style="display: none;">
                <input type="text" class="filter-search w-full px-2 py-1 mb-2 border rounded text-sm text-gray-700" placeholder="${key} Ara...">
                <div class="filter-options max-h-48 overflow-y-auto pr-2">
                    ${optionsHTML}
                </div>
            </div>
        </div>
        `;
    }
    container.innerHTML = html;
    
    // Event listener'ları ekle
    addFilterEventListeners();
}

// ### YENİ FONKSİYON: Filtre elemanlarına event listener ekler ###
function addFilterEventListeners() {
    const container = document.getElementById('filter-accordion');
    if (!container) return;

    // Akordiyon açma/kapatma
    container.querySelectorAll('.filter-group-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const content = button.nextElementSibling;
            const isOpen = content.style.display === 'block';
            
            // İsteğe bağlı: Tıklayınca diğerlerini kapatabilirsiniz
            // container.querySelectorAll('.filter-group-content').forEach(c => c.style.display = 'none');
            // container.querySelectorAll('.filter-group-toggle').forEach(b => b.classList.remove('open'));
            
            content.style.display = isOpen ? 'none' : 'block';
            button.classList.toggle('open', !isOpen);
        });
    });

    // Checkbox'lar
    container.querySelectorAll('.filter-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const key = checkbox.dataset.filterKey;
            const value = checkbox.value;

            if (!activeFilters[key]) {
                activeFilters[key] = [];
            }

            if (checkbox.checked) {
                if (!activeFilters[key].includes(value)) {
                    activeFilters[key].push(value);
                }
            } else {
                activeFilters[key] = activeFilters[key].filter(v => v !== value);
                if (activeFilters[key].length === 0) {
                    delete activeFilters[key];
                }
            }
            
            // Ürün listesini yeniden render et
            renderProducts();
        });
    });

    // Filtre içi arama
    container.querySelectorAll('.filter-search').forEach(input => {
        input.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const optionsContainer = e.target.nextElementSibling;
            optionsContainer.querySelectorAll('label').forEach(label => {
                const labelText = label.textContent.toLowerCase();
                if (labelText.includes(searchTerm)) {
                    label.style.display = 'flex';
                } else {
                    label.style.display = 'none';
                }
            });
        });
    });
}


// ### DEĞİŞTİ ###: Ürün filtreleme mantığı (Detaylı filtreler ve string/number hatası DÜZELTİLDİ)
   function filterProducts() {
        if (!products) return [];
        let baseProducts = products;
        
        if (isFavoritesFiltered) {
            baseProducts = products.filter(p => wishlist.includes(p.id));
        }

        // ### KATEGORİ FİLTRELEME MANTIĞI (Alt Kategoriler için) ###
        let categoryIdsToMatch = [];
        
        if (currentCategoryId !== 'all') {
            const mainCatIdNum = parseInt(currentCategoryId, 10);
            categoryIdsToMatch.push(mainCatIdNum);
            
            const subcategories = categories.filter(c => c.parentId === mainCatIdNum);
            subcategories.forEach(sub => categoryIdsToMatch.push(sub.id));
        }

        let filtered = baseProducts.filter(product => {
            // 1. Kategori Kontrolü
            let matchesCategory = false;
            if (currentCategoryId === 'all') {
                matchesCategory = true;
            } else {
                matchesCategory = categoryIdsToMatch.includes(product.categoryId);
            }
            
            // 2. Arama Çubuğu Kontrolü
            const matchesSearch = product.name.toLowerCase().includes(searchTerm.toLowerCase());
            
            // ### YENİ: 3. SPECS FİLTRELEME MANTIĞI ###
            let matchesFilters = true;
            const filterKeys = Object.keys(activeFilters);

            if (filterKeys.length > 0) {
                // EĞER ürünün 'specs' objesi yoksa veya boşsa, filtrelerden geçemez
                if (!product.specs || Object.keys(product.specs).length === 0) {
                    matchesFilters = false;
                } else {
                    // Her bir filtre grubu için (Marka, Bellek, vs.)
                    matchesFilters = filterKeys.every(key => {
                        const selectedValues = activeFilters[key]; //örn: ['16GB', '32GB']
                        const productValue = product.specs[key]; //örn: '16GB' veya ['16GB', 'Diğer']
                        
                        if (!productValue) return false; // Üründe o özellik yoksa

                        // Ürünün değeri array ise (örn: ['A', 'B'])
                        if (Array.isArray(productValue)) {
                            // Seçilen değerlerden *herhangi biri* ürünün değer array'inde var mı?
                            return selectedValues.some(v => productValue.includes(v));
                        }
                        
                        // Ürünün değeri string ise (örn: '16GB')
                        // Seçilen değerler ('16GB', '32GB') içinde ürünün değeri var mı?
                        return selectedValues.includes(productValue);
                    });
                }
            }
            // ### YENİ FİLTRELEME SONU ###

            return matchesCategory && matchesSearch && matchesFilters;
        });

        filtered.sort((a, b) => {
            switch(sortBy) {
                case 'price-low': return a.price - b.price;
                case 'price-high': return b.price - a.price;
                default: return a.name.localeCompare(b.name);
            }
        });
        
        return filtered;
   }
   
   // ##################################################################
   // ### BAŞLANGIÇ: getProductCardHTML İSTEĞİNİZE GÖRE DÜZENLENDİ ###
   // ##################################################################
   function getProductCardHTML(p) {
       return `
    <div class="product-card group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-800 leading-snug h-20 overflow-hidden line-clamp-3">${p.name}</h3>
        </div>
        <div class="relative flex-shrink-0">
            <a href="urun.php?id=${p.id}" class="block">
                <img src="${p.image || './Resimler/pc.jpg'}" alt="${p.name}" class="w-full h-36 object-contain transition-transform duration-300 group-hover:scale-105">
            </a>
            <button onclick="window.toggleWishlist(${p.id})" class="absolute top-3 right-3 wishlist-btn ${window.isInWishlist(p.id) ? 'active' : ''} bg-white rounded-full p-2 shadow-md hover:scale-110 transition-transform" title="Favorilere Ekle">
                <i class="fas fa-heart text-red-500 text-lg"></i>
            </button>
        </div>
        <div class="p-4 flex flex-col flex-grow">
            
            <div class="mt-auto">
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        ${p.oldPrice && p.oldPrice > p.price ? `<span class="text-sm text-gray-500 line-through">${formatPrice(p.oldPrice)} ₺</span>` : ''}
                        <span class="text-xl font-bold ${p.oldPrice && p.oldPrice > p.price ? 'price-pulse-color' : 'text-blue-800'}">${formatPrice(p.price)} ₺</span>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-3">
                    <div class="stock-status ${p.stock > 10 ? 'stock-in' : (p.stock > 0 ? 'stock-low' : 'stock-out')} text-xs px-2 py-1 rounded-full font-semibold inline-block">
                       ${p.stock > 10 ? 'Stokta Var' : (p.stock > 0 ? `Son ${p.stock} adet` : 'Tükendi')}
                    </div>
                    
                    ${p.stock > 0 ? `
                    <div class="flex items-center">
                        <button onclick="window.updateCardQuantity(${p.id}, -1, ${p.stock})" class="bg-gray-200 text-gray-700 px-2 py-1 rounded-l-md text-xs hover:bg-gray-300">-</button>
                        <input type="text" id="quantity-${p.id}" class="w-10 text-center border-t border-b border-gray-200 py-1 text-sm text-blue-900" value="1" readonly>
                        <button onclick="window.updateCardQuantity(${p.id}, 1, ${p.stock})" class="bg-gray-200 text-gray-700 px-2 py-1 rounded-r-md text-xs hover:bg-gray-300">+</button>
                    </div>
                    ` : ''}
                </div>

                <div class="flex gap-2">
                    <a href="urun.php?id=${p.id}" class="flex-1 bg-blue-600 text-white px-2 py-2 rounded-lg text-sm hover:bg-blue-700 text-center transition-colors">Detaylar</a>
                    <button ${p.stock === 0 ? 'disabled' : ''} onclick="window.addToCartFromCard(${p.id})" class="flex-1 bg-green-600 text-white px-2 py-2 rounded-lg text-sm hover:bg-green-700 disabled:bg-gray-400 transition-colors">Sepete Ekle</button>
                </div>
                </div>
        </div>
    </div>`;
   }
   // ################################################################
   // ### BİTİŞ: getProductCardHTML İSTEĞİNİZE GÖRE DÜZENLENDİ ###
   // ################################################################
   
   // ... (displayMoreProducts fonksiyonu aynı kalıyor) ...
   function displayMoreProducts() {
        if (isLoadingMore) return; 

        const productsToAppend = currentFilteredList.slice(renderedProductCount, renderedProductCount + LOAD_MORE_COUNT);

        if (productsToAppend.length === 0) {
            return;
        }

        isLoadingMore = true; 

        const htmlToAppend = productsToAppend.map(getProductCardHTML).join('');
        
        productList.insertAdjacentHTML('beforeend', htmlToAppend);
        renderedProductCount += productsToAppend.length;

        isLoadingMore = false; 
   }

   // ... (renderProducts fonksiyonu aynı kalıyor) ...
   function renderProducts() {
        if (!productList) return;

        currentFilteredList = filterProducts();
        renderedProductCount = 0; 
        
        const productsToRender = currentFilteredList.slice(0, INITIAL_LOAD_COUNT);

        if (productsToRender.length === 0) {
            productList.innerHTML = `<div class="col-span-full text-center py-8"><p class="text-gray-600">Bu kriterlere uygun ürün bulunamadı.</p></div>`;
        } else {
           productList.innerHTML = productsToRender.map(getProductCardHTML).join('');
           renderedProductCount = productsToRender.length;
        }
        
        isLoadingMore = false;
    }

    window.isInWishlist = function(productId) {
        return wishlist.includes(productId);
    }

    // ... (toggleWishlist fonksiyonu aynı kalıyor) ...
    window.toggleWishlist = async function(productId) {
        if (!currentUser) {
            showNotification('Favorilere eklemek için lütfen giriş yapın.', 'error');
            window.openAuthModal('login');
            return;
        }
        try {
            const result = await api('favorites', 'toggle', 'POST', { productId });
            if (result && result.success && Array.isArray(result.data)) {
                wishlist = result.data;
                showNotification(result.message, 'success');
            } else {
                console.error('Favori güncellerken hatalı veri alındı:', result);
                wishlist = []; 
                showNotification('Favori listeniz güncellenemedi.', 'error');
            }
        } catch (error) {
            console.error('toggleWishlist HATA:', error);
            wishlist = []; 
        }
        updateWishlistDisplay();
        renderProducts(); 
    };

    window.toggleFavoriteFilter = function() {
        document.body.classList.remove('modal-open');
        isFavoritesFiltered = !isFavoritesFiltered;
        const heartButton = document.querySelector('#wishlistBtnContainer button');
        
        if (isFavoritesFiltered) {
            // Masaüstü menü
            document.querySelectorAll('.category-btn').forEach(btn => {
                const isActive = btn.dataset.categoryId === 'all';
                btn.classList.toggle('active', isActive);
            });
            
            // Aktif filtreleri ve kategoriyi sıfırla
            currentCategoryId = 'all';
            activeFilters = {}; // Aktif filtreleri temizle
            renderFilters(); // Filtre checkboxlarını temizle
            
            if (heartButton) heartButton.classList.add('active');
            showNotification('Favori ürünleriniz listeleniyor.', 'info');
        } else {
            if (heartButton) heartButton.classList.remove('active');
        }
        renderProducts();
    };
    
// ### YENİ YARDIMCI FONKSİYON: Kategori tıklamasını yönetir ###
function handleCategoryClick(categoryId) {
    currentCategoryId = categoryId;
    
    // Varsa favori filtresini kaldır
    isFavoritesFiltered = false;
    const heartButton = document.querySelector('#wishlistBtnContainer button');
    if (heartButton) heartButton.classList.remove('active');
    
    // Filtreleri sıfırla
    activeFilters = {};
    renderFilters(); // Checkbox'ları temizlemek için yeniden render et
    
    renderProducts(); // Ürün listesini yenile
}

// ### DEĞİŞTİ ###: Alt kategorileri ve MOBİL MENÜYÜ oluşturacak şekilde güncellendi
// ### renderCategories FONKSİYONUNU BUNUNLA DEĞİŞTİRİN ###
// main.js dosyanızdaki eski renderCategories fonksiyonunu silip BUNU YAPIŞTIRIN:

function renderCategories(categoriesData) {
    if (!categoriesData || categoriesData.length === 0) {
        console.warn("Kategoriler yüklenemedi veya boş.");
        return;
    }

    // productListElement, e-ticaret.php'de var, index.php'de yok.
    const productListElement = document.getElementById('productList'); 

    // Kategorileri grupla
    const topLevelCategories = categoriesData.filter(c => !c.parentId);
    const subcategoriesMap = new Map();
    categoriesData.filter(c => c.parentId).forEach(sub => {
        if (!subcategoriesMap.has(sub.parentId)) {
            subcategoriesMap.set(sub.parentId, []);
        }
        subcategoriesMap.get(sub.parentId).push(sub);
    });

    // --- 1. Masaüstü Menü (categoryMenu) ---
    const desktopMenu = document.getElementById('categoryMenu');
    if (desktopMenu) {
        
        // 'Tümü' butonu SADECE e-ticaret.php'deysek ve ID 'all' ise aktif olsun.
        // index.php'de (productListElement null ise) ASLA aktif olmasın.
        const tumuActiveClass = (currentCategoryId === 'all' && productListElement) ? 'active' : '';
        
        let desktopHTML = `
            <div class="category-btn-wrapper">
                <button class="category-btn ${tumuActiveClass}" data-category-id="all">
                    Tümü
                </button>
            </div>`;

        topLevelCategories.forEach(cat => {
            const children = subcategoriesMap.get(cat.id);
            let subMenuHTML = '';

            // ID'leri string olarak değil, number olarak karşılaştırmak daha sağlıklı
            // ancak URL'den string geldiği için (==) kullanıyoruz.
            const isCatActive = (cat.id == currentCategoryId);
            let isChildActive = false; // Alt kategorilerden biri aktif mi?

            if (children && children.length > 0) {
                subMenuHTML = `<div class="subcategory-menu">`;
                children.forEach(sub => {
                    const isSubActive = (sub.id == currentCategoryId);
                    if (isSubActive) {
                        isChildActive = true; // Ana kategoriyi de aktif yapmak için
                    }
                    
                    subMenuHTML += `
                        <button class="subcategory-btn ${isSubActive ? 'active' : ''}" data-category-id="${sub.id}">
                            ${sub.name}
                        </button>`;
                });
                subMenuHTML += `</div>`;
            }
            
            // Ana kategori ID'si seçiliyse VEYA alt kategorilerinden biri seçiliyse, ana butonu 'active' yap
            const mainButtonClass = (isCatActive || isChildActive) ? 'active' : '';

            desktopHTML += `
                <div class="category-btn-wrapper">
                    <button class="category-btn ${mainButtonClass}" data-category-id="${cat.id}">
                        ${cat.name}
                    </button>
                    ${subMenuHTML}
                </div>`;
        });
        
        desktopMenu.innerHTML = desktopHTML;

        // Masaüstü butonlar için olay dinleyicileri (Bu kısım aynı)
        desktopMenu.querySelectorAll('.category-btn, .subcategory-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const categoryId = this.dataset.categoryId;

                if (productListElement) {
                    // e-ticaret.php'deyiz: Filtrele
                    handleCategoryClick(categoryId);

                    // Aktif sınıfını yönet (Bu artık renderCategories içinde yapılıyor ama tıklama için de kalsın)
                    document.querySelectorAll('#categoryMenu .category-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('#categoryMenu .subcategory-btn').forEach(b => b.classList.remove('active'));
                    
                    if (this.classList.contains('subcategory-btn')) {
                        // Alt kategoriye tıklandı
                        this.classList.add('active');
                        this.closest('.category-btn-wrapper').querySelector('.category-btn').classList.add('active');
                    } else {
                        // Ana kategoriye tıklandı
                        this.classList.add('active');
                    }
                } else {
                    // index.php'deyiz: Yönlendir
                    window.location.href = `e-ticaret.php?kategori=${categoryId}`;
                }
            });
        });
    }

    // --- 2. Mobil Menü (mobileCategoryList) ---
    // Mobil menü mantığı da URL'den etkilenmeli (e-ticaret.php'de)
    const mobileMenu = document.getElementById('mobileCategoryList');
    if (mobileMenu) {
        
        const tumuActiveClass = (currentCategoryId === 'all' && productListElement) ? 'active' : ''; // 'active' sınıfı mobil link için tanımlı değil ama mantık burada
        
        let mobileHTML = `
            <div class="mobile-category-item">
                <a href="#" class="mobile-category-link ${tumuActiveClass}" data-category-id="all">
                    <span>Tümü</span>
                </a>
            </div>`;

        topLevelCategories.forEach(cat => {
            const children = subcategoriesMap.get(cat.id);
            const isCatActive = (cat.id == currentCategoryId);
            let isChildActive = false;
            let subMenuHTML = '';

            if (children && children.length > 0) {
                subMenuHTML = `<div class="mobile-subcategory-list">`;
                children.forEach(sub => {
                    const isSubActive = (sub.id == currentCategoryId);
                    if(isSubActive) isChildActive = true;
                    
                    subMenuHTML += `
                        <a href="#" class="mobile-subcategory-link ${isSubActive ? 'mobile-active' : ''}" data-category-id="${sub.id}">
                            ${sub.name}
                        </a>`;
                });
                subMenuHTML += `</div>`;
            }
            
            const mainLinkClass = (isCatActive || isChildActive) ? 'mobile-active' : ''; // 'mobile-active' diye bir CSS sınıfı eklemeniz gerekebilir
            
            mobileHTML += `<div class="mobile-category-item">`;

            if (children && children.length > 0) {
                const isOpen = (isCatActive || isChildActive) ? 'open' : '';
                const sublistStyle = isOpen ? 'style="display: block;"' : '';
                
                mobileHTML += `
                    <a href="#" class="mobile-category-link ${mainLinkClass} ${isOpen}" data-category-id="${cat.id}">
                        <span>${cat.name}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </a>
                    <div class="mobile-subcategory-list" ${sublistStyle}>${subMenuHTML}</div>`;
            } else {
                mobileHTML += `
                    <a href="#" class="mobile-category-link ${mainLinkClass}" data-category-id="${cat.id}">
                        <span>${cat.name}</span>
                    </a>`;
            }
            mobileHTML += `</div>`;
        });
        mobileMenu.innerHTML = mobileHTML;

        // Mobil linkler için olay dinleyicileri (Bu kısım aynı)
        mobileMenu.querySelectorAll('.mobile-category-link, .mobile-subcategory-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const subList = this.nextElementSibling;
                const categoryId = this.dataset.categoryId;

                if (productListElement) {
                    // e-ticaret.php'deyiz: Filtrele

                    if (subList && subList.classList.contains('mobile-subcategory-list')) {
                        // Ana kategori (akordiyon)
                        const isOpen = subList.style.display === 'block';
                        subList.style.display = isOpen ? 'none' : 'block';
                        this.classList.toggle('open', !isOpen);
                        handleCategoryClick(categoryId); // Ana kategoriye tıklayınca da filtrele
                    } else {
                        // Alt kategori (veya çocuğu olmayan ana)
                        handleCategoryClick(categoryId);
                        closeMobileMenu(); // Seçim yapınca menüyü kapat
                    }
                } else {
                    // index.php'deyiz: Yönlendir
                    window.location.href = `e-ticaret.php?kategori=${categoryId}`;
                }
            });
        });
    }
}
// ### YENİ FONKSİYON: En Çok Satanları Render Et ###
function renderBestSellers() {
    const container = document.getElementById('best-selling-products');
    if (!container) return; // Sadece index.php'de çalışır

    // Ürünleri 'totalSold' alanına göre çoktan aza sırala
    const sortedProducts = [...products]
        .sort((a, b) => (b.totalSold || 0) - (a.totalSold || 0))
        .slice(0, 8); // İlk 8 ürünü al

    if (sortedProducts.length === 0) {
        container.innerHTML = '<p class="text-white col-span-full text-center">En çok satan ürünler yakında burada olacak.</p>';
        return;
    }

    // 'getProductCardHTML' fonksiyonunu kullanarak kartları bas
    container.innerHTML = sortedProducts.map(getProductCardHTML).join('');
}
// ### YENİ FONKSİYON: Dinamik Sayfalar Menüsünü Oluşturur ###
   // ### YENİ VE GÜNCELLENMİŞ FONKSİYON: Dinamik Sayfalar Menüsünü Oluşturur ###
// (Mobil Menü hatası düzeltildi, artık 'mobileCategoryList' içine ekleniyor)
// ### YENİ VE GÜNCELLENMİŞ FONKSİYON: Dinamik Sayfalar Menüsünü Oluşturur ###
// (Mobil Menü hatası düzeltildi, artık 'mobileCategoryList' içine ekleniyor)
// ### YENİ VE GÜNCELLENMİŞ FONKSİYON: Dinamik Sayfalar Menüsünü Oluşturur ###
// (Masaüstü stilini 'menu-link' ile eşleşecek şekilde ve mobil menü hedefini düzeltecek şekilde günceller)
// ### YENİ VE GÜNCELLENMİŞ FONKSİYON: Dinamik Sayfalar Menüsünü Oluşturur ###
// (Açılan butonların stili .subcategory-btn ile güncellendi)
function renderDynamicPagesMenu() {
    // 1. Gerekli HTML elementlerini bul
    const placeholder = document.getElementById('dynamic-pages-menu-placeholder');
    const mobileMenu = document.getElementById('mobileCategoryList');
    
    if (!window.allPages) return;

    // 2. Sadece 'showInMenu' olarak işaretlenmiş sayfaları al
    const pagesToShow = window.allPages.filter(page => page.showInMenu);

    if (pagesToShow.length === 0) {
        if (placeholder) placeholder.style.display = 'none';
        return;
    }

    // --- 3. Masaüstü Menü ---
    if (placeholder) {
        
        // ### DEĞİŞİKLİK BURADA (Açılan Butonlar) ###
        // 'index.php'deki .subcategory-btn stilini kullan
        const linksHTML = pagesToShow.map(page => `
            <a href="sayfa.php?slug=${page.slug}" class="subcategory-btn">${page.title}</a>
        `).join('');
        
        placeholder.innerHTML = `
            <div class="relative h-full" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                
                <a href="#" class="menu-link px-4 flex items-center hover:text-yellow-300 transition-all duration-300 h-full">
                    <i class="fas fa-file-alt mr-2"></i>
                    Dinamik Sayfalar
                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    <span class="bottom-border"></span>
                </a>

                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     
                     class="absolute z-50 top-full left-0 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                     style="display: none;">
                    
                    <div class="p-2"> 
                        ${linksHTML}
                    </div>
                </div>
            </div>
        `;
        placeholder.style.display = 'block';
    }
    
    // --- 4. Mobil Menü (Bu kısım zaten doğru ve profesyonel görünüyor) ---
    // (Bu kod, 'mobileCategoryList'in içine ekler ve .mobile-subcategory-link stilini kullanır)
    if (mobileMenu) {
        const mobileLinksHTML = pagesToShow.map(page => `
            <a href="sayfa.php?slug=${page.slug}" class="mobile-subcategory-link">${page.title}</a>
        `).join('');
        
        const mobileHTML = `
            <div class="mobile-category-item">
                <a href="#" class="mobile-category-link">
                    <span>Dinamik Sayfalar</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </a>
                <div class="mobile-subcategory-list">
                    ${mobileLinksHTML}
                </div>
            </div>
        `;
        
        mobileMenu.insertAdjacentHTML('beforeend', mobileHTML);
        
        const newItem = mobileMenu.lastElementChild; 
        if (newItem) {
            newItem.querySelectorAll('.mobile-category-link, .mobile-subcategory-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const subList = this.nextElementSibling;
                    const href = this.getAttribute('href');

                    if (subList && subList.classList.contains('mobile-subcategory-list')) {
                        const isOpen = subList.style.display === 'block';
                        subList.style.display = isOpen ? 'none' : 'block';
                        this.classList.toggle('open', !isOpen);
                    } else if (href && href !== '#') {
                        window.location.href = href;
                        closeMobileMenu();
                    }
                });
            });
        }
    }
}


// ##################################################################
// ### BAŞLANGIÇ: YENİ FONKSİYONLAR (Stilmoto Tipi Carousel için) ###
// ##################################################################

/**
 * index.php'deki CSS'e uygun yatay carousel HTML'ini oluşturur.
 * (Bu, renderBanners tarafından kullanılan yeni bir yardımcı fonksiyondur)
 */
function createCarouselHTML(idPrefix, banners) {
    // index.php'deki .banner-carousel-slide CSS'ine uygun HTML
    const slidesHTML = banners.map(banner => `
        <div class="banner-carousel-slide">
            <a href="${banner.link || '#'}" target="_blank">
                <img src="${banner.image}" alt="${banner.description || 'Banner'}">
                ${banner.description ? `<p>${banner.description}</p>` : ''}
            </a>
        </div>
    `).join('');

    // index.php'deki .banner-carousel-container CSS'ine uygun tam yapı
    return `
        <h3 class="text-2xl font-bold text-white mb-4">${idPrefix === 'firsat' ? 'Fırsat Ürünleri' : 'Vitrin'}</h3>
        <div class="banner-carousel-container">
            <button class="banner-carousel-btn prev" data-carousel-id="${idPrefix}"><i class="fas fa-chevron-left"></i></button>
            <div class="banner-carousel-track-container" id="track-container-${idPrefix}">
                <div class="banner-carousel-track">
                    ${slidesHTML}
                </div>
            </div>
            <button class="banner-carousel-btn next" data-carousel-id="${idPrefix}"><i class="fas fa-chevron-right"></i></button>
        </div>
    `;
}

/**
 * ### DEĞİŞTİ: renderBanners FONKSİYONU ###
 * Bu fonksiyon, eski basit slider yerine
 * 'Stilmoto' tarzı yatay kayan carousel'leri oluşturmak için tamamen yeniden yazıldı.
 */
function renderBanners() {
    const firsatContainer = document.getElementById('firsat-banner-container');
    const vitrinContainer = document.getElementById('vitrin-banner-container');
    
    if (!firsatContainer || !vitrinContainer || !window.allBanners) {
        console.warn("Banner containers or data not found. 'index.php' üzerinde olduğunuzdan emin olun.");
        return;
    }

    // Bannerları slot numarasına göre ayır (admin.js'deki mantıkla aynı)
    // ### DÜZELTME: Sadece resmin var olması yeterli. Link (b.link) zorunlu değil. ###
    const firsatBanners = window.allBanners.filter(b => b.slot >= 1 && b.slot <= 5 && b.image);
    const vitrinBanners = window.allBanners.filter(b => b.slot >= 6 && b.slot <= 10 && b.image);


    // --- 1. Fırsat Bannerları Carousel'ini Oluştur (Slot 1-5) ---
    // (CSS'e göre bu 'bir tık büyük' olan)
    if (firsatBanners.length > 0) {
        firsatContainer.innerHTML = createCarouselHTML('firsat', firsatBanners);
    } else {
        firsatContainer.innerHTML = '<h3 class="text-2xl font-bold text-white mb-4">Fırsat Ürünleri</h3><p class="text-white">Fırsat bannerları yakında...</p>';
    }

    // --- 2. Vitrin Bannerları Carousel'ini Oluştur (Slot 6-10) ---
    if (vitrinBanners.length > 0) {
        vitrinContainer.innerHTML = createCarouselHTML('vitrin', vitrinBanners);
    } else {
        vitrinContainer.innerHTML = '<h3 class="text-2xl font-bold text-white mb-4">Vitrin</h3><p class="text-white">Vitrin bannerları yakında...</p>';
    }
    
    // NOT: Bu carousel'lerin ileri/geri butonları 'setupEventListeners' fonksiyonu içine eklenmiştir.
}

// ################################################################
// ### BİTİŞ: YENİ FONKSİYONLAR (Stilmoto Tipi Carousel için) ###
// ################################################################


// ### YENİ FONKSİYON: Vitrin Ürünlerini Render Et ###
function renderShowcase() {
    const container = document.getElementById('showcase-products');
    if (!container) return; // Sadece index.php'de çalışır

    if (!window.allShowcaseItems || window.allShowcaseItems.length === 0) {
        container.innerHTML = '<p class="text-white col-span-full text-center">Vitrin ürünleri yakında burada olacak.</p>';
        return;
    }

    // Vitrin ürünlerini render et
    let showcaseHTML = '';
    window.allShowcaseItems.forEach(item => {
        // Admin panelinde 'custom: true' olarak eklenen özel ürünler
        if (item.custom) {
            // Özel banner/ürünler için basit bir kart (ürün kartına benzemiyor)
            showcaseHTML += `
            <div class="product-card group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden flex flex-col">
                <div class="relative flex-shrink-0">
                    <a href="${item.link || '#'}" target="_blank" class="block">
                        <img src="${item.image || './Resimler/pc.jpg'}" alt="${item.name}" class="w-full h-36 object-contain transition-transform duration-300 group-hover:scale-105">
                    </a>
                </div>
                <div class="p-4 flex flex-col flex-grow">
                     <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-bold text-gray-800 leading-snug h-20 overflow-hidden line-clamp-3">${item.name}</h3>
                    </div>
                    <div class="mt-auto">
                        <a href="${item.link || '#'}" target="_blank" class="w-full block text-center bg-blue-600 text-white px-2 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">Hemen İncele</a>
                    </div>
                </div>
            </div>`;
        } 
        // Admin panelinde 'custom: false' olarak, mevcut ürün listesinden eklenenler
        else {
            const product = products.find(p => p.id === item.productId);
            if (product) {
                showcaseHTML += getProductCardHTML(product); // Ürün kartı fonksiyonunu yeniden kullan
            }
        }
    });

    container.innerHTML = showcaseHTML;
}


    // ... (updateCartDisplay fonksiyonu aynı kalıyor) ...
    function updateCartDisplay() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        
        if (cartTotalEl) cartTotalEl.textContent = `${formatPrice(subtotal)} ₺`;
        if (cartCountEl) cartCountEl.textContent = count;
        if (cartCountFixedEl) cartCountFixedEl.textContent = count;
        
        if (cartItemsEl) {
            cartItemsEl.innerHTML = cart.length === 0 
                ? '<p class="text-center text-gray-500 py-4">Sepetiniz boş.</p>'
                : cart.map(item => `
                <div class="flex items-center justify-between p-4 border-b">
                    <div class="flex items-center gap-4">
                        <img src="${item.image || './Resimler/pc.jpg'}" alt="${item.name}" class="w-16 h-16 object-cover rounded">
                        <div>
                            <h4 class="font-semibold">${item.name}</h4>
                            <p class="text-sm text-gray-600">${formatPrice(item.price)} ₺</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="window.updateCartItemQuantity(${item.id}, ${item.quantity - 1})" class="bg-gray-200 px-2 py-1 rounded">-</button>
                        <span>${item.quantity}</span>
                        <button onclick="window.updateCartItemQuantity(${item.id}, ${item.quantity + 1})" class="bg-gray-200 px-2 py-1 rounded">+</button>
                        <button onclick="window.removeFromCart(${item.id})" class="text-red-500 ml-2"><i class="fas fa-trash"></i></button>
                    </div>
                </div>`).join('');
        }
    }
    
    // ... (updateWishlistDisplay fonksiyonu aynı kalıyor) ...
    function updateWishlistDisplay() {
        if (!wishlistCountEl) return;
        const count = Array.isArray(wishlist) ? wishlist.length : 0;
        wishlistCountEl.textContent = count;
        wishlistCountEl.classList.toggle('hidden', count === 0);
    }

    // ... (showWishlist fonksiyonu aynı kalıyor) ...
    window.showWishlist = function() {
        const contentEl = document.getElementById('wishlistContent');
        if (!contentEl) return;
        if (wishlist.length === 0) {
            contentEl.innerHTML = '<p class="text-center text-gray-500 py-8">Favori listeniz boş.</p>';
            openModal('wishlistModal');
            return;
        }
        const favoriteProducts = products.filter(p => wishlist.includes(p.id));
        contentEl.innerHTML = favoriteProducts.map(p => `
            <div class="flex items-center justify-between p-4 border-b hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <img src="${p.image || './Resimler/pc.jpg'}" alt="${p.name}" class="w-20 h-20 object-contain rounded">
                    <div>
                        <a href="urun.php?id=${p.id}" class="font-semibold text-blue-700 hover:underline">${p.name}</a>
                        <div class="flex items-center gap-2 mt-1">
                             ${p.oldPrice && p.oldPrice > p.price ? `<span class="text-sm text-gray-500 line-through">${formatPrice(p.oldPrice)} ₺</span>` : ''}
                             <span class="text-lg font-bold ${p.oldPrice && p.oldPrice > p.price ? 'text-red-600' : 'text-gray-800'}">${formatPrice(p.price)} ₺</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="urun.php?id=${p.id}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs hover:bg-blue-700 transition-colors">Detaylar</a>
                    <button onclick="window.toggleWishlist(${p.id}); window.showWishlist();" class="text-red-500 text-xl" title="Favorilerden Kaldır"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
        `).join('');
        openModal('wishlistModal');
    }

    // ... (Kalan fonksiyonlar - checkForFavoriteDiscounts, showUserInterface, showAuthInterface, handleLogin, handleRegister, handleLogout, editUserInfo, handleUpdateUser, showCustomerReport, showOrderHistory, updateCardQuantity, addToCartFromCard, addToCart, removeFromCart, updateCartItemQuantity, clearCart, handleOrder, openModal, closeModal, formatPrice, showNotification, showProductDetail, setupAuthModal - BÜYÜK ORANDA AYNI KALIYOR) ...
    
    // ... (Mevcut fonksiyonlarınızı buraya yapıştırın ...
    // ... (showProductDetail'e kadar) ...

    function checkForFavoriteDiscounts() {
        if (!currentUser || wishlist.length === 0) {
            return;
        }
        const discountedFavorites = products.filter(p => 
            wishlist.includes(p.id) && p.oldPrice && p.oldPrice > p.price
        );
        if (discountedFavorites.length > 0) {
            const modal = document.getElementById('discountNotificationModal');
            setTimeout(() => {
                if(modal) {
                   modal.classList.add('show');
                   setTimeout(() => closeModal('discountNotificationModal'), 15000);
                }
            }, 1500);
        }
    }

    function showUserInterface() {
    const authButtons = document.getElementById('authButtons');
    const userButtons = document.getElementById('userButtons');
    const userNameEl = document.getElementById('userName');

    if (authButtons) authButtons.classList.add('hidden');
    if (userButtons) {
        userButtons.classList.remove('hidden');
        userButtons.style.display = 'flex';
    }
    if (userNameEl) userNameEl.textContent = `Hoşgeldin ${currentUser.name}`;

    updateWishlistDisplay();
}

     function showAuthInterface() {
    const authButtons = document.getElementById('authButtons');
    const userButtons = document.getElementById('userButtons');

    if (authButtons) authButtons.classList.remove('hidden');
    if (userButtons) {
        userButtons.classList.add('hidden');
        userButtons.style.display = 'none';
    }

    if (products.length > 0) {
        renderProducts();
    }
}
    
   async function handleLogin(e) {
        e.preventDefault();
        const form = e.target;
        const loginData = { email: form.email.value, password: form.password.value };
        try {
            const result = await api('users', 'login', 'POST', loginData);
            currentUser = result.user;
            await fetchUserFavorites();
            showUserInterface();
            await api('cart', 'get').then(c => { cart = c; updateCartDisplay(); });
            window.closeAuthModal();
            form.reset();
            renderProducts(); 
            checkForFavoriteDiscounts();
        } catch (error) {}
    }
    async function handleRegister(e) {
        e.preventDefault();
        const form = e.target;
        const aydinlatmaOnay = form.querySelector('#aydinlatmaOnay').checked;
        const uyelikOnay = form.querySelector('#uyelikOnay').checked;
        if (!aydinlatmaOnay || !uyelikOnay) {
            showNotification('Lütfen üyelik sözleşmesini ve aydınlatma metnini onaylayın.', 'error');
            return;
        }
        const userData = {
            name: form.name.value,
            surname: form.surname.value,
            email: form.email.value,
            phone: form.phone.value,
            address: form.address.value,
            city: form.city.value,
            district: form.district.value,
            billingAddress: form.billingAddress.value,
            billingCity: form.billingCity.value,
            billingDistrict: form.billingDistrict.value,
            password: form.password.value,
            passwordConfirm: form.passwordConfirm.value
        };
        if(userData.password !== userData.passwordConfirm) {
            return showNotification('Şifreler eşleşmiyor!', 'error');
        }
        try {
            const result = await api('users', 'register', 'POST', userData);
            currentUser = result.user;
            showUserInterface();
            // await loadCart(); // loadCart diye bir fonksiyon yok, sepet zaten boş gelir
            await api('cart', 'get').then(c => { cart = c; updateCartDisplay(); });
            window.closeAuthModal();
            form.reset();
            showNotification('Kaydınız başarıyla tamamlandı!', 'success');
        } catch (error) {}
    }
    
    window.handleLogout = async function() {
       try {
           const result = await api('users', 'logout', 'POST');
           if (result.success) {
               currentUser = null;
               wishlist = [];
               cart = [];
               showAuthInterface(); 
               updateCartDisplay(); 
               updateWishlistDisplay();
               showNotification('Başarıyla çıkış yapıldı.', 'success');
           } else {
               showNotification('Çıkış yapılamadı, lütfen tekrar deneyin.', 'error');
           }
       } catch (error) {
           showNotification('Çıkış sırasında bir hata oluştu.', 'error');
       }
    };
    
    window.editUserInfo = async function() {
        if (!currentUser) {
            showNotification('Bu işlem için giriş yapmalısınız.', 'error');
            return;
        }
        try {
            const userDetails = await api('users', 'get_details');
            if (userDetails) {
                const form = document.getElementById('editUserForm');
                const fullName = `${userDetails.name || userDetails.firstName || ''} ${userDetails.surname || userDetails.lastName || ''}`.trim();
                form.querySelector('[name="editName"]').value = fullName;
                form.querySelector('[name="editEmail"]').value = userDetails.email || '';
                form.querySelector('[name="editAddress"]').value = userDetails.address || userDetails.shippingAddress || '';
                form.querySelector('[name="editPhone"]').value = userDetails.phone || '';
                form.querySelector('[name="editCity"]').value = userDetails.city || '';
                form.querySelector('[name="editDistrict"]').value = userDetails.district || '';
                form.querySelector('[name="editBillingAddress"]').value = userDetails.billingAddress || '';
                form.querySelector('[name="editBillingCity"]').value = userDetails.billingCity || '';
                form.querySelector('[name="editBillingDistrict"]').value = userDetails.billingDistrict || '';
                form.querySelector('[name="editPassword"]').value = '';
                form.querySelector('[name="editPasswordConfirm"]').value = '';
            }
            openModal('editUserModal');
        } catch(error) {
            showNotification('Kullanıcı bilgileri yüklenirken bir hata oluştu.', 'error');
        }
    };
    
    async function handleUpdateUser(e) {
        e.preventDefault();
        if (!currentUser) { return showNotification('Bu işlem için giriş yapmalısınız.', 'error'); }
        const form = e.target;
        const password = form.querySelector('[name="editPassword"]').value;
        const passwordConfirm = form.querySelector('[name="editPasswordConfirm"]').value;
        if (password && password !== passwordConfirm) { return showNotification('Yeni şifreler eşleşmiyor!', 'error'); }
        const fullName = form.querySelector('[name="editName"]').value.trim().split(' ');
        const surname = fullName.length > 1 ? fullName.pop() : '';
        const name = fullName.join(' ');
        const userData = {
            name: name,
            surname: surname,
            email: form.querySelector('[name="editEmail"]').value,
            phone: form.querySelector('[name="editPhone"]').value,
            address: form.querySelector('[name="editAddress"]').value,
            city: form.querySelector('[name="editCity"]').value,
            district: form.querySelector('[name="editDistrict"]').value,
            billingAddress: form.querySelector('[name="editBillingAddress"]').value,
            billingCity: form.querySelector('[name="editBillingCity"]').value, 
            billingDistrict: form.querySelector('[name="editBillingDistrict"]').value,
        };
        if (password) { userData.password = password; }
        try {
            const result = await api('users', 'update_details', 'POST', userData);
            if(result.success){
                currentUser.name = result.data.name; // Gelen veriden adı güncelle
                showUserInterface();
                showNotification('Bilgileriniz başarıyla güncellendi.', 'success');
                closeModal('editUserModal');
            }
        } catch (error) {}
    }

    async function showCustomerReport() {
        if (!currentUser) {
            return showNotification('Hesap ekstresini görmek için giriş yapmalısınız.', 'error');
        }
        const userId = currentUser.id;
        const user = currentUser;

        openModal('customer-reportModal');
        const contentEl = document.getElementById('customer-report-content');
        contentEl.innerHTML = '<p class="text-center p-8">Rapor oluşturuluyor...</p>';

        try {
            const allOrders = await api('orders');
            const userOrders = allOrders.filter(o => o.userId === userId).sort((a, b) => new Date(b.date) - new Date(a.date));
            
            const totalSpent = userOrders.reduce((sum, o) => sum + o.total, 0);
            
            let ordersHtml = userOrders.map(order => `
                <div class="p-3 border rounded-md mb-3 bg-gray-50">
                    <div class="flex justify-between items-center flex-wrap">
                        <h5 class="font-semibold">Sipariş #${order.id}</h5>
                        <span class="text-xs text-gray-500">${new Date(order.date).toLocaleDateString('tr-TR')}</span>
                    </div>
                    <p class="text-sm mt-1"><strong>Tutar:</strong> ${formatPrice(order.total)}₺ | <strong>Durum:</strong> ${order.status}</p>
                    <p class="text-xs mt-1 text-gray-600"><strong>Teslimat Adresi:</strong> ${order.customerInfo.address}, ${order.customerInfo.district || ''} / ${order.customerInfo.city || ''}</p>
                    <ul class="list-disc list-inside pl-4 mt-2 text-xs space-y-1">
                        ${order.items.map(item => `<li>${item.quantity} x ${item.name} (${formatPrice(item.price)}₺)</li>`).join('')}
                    </ul>
                </div>
            `).join('');

            if (userOrders.length === 0) {
                ordersHtml = '<p>Henüz siparişiniz bulunmuyor.</p>';
            }

            document.getElementById('customer-reportModalTitle').textContent = `${user.name} - Hesap Ekstresi`;
            contentEl.innerHTML = `
                <div id="report-to-export" class="p-2">
                    <div class="space-y-2 mb-4">
                        <p><strong>Toplam Harcama:</strong> <span class="font-bold text-lg">${formatPrice(totalSpent)} ₺</span></p>
                        <p><strong>Toplam Sipariş Sayısı:</strong> <span class="font-bold text-lg">${userOrders.length}</span></p>
                    </div>
                    <h4 class="text-lg font-semibold mt-4 border-t pt-3">Sipariş Detayları</h4>
                    <div class="mt-2">${ordersHtml}</div>
                </div>
            `;
            
            const pdfBtn = document.getElementById('export-pdf-btn-user');
            pdfBtn.onclick = () => exportToPDF('report-to-export', `hesap-ekstresi-${user.id}.pdf`);

        } catch (error) {
            showNotification('Rapor oluşturulurken bir hata oluştu.', 'error');
            console.error("Rapor hatası:", error);
        }
    }
    
    window.showOrderHistory = async function() {
    if (!currentUser) {
        showNotification('Sipariş geçmişini görmek için giriş yapmalısınız.', 'error');
        return;
    }
    openModal('orderHistoryModal');
    const contentEl = document.getElementById('orderHistoryContent');
    contentEl.innerHTML = '<p class="text-center text-gray-500">Siparişleriniz yükleniyor...</p>';
    try {
        const allOrders = await api('orders');
        const userOrders = allOrders.filter(order => order.userId === currentUser.id).sort((a, b) => new Date(b.date) - new Date(a.date));

        if (userOrders.length === 0) {
            contentEl.innerHTML = '<p class="text-center text-gray-500">Daha önce hiç sipariş vermediniz.</p>';
            return;
        }

        contentEl.innerHTML = userOrders.map(order => {
            let statusClass = 'bg-gray-100 text-gray-800';
            switch(order.status) {
                case 'Teslim Edildi': statusClass = 'bg-green-100 text-green-800'; break;
                case 'Ödeme Bekleniyor': statusClass = 'bg-red-600 text-white'; break;
                case 'Beklemede': statusClass = 'bg-yellow-100 text-yellow-800'; break;
                case 'Kargoda': statusClass = 'bg-blue-100 text-blue-800'; break;
                case 'İptal Edildi': statusClass = 'bg-red-100 text-red-800'; break;
            }
            
            let cargoHtmlInHeader = '';
            if (order.shippingCarrier && order.shippingTracking) {
                cargoHtmlInHeader = `
                <div class="text-center md:text-left">
                    <p class="font-bold text-lg text-gray-700">Kargo Bilgisi</p>
                    <p class="font-semibold text-sm text-blue-700">${order.shippingCarrier}</p>
                </div>`;
            }

            return `
            <div class="border rounded-lg mb-4 shadow-sm">
                <div class="bg-gray-50 p-4 flex justify-between items-center rounded-t-lg flex-wrap gap-x-6 gap-y-4">
                    <div class="text-center md:text-left">
                        <p class="font-bold text-lg text-gray-800">Sipariş #${order.id}</p>
                        <p class="text-sm text-gray-500">Tarih: ${new Date(order.date).toLocaleDateString('tr-TR')}</p>
                    </div>
                    ${cargoHtmlInHeader}
                    <div class="text-center md:text-left">
                        <p class="font-bold text-lg text-gray-700">Toplam Tutar</p>
                        <p class="font-bold text-xl text-blue-700">${formatPrice(order.total)} ₺</p>
                    </div>
                    <div class="text-sm font-semibold px-3 py-1 rounded-full ${statusClass}">
                    <p class="font-bold text-lg text-gray-700">Sipariş Durumu</p>
                        <p class="font-bold text-l text-blue-700">${order.status}</p>
                    </div>
                </div>
                <div class="p-4">
                    <h4 class="font-semibold mb-2 text-blue-700">Sipariş Detayları:</h4>
                    <ul class="space-y-2">
                        ${(order.items || []).map(item => `
                            <li class="flex justify-between items-center text-sm py-1 border-b border-gray-100">
                                <span class="text-black">${item.name || item.productName} (x${item.quantity})</span>
                                <span class="text-gray-600">${formatPrice((item.price || 0) * (item.quantity || 0))} ₺</span>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            </div>`;
        }).join('');
    } catch (error) {
        console.error("Sipariş geçmişi hatası:", error);
        contentEl.innerHTML = '<p class="text-center text-red-500">Siparişler yüklenirken bir hata oluştu.</p>';
    }
};
    
    // ... (updateCardQuantity, addToCartFromCard, addToCart, removeFromCart, updateCartItemQuantity, clearCart) ...
    // ... (Bu fonksiyonlar zaten günceldi, aynı kalıyor) ...

    window.updateCardQuantity = function(productId, change, stock) {
        const quantityInput = document.getElementById(`quantity-${productId}`);
        if (!quantityInput) return;

        let currentQuantity = parseInt(quantityInput.value, 10);
        let newQuantity = currentQuantity + change;

        if (newQuantity < 1) {
            newQuantity = 1;
        }
        
        if (newQuantity > stock) {
            newQuantity = stock;
            showNotification('Maksimum stok limitine ulaşıldı.', 'info');
        }

        quantityInput.value = newQuantity;
    }

    window.addToCartFromCard = async function(productId) {
        const quantityInput = document.getElementById(`quantity-${productId}`);
        if (!quantityInput) { 
             showNotification('Bu ürün stokta yok.', 'error');
             return; 
        }
        
        const quantity = parseInt(quantityInput.value, 10);
        if (quantity <= 0) return; 
        
        await window.addToCart(productId, quantity);
    }
    
    window.addToCart = async function(productId, quantity = 1) { 
        try {
            // *** DİKKAT: public_api.php dosyanızın bu 'quantity' parametresini işlemesi gerekir! ***
            const result = await api('cart', 'add', 'POST', { productId, quantity }); 
            cart = result;
            updateCartDisplay();
            showNotification(`${quantity} adet ürün sepete eklendi!`, 'success'); 
        } catch(error) {}
    };
    
    window.removeFromCart = async function(productId) {
         try {
            const result = await api('cart', 'remove', 'POST', { productId });
            cart = result;
            updateCartDisplay();
            showNotification('Ürün sepetten çıkarıldı.', 'info');
        } catch(error) {}
    };

    window.updateCartItemQuantity = async function(productId, quantity) {
         try {
            const result = await api('cart', 'update', 'POST', { productId, quantity });
            cart = result;
            updateCartDisplay();
        } catch(error) {}
    };

    window.clearCart = async function() {
        if (confirm('Sepetinizdeki tüm ürünleri kaldırmak istediğinizden emin misiniz?')) {
            try {
                await api('cart', 'clear', 'POST');
                cart = [];
                updateCartDisplay();
                showNotification('Sepet temizlendi.', 'info');
            } catch(error) {}
        }
    };
    
    async function handleOrder(e) {
        e.preventDefault();
        if(!currentUser) return showNotification('Sipariş vermek için giriş yapmalısınız.', 'error');
        if(cart.length === 0) return showNotification('Sepetiniz boş!', 'error');
        
        const form = e.target;
        const billingAddressChecked = form.billingAddressCheck.checked;
        
        if (billingAddressChecked) {
            if (!form.billingAddress.value || !form.billingCity.value || !form.billingDistrict.value) {
                showNotification('Farklı bir fatura adresi seçtiniz. Lütfen tüm fatura adresi alanlarını doldurun.', 'error');
                return;
            }
        }

        const orderData = {
            customer: {
                name: form.name.value,
                phone: form.phone.value,
                email: form.email.value,
                address: form.address.value,
                city: form.city.value,
                district: form.district.value,
                billingAddress: billingAddressChecked ? {
                    address: form.billingAddress.value,
                    city: form.billingCity.value,
                    district: form.billingDistrict.value,
                } : 'Teslimat adresi ile aynı'
            },
            paymentMethod: form.paymentMethod.value
        };
        
        try {
            const result = await api('orders', '', 'POST', orderData);
            showNotification(result.message || 'Siparişiniz başarıyla alındı!', 'success');
            cart = [];
            updateCartDisplay();
            closeModal('orderModal');
            renderProducts(); // loadProducts -> renderProducts olarak düzeltildi
        } catch(error) {
            showNotification(error.message || 'Sipariş oluşturulurken bir hata oluştu.', 'error');
        }
    }

   window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modalId !== 'discountNotificationModal') {
                document.body.classList.add('modal-open');
            }
            modal.classList.add('flex');
            modal.classList.remove('hidden');
        }
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modalId !== 'discountNotificationModal') {
                 document.body.classList.remove('modal-open');
            }
            if (modalId === 'discountNotificationModal') {
                modal.classList.remove('show');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    };

    function formatPrice(price) { return (price || 0).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const color = type === 'success' ? 'bg-green-500' : (type === 'error' ? 'bg-red-500' : 'bg-blue-500');
        notification.className = `fixed top-5 right-5 p-4 rounded-lg text-white shadow-lg z-[100000] ${color}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 4000);
    }

   window.showProductDetail = function(productId) {
        const product = products.find(p => p.id === productId);
        if(!product) return;
        document.getElementById('detailProductName').textContent = product.name;
        
        // Ürünün özelliklerini (specs) göstermek için HTML'i güncelle
        let specsHtml = '';
        if (product.specs) {
            specsHtml += '<h5 class="font-semibold mb-3">Özellikler:</h5><ul class="list-disc list-inside text-gray-600 space-y-2">';
            for (const key in product.specs) {
                specsHtml += `<li><strong>${key}:</strong> ${product.specs[key]}</li>`;
            }
            specsHtml += '</ul>';
        } else if (product.features) { // Eski features yapısını da destekle
             specsHtml += '<h5 class="font-semibold mb-3">Özellikler:</h5><ul class="list-disc list-inside text-gray-600 space-y-2">';
             specsHtml += (product.features || []).map(f => `<li>${f}</li>`).join('');
             specsHtml += '</ul>';
        }

        document.getElementById('productDetailContent').innerHTML = `
            <div class="grid md:grid-cols-2 gap-6">
                <img src="${product.image || './Resimler/pc.jpg'}" alt="${product.name}" class="w-full rounded-lg">
                <div>
                 <div class="text-center mt-4">
                        <a href="urun.php?id=${product.id}" class="text-sm text-blue-600 hover:underline">
                            Ürünün kendi sayfasına git <i class="fas fa-external-link-alt ml-1"></i>
                        </a>
                    </div>
                    <h4 class="text-2xl font-bold mb-4">${product.name}</h4>
                    
                    <div class="text-gray-600 mb-6 product-description-html">${product.description || 'Açıklama bulunmuyor.'}</div>

                    <div class="mb-6">
                        ${specsHtml}
                    </div>
                     <div class="flex justify-between items-center">
                        <span class="text-3xl font-bold text-red-600">${formatPrice(product.price)} ₺</span>
                        <button onclick="window.addToCart(${product.id}); closeModal('productDetailModal');" class="bg-red-600 text-white px-6 py-3 rounded-lg">Sepete Ekle</button>
                    </div>
                    </div>
            </div>`;
        openModal('productDetailModal');
    };

    // ... (setupAuthModal fonksiyonu aynı kalıyor) ...
    function setupAuthModal() {
        const authModal = document.getElementById('authModal');
        const authModalContent = document.getElementById('authModalContent');
        const authModalBtn = document.getElementById('authModalBtn');
        const closeAuthModalBtn = document.getElementById('closeAuthModalBtn');
        const loginTabBtn = document.getElementById('loginTabBtn');
        const registerTabBtn = document.getElementById('registerTabBtn');
        const loginTabContent = document.getElementById('loginTabContent');
        const registerTabContent = document.getElementById('registerTabContent');

        if (!authModal) return;

        const openAuthModal = (defaultTab = 'login') => {
            document.getElementById('loginForm')?.reset();
            document.getElementById('registerForm')?.reset();
            document.body.classList.add('modal-open');
            authModal.classList.add('flex', 'items-center');
            authModal.classList.remove('hidden');
            setTimeout(() => {
                authModalContent?.classList.remove('scale-95', 'opacity-0');
                authModalContent?.classList.add('scale-100', 'opacity-100');
            }, 50);

            if (defaultTab === 'register') {
                switchToRegisterTab();
            } else {
                switchToLoginTab();
            }
        };

        const closeAuthModal = () => {
             document.body.classList.remove('modal-open');
             authModalContent?.classList.add('scale-95', 'opacity-0');
             authModalContent?.classList.remove('scale-100', 'opacity-100');
             setTimeout(() => {
                authModal.classList.add('hidden');
                authModal.classList.remove('flex', 'items-center');
             }, 200);
        };

        const switchToLoginTab = () => {
            loginTabBtn.classList.add('text-blue-700', 'border-b-2', 'border-blue-700');
            loginTabBtn.classList.remove('text-gray-500');
            registerTabBtn.classList.add('text-gray-500');
            registerTabBtn.classList.remove('text-blue-700', 'border-b-2', 'border-blue-700');
            loginTabContent.classList.remove('hidden');
            registerTabContent.classList.add('hidden');
        };

        const switchToRegisterTab = () => {
            registerTabBtn.classList.add('text-blue-700', 'border-b-2', 'border-blue-700');
            registerTabBtn.classList.remove('text-gray-500');
            loginTabBtn.classList.add('text-gray-500');
            loginTabBtn.classList.remove('text-blue-700', 'border-b-2', 'border-blue-700');
            registerTabContent.classList.remove('hidden');
            loginTabContent.classList.add('hidden');
        };

        authModalBtn?.addEventListener('click', () => openAuthModal('login'));
        closeAuthModalBtn?.addEventListener('click', closeAuthModal);
        loginTabBtn?.addEventListener('click', switchToLoginTab);
        registerTabBtn?.addEventListener('click', switchToRegisterTab);

        window.openAuthModal = openAuthModal;
        window.closeAuthModal = closeAuthModal;
    }

    // ### DEĞİŞTİ: Mobil Menü Kontrol Fonksiyonları (Daha Kararlı) ###
    function openMobileMenu() {
        document.getElementById('mobileMenu')?.classList.add('active');
        
        const overlay = document.getElementById('mobileMenuOverlay');
        if (overlay) {
            overlay.classList.remove('hidden'); // 'hidden' sınıfını kaldır
            overlay.classList.add('active');
        }
        document.body.classList.add('modal-open');
    }

    function closeMobileMenu() {
        document.getElementById('mobileMenu')?.classList.remove('active');
        
        const overlay = document.getElementById('mobileMenuOverlay');
        if (overlay) {
            overlay.classList.remove('active');
            // Animasyon bittikten sonra gizle (CSS transition süresiyle eşleşmeli)
            setTimeout(() => {
                if (!overlay.classList.contains('active')) { // Hala kapalıysa gizle
                    overlay.classList.add('hidden');
                }
            }, 300); 
        }
        
        // Sadece mobil menü açıksa body kilidini kaldır
        if (!document.querySelector('.modal.flex')) { // Başka bir modal açık değilse
            document.body.classList.remove('modal-open');
        }
    }

    // ### DEĞİŞTİ ###: Mobil Menü dinleyicileri eklendi
    function setupEventListeners() {
        const profileBtn = document.getElementById('profileBtn');
        const userDropdown = document.getElementById('userDropdown');

        profileBtn?.addEventListener('click', (event) => {
            event.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        window.addEventListener('click', () => {
            if (userDropdown?.classList.contains('show')) {
                userDropdown.classList.remove('show');
            }
        });

        // #####################################################################
        // ### BAŞLANGIÇ: YENİ EKLENEN KOD (Stilmoto Carousel Butonları) ###
        // #####################################################################
        // Event delegation kullanarak body'e bir kere ekliyoruz.
        // Bu, 'renderBanners' çalıştıktan sonra oluşan dinamik butonlar için gereklidir.
        document.body.addEventListener('click', function(e) {
            const button = e.target.closest('.banner-carousel-btn');
            if (button) {
                const carouselId = button.dataset.carouselId;
                if (!carouselId) return;
                
                const container = document.getElementById(`track-container-${carouselId}`);
                if (!container) return;

                // index.php CSS'inden slide genişliğini almayı dene, yoksa varsayılanı kullan
                const slide = container.querySelector('.banner-carousel-slide');
                if (!slide) return;
                
                const slideWidth = slide.offsetWidth;
                const gap = parseInt(window.getComputedStyle(container.querySelector('.banner-carousel-track')).gap) || 16;
                
                // Bir seferde 2 kart kaydır
                const scrollAmount = (slideWidth + gap) * 2; 

                if (button.classList.contains('next')) {
                    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                } else if (button.classList.contains('prev')) {
                    container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                }
            }
        });
        // ###################################################################
        // ### BİTİŞ: YENİ EKLENEN KOD (Stilmoto Carousel Butonları) ###
        // ###################################################################


        // ### YENİ: Mobil Menü Olayları ###
        document.getElementById('mobileMenuBtn')?.addEventListener('click', openMobileMenu);
        document.getElementById('closeMobileMenuBtn')?.addEventListener('click', closeMobileMenu);
        document.getElementById('mobileMenuOverlay')?.addEventListener('click', closeMobileMenu);
        // ### YENİ SONU ###

        setupAuthModal();

        document.getElementById('registerForm')?.addEventListener('submit', handleRegister);
        document.getElementById('loginForm')?.addEventListener('submit', handleLogin);
        document.getElementById('orderForm')?.addEventListener('submit', handleOrder);
        document.getElementById('editUserForm')?.addEventListener('submit', handleUpdateUser);
        
        document.getElementById('checkoutBtn')?.addEventListener('click', () => {
             if(!currentUser) { 
                showNotification('Lütfen önce giriş yapın.', 'error'); 
                return window.openAuthModal('login');
             }
             openModal('orderModal');
        });

        document.getElementById('viewCartBtn')?.addEventListener('click', () => {
             window.location.href = 'cart.html';
        });

        const floatingCartButton = document.querySelector('#cart-button a');
        if (floatingCartButton) {
            floatingCartButton.addEventListener('click', (e) => {
                e.preventDefault();
                window.location.href = 'cart.html';
            });
        }
        
        const goToCartBtnInModal = document.getElementById('goToCartBtn');
        if (goToCartBtnInModal) {
            goToCartBtnInModal.addEventListener('click', () => {
                window.location.href = 'cart.html';
            });
        }

        const clearCartBtn = document.getElementById('clearCartBtn');
        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', window.clearCart);
        }

        document.querySelectorAll('[id^="close"], [id^="cancel"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('[id$="Modal"]');
                if(modal) closeModal(modal.id);
            });
        });

        document.getElementById('productSearch')?.addEventListener('input', (e) => { 
            searchTerm = e.target.value; 
            renderProducts();
        });
        document.getElementById('productSort')?.addEventListener('change', (e) => { 
            sortBy = e.target.value; 
            renderProducts();
        });
        
        // --- YUKARI ÇIK BUTONU KODU BURADA ---
        const scrollTopBtn = document.getElementById("scrollTopBtn");
        if(scrollTopBtn) {
            window.addEventListener('scroll', function() {
                if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                    scrollTopBtn.style.display = "flex";
                } else {
                    scrollTopBtn.style.display = "none";
                }
            });
        }

        // ### YENİ ###: Sonsuz Kaydırma (Infinite Scroll) Dinleyicisi
        window.addEventListener('scroll', () => {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
                if (renderedProductCount < currentFilteredList.length && !isLoadingMore) {
                    displayMoreProducts();
                }
            }
        });
        
        document.getElementById('billingAddressCheck')?.addEventListener('change', (e) => {
            const section = document.getElementById('billingAddressSection');
            const inputs = section.querySelectorAll('input, textarea');
            if (e.target.checked) {
                section.classList.remove('hidden');
                inputs.forEach(input => input.required = true);
            } else {
                section.classList.add('hidden');
                inputs.forEach(input => input.required = false);
            }
        });

        document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const bankInfo = document.getElementById('bankInfo');
                if (e.target.value === 'Havale/EFT') {
                    bankInfo.classList.remove('hidden');
                } else {
                    bankInfo.classList.add('hidden');
                }
            });
        });

        document.querySelectorAll('.mobile-menu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    document.querySelector(href).scrollIntoView({ behavior: 'smooth' });
                }
                const menu = document.querySelector('.mobile-menu-content');
                if (menu?.classList.contains('active')) {
                    menu.classList.remove('active');
                }
            });
        });
    }

    // ... (Kalan fonksiyonlar - checkUrlForCheckout, scrollToTop, toggleMobileMenu, setupSliders, vb. - AYNI KALIYOR) ...
    
    function checkUrlForCheckout() {
        const params = new URLSearchParams(window.location.search);
        if (params.get('open') === 'checkout') {
            window.history.replaceState({}, document.title, window.location.pathname);
            if (!currentUser) {
                showNotification('Sipariş vermek için önce giriş yapın.', 'info');
                openModal('authModal');
            } else {
                openModal('orderModal');
            }
        }
    }
    
    window.scrollToTop = function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

     window.toggleMobileMenu = function() {
        const menu = document.querySelector('.mobile-menu-content');
        menu.classList.toggle('active');
    }
    
    const mobileMenuButton = document.querySelector('.mobile-menu');
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', toggleMobileMenu);
    }

    // Banner slider (orijinal kodunuzda zaten vardı)
    let currentSlide = 0;
    const slides = document.querySelectorAll('.banner-slide');
    const dots = document.querySelectorAll('.banner-dot');
    const totalSlides = slides.length;

    function goToSlide(slideIndex) {
        if (slides.length === 0) return;
        slides[currentSlide].classList.remove('active');
        dots[currentSlide].classList.remove('active');
        currentSlide = (slideIndex + totalSlides) % totalSlides;
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }

    function nextSlide() {
        goToSlide(currentSlide + 1);
    }

    window.goToSlide = goToSlide;
    window.nextSlide = nextSlide;
    window.prevSlide = function() {
        goToSlide(currentSlide - 1);
    };

    if (totalSlides > 0) {
        if(slides[0]) slides[0].classList.add('active');
        if(dots[0]) dots[0].classList.add('active');
        setInterval(nextSlide, 5000);
    }
    // Banner slider sonu

    function setupSliders() {
        let currentBgSlide = 0;
        const bgSlides = document.querySelectorAll('.slider-container .slider');
        if (bgSlides.length > 0) {
            bgSlides[0].classList.add('active');
            setInterval(() => {
                bgSlides[currentBgSlide].classList.remove('active');
                currentBgSlide = (currentBgSlide + 1) % bgSlides.length;
                bgSlides[currentBgSlide].classList.add('active');
            }, 5000);
        }

        // Banner slider'ın setup'ı (zaten global'de yapılmıştı, burada tekrar tanımlanmış,
        // global'dekini kullanmak daha doğru olur ama orijinal yapıya dokunmuyorum)
        let currentBannerSlide = 0;
        const bannerSlides = document.querySelectorAll('.banner-slide');
        const bannerDots = document.querySelectorAll('.banner-dot');
        const totalBannerSlides = bannerSlides.length;

        window.goToSlide = function(slideIndex) {
            if (totalBannerSlides === 0) return;
            if (bannerSlides[currentBannerSlide]) bannerSlides[currentBannerSlide].classList.remove('active');
            if (bannerDots[currentBannerSlide]) bannerDots[currentBannerSlide].classList.remove('active');
            currentBannerSlide = (slideIndex + totalBannerSlides) % totalBannerSlides;
            if (bannerSlides[currentBannerSlide]) bannerSlides[currentBannerSlide].classList.add('active');
            if (bannerDots[currentBannerSlide]) bannerDots[currentBannerSlide].classList.add('active');
        }

        if (totalBannerSlides > 0) {
            if (bannerSlides[0]) bannerSlides[0].classList.add('active');
            if (bannerDots[0]) bannerDots[0].classList.add('active');
            // setInterval(window.nextSlide, 5000); // Bu zaten global'de var, tekrarı önle
        }

        const campaignSlider = document.querySelector('.campaign-slider');
        const campaignDots = document.querySelectorAll('.campaign-dot');

        window.goToCampaignSlide = function(slideIndex) {
            if (!campaignSlider) return;
            const slides = campaignSlider.querySelectorAll('.campaign-item');
            if (slides.length > 0) {
                const slideWidth = slides[0].offsetWidth;
                const gap = parseInt(window.getComputedStyle(campaignSlider).gap) || 24;
                const scrollAmount = (slideWidth + gap) * slideIndex;
                campaignSlider.scrollTo({ left: scrollAmount, behavior: 'smooth' });

                campaignDots.forEach(dot => dot.classList.remove('active'));
                if (campaignDots[slideIndex]) {
                    campaignDots[slideIndex].classList.add('active');
                }
            }
        }
    }
    
    // ### NOT: Bu fonksiyon (initializeSimpleSlider) artık kullanılmıyor ###
    // ### renderBanners tarafından çağrılmıyor. ###
    function initializeSimpleSlider(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        // ... (Bu fonksiyonun içi artık relevant değil, çünkü renderBanners bunu kullanmıyor) ...
    }
    
// Sayfa ilk yüklendiğinde başlat
            initializePage();
            
            // --- YENİ KOD (Geri Tuşu / BFcache Düzeltmesi) ---
            // 'pageshow' olayı, sayfa (önbellekten bile) her görüntülendiğinde çalışır
            window.addEventListener('pageshow', function(event) {
                
                // event.persisted = true ise, sayfa 'Geri' tuşuyla önbellekten (bfcache) yüklendi demektir.
                if (event.persisted) {
                    
                    // Sayfa önbellekten yüklendiğinde, ürünler listesi boş kalır
                    // çünkü 'DOMContentLoaded' tekrar çalışmaz.
                    // Bu yüzden, 'initializePage' fonksiyonunu manuel olarak tekrar tetikliyoruz
                    // ki ürünler, sepet vs. her şey yeniden yüklensin.
                    
                    // AOS animasyonlarını sıfırla (eğer takılı kalmışsa)
                    if (typeof AOS !== 'undefined') {
                        AOS.refresh();
                    }
                   
                    // Ana başlatma fonksiyonunu tekrar çağır
                    initializePage();
                }
            });
             });