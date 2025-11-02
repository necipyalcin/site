// DOSYA ADI: admin.js (Sunucu Taraflı Giriş + Yönetim Paneli Eklenmiş)
// VERSİYON: 3 (Banner ve Vitrin Güncellemeleri)

class AdminPanel {
    constructor() {
        // 'vitrinBanners' ve 'pages' eklendi
        this.data = { products: [], categories: [], users: [], orders: [], settings: {}, banners: [], showcase: [], vitrinBanners: [], pages: [] };
        // ...
        this.filters = {
            products: { search: '', categoryId: '', stock: '' },
            categories: { search: '' },
            orders: { search: '', status: '', startDate: '', endDate: '' },
            users: { search: '' },
            vitrinBanners: { search: '' }, // YENİ
            pages: { search: '' } // YENİ
        };
        this.selectedIds = {
            products: new Set(),
            categories: new Set(),
            orders: new Set(),
            users: new Set(),
            vitrinBanners: new Set(), // YENİ
            pages: new Set() // YENİ
        };
        this.currentBulkResource = null;
    }

    async init() {
        this.setupEventListeners();
        
        const results = await Promise.all([
            this.api('products'), 
            this.api('categories'), 
            this.api('users'), 
            this.api('orders'),
            this.api('settings'),
            this.api('banners'),
            this.api('showcase'),
            this.api('vitrinBanners'), // YENİ
            this.api('pages')          // YENİ
        ]);
        
        if (results.some(res => res === null)) {
            // ... (hata kodu aynı) ...
            return;
        }

       // Atamayı güncelle (sıra önemli!)
       [this.data.products, this.data.categories, this.data.users, this.data.orders, this.data.settings, this.data.banners, this.data.showcase, this.data.vitrinBanners, this.data.pages] = results;

        this.switchTab('dashboard');
        if (Notification.permission !== "granted") { Notification.requestPermission(); }
        this.startNewOrderCheck();
    }

