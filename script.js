// Sepet işlemleri
let cart = [];
let cartTotal = 0;

// Fiyat formatı fonksiyonu - 1.250,00 formatında
function formatPrice(price) {
  return price.toLocaleString('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

// DOM elementleri
const cartIcon = document.querySelector('.cart');
const cartModal = document.getElementById('cart-modal');
const cartItems = document.querySelector('.cart-items');
const cartCount = document.querySelector('.cart-count');
const cartTotalElement = document.querySelector('.cart-total span');
const addToCartButtons = document.querySelectorAll('.add-to-cart');

// Sepet modalını aç/kapa
cartIcon.addEventListener('click', () => {
    cartModal.style.display = cartModal.style.display === 'block' ? 'none' : 'block';
});

// Sepete ürün ekle
addToCartButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        const card = e.target.closest('.product-card');
        const product = {
            name: card.querySelector('h3').textContent,
            price: parseFloat(card.querySelector('.price').textContent.replace('₺', '')),
            quantity: 1
        };

        addToCart(product);
        updateCartUI();
        cartModal.style.display = 'block';
    });
});

// Sepete ürün ekleme fonksiyonu
function addToCart(product) {
    const existingProduct = cart.find(item => item.name === product.name);
    
    if (existingProduct) {
        existingProduct.quantity++;
    } else {
        cart.push(product);
    }
    
    updateCartTotal();
}

// Sepet toplamını güncelle
function updateCartTotal() {
    cartTotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

// Sepet arayüzünü güncelle
function updateCartUI() {
    cartItems.innerHTML = cart.map(item => `
        <div class="cart-item">
            <span>${item.name}</span>
            <span>${item.quantity}x</span>
            <span>${formatPrice(item.price * item.quantity)} ₺</span>
        </div>
    `).join('');
    
    cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
    cartTotalElement.textContent = `${formatPrice(cartTotal)} ₺`;
}

// İletişim formu gönderimi
const contactForm = document.getElementById('contact-form');
contactForm.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Mesajınız gönderildi! Size en kısa sürede dönüş yapacağız.');
    contactForm.reset();
});

// Sayfa yüklendiğinde sepet arayüzünü güncelle
updateCartUI();

// Smooth scroll için
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Scroll Reveal Animasyonları
function reveal() {
    const reveals = document.querySelectorAll('.reveal');
    
    reveals.forEach(element => {
        const windowHeight = window.innerHeight;
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < windowHeight - elementVisible) {
            element.classList.add('active');
        }
    });
}

window.addEventListener('scroll', reveal);

// Paralax Efekti
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.parallax');
    
    parallaxElements.forEach(element => {
        const speed = element.dataset.speed || 0.5;
        element.style.transform = `translateY(${scrolled * speed}px)`;
    });
});

// Sayfa yüklendiğinde reveal'i çalıştır
document.addEventListener('DOMContentLoaded', () => {
    reveal();
    
    // Service kartlarına reveal ve parallax sınıfı ekle
    document.querySelectorAll('.service-card').forEach((card, index) => {
        card.classList.add('reveal');
        card.classList.add('parallax');
        card.dataset.speed = '0.1';
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Ürün kartlarına reveal ve parallax sınıfı ekle
    document.querySelectorAll('.product-card').forEach((card, index) => {
        card.classList.add('reveal');
        card.classList.add('parallax');
        card.dataset.speed = '0.15';
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Hero içeriğine parallax efekti ekle
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
        heroContent.classList.add('parallax');
        heroContent.dataset.speed = '-0.2';
    }
});

// Smooth Scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// İletişim Formu ve Animasyonlar
const contactForm = document.getElementById('contact-form');

if (contactForm) {
    // Input animasyonları
    const inputs = contactForm.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', () => {
            if (!input.value) {
                input.parentElement.classList.remove('focused');
            }
        });
    });

    // Form gönderimi
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Form verilerini al
        const formData = new FormData(contactForm);
        const data = Object.fromEntries(formData);
        
        // Animasyon ekle
        const button = contactForm.querySelector('button');
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...';
        button.disabled = true;
        
        // Simüle edilmiş form gönderimi (3 saniye)
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-check"></i> Gönderildi!';
            button.style.background = 'var(--gradient-secondary)';
            
            // Success animasyonu
            const successAnimation = document.createElement('div');
            successAnimation.className = 'success-animation';
            contactForm.appendChild(successAnimation);
            
            // Form'u temizle
            contactForm.reset();
            
            // 2 saniye sonra butonu eski haline getir
            setTimeout(() => {
                button.innerHTML = 'Gönder';
                button.disabled = false;
                button.style.background = '';
                successAnimation.remove();
            }, 2000);
        }, 3000);
    });
}

// Navbar Scroll Efekti
let lastScroll = 0;
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll <= 0) {
        navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
        navbar.style.transform = 'translateY(0)';
        return;
    }
    
    if (currentScroll > lastScroll && currentScroll > 100) {
        // Aşağı scroll
        navbar.style.transform = 'translateY(-100%)';
    } else {
        // Yukarı scroll
        navbar.style.transform = 'translateY(0)';
        navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.2)';
    }
    
    lastScroll = currentScroll;
});

// Mouse Hareket Efekti
document.addEventListener('mousemove', (e) => {
    const cards = document.querySelectorAll('.service-card, .product-card');
    
    cards.forEach(card => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        card.style.setProperty('--mouse-x', `${x}px`);
        card.style.setProperty('--mouse-y', `${y}px`);
    });
});

// Fiyat Sorgulama Butonu ve Animasyonlar
document.querySelectorAll('.contact-button').forEach(button => {
    button.addEventListener('click', function() {
        const productName = this.closest('.product-card').querySelector('h3').textContent;
        const contactSection = document.getElementById('contact');
        
        // Contact section'a smooth scroll
        contactSection.scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
        
        // Mesaj alanını otomatik doldur ve animasyon ekle
        const messageArea = document.querySelector('#contact-form textarea');
        if (messageArea) {
            messageArea.value = `${productName} ürünü hakkında bilgi almak istiyorum.`;
            messageArea.classList.add('highlight');
            
            setTimeout(() => {
                messageArea.classList.remove('highlight');
            }, 1000);
        }
        
        // Butona tıklama animasyonu ekle
        this.classList.add('clicked');
        setTimeout(() => {
            this.classList.remove('clicked');
        }, 300);
    });
});

// Sosyal Medya İkonları Hover Efekti
document.querySelectorAll('.social-links a').forEach(link => {
    link.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px) rotate(360deg)';
    });
    
    link.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) rotate(0deg)';
    });
}); 