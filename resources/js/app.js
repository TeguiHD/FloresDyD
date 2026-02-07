/**
 * FLORES D&D - JavaScript Principal
 * Alpine.js + Animaciones + Interactividad
 */

import './bootstrap';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';

// Usar la instancia de Alpine cargada por Livewire/Flux
document.addEventListener('alpine:init', () => {
    const Alpine = window.Alpine;
    if (!Alpine) {
        return;
    }

    // Registrar plugins de Alpine
    Alpine.plugin(focus);
    Alpine.plugin(collapse);
    Alpine.plugin(intersect);

// =============================================
// COMPONENTES ALPINE GLOBALES
// =============================================

/**
 * Efecto Typewriter para textos animados
 */
    Alpine.data('typewriter', () => ({
    text: '',
    fullText: '',
    index: 0,
    typing: true,
    delay: 100,
    pauseDelay: 2000,
    
    init() {
        this.fullText = this.$el.dataset.text || '';
        this.startTyping();
    },
    
    startTyping() {
        if (this.index < this.fullText.length) {
            this.text += this.fullText.charAt(this.index);
            this.index++;
            setTimeout(() => this.startTyping(), this.delay);
        } else {
            this.typing = false;
            // Si hay múltiples textos, reiniciar después de pausa
            if (this.$el.dataset.loop === 'true') {
                setTimeout(() => {
                    this.text = '';
                    this.index = 0;
                    this.typing = true;
                    this.startTyping();
                }, this.pauseDelay);
            }
        }
    }
    }));

/**
 * Countdown Timer para promociones
 */
    Alpine.data('countdown', (targetDate) => ({
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0,
    expired: false,
    
    init() {
        this.target = new Date(targetDate).getTime();
        this.updateCountdown();
        setInterval(() => this.updateCountdown(), 1000);
    },
    
    updateCountdown() {
        const now = new Date().getTime();
        const distance = this.target - now;
        
        if (distance < 0) {
            this.expired = true;
            this.days = this.hours = this.minutes = this.seconds = 0;
            return;
        }
        
        this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
        this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
    },
    
    formatNumber(num) {
        return num.toString().padStart(2, '0');
    }
    }));

/**
 * Navbar scroll behavior
 */
    Alpine.data('navbar', () => ({
    scrolled: false,
    mobileMenuOpen: false,
    
    init() {
        window.addEventListener('scroll', () => {
            this.scrolled = window.scrollY > 20;
        });
    }
    }));

/**
 * Cart Drawer (lateral)
 */
    Alpine.data('cartDrawer', () => ({
    open: false,
    items: [],
    
    init() {
        // Escuchar eventos de carrito
        window.addEventListener('cart-updated', (e) => {
            this.items = e.detail.items || [];
        });
        
        window.addEventListener('toggle-cart', () => {
            this.open = !this.open;
        });
    },
    
    get total() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },
    
    get count() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    },
    
    formatPrice(price) {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(price);
    }
    }));

// =============================================
// External Link Warning Modal
// =============================================

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (!link) {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return;
        }

        const shouldWarn = link.dataset.external === 'true' || link.target === '_blank';
        if (!shouldWarn) {
            return;
        }

        let url;
        try {
            url = new URL(href, window.location.origin);
        } catch (e) {
            return;
        }

        if (url.origin === window.location.origin) {
            return;
        }

        event.preventDefault();
        window.dispatchEvent(new CustomEvent('open-external-link', { detail: { url: url.href } }));
    });

/**
 * Quick View Modal
 */
    Alpine.data('quickView', () => ({
    open: false,
    product: null,
    selectedQuantity: 1,
    loading: false,
    
    init() {
        window.addEventListener('open-quick-view', (e) => {
            this.product = e.detail;
            this.selectedQuantity = 1;
            this.open = true;
        });
    },
    
    increment() {
        if (this.selectedQuantity < (this.product?.stock || 10)) {
            this.selectedQuantity++;
        }
    },
    
    decrement() {
        if (this.selectedQuantity > 1) {
            this.selectedQuantity--;
        }
    }
    }));