    _showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) { console.error('Toast container not found in HTML!'); return; }
        const toast = document.createElement('div');
        const iconClass = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-times-circle' : 'fa-info-circle');
        toast.className = `toast ${type}`;
        toast.innerHTML = `<i class="fas ${iconClass}"></i> ${message}`;
        container.appendChild(toast);
        setTimeout(() => { toast.remove(); }, 5000);
    }

    async api(resource, method = 'GET', data = null, id = null, action = null) {
        let url = `api.php?resource=${resource}`;
        if (id) url += `&id=${id}`;
        if (action) url += `&action=${action}`;
        
        const options = { method, headers: { 'Content-Type': 'application/json' } };
        if (data) options.body = JSON.stringify(data);
        
        try {
            const response = await fetch(url, options);
            if (response.status === 404) {
                throw new Error(`API dosyası (${url}) sunucuda bulunamadı (404).`);
            }
            if (response.status === 401 || response.status === 403) {
                this._showToast('Oturum süreniz doldu veya yetkiniz yok. Lütfen tekrar giriş yapın.', 'error');
                setTimeout(() => window.location.href = 'yonetim-giris-a4b8c2.php', 2000);
                return null;
            }
            if (!response.ok && response.status !== 204) {
                 const errorData = await response.json().catch(() => ({ message: `Sunucudan geçersiz yanıt: ${response.statusText}` }));
                 throw new Error(errorData?.message || `API isteği başarısız: ${response.statusText}`);
            }
            if (response.status === 204) return { success: true };
            return await response.json();
        } catch (error) { 
            console.error(`API hatası (${resource}):`, error); 
            this._showToast(error.message || 'Bir hata oluştu.', 'error');
            return null; 
        }
    }
    
    async guncelleVeKaydetUrunlerHTML() {
        console.log('Ana sayfa HTML güncelleniyor...');
        const allProducts = await this.api('products');
        if (!allProducts) {
            this._showToast('Ürün listesi alınamadı, ana sayfa güncellenemedi.', 'error');
            return;
        }
        try {
            const response = await fetch('urunleri_kaydet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(allProducts)
            });
            const result = await response.json();
            if (result.success) {
                this._showToast('Ana sayfa ürün listesi başarıyla güncellendi!', 'success');
            } else {
                throw new Error(result.message || 'Bilinmeyen bir PHP hatası.');
            }
        } catch (error) {
            console.error('HTML kaydetme hatası:', error);
            this._showToast(`Ana sayfa güncellenirken hata: ${error.message}`, 'error');
        }
    }

    setupEventListeners() {
        document.getElementById('logoutBtn').addEventListener('click', () => {
            window.location.href = 'logout.php';
        });
        document.querySelectorAll('.nav-item').forEach(b => b.addEventListener('click', () => this.switchTab(b.dataset.tab)));
        document.getElementById('productForm').addEventListener('submit', (e) => { e.preventDefault(); this.saveProduct(); });
        document.getElementById('categoryForm').addEventListener('submit', (e) => { e.preventDefault(); this.saveCategory(); });
        document.getElementById('userForm').addEventListener('submit', (e) => { e.preventDefault(); this.saveUser(); });
        document.getElementById('productCategoryId').addEventListener('change', (e) => this.populateSubCategorySelect(e.target.value));
        
        // ### YENİ: Banner Formu Listener'ları ###
        document.getElementById('bannerForm').addEventListener('submit', (e) => { e.preventDefault(); this.saveBanner(); });
        document.getElementById('bannerLinkType').addEventListener('change', (e) => this.toggleBannerLinkType(e.target.value));
        document.getElementById('bannerProductSearch').addEventListener('input', (e) => this.searchBannerProducts(e.target.value));
        
        // ### YENİ: Vitrin Banner Formu Listener'ları ###
        document.getElementById('vitrinBannerForm').addEventListener('submit', (e) => { e.preventDefault(); this.saveVitrinBanner(); });
        document.getElementById('vitrinBannerLinkType').addEventListener('change', (e) => this.toggleVitrinBannerLinkType(e.target.value));
        document.getElementById('vitrinBannerProductSearch').addEventListener('input', (e) => this.searchVitrinBannerProducts(e.target.value));
        
        // ### YENİ: Dinamik Sayfa Formu Listener'ı ###
        document.getElementById('pageForm').addEventListener('submit', (e) => { e.preventDefault(); this.savePage(); });
        // 'iletisim' slug'ına özel alanları göstermek için
        document.getElementById('pageSlug').addEventListener('input', (e) => this.toggleContactFields(e.target.value));
        
        document.getElementById('confirm-bulk-delete-btn').addEventListener('click', () => this.handleBulkDelete());
        // ... (geri kalanı aynı) ...
        document.getElementById('bannerLinkType').addEventListener('change', (e) => this.toggleBannerLinkType(e.target.value));
        document.getElementById('bannerProductSearch').addEventListener('input', (e) => this.searchBannerProducts(e.target.value));
        
        document.getElementById('confirm-bulk-delete-btn').addEventListener('click', () => this.handleBulkDelete());
        document.getElementById('bulk-delete-input').addEventListener('input', (e) => {
            const confirmBtn = document.getElementById('confirm-bulk-delete-btn');
            if (e.target.value.trim().toUpperCase() === 'SİL') {
                confirmBtn.disabled = false;
                confirmBtn.classList.replace('bg-red-300', 'bg-red-600');
            } else {
                confirmBtn.disabled = true;
                confirmBtn.classList.replace('bg-red-600', 'bg-red-300');
            }
        });
        document.getElementById('bulk-price-update-form').addEventListener('submit', (e) => { e.preventDefault(); this.handleBulkPriceUpdate(); });
        document.getElementById('bulk-stock-update-form').addEventListener('submit', (e) => { e.preventDefault(); this.handleBulkStockUpdate(); });
    }

    switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
        const tab = document.getElementById(tabName);
        if (tab) tab.classList.add('active');
        const navItem = document.querySelector(`.nav-item[data-tab="${tabName}"]`);
        if (navItem) navItem.classList.add('active');
        const renderFunction = this['render' + tabName.charAt(0).toUpperCase() + tabName.slice(1)];
        if (typeof renderFunction === 'function') renderFunction.call(this);
    }
    
    async exportToPDF(contentId, fileName) {
        this._showToast('PDF oluşturuluyor, lütfen bekleyin...', 'info');
        const { jsPDF } = window.jspdf;
        const content = document.getElementById(contentId);
        
        try {
            const canvas = await html2canvas(content, { scale: 2, useCORS: true });
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF('p', 'mm', 'a4');
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
            let heightLeft = pdfHeight;
            let position = 0;
            
            pdf.addImage(imgData, 'PNG', 0, position, pdfWidth, pdfHeight);
            heightLeft -= pdf.internal.pageSize.getHeight();

            while (heightLeft >= 0) {
              position = heightLeft - pdfHeight;
              pdf.addPage();
              pdf.addImage(imgData, 'PNG', 0, position, pdfWidth, pdfHeight);
              heightLeft -= pdf.internal.pageSize.getHeight();
            }
            pdf.save(fileName);
        } catch (error) {
            console.error("PDF oluşturma hatası:", error);
            this._showToast('PDF oluşturulurken bir hata oluştu.', 'error');
        }
    }

    async showCustomerReport(userId) {
        const user = this.data.users.find(u => u.id === userId);
        if (!user) return this._showToast('Müşteri bulunamadı.', 'error');

        const userOrders = this.data.orders.filter(o => o.userId === userId).sort((a, b) => new Date(b.date) - new Date(a.date));
        const totalSpent = userOrders.reduce((sum, o) => sum + (o.total || 0), 0);
        
        let ordersHtml = userOrders.map(order => `
            <div class="p-3 border rounded-md mb-3 bg-gray-50">
                <div class="flex justify-between items-center flex-wrap">
                    <h5 class="font-semibold">Sipariş #${order.id}</h5>
                    <span class="text-xs text-gray-500">${new Date(order.date).toLocaleDateString('tr-TR')}</span>
                </div>
                <p class="text-sm mt-1"><strong>Tutar:</strong> ${(order.total || 0).toFixed(2)}₺ | <strong>Durum:</strong> ${order.status}</p>
                <p class="text-xs mt-1 text-gray-600"><strong>Teslimat Adresi:</strong> ${order.customerInfo.address}, ${order.customerInfo.district || ''} / ${order.customerInfo.city || ''}</p>
                <ul class="list-disc list-inside pl-4 mt-2 text-xs space-y-1">
                    ${order.items.map(item => `<li>${item.quantity} x ${item.name} (${(item.price || 0).toFixed(2)}₺)</li>`).join('')}
                </ul>
            </div>
        `).join('');

        if (userOrders.length === 0) {
            ordersHtml = '<p>Bu müşterinin henüz siparişi bulunmuyor.</p>';
        }

        const reportContentContainer = document.getElementById('customer-report-content');
        if(reportContentContainer) {
            reportContentContainer.innerHTML = `
                <div id="report-to-export" class="p-2">
                    <div class="space-y-2 mb-4">
                        <p><strong>Toplam Harcama:</strong> <span class="font-bold text-lg">${totalSpent.toFixed(2)} ₺</span></p>
                        <p><strong>Toplam Sipariş Sayısı:</strong> <span class="font-bold text-lg">${userOrders.length}</span></p>
                    </div>
                    <h4 class="text-lg font-semibold mt-4 border-t pt-3">Sipariş Detayları</h4>
                    <div class="mt-2">${ordersHtml}</div>
                </div>
            `;
        }
        
        document.getElementById('customer-reportModalTitle').textContent = `${user.name || user.firstName} ${user.surname || user.lastName} - Müşteri Raporu`;
        
        const pdfBtn = document.getElementById('export-pdf-btn');
        pdfBtn.onclick = () => this.exportToPDF('report-to-export', `rapor-${user.id}.pdf`);

        this.showModal('customer-report');
    }

    renderDashboard() {
        const container = document.getElementById('dashboard');
        if (!container) return;
        const statusColors = { 'Beklemede': 'bg-yellow-100 text-yellow-800', 'Ödeme Bekleniyor': 'bg-orange-100 text-orange-800', 'Hazırlanıyor': 'bg-blue-100 text-blue-800', 'Kargoda': 'bg-indigo-100 text-indigo-800', 'Teslim Edildi': 'bg-green-100 text-green-800', 'İptal Edildi': 'bg-red-100 text-red-800' };
        const totalSales = (this.data.orders || []).reduce((sum, order) => sum + (parseFloat(order.total) || 0), 0);
        const totalOrders = (this.data.orders || []).length;
        const totalCustomers = (this.data.users || []).length;
        const totalProducts = (this.data.products || []).length;
        const statusCounts = { 'Beklemede': 0, 'Ödeme Bekleniyor': 0, 'Hazırlanıyor': 0, 'Kargoda': 0, 'Teslim Edildi': 0, 'İptal Edildi': 0 };
        (this.data.orders || []).forEach(order => { if (statusCounts.hasOwnProperty(order.status)) { statusCounts[order.status]++; } });
        const monthlyTotals = Array(12).fill(0);
        (this.data.orders || []).forEach(order => { const d = new Date(order.date); if(d.getFullYear() === 2025) { monthlyTotals[d.getMonth()] += parseFloat(order.total)||0;} });
        container.innerHTML = `
            <div class="space-y-8">
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Genel Durum</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-center">
                        <div class="bg-yellow-100 p-6 rounded-lg shadow-sm"><h3 class="text-sm font-semibold text-yellow-800">Toplam Satış</h3><p class="text-3xl font-bold text-yellow-900">₺${totalSales.toLocaleString('tr-TR', { minimumFractionDigits: 2 })}</p></div>
                        <div class="bg-green-100 p-6 rounded-lg shadow-sm"><h3 class="text-sm font-semibold text-green-800">Toplam Sipariş</h3><p class="text-3xl font-bold text-green-900">${totalOrders}</p></div>
                        <div class="bg-blue-100 p-6 rounded-lg shadow-sm"><h3 class="text-sm font-semibold text-blue-800">Toplam Müşteri</h3><p class="text-3xl font-bold text-blue-900">${totalCustomers}</p></div>
                        <div class="bg-purple-100 p-6 rounded-lg shadow-sm"><h3 class="text-sm font-semibold text-purple-800">Toplam Ürün</h3><p class="text-3xl font-bold text-purple-900">${totalProducts}</p></div>
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Sipariş Özeti</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                        ${Object.entries(statusCounts).map(([status, count]) => `
                            <div class="${statusColors[status]} p-4 rounded-lg text-center shadow-sm">
                                <h3 class="text-sm font-semibold">${status}</h3><p class="text-3xl font-bold">${count}</p>
                            </div>`).join('')}
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm w-full">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">2025 Satış Grafiği</h2>
                    <div class="relative h-[350px]"><canvas id="salesChart"></canvas></div>
                </div>
            </div>`;
        const ctx = document.getElementById('salesChart').getContext('2d');
        if (window.mySalesChart) window.mySalesChart.destroy();
        window.mySalesChart = new Chart(ctx, { type: 'line', data: { labels: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'], datasets: [{ label: 'Satış (₺)', data: monthlyTotals, borderColor: '#4f46e5', tension: 0.1, fill: true, backgroundColor: 'rgba(79, 70, 229, 0.1)' }] }, options: { maintainAspectRatio: false } });
    }

    renderProducts() {
        const container = document.getElementById('products');
        if (!container) return;
        const categoryOptions = (this.data.categories || []).map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        container.innerHTML = `
            <div class="flex justify-between items-center mb-6"><h2 class="text-3xl font-bold">Ürün Yönetimi</h2><button onclick="adminPanel.showModal('product')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"><i class="fas fa-plus mr-2"></i>Yeni Ürün</button></div>
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" id="product-search" placeholder="Ürün adı veya ID ile ara..." class="p-2 border rounded">
                    <select id="product-category-filter" class="p-2 border rounded"><option value="">Tüm Kategoriler</option>${categoryOptions}</select>
                    <select id="product-stock-filter" class="p-2 border rounded"><option value="">Tüm Stok Durumları</option><option value="in_stock">Stokta Var</option><option value="out_of_stock">Stok Tükendi</option></select>
                </div>
            </div>
            <div id="bulk-actions-products" class="hidden mb-4 flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <span id="selected-count-products" class="font-bold"></span>
                <button onclick="adminPanel.showModal('bulk-price-update')" class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600">Toplu Fiyat Güncelle</button>
                <button onclick="adminPanel.showModal('bulk-stock-update')" class="bg-green-500 text-white px-3 py-1 rounded-md text-sm hover:bg-green-600">Toplu Stok Güncelle</button>
                <button onclick="adminPanel.showBulkDeleteModal('products')" class="bg-red-500 text-white px-3 py-1 rounded-md text-sm hover:bg-red-600">Seçilenleri Sil</button>
            </div>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4 w-10"><input type="checkbox" onchange="adminPanel.toggleSelectAll('products', this.checked)"></th>
                            <th class="p-4 text-left">#</th><th class="p-4 text-left">Resim</th><th class="p-4 text-left">Ürün Adı</th>
                            <th class="p-4 text-left">Stok (Giriş/Satış/Kalan)</th><th class="p-4 text-left">Fiyat</th><th class="p-4 text-left">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="productsTable" class="divide-y"></tbody>
                </table>
            </div>`;
        document.getElementById('product-search').addEventListener('input', (e) => { this.filters.products.search = e.target.value; this.displayProducts(); });
        document.getElementById('product-category-filter').addEventListener('change', (e) => { this.filters.products.categoryId = e.target.value; this.displayProducts(); });
        document.getElementById('product-stock-filter').addEventListener('change', (e) => { this.filters.products.stock = e.target.value; this.displayProducts(); });
        this.displayProducts();
    }

    displayProducts() {
        const { search, categoryId, stock } = this.filters.products;
        let filtered = (this.data.products || []).filter(p => {
            const matchesSearch = (p.name || '').toLowerCase().includes(search.toLowerCase()) || (p.id || '').toString().includes(search);
            const matchesCategory = !categoryId || p.categoryId == categoryId;
            const matchesStock = !stock || (stock === 'in_stock' && p.stock > 0) || (stock === 'out_of_stock' && p.stock <= 0);
            return matchesSearch && matchesCategory && matchesStock;
        });
        const tableBody = document.getElementById('productsTable');
        tableBody.innerHTML = filtered.map((p, index) => `
            <tr>
                <td class="p-4"><input type="checkbox" onchange="adminPanel.toggleSelection('products', ${p.id}, this.checked)" ${this.selectedIds.products.has(p.id) ? 'checked' : ''}></td>
                <td class="p-4 text-sm text-gray-500">${index + 1}</td>
                <td class="p-4"><img src="${p.image || 'https://via.placeholder.com/150'}" alt="${p.name}" class="w-16 h-16 object-cover rounded"></td>
                <td class="p-4 font-medium">${p.name}</td>
                <td class="p-4 text-sm">
                    <span class="text-green-600 font-semibold">Giriş: ${p.totalStockIn || 0}</span> / 
                    <span class="text-red-600 font-semibold">Satış: ${p.totalSold || 0}</span> / 
                    <span class="text-blue-600 font-bold">Kalan: ${p.stock}</span>
                </td>
                <td class="p-4">${(p.price || 0).toFixed(2)}₺</td>
                <td class="p-4 space-x-2 whitespace-nowrap"><button onclick="adminPanel.showModal('product', ${p.id})" class="text-indigo-600">Düzenle</button><button onclick="adminPanel.deleteResource('products', ${p.id})" class="text-red-600">Sil</button></td>
            </tr>`).join('');
    }

    renderCategories() {
        const container = document.getElementById('categories');
        if(!container) return;
        container.innerHTML = `
            <div class="flex justify-between items-center mb-6"><h2 class="text-3xl font-bold">Kategori Yönetimi</h2><button onclick="adminPanel.showModal('category')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"><i class="fas fa-plus mr-2"></i>Yeni Kategori</button></div>
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4"><input type="text" id="category-search" placeholder="Kategori ara..." class="p-2 border rounded w-full md:w-1/3"></div>
            <div id="bulk-actions-categories" class="hidden mb-4 flex items-center gap-4 p-3 bg-gray-50 rounded-lg"><span id="selected-count-categories" class="font-bold"></span><button onclick="adminPanel.showBulkDeleteModal('categories')" class="bg-red-500 text-white px-3 py-1 rounded-md text-sm hover:bg-red-600"><i class="fas fa-trash-alt mr-1"></i>Seçilenleri Sil</button></div>
            <div class="bg-white rounded-lg shadow overflow-x-auto"><table class="min-w-full"><thead class="bg-gray-50"><tr><th class="p-4 w-10"><input type="checkbox" onchange="adminPanel.toggleSelectAll('categories', this.checked)"></th><th class="p-4 text-left">#</th><th class="p-4 text-left">Kategori Adı</th><th class="p-4 text-left">Üst Kategori</th><th class="p-4 text-left">İşlemler</th></tr></thead><tbody id="categoriesTable" class="divide-y"></tbody></table></div>`;
        document.getElementById('category-search').addEventListener('input', (e) => { this.filters.categories.search = e.target.value; this.displayCategories(); });
        this.displayCategories();
    }

    displayCategories() {
        const { search } = this.filters.categories;
        const filtered = (this.data.categories || []).filter(c => c.name.toLowerCase().includes(search.toLowerCase()));
        document.getElementById('categoriesTable').innerHTML = filtered.map((c, index) => {
            const parent = (this.data.categories || []).find(p => p.id === c.parentId);
            return `<tr>
                <td class="p-4"><input type="checkbox" onchange="adminPanel.toggleSelection('categories', ${c.id}, this.checked)" ${this.selectedIds.categories.has(c.id) ? 'checked' : ''}></td>
                <td class="p-4 text-sm text-gray-500">${index + 1}</td>
                <td class="p-4">${c.name}</td><td class="p-4">${parent?parent.name:'Ana Kategori'}</td>
                <td class="p-4 space-x-2"><button onclick="adminPanel.showModal('category', ${c.id})" class="text-indigo-600">Düzenle</button><button onclick="adminPanel.deleteResource('categories', ${c.id})" class="text-red-600">Sil</button></td>
            </tr>`;
        }).join('');
    }
    
    renderOrders() {
        const container = document.getElementById('orders');
        if (!container) return;
        container.innerHTML = `
            <h2 class="text-3xl font-bold mb-6">Sipariş Yönetimi</h2>
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4"><div class="grid grid-cols-1 md:grid-cols-4 gap-4"><input type="text" id="order-search" placeholder="Müşteri adı veya Sipariş ID..." class="p-2 border rounded"><select id="order-status-filter" class="p-2 border rounded"><option value="">Tüm Durumlar</option>${['Beklemede', 'Ödeme Bekleniyor', 'Hazırlanıyor', 'Kargoda', 'Teslim Edildi', 'İptal Edildi'].map(s=>`<option>${s}</option>`).join('')}</select><input type="date" id="order-start-date" class="p-2 border rounded"><input type="date" id="order-end-date" class="p-2 border rounded"></div></div>
            <div id="bulk-actions-orders" class="hidden mb-4 flex items-center gap-4 p-3 bg-gray-50 rounded-lg"><span id="selected-count-orders" class="font-bold"></span><button onclick="adminPanel.showBulkDeleteModal('orders')" class="bg-red-500 text-white px-3 py-1 rounded-md text-sm hover:bg-red-600">Seçilenleri Sil</button></div>
            <div class="bg-white rounded-lg shadow overflow-x-auto"><table class="min-w-full"><thead class="bg-gray-50"><tr><th class="p-4 w-10"><input type="checkbox" onchange="adminPanel.toggleSelectAll('orders', this.checked)"></th><th class="p-4 text-left">#</th><th class="p-4 text-left">Sipariş ID</th><th class="p-4 text-left">Müşteri</th><th class="p-4 text-left">Tarih</th><th class="p-4 text-left">Tutar</th><th class="p-4 text-left">Durum</th><th class="p-4 text-left">İşlemler</th></tr></thead><tbody id="ordersTable" class="divide-y"></tbody></table></div>`;
        ['order-search', 'order-status-filter', 'order-start-date', 'order-end-date'].forEach(id => {
            document.getElementById(id).addEventListener('input', (e) => {
                const keyMap = { 'order-search': 'search', 'order-status-filter': 'status', 'order-start-date': 'startDate', 'order-end-date': 'endDate' };
                this.filters.orders[keyMap[id]] = e.target.value;
                this.displayOrders();
            });
        });
        this.displayOrders();
    }
    
    displayOrders() {
        const { search, status, startDate, endDate } = this.filters.orders;
        const filtered = (this.data.orders || []).filter(o => {
            const customerName = (o.customerInfo?.name || '').toLowerCase();
            return (customerName.includes(search.toLowerCase()) || o.id.toString().includes(search)) &&
                   (!status || o.status === status) &&
                   (!startDate || new Date(o.date) >= new Date(startDate)) &&
                   (!endDate || new Date(o.date) <= new Date(endDate));
        });
        document.getElementById('ordersTable').innerHTML = filtered.map((o, index) => `
            <tr>
                <td class="p-4"><input type="checkbox" onchange="adminPanel.toggleSelection('orders', ${o.id}, this.checked)" ${this.selectedIds.orders.has(o.id) ? 'checked' : ''}></td>
                <td class="p-4 text-sm text-gray-500">${index + 1}</td>
                <td class="p-4 font-bold text-gray-500">#${o.id}</td>
                <td class="p-4">${o.customerInfo.name}</td>
                <td class="p-4">${new Date(o.date).toLocaleDateString('tr-TR')}</td>
                <td class="p-4">${(o.total || 0).toFixed(2)}₺</td><td class="p-4">${o.status}</td>
                <td class="p-4 space-x-2 whitespace-nowrap">
                    <button onclick="adminPanel.showModal('order', ${o.id})" class="text-indigo-600">Detay</button>
                    <button onclick="adminPanel.deleteResource('orders', ${o.id})" class="text-red-600">Sil</button>
                </td>
            </tr>`).join('');
    }

    renderUsers() {
        const container = document.getElementById('users');
        if(!container) return;
        container.innerHTML = `
            <div class="flex justify-between items-center mb-6"><h2 class="text-3xl font-bold">Müşteri Yönetimi</h2><div><button onclick="adminPanel.switchTab('users')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 mr-2"><i class="fas fa-sync-alt mr-2"></i>Yenile</button><button onclick="adminPanel.showModal('user')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"><i class="fas fa-plus mr-2"></i>Yeni Müşteri</button></div></div>
            <div class="bg-white p-4 rounded-lg shadow-sm mb-4"><input type="text" id="user-search" placeholder="Ad, soyad veya email ile ara..." class="p-2 border rounded w-full md:w-1/3"></div>
            <div id="bulk-actions-users" class="hidden mb-4 flex items-center gap-4 p-3 bg-gray-50 rounded-lg"><span id="selected-count-users" class="font-bold"></span><button onclick="adminPanel.showBulkDeleteModal('users')" class="bg-red-500 text-white px-3 py-1 rounded-md text-sm hover:bg-red-600">Seçilenleri Sil</button></div>
            <div class="bg-white rounded-lg shadow overflow-x-auto"><table class="min-w-full"><thead class="bg-gray-50"><tr><th class="p-4 w-10"><input type="checkbox" onchange="adminPanel.toggleSelectAll('users', this.checked)"></th><th class="p-4 text-left">#</th><th class="p-4 text-left">Ad Soyad</th><th class="p-4 text-left">Email</th><th class="p-4 text-left">Kayıt Tarihi</th><th class="p-4 text-left">İşlemler</th></tr></thead><tbody id="usersTable" class="divide-y"></tbody></table></div>`;
        document.getElementById('user-search').addEventListener('input', (e) => { this.filters.users.search = e.target.value; this.displayUsers(); });
        this.displayUsers();
    }
    
    displayUsers() {
        const { search } = this.filters.users;
        const filtered = (this.data.users || []).filter(u => `${u.name || u.firstName || ''} ${u.surname || u.lastName || ''}`.toLowerCase().includes(search.toLowerCase()) || (u.email || '').toLowerCase().includes(search.toLowerCase()));
        const tableBody = document.getElementById('usersTable');
        tableBody.innerHTML = filtered.map((u, index) => `
            <tr>
                <td class="p-4"><input type="checkbox" onchange="adminPanel.toggleSelection('users', ${u.id}, this.checked)" ${this.selectedIds.users.has(u.id) ? 'checked' : ''}></td>
                <td class="p-4 text-sm text-gray-500">${index + 1}</td>
                <td class="p-4 font-medium">${u.name || u.firstName || ''} ${u.surname || u.lastName || ''}</td>
                <td class="p-4">${u.email}</td><td class="p-4 text-sm">${u.registeredAt ? new Date(u.registeredAt).toLocaleDateString('tr-TR') : '-'}</td>
                <td class="p-4 space-x-3 whitespace-nowrap">
                    <button onclick="adminPanel.showCustomerReport(${u.id})" class="text-green-600 hover:text-green-800 font-medium text-sm">Rapor</button>
                    <button onclick="adminPanel.showModal('user', ${u.id})" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Düzenle</button>
                    <button onclick="adminPanel.deleteResource('users', ${u.id})" class="text-red-600 hover:text-red-800 font-medium text-sm">Sil</button>
                </td>
            </tr>`).join('');
    }
    
    showModal(type, id = null, ...args) { // <<< Fonksiyon imzasını "...args" ile güncelleyin
        this.currentEditId = id;
        const form = document.getElementById(`${type}Form`);
        if (form) form.reset();
        const modalTitle = document.getElementById(`${type}ModalTitle`);
        if(modalTitle) modalTitle.textContent = id ? `Kaydı Düzenle (ID: ${id})` : `Yeni Kayıt Ekle`;
        
        if (type === 'product') this.prepareProductModal(id);
        else if (type === 'category') this.prepareCategoryModal(id);
        else if (type === 'order') this.prepareOrderModal(id);
        else if (type === 'user') this.prepareUserModal(id);
        else if (type === 'banner') this.prepareBannerModal(id);
        else if (type === 'vitrinBanner') this.prepareVitrinBannerModal(id); // YENİ
        else if (type === 'page') this.preparePageModal(id, args[0] || null, args[1] || null); // YENİ (args'ı geç)
        
        const modal = document.getElementById(`${type}Modal`);
        if (modal) modal.classList.add('active');
    }

   hideModal(type) { 
        // YENİ: Sayfa modalı kapanırken TinyMCE'yi yok et
        if (type === 'page' && window.tinymce && tinymce.get('pageContent')) {
            tinymce.remove('#pageContent');
        }
        
        const modal = document.getElementById(`${type}Modal`);
        if (modal) modal.classList.remove('active');
    }

  // ### GÜNCELLENDİ: preparePageModal (GELİŞMİŞ TinyMCE + Checkbox eklendi) ###
    preparePageModal(id, defaultSlug = null, defaultTitle = null) {
        const form = document.getElementById('pageForm');
        form.reset();
        this.toggleContactFields(defaultSlug); 

        // Önceki TinyMCE editörlerini (varsa) kaldır
        if (window.tinymce && tinymce.get('pageContent')) {
            tinymce.remove('#pageContent');
        }

        if (id) {
            // Mevcut kaydı düzenle
            const item = this.data.pages.find(i => i.id === id);
            document.getElementById('pageModalTitle').textContent = `Sayfayı Düzenle: ${item.title}`;
            document.getElementById('pageId').value = item.id;
            document.getElementById('pageTitle').value = item.title;
            document.getElementById('pageSlug').value = item.slug;
            document.getElementById('pageContent').value = item.content || ''; // Textarea'ya içeriği bas
            document.getElementById('pageShowInMenu').checked = !!item.showInMenu; // Checkbox'ı ayarla
            
            // İletişim özel alanları
            if (item.slug === 'iletisim') {
                document.getElementById('pageAddress').value = item.address || '';
                document.getElementById('pagePhone').value = item.phone || '';
                document.getElementById('pageContactEmail').value = item.contactEmail || '';
                document.getElementById('pageMapUrl').value = item.mapUrl || '';
                this.toggleContactFields('iletisim');
            }
        } else {
            // Yeni kayıt
            document.getElementById('pageModalTitle').textContent = 'Yeni Dinamik Sayfa Ekle';
            document.getElementById('pageId').value = '';
            document.getElementById('pageTitle').value = defaultTitle || '';
            document.getElementById('pageSlug').value = defaultSlug || '';
            document.getElementById('pageContent').value = ''; // İçeriği boşalt
            document.getElementById('pageShowInMenu').checked = true; // Varsayılan olarak menüde göster
            this.toggleContactFields(defaultSlug);
        }
        
        // ### YENİ: İSTEDİĞİNİZ GELİŞMİŞ TinyMCE Editörünü Başlat ###
        // (Yazı Tipi, Boyutu, Rengi ve Google Harita için 'media' eklentisi dahil)
        tinymce.init({
            selector: '#pageContent',
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount visualchars emoticons template nonbreaking',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media template | forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | help',
            menubar: 'file edit view insert format tools table help',
            height: 500,
            media_embed: true, // Google Harita (media plugin'i ile iframe olarak eklemeyi sağlar)
            setup: function (editor) {
                editor.on('init', function () {
                    // Düzenle modunda içeriği ayarla
                    if (id) {
                        const item = adminPanel.data.pages.find(i => i.id === id);
                        editor.setContent(item.content || '');
                    } else {
                        editor.setContent('');
                    }
                });
            }
        });
    }

    prepareCategoryModal(id) {
        document.getElementById('categoryParentId').innerHTML = '<option value="">Ana Kategori</option>' + (this.data.categories || []).filter(c => !c.parentId && c.id !== id).map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        if (id) {
            const item = this.data.categories.find(i => i.id === id);
            Object.keys(item).forEach(k => { const el = document.getElementById(`category${k.charAt(0).toUpperCase()+k.slice(1)}`); if(el) el.value = item[k]; });
        }
    }

    prepareUserModal(id) {
        if(id) {
            const item = this.data.users.find(i => i.id === id);
            document.getElementById('userName').value = item.name || item.firstName || '';
            document.getElementById('userSurname').value = item.surname || item.lastName || '';
            document.getElementById('userEmail').value = item.email || '';
            // Diğer adres vs. alanları da buraya eklenebilir
        }
    }

    prepareOrderModal(id) {
        const order = this.data.orders.find(o => o.id === id);
        if (!order) return;
        const itemsHtml = (order.items || []).map((item, index) => `
            <tr class="hover:bg-gray-50"><td class="p-2">${item.name || item.productName}</td><td class="p-2">${item.quantity}</td><td class="p-2">${(item.price || 0).toFixed(2)} ₺</td><td class="p-2"><button onclick="adminPanel.removeOrderItem(${order.id}, ${index})" class="text-red-500 text-xs"><i class="fas fa-trash"></i> Sil</button></td></tr>`).join('');
        const deliveryAddress = `${order.customerInfo.address || ''}, ${order.customerInfo.district || ''} / ${order.customerInfo.city || ''}`;
        const billingAddress = typeof order.customerInfo.billingAddress === 'object' 
            ? `${order.customerInfo.billingAddress.address || ''}, ${order.customerInfo.billingAddress.district || ''} / ${order.customerInfo.billingAddress.city || ''}` 
            : (order.customerInfo.billingAddress || 'Teslimat adresi ile aynı');
        document.getElementById('orderDetailsContent').innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-bold text-lg mb-2">Müşteri ve Sipariş Bilgileri</h4>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2 text-sm">
                        <p><strong>Ad Soyad:</strong> ${order.customerInfo.name}</p>
                        <p><strong>Ödeme Yöntemi:</strong> ${order.paymentMethod || 'Belirtilmemiş'}</p>
                        <p><strong>Teslimat Adresi:</strong> ${deliveryAddress}</p>
                        <p><strong>Fatura Adresi:</strong> ${billingAddress}</p>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-2">Sipariş Durumu ve Kargo</h4>
                    <form id="orderUpdateForm" class="space-y-3">
                        <input type="hidden" id="orderUpdateId" value="${order.id}">
                        <div><label class="text-sm">Durum</label><select id="orderUpdateStatus" class="w-full p-2 border rounded mt-1">${['Ödeme Bekleniyor', 'Beklemede', 'Hazırlanıyor', 'Kargoda', 'Teslim Edildi', 'İptal Edildi'].map(s => `<option value="${s}" ${order.status === s ? 'selected' : ''}>${s}</option>`).join('')}</select></div>
                        <div><label class="text-sm">Kargo Firması</label><input type="text" id="orderShippingCarrier" value="${order.shippingCarrier || ''}" class="w-full p-2 border rounded mt-1"></div>
                        <div><label class="text-sm">Takip Numarası</label><input type="text" id="orderShippingTracking" value="${order.shippingTracking || ''}" class="w-full p-2 border rounded mt-1"></div>
                        <button type="submit" class="w-full mt-3 bg-indigo-600 text-white p-2 rounded hover:bg-indigo-700">Kaydet</button>
                    </form>
                </div>
            </div>
            <div class="mt-6"><h4 class="font-bold text-lg mb-2">Sipariş İçeriği</h4><div class="bg-white rounded-lg shadow overflow-x-auto"><table class="min-w-full"><thead class="bg-gray-50"><tr><th class="p-2 text-left text-sm">Ürün</th><th class="p-2 text-left text-sm">Adet</th><th class="p-2 text-left text-sm">Birim Fiyat</th><th class="p-2 text-left text-sm">İşlem</th></tr></thead><tbody class="divide-y">${itemsHtml}</tbody></table></div><p class="text-right font-bold text-xl mt-4">Toplam: ${(order.total || 0).toFixed(2)} ₺</p></div>`;
        document.getElementById('orderUpdateForm').addEventListener('submit', (e) => { e.preventDefault(); this.saveOrder(); });
    }
    
    // ### YENİ: Banner Modalını Hazırlama Fonksiyonu ###
    prepareBannerModal(id) {
        document.getElementById('bannerForm').reset();
        document.getElementById('bannerId').value = '';
        document.getElementById('bannerProductId').value = '';
        document.getElementById('bannerSelectedProduct').textContent = '';
        document.getElementById('bannerProductList').innerHTML = '';
        this.toggleBannerLinkType('custom'); // Varsayılana dön

        if (id) {
            const item = this.data.banners.find(i => i.id === id);
            document.getElementById('bannerModalTitle').textContent = `Banner Düzenle (ID: ${id})`;
            document.getElementById('bannerId').value = item.id;
            document.getElementById('bannerSlot').value = item.slot;
            document.getElementById('bannerImage').value = item.image;
            document.getElementById('bannerDescription').value = item.description;

            if (item.productId) {
                // Bu bir ürün linki
                this.toggleBannerLinkType('product');
                document.getElementById('bannerLinkType').value = 'product';
                document.getElementById('bannerProductId').value = item.productId;
                const product = this.data.products.find(p => p.id == item.productId);
                document.getElementById('bannerSelectedProduct').textContent = `Seçili Ürün: ${product ? product.name : 'Bilinmeyen'}`;
                document.getElementById('bannerLink').value = `urun.php?id=${item.productId}`;
            } else {
                // Bu özel bir link
                this.toggleBannerLinkType('custom');
                document.getElementById('bannerLinkType').value = 'custom';
                document.getElementById('bannerLink').value = item.link;
            }
        } else {
            document.getElementById('bannerModalTitle').textContent = 'Yeni Banner Ekle';
        }
    }

    // ### YENİ: Banner Modalında Link Tipi Değiştirme ###
    toggleBannerLinkType(type) {
        if (type === 'product') {
            document.getElementById('bannerCustomLinkGroup').classList.add('hidden');
            document.getElementById('bannerProductLinkGroup').classList.remove('hidden');
            document.getElementById('bannerLink').disabled = true;
            this.searchBannerProducts(''); // İlk ürünleri listele
        } else {
            document.getElementById('bannerCustomLinkGroup').classList.remove('hidden');
            document.getElementById('bannerProductLinkGroup').classList.add('hidden');
            document.getElementById('bannerLink').disabled = false;
        }
    }

    // ### YENİ: Banner Modalı İçin Ürün Arama ###
    searchBannerProducts(searchTerm) {
        const listEl = document.getElementById('bannerProductList');
        searchTerm = searchTerm.toLowerCase();
        let filtered;
        if (!searchTerm) {
            filtered = this.data.products.slice(0, 10);
        } else {
            filtered = this.data.products
                .filter(p => p.name.toLowerCase().includes(searchTerm))
                .slice(0, 10);
        }
        
        listEl.innerHTML = filtered.map(p => `
            <div class.="p-2 hover:bg-gray-100 cursor-pointer flex justify-between items-center" onclick="adminPanel.selectBannerProduct(${p.id}, '${p.name.replace(/'/g, "\\'")}')">
                <span class="text-sm">${p.name}</span>
                <i class="fas fa-plus text-green-500"></i>
            </div>
        `).join('');
    }

    // ### YENİ: Banner Modalı İçin Ürün Seçme ###
    selectBannerProduct(id, name) {
        document.getElementById('bannerProductId').value = id;
        document.getElementById('bannerLink').value = `urun.php?id=${id}`;
        document.getElementById('bannerSelectedProduct').textContent = `Seçili Ürün: ${name}`;
        document.getElementById('bannerProductList').innerHTML = ''; // Listeyi temizle
    }


    populateSubCategorySelect(parentId, selectedSubId = null) {
        const subCatSelect = document.getElementById('productSubCategoryId');
        subCatSelect.innerHTML = '<option value="">Alt Kategori Seçin</option>';
        if (parentId) { (this.data.categories || []).filter(c => c.parentId == parentId).forEach(c => subCatSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`); }
        if (selectedSubId) subCatSelect.value = selectedSubId;
    }
    
   async saveData(resource, data, id) {
        const result = await this.api(resource, id ? 'PUT' : 'POST', data, id);
        if(result) {
            this.data[resource] = await this.api(resource);
            this.switchTab(resource);
            
            // ### BU BÖLÜMÜ GÜNCELLEYİN ###
            const modalTypeMap = {
                products: 'product',
                categories: 'category',
                users: 'user',
                banners: 'banner',
                vitrinBanners: 'vitrinBanner', // YENİ
                pages: 'page' // YENİ
            };
            
            const modalType = modalTypeMap[resource];
            if (modalType) {
                this.hideModal(modalType);
            }
            // ### GÜNCELLEME SONU ###
            
            this.renderDashboard();
            this._showToast(id ? 'Kayıt güncellendi!' : 'Kayıt eklendi!');
            
            if (resource === 'products') {
                await this.guncelleVeKaydetUrunlerHTML();
            }
        }
    }
    
    async saveProduct() {
        const id = this.currentEditId;
        const oldProduct = id ? this.data.products.find(p => p.id === id) : { stock: 0 };
        const oldStock = oldProduct.stock;
        
        const data = { 
            name: document.getElementById('productName').value, brand: document.getElementById('productBrand').value, 
            categoryId: parseInt(document.getElementById('productCategoryId').value), subCategoryId: parseInt(document.getElementById('productSubCategoryId').value) || null, 
            price: parseFloat(document.getElementById('productPrice').value), oldPrice: parseFloat(document.getElementById('productOldPrice').value) || null, 
            stock: parseInt(document.getElementById('productStock').value), image: document.getElementById('productImage').value, 
            description: document.getElementById('productDescription').value, features: document.getElementById('productFeatures').value.split('\n').filter(f => f) 
        };

        const newStock = data.stock;
        const stockDifference = newStock - oldStock;
        
        if (id) {
            if (stockDifference > 0) {
                 data.totalStockIn = (oldProduct.totalStockIn || 0) + stockDifference;
            } else {
                 data.totalStockIn = oldProduct.totalStockIn;
            }
            data.totalSold = oldProduct.totalSold;
        } else {
             data.totalStockIn = newStock;
             data.totalSold = 0;
        }

        await this.saveData('products', data, id);
    }

    async saveCategory() {
        const id = this.currentEditId;
        const data = { name: document.getElementById('categoryName').value, parentId: parseInt(document.getElementById('categoryParentId').value) || null };
        await this.saveData('categories', data, id);
    }

    async saveUser() {
        const id = this.currentEditId;
        const data = { name: document.getElementById('userName').value, surname: document.getElementById('userSurname').value, email: document.getElementById('userEmail').value };
        const password = document.getElementById('userPassword').value;
        if (password) data.password = password;
        await this.saveData('users', data, id);
    }

    async saveOrder() {
        const id = document.getElementById('orderUpdateId').value;
        const data = { status: document.getElementById('orderUpdateStatus').value, shippingCarrier: document.getElementById('orderShippingCarrier').value, shippingTracking: document.getElementById('orderShippingTracking').value };
        if(await this.api('orders', 'PUT', data, id)) {
            this.data.orders = await this.api('orders');
            this.displayOrders();
            this.renderDashboard();
            this.hideModal('order');
            this._showToast(`Sipariş #${id} güncellendi!`);
        }
    }
    
    // ### GÜNCELLENDİ: saveBanner ###
    async saveBanner() {
        const id = document.getElementById('bannerId').value ? parseInt(document.getElementById('bannerId').value) : null;
        
        const linkType = document.getElementById('bannerLinkType').value;
        let link = document.getElementById('bannerLink').value;
        let productId = null;

        if (linkType === 'product') {
            productId = document.getElementById('bannerProductId').value ? parseInt(document.getElementById('bannerProductId').value) : null;
            if (productId) {
                link = `urun.php?id=${productId}`;
            } else {
                this._showToast('Lütfen bir ürün seçin veya "Özel Link" kullanın.', 'error');
                return;
            }
        }

        const data = {
            slot: parseInt(document.getElementById('bannerSlot').value),
            image: document.getElementById('bannerImage').value,
            description: document.getElementById('bannerDescription').value,
            link: link,
            productId: productId
        };
        
        // API'ye PUT veya POST isteği gönder
        const result = await this.api('banners', id ? 'PUT' : 'POST', data, id);
        
        if (result) {
            this._showToast(`Banner (Slot #${data.slot}) başarıyla kaydedildi.`, 'success');
            // Veriyi yenile ve modalı kapat
            this.data.banners = await this.api('banners');
            this.hideModal('banner');
            this.renderBanners(); // Listeyi yenile
        }
    }


    async removeOrderItem(orderId, itemIndex) {
        if (!confirm('Bu ürünü siparişten silmek istediğinizden emin misiniz? Stoklar güncellenecektir.')) return;
        const result = await this.api('orders', 'POST', { orderId, itemIndex }, null, 'remove_item');
        if (result && result.success) {
            this.data.orders = await this.api('orders');
            this.data.products = await this.api('products');
            this.prepareOrderModal(orderId);
            this.displayOrders();
            this.renderDashboard();
            this._showToast('Ürün siparişten silindi ve stok güncellendi.');
        }
    }
    
    async deleteResource(resource, id) {
        if (confirm('Bu kaydı silmek istediğinizden emin misiniz?')) {
            if(await this.api(resource, 'DELETE', null, id)) {
                this.data[resource] = await this.api(resource);
                this.switchTab(resource); // Aktif sekmeyi yenile
                
                // Eğer banner'daysak renderBanners'ı çağır   
                if (resource === 'banners') this.renderBanners();
                if (resource === 'vitrinBanners') this.renderVitrinBanners(); // YENİ
                if (resource === 'pages') this.renderPages(); // YENİ

                this.renderDashboard();
                this._showToast('Kayıt başarıyla silindi!', 'error');
                if (resource === 'products') {
                    await this.guncelleVeKaydetUrunlerHTML();
                }
            }
        }
    }
    
    toggleSelectAll(resource, isChecked) {
        const filteredData = this.getFilteredData(resource);
        filteredData.forEach(item => {
            if(isChecked) this.selectedIds[resource].add(item.id);
            else this.selectedIds[resource].delete(item.id);
        });
        this[`display${resource.charAt(0).toUpperCase() + resource.slice(1)}`]();
        this.updateBulkActionUI(resource);
    }
    
    toggleSelection(resource, id, isChecked) {
        if(isChecked) this.selectedIds[resource].add(id);
        else this.selectedIds[resource].delete(id);
        this.updateBulkActionUI(resource);
    }
    
    getFilteredData(resource) {
        const filter = this.filters[resource];
        if (resource === 'products') {
            return (this.data.products || []).filter(p => (p.name.toLowerCase().includes(filter.search.toLowerCase()) || p.id.toString().includes(filter.search)) && (!filter.categoryId || p.categoryId == filter.categoryId) && (!filter.stock || (filter.stock === 'in_stock' && p.stock > 0) || (filter.stock === 'out_of_stock' && p.stock <= 0)));
        }
        if (resource === 'categories') {
            return (this.data.categories || []).filter(c => c.name.toLowerCase().includes(filter.search.toLowerCase()));
        }
        if (resource === 'users') {
             return (this.data.users || []).filter(u => `${u.name || u.firstName || ''} ${u.surname || u.lastName || ''}`.toLowerCase().includes(filter.search.toLowerCase()) || (u.email || '').toLowerCase().includes(filter.search.toLowerCase()));
        }
        if (resource === 'orders') {
            return (this.data.orders || []).filter(o => (o.customerInfo.name.toLowerCase().includes(filter.search.toLowerCase()) || o.id.toString().includes(filter.search)) && (!filter.status || o.status === filter.status) && (!filter.startDate || new Date(o.date) >= new Date(filter.startDate)) && (!filter.endDate || new Date(o.date) <= new Date(filter.endDate)));
        }
        return this.data[resource] || [];
    }

    updateBulkActionUI(resource) {
        const container = document.getElementById(`bulk-actions-${resource}`);
        const countSpan = document.getElementById(`selected-count-${resource}`);
        const count = this.selectedIds[resource].size;
        if (count > 0) {
            container.classList.remove('hidden');
            if (countSpan) countSpan.textContent = `${count} öğe seçildi.`;
        } else {
            container.classList.add('hidden');
        }
    }

    showBulkDeleteModal(resource) {
        this.currentBulkResource = resource;
        document.getElementById('bulk-delete-input').value = '';
        document.getElementById('confirm-bulk-delete-btn').disabled = true;
        document.getElementById('confirm-bulk-delete-btn').classList.replace('bg-red-600', 'bg-red-300');
        this.showModal('bulk-delete');
    }

    async handleBulkDelete() {
        const resource = this.currentBulkResource;
        const idsToDelete = Array.from(this.selectedIds[resource]);
        if (idsToDelete.length === 0) return;
        const result = await this.api(resource, 'DELETE', { ids: idsToDelete });
        if (result && result.success) {
            this.data[resource] = await this.api(resource);
            this.selectedIds[resource].clear();
            this.updateBulkActionUI(resource);
            this[`display${resource.charAt(0).toUpperCase() + resource.slice(1)}`]();
            this.renderDashboard();
            this.hideModal('bulk-delete');
            this._showToast(`${idsToDelete.length} kayıt başarıyla silindi.`, 'error');
            if (resource === 'products') {
                await this.guncelleVeKaydetUrunlerHTML();
            }
        }
    }
    
    async handleBulkPriceUpdate() {
        const ids = Array.from(this.selectedIds.products);
        if (ids.length === 0) return;
        const type = document.getElementById('price-update-type').value;
        const amount = parseFloat(document.getElementById('price-update-amount').value);
        if (isNaN(amount) || amount <= 0) return this._showToast('Lütfen geçerli bir değer girin.', 'error');
        
        const result = await this.api('products', 'POST', { ids, type, amount }, null, 'bulk_update_price');
        if (result && result.success) {
            this.data.products = await this.api('products');
            this.selectedIds.products.clear();
            this.updateBulkActionUI('products');
            this.displayProducts();
            this.hideModal('bulk-price-update');
            this._showToast(result.message);
            await this.guncelleVeKaydetUrunlerHTML();
        }
    }

    async handleBulkStockUpdate() {
        const newStock = parseInt(document.getElementById('stock-update-amount').value);
        if (isNaN(newStock) || newStock < 0) { return this._showToast('Lütfen geçerli bir stok miktarı girin.', 'error'); }
        const idsToUpdate = Array.from(this.selectedIds.products);
        if (idsToUpdate.length === 0) { return this._showToast('Önce güncellenecek ürünleri seçin.', 'error'); }
        const promises = idsToUpdate.map(id => {
            const product = this.data.products.find(p => p.id === id);
            if (product) {
                const stockDifference = newStock - product.stock;
                product.stock = newStock;
                if (stockDifference > 0) { product.totalStockIn = (product.totalStockIn || 0) + stockDifference; }
                return this.api('products', 'PUT', product, id);
            }
        });
        await Promise.all(promises);
        this._showToast(`${idsToUpdate.length} ürünün stoğu güncellendi.`, 'success');
        this.data.products = await this.api('products');
        this.displayProducts();
        this.hideModal('bulk-stock-update');
        await this.guncelleVeKaydetUrunlerHTML();
    }

    renderXmlIntegration() {
        const container = document.getElementById('xmlIntegration');
        if (!container) return;
        container.innerHTML = `
            <h2 class="text-3xl font-bold mb-6">XML Entegrasyonu</h2>
            <div class="bg-white p-6 rounded-lg shadow-sm max-w-2xl">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Güneş Bilgisayar Ürün Entegrasyonu</h3>
                <p class="text-gray-600 mb-4">
                    Bu işlemi başlatmak, Güneş Bilgisayar XML veritabanındaki tüm ürünleri sitenize aktaracaktır.
                    <strong>UYARI:</strong> Bu işlem, mevcut <code>products.json</code> dosyanızın üzerine yazar. Önceki tüm ürünler silinir ve XML'den gelen yeni ürünler eklenir.
                </p>
                <p class="text-gray-600 mb-6">
                    Bu işlem, veritabanını güncelledikten sonra otomatik olarak ana sayfadaki <code>urunler.html</code> dosyasını da yeniden oluşturacaktır.
                </p>
                
                <button id="run-xml-import" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-all flex items-center justify-center">
                    <i class="fas fa-sync-alt fa-spin mr-3 hidden" id="xml-spinner"></i>
                    <span id="xml-button-text">Güncelleme İşlemini Başlat</span>
                </button>
                
                <div id="xml-import-status" class="mt-4 p-4 bg-gray-50 rounded-lg text-gray-700" style="display: none;">
                    Durum bekleniyor...
                </div>
            </div>
        `;
        document.getElementById('run-xml-import').addEventListener('click', async () => {
            if (!confirm('Emin misiniz? Bu işlem tüm mevcut ürünlerinizi silecek ve XML\'den gelenlerle değiştirecektir. Bu işlem geri alınamaz.')) {
                return;
            }
            const statusEl = document.getElementById('xml-import-status');
            const button = document.getElementById('run-xml-import');
            const buttonText = document.getElementById('xml-button-text');
            const spinner = document.getElementById('xml-spinner');

            statusEl.style.display = 'block';
            statusEl.textContent = 'XML verisi çekiliyor, bu işlem birkaç dakika sürebilir... Lütfen bekleyin.';
            button.disabled = true;
            spinner.classList.remove('hidden');
            buttonText.textContent = 'İşleniyor...';
            this._showToast('XML entegrasyonu başladı...', 'info');

            try {
                const result = await fetch('xml_import.php');
                if (!result.ok) {
                    const errorText = await result.text();
                    throw new Error(`PHP Hatası: ${result.status} ${result.statusText} - ${errorText}`);
                }
                const response = await result.json();
                if (!response.success) {
                    throw new Error(response.message || 'Bilinmeyen bir PHP hatası.');
                }
                
                statusEl.textContent = response.message + ' Şimdi ana sayfa (urunler.html) güncelleniyor...';
                this._showToast(response.message, 'success');
                this.data.products = await this.api('products');
                await this.guncelleVeKaydetUrunlerHTML();
                this.renderDashboard();
                if (document.getElementById('products').classList.contains('active')) {
                    this.displayProducts();
                }
                statusEl.textContent = response.message + ' Ana sayfa başarıyla güncellendi.';
                this._showToast('Ana sayfa (urunler.html) başarıyla güncellendi.', 'success');
            } catch (error) {
                console.error('XML Import Hatası:', error);
                const errorMessage = 'Hata: ' + (error.message || 'Bilinmeyen bir hata oluştu.');
                statusEl.textContent = errorMessage;
                this._showToast(errorMessage, 'error');
            } finally {
                button.disabled = false;
                spinner.classList.add('hidden');
                buttonText.textContent = 'Güncelleme İşlemini Başlat';
            }
        });
    }
    
    renderManagement() {
        const container = document.getElementById('management');
        if (!container) return;

        const info = this.data.settings.companyInfo || {};

        container.innerHTML = `
            <h2 class="text-3xl font-bold mb-6">Genel Yönetim ve Ayarlar</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Şirket Bilgileri</h3>
                    <form id="companyInfoForm" class="space-y-4">
                        <div>
                            <label for="companyTitle" class="block text-sm font-medium text-gray-700">Ticari Unvan</label>
                            <input type="text" id="companyTitle" value="${info.title || ''}" class="w-full p-2 border rounded mt-1">
                        </div>
                        <div>
                            <label for="companyContact" class="block text-sm font-medium text-gray-700">Yetkili Kişi</label>
                            <input type="text" id="companyContact" value="${info.contact || ''}" class="w-full p-2 border rounded mt-1">
                        </div>
                        <div>
                            <label for="companyEmail" class="block text-sm font-medium text-gray-700">E-posta Adresi</label>
                            <input type="email" id="companyEmail" value="${info.email || ''}" class="w-full p-2 border rounded mt-1">
                        </div>
                        <div>
                            <label for="companyAddress" class="block text-sm font-medium text-gray-700">Adres</label>
                            <textarea id="companyAddress" rows="3" class="w-full p-2 border rounded mt-1">${info.address || ''}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700">Bilgileri Kaydet</button>
                    </form>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Admin Şifresini Değiştir</h3>
                    <form id="passwordChangeForm" class="space-y-4">
                        <div>
                            <label for="oldPassword" class="block text-sm font-medium text-gray-700">Mevcut Şifre</label>
                            <input type="password" id="oldPassword" class="w-full p-2 border rounded mt-1" required>
                        </div>
                        <div>
                            <label for="newPassword" class="block text-sm font-medium text-gray-700">Yeni Şifre</label>
                            <input type="password" id="newPassword" class="w-full p-2 border rounded mt-1" required>
                        </div>
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Yeni Şifre (Tekrar)</label>
                            <input type="password" id="confirmPassword" class="w-full p-2 border rounded mt-1" required>
                        </div>
                        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg font-semibold hover:bg-red-700">Şifreyi Değiştir</button>
                    </form>
                </div>
                
            </div>
        `;
        
        document.getElementById('companyInfoForm').addEventListener('submit', (e) => this.saveCompanyInfo(e));
        document.getElementById('passwordChangeForm').addEventListener('submit', (e) => this.saveAdminPassword(e));
    }
    
