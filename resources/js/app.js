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

    /**
     * Marketing Popups Manager
     */
    Alpine.data('popupManager', (config = {}) => ({
        popups: config.popups || [],
        userId: config.userId || null,
        viewEndpoint: config.viewEndpoint || '',
        csrf: config.csrf || '',
        queue: [],
        current: null,
        isOpen: false,
        _scrollHandler: null,
        _exitHandler: null,
        _timeout: null,

        init() {
            this.queue = (this.popups || []).filter(popup => this.shouldConsider(popup));
            this.processQueue();
        },

        shouldConsider(popup) {
            if (!popup) return false;
            const baseKey = `popup_seen_${popup.id}`;
            if (popup.showOncePerSession && sessionStorage.getItem(baseKey)) {
                return false;
            }
            if (popup.showOncePerUser) {
                const userKey = `${baseKey}_${this.userId ?? 'anon'}`;
                if (localStorage.getItem(userKey)) {
                    return false;
                }
            }
            return true;
        },

        processQueue() {
            if (this.isOpen || this.queue.length === 0) {
                return;
            }
            const nextPopup = this.queue.shift();
            this.setupTrigger(nextPopup);
        },

        setupTrigger(popup) {
            this.clearTrigger();
            const show = () => this.showPopup(popup);

            if (popup.trigger === 'time_delay') {
                const delay = Math.max(1, Number(popup.triggerValue || 3));
                this._timeout = setTimeout(show, delay * 1000);
                return;
            }

            if (popup.trigger === 'scroll') {
                const targetPercent = Math.min(100, Math.max(5, Number(popup.triggerValue || 30)));
                this._scrollHandler = () => {
                    const doc = document.documentElement;
                    const total = doc.scrollHeight - window.innerHeight;
                    if (total <= 0) return;
                    const percent = Math.round((window.scrollY / total) * 100);
                    if (percent >= targetPercent) {
                        this.clearTrigger();
                        show();
                    }
                };
                window.addEventListener('scroll', this._scrollHandler, { passive: true });
                return;
            }

            if (popup.trigger === 'exit_intent') {
                const isDesktop = window.matchMedia('(pointer: fine)').matches;
                if (!isDesktop) {
                    show();
                    return;
                }
                this._exitHandler = (event) => {
                    if (event.clientY <= 0) {
                        this.clearTrigger();
                        show();
                    }
                };
                document.addEventListener('mouseleave', this._exitHandler);
                return;
            }

            // page_load (default)
            this._timeout = setTimeout(show, 400);
        },

        showPopup(popup) {
            this.current = popup;
            this.isOpen = true;
            this.markSeen(popup);
            this.track('view', popup);
        },

        close() {
            this.isOpen = false;
            this.current = null;
            this.clearTrigger();
            setTimeout(() => this.processQueue(), 200);
        },

        markSeen(popup) {
            const baseKey = `popup_seen_${popup.id}`;
            if (popup.showOncePerSession) {
                sessionStorage.setItem(baseKey, '1');
            }
            if (popup.showOncePerUser) {
                const userKey = `${baseKey}_${this.userId ?? 'anon'}`;
                localStorage.setItem(userKey, '1');
            }
        },

        registerClick(popup) {
            this.track('click', popup);
            this.close();
        },

        track(type, popup) {
            if (!this.viewEndpoint || !popup?.id) {
                return;
            }
            const url = `${this.viewEndpoint}/${popup.id}/${type}`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrf,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                keepalive: true,
            }).catch(() => { });
        },

        isExternal(url) {
            if (!url) return false;
            if (url.startsWith('mailto:') || url.startsWith('tel:') || url.startsWith('#')) {
                return false;
            }
            try {
                const urlObj = new URL(url, window.location.origin);
                return urlObj.origin !== window.location.origin;
            } catch {
                return false;
            }
        },

        clearTrigger() {
            if (this._timeout) {
                clearTimeout(this._timeout);
                this._timeout = null;
            }
            if (this._scrollHandler) {
                window.removeEventListener('scroll', this._scrollHandler);
                this._scrollHandler = null;
            }
            if (this._exitHandler) {
                document.removeEventListener('mouseleave', this._exitHandler);
                this._exitHandler = null;
            }
        }
    }));

    /**
     * Hero Parallax – subtle orb movement on scroll (2026)
     */
    Alpine.data('heroParallax', () => ({
        offsetY: 0,
        ticking: false,

        init() {
            this._onScroll = () => {
                if (!this.ticking) {
                    window.requestAnimationFrame(() => {
                        this.offsetY = window.scrollY;
                        this.ticking = false;
                    });
                    this.ticking = true;
                }
            };
            window.addEventListener('scroll', this._onScroll, { passive: true });
        },

        destroy() {
            window.removeEventListener('scroll', this._onScroll);
        }
    }));

    /**
     * Testimonial Scroll – pause/resume marquee on hover (2026)
     */
    Alpine.data('testimonialScroll', () => ({
        paused: false,

        pause() { this.paused = true; },
        resume() { this.paused = false; }
    }));

    /**
     * Counter Up – animate numbers when visible (2026)
     */
    Alpine.data('counterUp', (target = 0, isDecimal = false) => ({
        current: 0,
        display: '0',
        target: Number(target),
        started: false,

        start() {
            if (this.started) return;
            this.started = true;

            const duration = 2000; // ms
            const steps = 60;
            const stepTime = duration / steps;
            const increment = this.target / steps;
            let step = 0;

            const timer = setInterval(() => {
                step++;
                if (step >= steps) {
                    this.current = this.target;
                    clearInterval(timer);
                } else {
                    this.current = isDecimal
                        ? parseFloat((increment * step).toFixed(1))
                        : Math.round(increment * step);
                }
                this.display = isDecimal
                    ? this.current.toFixed(1)
                    : this.current.toLocaleString('es-MX');
            }, stepTime);
        }
    }));

    /**
     * Admin Date Picker – reusable calendar component for Livewire forms
     * Usage: x-data="adminDatepicker($wire, 'propertyName')"
     */
    window.adminDatepicker = ($wire, prop) => {
        const MONTHS = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
        ];

        const pad = (n) => String(n).padStart(2, '0');

        const parseWireValue = (val) => {
            if (!val) return null;
            const d = new Date(val);
            return isNaN(d.getTime()) ? null : d;
        };

        const now = new Date();
        const initial = parseWireValue($wire.get(prop));

        return {
            open: false,
            viewYear: initial ? initial.getFullYear() : now.getFullYear(),
            viewMonth: initial ? initial.getMonth() : now.getMonth(),
            selectedDate: initial,
            hour: initial ? pad(initial.getHours()) : '12',
            minute: initial ? pad(initial.getMinutes()) : '00',

            get monthLabel() {
                return `${MONTHS[this.viewMonth]} ${this.viewYear}`;
            },

            get displayValue() {
                if (!this.selectedDate) return '';
                const d = this.selectedDate;
                return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(this.hour)}:${pad(this.minute)}`;
            },

            get calendarDays() {
                const cells = [];
                const first = new Date(this.viewYear, this.viewMonth, 1);
                // ISO weekday: Monday=0 ... Sunday=6
                let startDay = first.getDay() - 1;
                if (startDay < 0) startDay = 6;

                const prevMonthDays = new Date(this.viewYear, this.viewMonth, 0).getDate();
                const daysInMonth = new Date(this.viewYear, this.viewMonth + 1, 0).getDate();
                const today = new Date();

                // Previous month fill
                for (let i = startDay - 1; i >= 0; i--) {
                    const day = prevMonthDays - i;
                    cells.push({
                        key: `prev-${day}`,
                        day,
                        outside: true,
                        disabled: true,
                        isToday: false,
                        isSelected: false,
                        date: null
                    });
                }

                // Current month
                for (let d = 1; d <= daysInMonth; d++) {
                    const date = new Date(this.viewYear, this.viewMonth, d);
                    const isToday = date.toDateString() === today.toDateString();
                    const isSelected = this.selectedDate && date.toDateString() === this.selectedDate.toDateString();
                    cells.push({
                        key: `cur-${d}`,
                        day: d,
                        outside: false,
                        disabled: false,
                        isToday,
                        isSelected,
                        date
                    });
                }

                // Next month fill (to complete 6 rows = 42 cells)
                const remaining = 42 - cells.length;
                for (let d = 1; d <= remaining; d++) {
                    cells.push({
                        key: `next-${d}`,
                        day: d,
                        outside: true,
                        disabled: true,
                        isToday: false,
                        isSelected: false,
                        date: null
                    });
                }

                return cells;
            },

            toggle() {
                this.open = !this.open;
            },

            prevMonth() {
                if (this.viewMonth === 0) {
                    this.viewMonth = 11;
                    this.viewYear--;
                } else {
                    this.viewMonth--;
                }
            },

            nextMonth() {
                if (this.viewMonth === 11) {
                    this.viewMonth = 0;
                    this.viewYear++;
                } else {
                    this.viewMonth++;
                }
            },

            selectDay(cell) {
                if (cell.disabled || !cell.date) return;
                this.selectedDate = cell.date;
                this.syncToWire();
            },

            syncToWire() {
                if (!this.selectedDate) return;
                const d = this.selectedDate;
                const h = Math.max(0, Math.min(23, parseInt(this.hour) || 0));
                const m = Math.max(0, Math.min(59, parseInt(this.minute) || 0));
                this.hour = pad(h);
                this.minute = pad(m);
                const val = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(h)}:${pad(m)}:00`;
                $wire.set(prop, val);
            },

            clear() {
                this.selectedDate = null;
                this.hour = '12';
                this.minute = '00';
                $wire.set(prop, null);
            }
        };
    };
});