/**
 * Toast notifications
 */
    Alpine.data('toast', () => ({
    visible: false,
    message: '',
    type: 'success',
    
    init() {
        window.addEventListener('show-toast', (e) => {
            this.message = e.detail.message;
            this.type = e.detail.type || 'success';
            this.show();
        });
    },
    
    show() {
        this.visible = true;
        setTimeout(() => this.visible = false, 4000);
    }
    }));

    /**
     * Image Gallery con zoom
     */
    Alpine.data('gallery', () => ({
    currentIndex: 0,
    images: [],
    zoomed: false,
    
    init() {
        this.images = JSON.parse(this.$el.dataset.images || '[]');
    },
    
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
    },
    
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
    },
    
    select(index) {
        this.currentIndex = index;
    }
    }));

    /**
     * Promo Banner con countdown
     */
    Alpine.data('promoBanner', () => ({
    visible: true,
    dismissed: false,
    
    init() {
        // Revisar si ya fue cerrado en esta sesión
        this.dismissed = sessionStorage.getItem('promoBannerDismissed') === 'true';
        this.visible = !this.dismissed;
    },
    
    dismiss() {
        this.visible = false;
        sessionStorage.setItem('promoBannerDismissed', 'true');
    }
    }));
});

// =============================================
// INICIALIZACIÓN
// =============================================

document.addEventListener('DOMContentLoaded', () => {
    // Observer para animaciones on scroll
    const animateOnScrollObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    // Opcional: dejar de observar después de animar
                    // animateOnScrollObserver.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        }
    );
    
    // Observar elementos con clase animate-on-scroll
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        animateOnScrollObserver.observe(el);
    });
    
    // Lazy loading para imágenes
    if ('loading' in HTMLImageElement.prototype) {
        document.querySelectorAll('img[data-src]').forEach(img => {
            img.src = img.dataset.src;
        });
    } else {
        // Fallback con Intersection Observer
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});

// =============================================
// UTILIDADES GLOBALES
// =============================================

/**
 * Formatear precio a MXN
 */
window.formatPrice = (price) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(price);
};

/**
 * Disparar toast desde cualquier lugar
 */
window.showToast = (message, type = 'success') => {
    window.dispatchEvent(new CustomEvent('show-toast', {
        detail: { message, type }
    }));
};

/**
 * Toggle del carrito
 */
window.toggleCart = () => {
    window.dispatchEvent(new CustomEvent('toggle-cart'));
};

/**
 * Abrir quick view de producto
 */
window.openQuickView = (product) => {
    window.dispatchEvent(new CustomEvent('open-quick-view', {
        detail: product
    }));
};

/**
 * Scroll suave a sección
 */
window.scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
};

/**
 * Copiar texto al portapapeles
 */
window.copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        showToast('Copiado al portapapeles');
        return true;
    } catch (err) {
        console.error('Error al copiar:', err);
        return false;
    }
};

/**
 * Validar que link sea seguro (para modal de enlaces externos)
 */
window.isExternalLink = (url) => {
    try {
        const urlObj = new URL(url);
        return urlObj.hostname !== window.location.hostname;
    } catch {
        return false;
    }
};

// =============================================
// ANALYTICS (preparado para integración)
// =============================================

/**
 * Track de eventos para analytics
 */
window.trackEvent = (eventName, eventData = {}) => {
    // Integración con Google Analytics 4
    if (typeof gtag !== 'undefined') {
        gtag('event', eventName, eventData);
    }
    
    // Integración con Meta Pixel
    if (typeof fbq !== 'undefined') {
        fbq('track', eventName, eventData);
    }
    
    // Log para desarrollo
    if (process.env.NODE_ENV === 'development') {
        console.log('📊 Event tracked:', eventName, eventData);
    }
};

// Eventos de e-commerce estándar
window.trackViewItem = (product) => {
    trackEvent('view_item', {
        currency: 'MXN',
        value: product.price,
        items: [{
            item_id: product.id,
            item_name: product.name,
            price: product.price,
            quantity: 1
        }]
    });
};

window.trackAddToCart = (product, quantity = 1) => {
    trackEvent('add_to_cart', {
        currency: 'MXN',
        value: product.price * quantity,
        items: [{
            item_id: product.id,
            item_name: product.name,
            price: product.price,
            quantity: quantity
        }]
    });
};

window.trackPurchase = (order) => {
    trackEvent('purchase', {
        transaction_id: order.id,
        currency: 'MXN',
        value: order.total,
        shipping: order.shipping,
        items: order.items.map(item => ({
            item_id: item.product_id,
            item_name: item.name,
            price: item.price,
            quantity: item.quantity
        }))
    });
};