// ### GÜNCELLENDİ: renderBanners (Modal Kullanımlı) ###
renderBanners() {
    const container = document.getElementById('banners');
    if (!container) return;

    let html = `
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold">Banner Yönetimi (Ana Sayfa)</h2>
            <button onclick="adminPanel.showModal('banner')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>Yeni Banner Ekle
            </button>
        </div>
        <p class="text-gray-600 mb-4">Mevcut banner'ları (en fazla 10 adet) yönetin. Slot numarasına göre sıralanırlar.</p>
    `;

    // Banner'ları slot numarasına göre sırala
    const sortedBanners = this.data.banners.sort((a, b) => (a.slot || 99) - (b.slot || 99));

    if (sortedBanners.length === 0) {
        html += '<div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">Henüz banner eklenmemiş.</div>';
    } else {
        html += `<div class="grid grid-cols-1 md:grid-cols-2 gap-6">`;
        sortedBanners.forEach(banner => {
            const product = banner.productId ? this.data.products.find(p => p.id == banner.productId) : null;
            html += `
            <div class="bg-white p-4 rounded-lg shadow-sm flex gap-4">
                <img src="${banner.image || 'https://via.placeholder.com/150'}" alt="${banner.description}" class="w-32 h-32 object-cover rounded-md flex-shrink-0">
                <div class="flex-grow">
                    <h3 class="text-lg font-semibold text-gray-800">Slot #${banner.slot} - ${banner.description || 'Başlıksız'}</h3>
                    <p class="text-sm text-gray-600 truncate mt-1" title="Link: ${banner.link}">
                        <i class="fas fa-link mr-2"></i>
                        ${product ? `Ürün: ${product.name}` : (banner.link || 'Link Yok')}
                    </p>
                    <div class="mt-4 space-x-2">
                        <button onclick="adminPanel.showModal('banner', ${banner.id})" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">Düzenle</button>
                        <button onclick="adminPanel.deleteResource('banners', ${banner.id})" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Sil</button>
                    </div>
                </div>
            </div>
            `;
        });
        html += `</div>`;
    }

    container.innerHTML = html;
}