// =============================================
// INICIALIZACIÓN
// =============================================

document.addEventListener('DOMContentLoaded', () => {
    // Observer para animaciones on scroll (reveal-up + animate-on-scroll)
    const animateOnScrollObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        },
        {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        }
    );

    /**
     * Observe all reveal/animation targets in a root element.
     * Safe to call multiple times (already-observed elements just get re-observed).
     */
    function observeAnimatedElements(root = document) {
        root.querySelectorAll('.animate-on-scroll, .reveal-up').forEach(el => {
            animateOnScrollObserver.observe(el);
        });
    }

    // Initial pass (for non-Livewire pages)
    observeAnimatedElements();

    // Re-observe after every Livewire DOM morph / navigation
    document.addEventListener('livewire:navigated', () => observeAnimatedElements());
    document.addEventListener('livewire:morph', () => observeAnimatedElements());

    // Fallback: MutationObserver catches any late DOM insertions
    const domWatcher = new MutationObserver((mutations) => {
        let found = false;
        for (const m of mutations) {
            for (const node of m.addedNodes) {
                if (node.nodeType === 1 && (node.matches?.('.reveal-up, .animate-on-scroll') || node.querySelector?.('.reveal-up, .animate-on-scroll'))) {
                    found = true;
                    break;
                }
            }
            if (found) break;
        }
        if (found) observeAnimatedElements();
    });
    domWatcher.observe(document.body, { childList: true, subtree: true });

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
