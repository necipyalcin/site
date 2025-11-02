// DOSYA ADI: cart.js (Tüm Güncellemeler Dahil - Tam Sürüm)

document.addEventListener('DOMContentLoaded', async () => {
    const cartContainer = document.getElementById('cart-container');
    let products = [];
    let cart = [];
    let currentUser = null;

    // --- API & Helper Functions ---
    const api = async (resource, action = '', method = 'GET', data = null) => {
        let url = `public_api.php?resource=${resource}${action ? '&action=' + action : ''}`;
        const options = { method, headers: { 'Content-Type': 'application/json' }, body: data ? JSON.stringify(data) : null };
        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                const errorResult = await response.json().catch(() => null);
                throw new Error(errorResult?.message || 'Sunucu hatası oluştu.');
            }
            return await response.json();
        } catch (error) {
            console.error(`API Hatası [${resource}/${action}]:`, error);
            showToastNotification(error.message, 'error');
            return null;
        }
    };

    const formatPrice = (price) => (price || 0).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    // GÜNCELLENDİ: Fonksiyona 'duration' parametresi eklendi.
    const showToastNotification = (message, type = 'error', duration = 4000) => {
        const container = document.getElementById('notification-container');
        if (!container) return;
        const toast = document.createElement('div');
        const color = type === 'error' ? 'bg-red-500' : (type === 'success' ? 'bg-green-600' : 'bg-blue-500');
        
        toast.className = `p-4 text-white rounded-lg shadow-xl transform transition-all duration-300 opacity-0 translate-x-10 ${color}`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-x-10');
        }, 10);
        setTimeout(() => {
            toast.classList.add('opacity-0');
            toast.addEventListener('transitionend', () => toast.remove());
        }, duration); // Süre artık dinamik.
    };

    // --- Render Functions ---
    const renderCartPage = () => {
        if (!currentUser) {
            renderLoginRequired();
            return;
        }
        if (cart.length === 0) {
            renderEmptyCart();
            return;
        }

        const totalQuantity = cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalAmount = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

        cartContainer.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-yellow-300 p-4 rounded-xl shadow-lg text-center">
                    <h2 class="text-lg font-semibold text-gray-800 mb-1">Toplam Ürün Adedi</h2>
                    <p class="text-3xl font-bold text-blue-900">${totalQuantity}</p>
                </div>
                <div class="bg-yellow-300 p-4 rounded-xl shadow-lg text-center">
                    <h2 class="text-lg font-semibold text-gray-800 mb-1">Toplam Tutar</h2>
                    <p class="text-3xl font-bold text-green-700">${formatPrice(totalAmount)} ₺</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <div class="w-full lg:w-3/5 bg-blue-700 p-4 rounded-xl shadow-lg">
                    <h2 class="text-xl font-bold mb-4 border-b border-blue-500 pb-2 text-white">Sepetimdeki Ürünler</h2>
                    <div id="cartItemList" class="space-y-4">
                        ${cart.map(renderCartItem).join('')}
                    </div>
                </div>

                <aside class="w-full lg:w-2/5">
                    <form id="orderForm" class="bg-green-100 p-4 rounded-xl shadow-lg sticky top-6">
                        <div class="grid grid-cols-1 gap-y-3">
                            <h4 class="text-xl font-bold border-b pb-2 mb-2 text-blue-900">Teslimat Bilgileri</h4>
                            <div><label class="block text-xs font-medium mb-1 text-blue-900">Ad Soyad *</label><input type="text" name="name" required class="w-full p-2 text-sm border rounded-md bg-white"></div>
                            <div><label class="block text-xs font-medium mb-1 text-blue-900">Telefon *</label><input type="tel" name="phone" required class="w-full p-2 text-sm border rounded-md bg-white"></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="block text-xs font-medium mb-1 text-blue-900">İl (Şehir) *</label><input type="text" name="city" required class="w-full p-2 text-sm border rounded-md bg-white"></div>
                                <div><label class="block text-xs font-medium mb-1 text-blue-900">İlçe *</label><input type="text" name="district" required class="w-full p-2 text-sm border rounded-md bg-white"></div>
                            </div>
                            <div><label class="block text-xs font-medium mb-1 text-blue-900">Adres *</label><textarea name="address" required rows="2" class="w-full p-2 text-sm border rounded-md bg-white"></textarea></div>
                            
                            <div class="mt-2"><label class="block text-xs font-medium mb-1 text-blue-900">Sipariş Notu (isteğe bağlı)</label><textarea name="orderNote" rows="2" class="w-full p-2 text-sm border rounded-md bg-white" placeholder="Siparişinizle ilgili özel bir notunuz varsa buraya yazabilirsiniz..."></textarea></div>

                            <div class="mt-1"><label class="flex items-center text-sm text-blue-900"><input type="checkbox" id="billingAddressCheck" class="mr-2"> Fatura adresim farklı</label></div>
                            
                            <div id="billingAddressSection" class="grid grid-cols-1 gap-y-3 hidden mt-2 border-t pt-3">
                                <h4 class="text-md font-semibold border-b pb-1 mb-1 text-blue-900">Fatura Adresi</h4>
                                <div><label class="block text-xs font-medium mb-1 text-blue-900">Fatura Adresi *</label><textarea name="billingAddress" rows="2" class="w-full p-2 text-sm border rounded-md bg-white"></textarea></div>
                                <div class="grid grid-cols-2 gap-2">
                                  <div><label class="block text-xs font-medium mb-1 text-blue-900">Fatura İli *</label><input type="text" name="billingCity" class="w-full p-2 text-sm border rounded-md bg-white"></div>
                                  <div><label class="block text-xs font-medium mb-1 text-blue-900">Fatura İlçesi *</label><input type="text" name="billingDistrict" class="w-full p-2 text-sm border rounded-md bg-white"></div>
                                </div>
                            </div>
                            
                            <h4 class="text-xl font-bold border-b pb-2 mb-2 mt-3 text-blue-900">Ödeme Yöntemi</h4>
                            <div class="flex gap-3">
                                <div class="payment-option p-3 rounded-lg flex-1 flex items-center">
                                    <input type="radio" name="paymentMethod" value="Kredi Kartı" id="pm-cc" class="mr-2">
                                   a <label for="pm-cc" class="font-bold text-sm cursor-pointer">Kredi Kartı (Yakında)</label>
                                </div>
                                <div class="payment-option p-3 rounded-lg flex-1 flex items-center">
                                    <input type="radio" name="paymentMethod" value="Havale/EFT" id="pm-eft" class="mr-2">
                                    <label for="pm-eft" class="font-bold text-sm cursor-pointer">Havale/EFT</label>
                                </div>
                            </div>
                            
                            <div id="bankInfo" class="mt-2 p-3 bg-white rounded-lg hidden">
                                <h5 class="font-semibold mb-1 text-sm text-blue-900">Banka Bilgileri</h5>
                                <p class="text-xs"><strong>Alıcı:</strong> Müzeyyen Yalçın</p>
                                <p class="text-xs"><strong>Banka:</strong> Akbank</p>
                                <p class="text-xs"><strong>IBAN:</strong> TR12 0004 6002 6088 8000 1439 67</p>
                               <p class="text-sm mt-1 text-red-600 font-bold">Lütfen açıklama kısmına sipariş numaranızı yazınız.</p>
                                <p class="text-sm mt-4 text-red-600 font-bold">Ödemesi EFT ya da havale yapılmayan siparişler dikkate alınmayacaktır.</p>
                            </div>
                        </div>
                        
                        
                        <div class="mt-4 flex items-center gap-3">
                            <button type="button" id="clear-cart-btn" class="w-auto bg-red-600 text-white py-3 px-4 rounded-lg font-semibold text-sm hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash"></i> Sepeti Temizle
                            </button>
                            <button type="submit" class="flex-grow bg-green-600 text-white py-3 rounded-lg font-semibold text-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-shield-alt mr-2"></i>Güvenli Ödeme Yap
                            </button>
                        </div>

                    </form>
                </aside>
            </div>
        `;
        attachEventListeners();
        prefillCheckoutForm();
    };
    
    // ### DEĞİŞİKLİK BURADA BAŞLIYOR ###
    const renderCartItem = (item) => {
        const product = products.find(p => p.id === item.id) || { stock: 0 };
        const subtotal = item.price * item.quantity;
        return `
        <div class="cart-item flex items-start sm:items-center justify-between p-2 border-b border-blue-600 last:border-b-0 gap-3 flex-wrap">
            
            <div class="flex items-center gap-4 flex-grow" style="min-width: 250px;">
                <img src="${item.image || './Resimler/pc.jpg'}" alt="${item.name}" class="w-24 h-24 sm:w-32 sm:h-32 object-contain rounded-md bg-white p-1 flex-shrink-0">
                <div class="flex-grow">
                    <h3 class="font-bold text-lg text-white">${item.name}</h3>
                    <p class="font-semibold text-base text-gray-300">${formatPrice(item.price)} ₺</p>
                </div>
            </div>

            <div class="flex flex-col items-end gap-y-3 ml-auto">
                <div class="flex items-center gap-2">
                    <button data-id="${item.id}" class="quantity-btn quantity-decrease bg-gray-200 text-blue-900 text-lg w-8 h-8 rounded-full flex-shrink-0">-</button>
                    <input type="number" value="${item.quantity}" min="1" max="${product.stock}" data-id="${item.id}" class="quantity-input w-12 border rounded px-1 py-1 text-sm text-center font-semibold text-blue-900">
                    <button data-id="${item.id}" class="quantity-btn quantity-increase bg-gray-200 text-blue-900 text-lg w-8 h-8 rounded-full flex-shrink-0">+</button>
                </div>
                <div class="font-bold text-lg text-right w-28 text-white">${formatPrice(subtotal)} ₺</div>
            </div>

            <div class="flex items-center pl-2">
                 <button data-id="${item.id}" class="remove-item text-red-500 text-xl hover:text-red-400"><i class="fas fa-trash-alt"></i></button>
            </div>
        </div>`;
    };
    // ### DEĞİŞİKLİK BURADA BİTİYOR ###


    const renderEmptyCart = () => {
        cartContainer.innerHTML = `
        <div class="text-center bg-white p-12 rounded-xl shadow-lg">
            <i class="fas fa-shopping-basket text-7xl text-gray-300 mb-6"></i>
            <h2 class="text-3xl font-bold text-gray-800">Sepetinizde Ürün Bulunmuyor</h2>
            <p class="text-gray-600 my-4 text-lg">Görünüşe göre henüz sepetinize bir şey eklemediniz.</p>
            <a href="index.php" class="inline-block mt-4 bg-blue-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-blue-700 transition-all transform hover:scale-105 shadow-lg">
                <i class="fas fa-store mr-2"></i>Hemen Alışverişe Başla
            </a>
        </div>`;
    };

    const renderLoginRequired = () => {
        cartContainer.innerHTML = `
        <div class="text-center bg-white p-12 rounded-xl shadow-lg">
            <i class="fas fa-user-lock text-7xl text-gray-300 mb-6"></i>
            <h2 class="text-3xl font-bold text-gray-800">Lütfen Giriş Yapın</h2>
            <p class="text-gray-600 my-4 text-lg">Sepetinizi görüntülemek ve sipariş vermek için giriş yapmanız gerekmektedir.</p>
            <a href="index.php" class="inline-block mt-4 bg-blue-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-blue-700">
                <i class="fas fa-sign-in-alt mr-2"></i>Giriş Yapmak İçin Ana Sayfaya Dön
            </a>
        </div>`;
    };

    const handleClearCart = async () => {
        if (confirm('Sepetinizdeki tüm ürünleri kalıcı olarak silmek istediğinizden emin misiniz?')) {
            const result = await api('cart', 'clear', 'POST');
            if (result !== null) {
                cart = [];
                renderCartPage();
                showToastNotification('Sepetiniz başarıyla temizlendi.', 'success');
            }
        }
    };

    const attachEventListeners = () => {
        document.querySelectorAll('.quantity-btn').forEach(btn => btn.addEventListener('click', handleQuantityChange));
        document.querySelectorAll('.quantity-input').forEach(input => input.addEventListener('change', handleQuantityInputChange));
        document.querySelectorAll('.remove-item').forEach(btn => btn.addEventListener('click', handleRemoveItem));
        document.getElementById('clear-cart-btn')?.addEventListener('click', handleClearCart);

        const orderForm = document.getElementById('orderForm');
        if (orderForm) {
            orderForm.addEventListener('submit', handleOrder);
            document.getElementById('billingAddressCheck')?.addEventListener('change', (e) => {
                const section = document.getElementById('billingAddressSection');
                section.classList.toggle('hidden', !e.target.checked);
            });
            
            const paymentOptions = document.querySelectorAll('input[name="paymentMethod"]');
            paymentOptions.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
                    const selectedLabel = e.target.closest('.payment-option');
                    if(selectedLabel) selectedLabel.classList.add('selected');
                    document.getElementById('bankInfo').classList.toggle('hidden', e.target.value !== 'Havale/EFT');

                    // GÜNCELLENDİ: Kredi kartı seçildiğinde uyarı gösterme
                    if (e.target.value === 'Kredi Kartı') {
                        showToastNotification(
                            'Kredi kartı ödeme aktif değildir yakında aktif olacaktır lütfen havale eft yi seçin.',
                            'info', // Mavi renkli bilgi uyarısı
                            10000   // 10 saniye ekranda kalır
                        );
                    }
                });
            });
        }
    };

    const handleQuantityChange = (e) => {
        const productId = e.currentTarget.dataset.id;
        const currentItem = cart.find(item => item.id == productId);
        if (!currentItem) return;

        if (e.currentTarget.classList.contains('quantity-decrease')) {
            if (currentItem.quantity === 1) {
                if (confirm('Ürünü sepetten kaldırmak istediğinize emin misiniz?')) {
                    updateQuantity(productId, 0);
                }
            } else {
                updateQuantity(productId, currentItem.quantity - 1);
            }
        } else {
            updateQuantity(productId, currentItem.quantity + 1);
        }
    };

    const handleQuantityInputChange = (e) => {
        const productId = e.target.dataset.id;
        let newQuantity = parseInt(e.target.value, 10);
        if (isNaN(newQuantity) || newQuantity < 0) newQuantity = 1;
        updateQuantity(productId, newQuantity);
    };
    
    const handleRemoveItem = async (e) => {
        const productId = e.currentTarget.dataset.id;
        if (confirm('Bu ürünü sepetten silmek istediğinize emin misiniz?')) {
            const result = await api('cart', 'remove', 'POST', { productId });
            if (result !== null) {
                cart = result;
                renderCartPage();
            }
        }
    };

    const updateQuantity = async (productId, newQuantity) => {
        const product = products.find(p => p.id == productId);
        if (!product && newQuantity > 0) return;
        
        if (newQuantity > 0 && newQuantity > product.stock) {
            showToastNotification(`Stok aşıldı! En fazla ${product.stock} adet ekleyebilirsiniz.`, 'error');
            const inputEl = document.querySelector(`.quantity-input[data-id="${productId}"]`);
            if (inputEl) inputEl.value = product.stock;
            return;
        }
        
        if (newQuantity < 1) {
            newQuantity = 0;
        }

        const result = await api('cart', 'update', 'POST', { productId, quantity: newQuantity });
        if (result !== null) { 
            cart = result; 
            renderCartPage(); 
        }
    };

    const handleOrder = async (e) => {
        e.preventDefault();
        const form = e.target;
        
        const paymentMethod = form.querySelector('input[name="paymentMethod"]:checked');
        if (!paymentMethod) {
            return showToastNotification('Lütfen bir ödeme yöntemi seçin.', 'error');
        }

        // GÜNCELLENDİ: Kredi kartı seçiliyse siparişi engelleme
        if (paymentMethod.value === 'Kredi Kartı') {
            return showToastNotification(
                'Kredi kartı ile ödeme şu anda mümkün değildir. Lütfen Havale/EFT yöntemini seçiniz.',
                'error', // Kırmızı renkli hata uyarısı
                10000    // 10 saniye ekranda kalır
            );
        }

        const billingAddressChecked = form.billingAddressCheck.checked;
        if (billingAddressChecked && (!form.billingAddress.value || !form.billingCity.value || !form.billingDistrict.value)) {
            return showToastNotification('Lütfen tüm fatura adresi alanlarını doldurun.', 'error');
        }

        const orderData = {
            customer: {
                name: form.name.value,
                phone: form.phone.value,
                address: form.address.value,
                city: form.city.value,
                district: form.district.value,
                note: form.orderNote.value,
                billingAddress: billingAddressChecked ? {
                    address: form.billingAddress.value, city: form.billingCity.value, district: form.billingDistrict.value,
                } : 'Teslimat adresi ile aynı'
            },
            paymentMethod: paymentMethod.value
        };

        try {
            const result = await api('orders', '', 'POST', orderData);
            showToastNotification(result.message || 'Siparişiniz başarıyla alındı!', 'success');
            cart = [];
            renderCartPage();
        } catch (error) { /* API function already shows error */ }
    };
    
    const prefillCheckoutForm = async () => {
        const userDetails = await api('users', 'get_details');
        if (userDetails) {
            const form = document.getElementById('orderForm');
            if (!form) return;
            
            form.querySelector('[name="name"]').value = `${userDetails.name || ''} ${userDetails.surname || ''}`.trim();
            form.querySelector('[name="phone"]').value = userDetails.phone || '';
            form.querySelector('[name="address"]').value = userDetails.address || '';
            form.querySelector('[name="city"]').value = userDetails.city || '';
            form.querySelector('[name="district"]').value = userDetails.district || '';

            form.querySelector('[name="billingAddress"]').value = userDetails.billingAddress || '';
            form.querySelector('[name="billingCity"]').value = userDetails.billingCity || '';
            form.querySelector('[name="billingDistrict"]').value = userDetails.billingDistrict || '';
        }
    };

    const init = async () => {
        const status = await api('users', 'status');
        if (status && status.loggedIn) {
            currentUser = status.user;
            products = await api('products') || [];
            cart = await api('cart', 'get') || [];
        }
        renderCartPage();
    };

    init();
});