// ### GÜNCELLENDİ: renderShowcase (Kategori Filtreli) ###
renderShowcase() {
    const container = document.getElementById('showcase');
    if (!container) return;

    // Kategori seçeneklerini oluştur
    const categoryOptions = '<option value="">Tüm Kategoriler</option>' + 
        this.data.categories
            .map(c => `<option value="${c.id}">${c.name}</option>`)
            .join('');

    // Vitrindeki mevcut ürünleri listele
    let currentShowcaseHTML = '<h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Vitrindeki Mevcut Ürünler</h3>';
    if (this.data.showcase.length === 0) {
        currentShowcaseHTML += '<p class="text-gray-500">Vitrine henüz ürün eklenmemiş.</p>';
    } else {
        currentShowcaseHTML += `<div class="grid grid-cols-1 md:grid-cols-3 gap-4">`;
        this.data.showcase.forEach(item => {
            currentShowcaseHTML += `
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm flex justify-between items-center">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold truncate" title="${item.name}">${item.name}</p>
                    <p class="text-sm text-gray-600">${item.custom ? '(Özel Eklendi)' : `(ID: ${item.productId})`}</p>
                </div>
                <button onclick="adminPanel.deleteShowcaseItem(${item.id})" class="text-red-500 hover:text-red-700 ml-2"><i class="fas fa-trash"></i></button>
            </div>`;
        });
        currentShowcaseHTML += `</div>`;
    }

    // Ürün ekleme alanı
    let addProductHTML = `
        <h3 class="text-xl font-semibold text-gray-800 mt-8 mb-4 border-b pb-2">Vitrine Yeni Ürün Ekle</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h4 class="font-semibold mb-3">Özel Vitrin Ürünü Ekle</h4>
                <form id="customShowcaseForm" class="space-y-3">
                    <label class="block text-sm">Ürün Adı</label>
                    <input type="text" name="name" class="w-full p-2 border rounded" required>
                    <label class="block text-sm">Resim URL</label>
                    <input type="url" name="image" class="w-full p-2 border rounded" placeholder="https://..." required>
                    <label class="block text-sm">Yönlendirme Linki</label>
                    <input type="url" name="link" class="w-full p-2 border rounded" placeholder="https://..." required>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700">Özel Ürün Ekle</button>
                </form>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h4 class="font-semibold mb-3">Mevcut Ürünlerden Seç</h4>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-sm">Kategori Filtresi</label>
                        <select id="showcaseCategoryFilter" class="w-full p-2 border rounded mt-1">${categoryOptions}</select>
                    </div>
                    <div>
                        <label class="block text-sm">Ürün Ara</label>
                        <input type="text" id="showcaseProductSearch" class="w-full p-2 border rounded mt-1" placeholder="Eklenecek ürünü ara...">
                    </div>
                </div>
                <div id="showcaseProductList" class="max-h-60 overflow-y-auto border rounded divide-y">
                    </div>
            </div>
        </div>
    `;

    container.innerHTML = currentShowcaseHTML + addProductHTML;

    // Event listener'ları ekle
    document.getElementById('customShowcaseForm').addEventListener('submit', (e) => this.saveShowcaseItem(e));
    document.getElementById('showcaseProductSearch').addEventListener('input', () => this.searchShowcaseProducts());
    document.getElementById('showcaseCategoryFilter').addEventListener('change', () => this.searchShowcaseProducts());

    // İlk ürün listesini yükle
    this.searchShowcaseProducts();
}
// #################################################
    // ### YENİ FONKSİYONLAR: VİTRİN BANNERLARI İÇİN ###
    // #################################################

    // ### YENİ: Vitrin Banner Modalını Hazırlama ###
    prepareVitrinBannerModal(id) {
        document.getElementById('vitrinBannerForm').reset();
        document.getElementById('vitrinBannerId').value = '';
        document.getElementById('vitrinBannerProductId').value = '';
        document.getElementById('vitrinBannerSelectedProduct').textContent = '';
        document.getElementById('vitrinBannerProductList').innerHTML = '';
        this.toggleVitrinBannerLinkType('custom'); // Varsayılana dön

        if (id) {
            const item = this.data.vitrinBanners.find(i => i.id === id);
            document.getElementById('vitrinBannerModalTitle').textContent = `Vitrin Banner Düzenle (ID: ${id})`;
            document.getElementById('vitrinBannerId').value = item.id;
            document.getElementById('vitrinBannerSlot').value = item.slot;
            document.getElementById('vitrinBannerImage').value = item.image;
            document.getElementById('vitrinBannerDescription').value = item.description;

            if (item.productId) {
                this.toggleVitrinBannerLinkType('product');
                document.getElementById('vitrinBannerLinkType').value = 'product';
                document.getElementById('vitrinBannerProductId').value = item.productId;
                const product = this.data.products.find(p => p.id == item.productId);
                document.getElementById('vitrinBannerSelectedProduct').textContent = `Seçili Ürün: ${product ? product.name : 'Bilinmeyen'}`;
                document.getElementById('vitrinBannerLink').value = `urun.php?id=${item.productId}`;
            } else {
                this.toggleVitrinBannerLinkType('custom');
                document.getElementById('vitrinBannerLinkType').value = 'custom';
                document.getElementById('vitrinBannerLink').value = item.link;
            }
        } else {
            document.getElementById('vitrinBannerModalTitle').textContent = 'Yeni Vitrin Banner Ekle';
        }
    }

    // ### YENİ: Vitrin Banner Link Tipi Değiştirme ###
    toggleVitrinBannerLinkType(type) {
        if (type === 'product') {
            document.getElementById('vitrinBannerCustomLinkGroup').classList.add('hidden');
            document.getElementById('vitrinBannerProductLinkGroup').classList.remove('hidden');
            document.getElementById('vitrinBannerLink').disabled = true;
            this.searchVitrinBannerProducts('');
        } else {
            document.getElementById('vitrinBannerCustomLinkGroup').classList.remove('hidden');
            document.getElementById('vitrinBannerProductLinkGroup').classList.add('hidden');
            document.getElementById('vitrinBannerLink').disabled = false;
        }
    }

    // ### YENİ: Vitrin Banner İçin Ürün Arama ###
    searchVitrinBannerProducts(searchTerm) {
        const listEl = document.getElementById('vitrinBannerProductList');
        searchTerm = searchTerm.toLowerCase();
        let filtered;
        if (!searchTerm) {
            filtered = this.data.products.slice(0, 10);
        } else {
            filtered = this.data.products
                .filter(p => p.name.toLowerCase().includes(searchTerm))
                .slice(0, 10);
        }
        
        listEl.innerHTML = filtered.map(p => `
            <div class.="p-2 hover:bg-gray-100 cursor-pointer flex justify-between items-center" onclick="adminPanel.selectVitrinBannerProduct(${p.id}, '${p.name.replace(/'/g, "\\'")}')">
                <span class="text-sm">${p.name}</span>
                <i class="fas fa-plus text-green-500"></i>
            </div>
        `).join('');
    }

    // ### YENİ: Vitrin Banner İçin Ürün Seçme ###
    selectVitrinBannerProduct(id, name) {
        document.getElementById('vitrinBannerProductId').value = id;
        document.getElementById('vitrinBannerLink').value = `urun.php?id=${id}`;
        document.getElementById('vitrinBannerSelectedProduct').textContent = `Seçili Ürün: ${name}`;
        document.getElementById('vitrinBannerProductList').innerHTML = '';
    }

    // ### YENİ: saveVitrinBanner ###
    async saveVitrinBanner() {
        const id = document.getElementById('vitrinBannerId').value ? parseInt(document.getElementById('vitrinBannerId').value) : null;
        
        const linkType = document.getElementById('vitrinBannerLinkType').value;
        let link = document.getElementById('vitrinBannerLink').value;
        let productId = null;

        if (linkType === 'product') {
            productId = document.getElementById('vitrinBannerProductId').value ? parseInt(document.getElementById('vitrinBannerProductId').value) : null;
            if (productId) {
                link = `urun.php?id=${productId}`;
            } else {
                this._showToast('Lütfen bir ürün seçin veya "Özel Link" kullanın.', 'error');
                return;
            }
        }

        const data = {
            slot: parseInt(document.getElementById('vitrinBannerSlot').value),
            image: document.getElementById('vitrinBannerImage').value,
            description: document.getElementById('vitrinBannerDescription').value,
            link: link,
            productId: productId
        };
        
        // 'saveData' fonksiyonu 'vitrinBanners' kaynağını otomatik işleyecek
        await this.saveData('vitrinBanners', data, id);
    }

    // ### YENİ: renderVitrinBanners ###
    renderVitrinBanners() {
        const container = document.getElementById('vitrinBanners');
        if (!container) return;

        let html = `
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold">Vitrin Bannerları (Orta Bölüm)</h2>
                <button onclick="adminPanel.showModal('vitrinBanner')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Yeni Vitrin Banner Ekle
                </button>
            </div>
            <p class="text-gray-600 mb-4">Mevcut vitrin banner'larını (en fazla 20 adet) yönetin. Slot numarasına göre sıralanırlar.</p>
        `;

        // Banner'ları slot numarasına göre sırala
        const sortedBanners = this.data.vitrinBanners.sort((a, b) => (a.slot || 99) - (b.slot || 99));

        if (sortedBanners.length === 0) {
            html += '<div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">Henüz vitrin banner\'ı eklenmemiş.</div>';
        } else {
            // Liste görünümü (Grid)
            html += `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">`;
            sortedBanners.forEach(banner => {
                const product = banner.productId ? this.data.products.find(p => p.id == banner.productId) : null;
                html += `
                <div class="bg-white p-4 rounded-lg shadow-sm flex gap-4">
                    <img src="${banner.image || 'https://via.placeholder.com/150'}" alt="${banner.description}" class="w-24 h-24 object-cover rounded-md flex-shrink-0">
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-gray-800">Slot #${banner.slot}</h3>
                        <p class="text-sm text-gray-700">${banner.description || 'Başlıksız'}</p>
                        <p class="text-xs text-gray-500 truncate mt-1" title="Link: ${banner.link}">
                            <i class="fas fa-link mr-1"></i>
                            ${product ? `Ürün: ${product.name}` : (banner.link || 'Link Yok')}
                        </p>
                        <div class="mt-3 space-x-2">
                            <button onclick="adminPanel.showModal('vitrinBanner', ${banner.id})" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">Düzenle</button>
                            <button onclick="adminPanel.deleteResource('vitrinBanners', ${banner.id})" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Sil</button>
                        </div>
                    </div>
                </div>
                `;
            });
            html += `</div>`;
        }
        container.innerHTML = html;
    }


    // #################################################
    // ### YENİ FONKSİYONLAR: DİNAMİK SAYFALAR İÇİN ###
    // #################################################
    
    // ### YENİ: renderPages ###
    renderPages() {
        const container = document.getElementById('pages');
        if (!container) return;
        
        // Slug'a göre arama yapabilmek için bir map oluşturalım
        const pagesBySlug = new Map(this.data.pages.map(p => [p.slug, p]));

        // Göstermek istediğimiz sabit (ön-tanımlı) sayfalar
        const definedPages = [
            { slug: 'iletisim', title: 'İletişim Sayfası', description: 'İş yeri adresi, harita ve iletişim formu bilgileri.' },
            { slug: 'hakkimizda', title: 'Hakkımızda', description: 'Şirketiniz ve tarihçeniz hakkında bilgiler.' },
            { slug: 'cerez-politikasi', title: 'Çerez Politikası', description: 'Site çerez kullanımı hakkında yasal metin.' },
            { slug: 'kvkk', title: 'KVKK Metni', description: 'Kişisel Verilerin Korunması Kanunu metni.' },
            { slug: 'mesafeli-satis', title: 'Mesafeli Satış Sözleşmesi', description: 'E-ticaret için yasal satış sözleşmesi.' }
        ];

        let html = `
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold">Dinamik Sayfalar</h2>
                <button onclick="adminPanel.showModal('page')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Yeni Sayfa Ekle
                </button>
            </div>
            <p class="text-gray-600 mb-4">Sitenizdeki 'Hakkımızda', 'İletişim' gibi statik sayfaların içeriğini buradan yönetin.</p>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4 text-left">Sayfa Adı (Başlık)</th>
                            <th class="p-4 text-left">URL (Slug)</th>
                            <th class="p-4 text-left">Açıklama / Durum</th>
                            <th class="p-4 text-left">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="pagesTable" class="divide-y">
        `;
        
        // 1. Önce veritabanından gelen, ama bizim listemizde olmayan (ekstra) sayfaları listele
        this.data.pages.forEach(page => {
            if (!definedPages.some(dp => dp.slug === page.slug)) {
                html += this.getPageTableRow(page, true); // 'true' = bulundu
            }
        });

        // 2. Sonra bizim tanımlı sayfalarımızı listele
        definedPages.forEach(dp => {
            const pageData = pagesBySlug.get(dp.slug);
            if (pageData) {
                // Veritabanında (pages.json) bulundu, dolu satır göster
                html += this.getPageTableRow(pageData, true);
            } else {
                // Veritabanında yok, "Oluştur" butonuyla boş satır göster
                html += `
                    <tr class="bg-yellow-50 hover:bg-yellow-100">
                        <td class="p-4 font-medium">${dp.title}</td>
                        <td class="p-4 font-mono text-sm">${dp.slug}</td>
                        <td class="p-4 text-sm text-gray-500">${dp.description} <span class="font-bold text-red-600">(İçerik Boş)</span></td>
                        <td class="p-4 space-x-2 whitespace-nowrap">
                            <button onclick="adminPanel.showModal('page', null, '${dp.slug}', '${dp.title}')" class="text-green-600 font-bold hover:text-green-800">
                                <i class="fas fa-plus-circle mr-1"></i>Oluştur
                            </button>
                        </td>
                    </tr>
                `;
            }
        });
        
        html += `</tbody></table></div>`;
        container.innerHTML = html;
    }
    
    // ### YENİ: getPageTableRow (renderPages için yardımcı) ###
    getPageTableRow(page, isFound) {
        return `
            <tr class_="${isFound ? 'hover:bg-gray-50' : 'bg-gray-50 opacity-70'}">
                <td class="p-4 font-medium">${page.title}</td>
                <td class="p-4 font-mono text-sm">${page.slug}</td>
                <td class="p-4 text-sm ${isFound ? 'text-green-700 font-semibold' : 'text-gray-500'}">
                    <i class="fas fa-check-circle mr-1"></i> İçerik Yüklendi
                </td>
                <td class="p-4 space-x-2 whitespace-nowrap">
                    <button onclick="adminPanel.showModal('page', ${page.id})" class="text-indigo-600 hover:text-indigo-800">Düzenle</button>
                    <button onclick="adminPanel.deleteResource('pages', ${page.id})" class="text-red-600 hover:text-red-800">Sil</button>
                </td>
            </tr>
        `;
    }

    // ### YENİ: toggleContactFields (İletişim'e özel alanları yönetir) ###
    toggleContactFields(slug) {
        const contactFields = document.getElementById('contactPageFields');
        if (slug === 'iletisim') {
            contactFields.classList.remove('hidden');
        } else {
            contactFields.classList.add('hidden');
        }
    }
    
    // ### YENİ: preparePageModal ###
   // ### GÜNCELLENDİ: preparePageModal (TinyMCE + Checkbox eklendi) ###
    preparePageModal(id, defaultSlug = null, defaultTitle = null) {
        const form = document.getElementById('pageForm');
        form.reset();
        this.toggleContactFields(defaultSlug); 

        // Önceki TinyMCE editörlerini (varsa) kaldır
        if (window.tinymce) {
            tinymce.remove('#pageContent');
        }

        if (id) {
            // Mevcut kaydı düzenle
            const item = this.data.pages.find(i => i.id === id);
            document.getElementById('pageModalTitle').textContent = `Sayfayı Düzenle: ${item.title}`;
            document.getElementById('pageId').value = item.id;
            document.getElementById('pageTitle').value = item.title;
            document.getElementById('pageSlug').value = item.slug;
            document.getElementById('pageContent').value = item.content || ''; // Textarea'ya içeriği bas
            document.getElementById('pageShowInMenu').checked = !!item.showInMenu; // Checkbox'ı ayarla (!! = true/false yapar)
            
            // İletişim özel alanları
            if (item.slug === 'iletisim') {
                document.getElementById('pageAddress').value = item.address || '';
                document.getElementById('pagePhone').value = item.phone || '';
                document.getElementById('pageContactEmail').value = item.contactEmail || '';
                document.getElementById('pageMapUrl').value = item.mapUrl || '';
                this.toggleContactFields('iletisim');
            }
        } else {
            // Yeni kayıt
            document.getElementById('pageModalTitle').textContent = 'Yeni Dinamik Sayfa Ekle';
            document.getElementById('pageId').value = '';
            document.getElementById('pageTitle').value = defaultTitle || '';
            document.getElementById('pageSlug').value = defaultSlug || '';
            document.getElementById('pageContent').value = ''; // İçeriği boşalt
            document.getElementById('pageShowInMenu').checked = true; // Varsayılan olarak menüde göster
            this.toggleContactFields(defaultSlug);
        }
        
        // ### YENİ: TinyMCE Editörünü Başlat ###
        // (Gelişmiş editör, Google Harita için 'media' eklentisi dahil)
        tinymce.init({
            selector: '#pageContent',
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | \
                      alignleft aligncenter alignright alignjustify | \
                      bullist numlist outdent indent | removeformat | image media link | code | help',
            height: 400,
            // Google Harita (media plugin'i ile iframe olarak eklemeyi sağlar)
            media_dimensions: false, 
            media_alt_source: false,
            media_poster: false,
            media_embed: true
        });
    }

    // ### YENİ: savePage ###
   // ### GÜNCELLENDİ: savePage (TinyMCE + Checkbox eklendi) ###
   // ### GÜNCELLENDİ: savePage (TinyMCE + Checkbox eklendi) ###
    async savePage() {
        const id = document.getElementById('pageId').value ? parseInt(document.getElementById('pageId').value) : null;
        const slug = document.getElementById('pageSlug').value;
        
        if (!slug || !document.getElementById('pageTitle').value) {
            this._showToast('Sayfa Başlığı ve URL (Slug) alanları zorunludur.', 'error');
            return;
        }

        // TinyMCE'den içeriği al
        const content = tinymce.get('pageContent') ? tinymce.get('pageContent').getContent() : document.getElementById('pageContent').value;

        const data = {
            title: document.getElementById('pageTitle').value,
            slug: slug,
            content: content, // TinyMCE içeriği
            showInMenu: document.getElementById('pageShowInMenu').checked // Checkbox değeri
        };

        // Eğer iletişim sayfası ise özel verileri ekle
        if (slug === 'iletisim') {
            data.address = document.getElementById('pageAddress').value;
            data.phone = document.getElementById('pagePhone').value;
            data.contactEmail = document.getElementById('pageContactEmail').value;
            data.mapUrl = document.getElementById('pageMapUrl').value;
        }
        
        await this.saveData('pages', data, id);
    }

getShowcaseProductRow(product) {
    const isAdded = this.data.showcase.some(item => item.productId === product.id);
    return `
    <div class="p-3 flex justify-between items-center hover:bg-gray-50">
        <span class="text-sm truncate" title="${product.name}">${product.name}</span>
        <button onclick="adminPanel.addShowcaseItemFromList(${product.id})" class="bg-blue-500 text-white px-3 py-1 rounded text-xs flex-shrink-0" ${isAdded ? 'disabled' : ''}>
            ${isAdded ? 'Eklendi' : 'Ekle'}
        </button>
    </div>`;
}

// ### GÜNCELLENDİ: searchShowcaseProducts (Kategori Filtreli) ###
searchShowcaseProducts() {
    const searchTerm = document.getElementById('showcaseProductSearch').value.toLowerCase();
    const categoryId = document.getElementById('showcaseCategoryFilter').value;
    const listEl = document.getElementById('showcaseProductList');

    const filtered = this.data.products
        .filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(searchTerm);
            // Kategori ID'si seçiliyse ve ürünün kategorisiyle (veya alt kategorisiyle) eşleşmiyorsa false dön
            // Not: Bu sadece ana kategoriyi (categoryId) kontrol eder. Daha karmaşık bir ağaç yapısı için recursive fonksiyon gerekir.
            // Şimdilik ana kategori (p.categoryId) yeterli olacaktır.
            const matchesCategory = !categoryId || (p.categoryId == categoryId); 
            return matchesSearch && matchesCategory;
        })
        .slice(0, 20); // Performans için ilk 20'yi göster
        
    listEl.innerHTML = filtered.map(p => this.getShowcaseProductRow(p)).join('');
}


async addShowcaseItemFromList(productId) {
    const product = this.data.products.find(p => p.id === productId);
    if (!product) return;

    const data = {
        productId: product.id,
        name: product.name,
        image: product.image,
        link: `urun.php?id=${product.id}`, // Otomatik link
        custom: false
    };

    await this.saveShowcaseData(data);
}

async saveShowcaseItem(e) {
    e.preventDefault();
    const form = e.target;
    const data = {
        productId: null,
        name: form.querySelector('[name="name"]').value,
        image: form.querySelector('[name="image"]').value,
        link: form.querySelector('[name="link"]').value,
        custom: true
    };

    await this.saveShowcaseData(data, form);
}

async saveShowcaseData(data, form = null) {
    // Vitrinde olup olmadığını tekrar kontrol et
    if (data.productId && this.data.showcase.some(item => item.productId === data.productId)) {
        this._showToast('Bu ürün zaten vitrinde.', 'error');
        return;
    }

    const result = await this.api('showcase', 'POST', data, null);
    if (result) {
        this._showToast('Ürün vitrine eklendi.', 'success');
        this.data.showcase = await this.api('showcase');
        this.renderShowcase(); // Sayfayı yenile
        if (form) form.reset();
    }
}

async deleteShowcaseItem(id) {
    if (!confirm('Bu ürünü vitrinden kaldırmak istediğinizden emin misiniz?')) return;

    const result = await this.api('showcase', 'DELETE', null, id);
    if (result && result.success) {
        this._showToast('Ürün vitrinden kaldırıldı.', 'error');
        this.data.showcase = await this.api('showcase');
        this.renderShowcase(); // Sayfayı yenile
    }
}
    
    async saveCompanyInfo(e) {
        e.preventDefault();
        const data = {
            title: document.getElementById('companyTitle').value,
            contact: document.getElementById('companyContact').value,
            email: document.getElementById('companyEmail').value,
            address: document.getElementById('companyAddress').value,
        };
        
        const result = await this.api('settings', 'POST', data, null, 'update_info');
        if (result && result.success) {
            this.data.settings = result.data;
            this._showToast('Şirket bilgileri güncellendi.', 'success');
        } else {
            this._showToast(result.message || 'Bir hata oluştu.', 'error');
        }
    }
    
    async saveAdminPassword(e) {
        e.preventDefault();
        const oldPassword = document.getElementById('oldPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (newPassword !== confirmPassword) {
            return this._showToast('Yeni şifreler eşleşmiyor.', 'error');
        }
        if (!newPassword || newPassword.length < 6) {
             return this._showToast('Yeni şifre en az 6 karakter olmalıdır.', 'error');
        }

        const data = { oldPassword, newPassword, confirmPassword };
        
        const result = await this.api('settings', 'POST', data, null, 'change_pass');
        if (result && result.success) {
            this._showToast(result.message, 'success');
            e.target.reset();
        } else {
            if(result && result.message) {
                 this._showToast(result.message, 'error');
            }
        }
    }
    
    startNewOrderCheck() {
        if (this.data.orders && this.data.orders.length > 0) {
            this.lastOrderId = Math.max(0, ...this.data.orders.map(o => o.id));
        } else {
            this.lastOrderId = 0;
        }
        
        setInterval(async () => {
            const [latestOrders, latestUsers] = await Promise.all([this.api('orders'), this.api('users')]);
            
            if (latestUsers && this.data.users && latestUsers.length > this.data.users.length) {
                this._showToast('Yeni bir müşteri kayıt oldu!', 'success');
                this.data.users = latestUsers;
                if(document.getElementById('users').classList.contains('active')) this.displayUsers();
                if(document.getElementById('dashboard').classList.contains('active')) this.renderDashboard();
            }

            if (latestOrders && latestOrders.length > 0) {
                const newLatestOrderId = Math.max(0, ...latestOrders.map(o => o.id));
                if (newLatestOrderId > this.lastOrderId) {
                    this.lastOrderId = newLatestOrderId;
                    this.data.orders = latestOrders;
                    this.notificationSound.play().catch(e => {});
                    this._showToast(`Yeni sipariş alındı: #${newLatestOrderId}`);
                    if (Notification.permission === "granted") new Notification("Yeni Sipariş!", { body: `Sipariş No: #${newLatestOrderId}` });
                    if(document.getElementById('dashboard').classList.contains('active')) this.renderDashboard();
                    if(document.getElementById('orders').classList.contains('active')) this.displayOrders();
                }
            }
        }, 15000);
    }
}


document.addEventListener('DOMContentLoaded', function() {
    window.adminPanel = new AdminPanel();
    adminPanel.init();
});