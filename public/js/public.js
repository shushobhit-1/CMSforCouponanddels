/**
 * Public JavaScript for Coupon Deals CMS
 * Handles interactive functionality, animations, and user experience
 */

(function() {
    'use strict';

    // ===== GLOBAL VARIABLES =====
    let currentCoupon = null;
    let isScrolled = false;

    // ===== DOM READY =====
    document.addEventListener('DOMContentLoaded', function() {
        initializeComponents();
        bindEvents();
        initializeAnimations();
        setupScrollEffects();
    });

    // ===== INITIALIZATION =====
    function initializeComponents() {
        // Initialize tooltips
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Initialize popovers
        if (typeof bootstrap !== 'undefined') {
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        }

        // Initialize Select2
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        // Initialize Flatpickr
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.datepicker', {
                dateFormat: 'Y-m-d',
                allowInput: true
            });
        }

        // Initialize DataTables
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('.datatable').DataTable({
                responsive: true,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        previous: "Previous",
                        next: "Next",
                        last: "Last"
                    }
                }
            });
        }

        // Initialize Swiper
        if (typeof Swiper !== 'undefined') {
            const swiper = new Swiper('.swiper-container', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    }
                }
            });
        }

        // Initialize Particles.js
        if (typeof particlesJS !== 'undefined') {
            particlesJS('particles-js', {
                particles: {
                    number: {
                        value: 80,
                        density: {
                            enable: true,
                            value_area: 800
                        }
                    },
                    color: {
                        value: '#007bff'
                    },
                    shape: {
                        type: 'circle',
                        stroke: {
                            width: 0,
                            color: '#000000'
                        }
                    },
                    opacity: {
                        value: 0.5,
                        random: false,
                        anim: {
                            enable: false,
                            speed: 1,
                            opacity_min: 0.1,
                            sync: false
                        }
                    },
                    size: {
                        value: 3,
                        random: true,
                        anim: {
                            enable: false,
                            speed: 40,
                            size_min: 0.1,
                            sync: false
                        }
                    },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: '#007bff',
                        opacity: 0.4,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 6,
                        direction: 'none',
                        random: false,
                        straight: false,
                        out_mode: 'out',
                        bounce: false,
                        attract: {
                            enable: false,
                            rotateX: 600,
                            rotateY: 1200
                        }
                    }
                },
                interactivity: {
                    detect_on: 'canvas',
                    events: {
                        onhover: {
                            enable: true,
                            mode: 'repulse'
                        },
                        onclick: {
                            enable: true,
                            mode: 'push'
                        },
                        resize: true
                    },
                    modes: {
                        grab: {
                            distance: 400,
                            line_linked: {
                                opacity: 1
                            }
                        },
                        bubble: {
                            distance: 400,
                            size: 40,
                            duration: 2,
                            opacity: 8,
                            speed: 3
                        },
                        repulse: {
                            distance: 200,
                            duration: 0.4
                        },
                        push: {
                            particles_nb: 4
                        },
                        remove: {
                            particles_nb: 2
                        }
                    }
                },
                retina_detect: true
            });
        }

        // Initialize Typed.js
        if (typeof Typed !== 'undefined') {
            const typedElements = document.querySelectorAll('.typed-text');
            typedElements.forEach(element => {
                const strings = element.dataset.strings ? JSON.parse(element.dataset.strings) : ['Welcome to Coupon Deals CMS'];
                new Typed(element, {
                    strings: strings,
                    typeSpeed: 50,
                    backSpeed: 30,
                    loop: true,
                    showCursor: true,
                    cursorChar: '|'
                });
            });
        }

        // Initialize CountUp.js
        if (typeof CountUp !== 'undefined') {
            const countElements = document.querySelectorAll('.count-up');
            countElements.forEach(element => {
                const endVal = parseInt(element.dataset.end) || 0;
                const countUp = new CountUp(element, endVal, {
                    duration: 2.5,
                    separator: ',',
                    decimal: '.'
                });
                
                // Start counting when element is in view
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            countUp.start();
                            observer.unobserve(entry.target);
                        }
                    });
                });
                observer.observe(element);
            });
        }

        // Initialize Chart.js
        if (typeof Chart !== 'undefined') {
            const chartElements = document.querySelectorAll('.chart-canvas');
            chartElements.forEach(element => {
                const ctx = element.getContext('2d');
                const chartData = element.dataset.chart ? JSON.parse(element.dataset.chart) : {};
                
                if (chartData.type && chartData.data) {
                    new Chart(ctx, {
                        type: chartData.type,
                        data: chartData.data,
                        options: chartData.options || {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            });
        }
    }

    // ===== EVENT BINDING =====
    function bindEvents() {
        // Coupon popup events
        document.addEventListener('click', function(e) {
            if (e.target.matches('.coupon-popup-trigger')) {
                e.preventDefault();
                showCouponPopup(e.target.dataset);
            }
        });

        // Share button events
        document.addEventListener('click', function(e) {
            if (e.target.matches('.share-btn')) {
                e.preventDefault();
                handleShare(e.target.dataset.platform);
            }
        });

        // Back to top button
        const backToTopBtn = document.getElementById('backToTop');
        if (backToTopBtn) {
            backToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

        // Newsletter form
        const newsletterForm = document.querySelector('.newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                handleNewsletterSignup(this);
            });
        }

        // Search form enhancement
        const searchForm = document.querySelector('form[action*="search"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                enhanceSearchForm(this);
            });
        }

        // Lazy loading for images
        if (typeof LazyLoad !== 'undefined') {
            new LazyLoad({
                elements_selector: '.lazy',
                threshold: 0,
                callback_loaded: function(el) {
                    el.classList.add('loaded');
                }
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
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

        // Mobile menu toggle enhancement
        const navbarToggler = document.querySelector('.navbar-toggler');
        if (navbarToggler) {
            navbarToggler.addEventListener('click', function() {
                document.body.classList.toggle('mobile-menu-open');
            });
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.navbar') && document.body.classList.contains('mobile-menu-open')) {
                document.body.classList.remove('mobile-menu-open');
            }
        });
    }

    // ===== ANIMATIONS =====
    function initializeAnimations() {
        // Initialize AOS (Animate On Scroll)
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                offset: 100
            });
        }

        // Custom scroll animations
        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        if (animatedElements.length > 0) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            animatedElements.forEach(el => observer.observe(el));
        }

        // Parallax effects
        const parallaxElements = document.querySelectorAll('.parallax');
        if (parallaxElements.length > 0) {
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                parallaxElements.forEach(element => {
                    const speed = element.dataset.speed || 0.5;
                    const yPos = -(scrolled * speed);
                    element.style.transform = `translateY(${yPos}px)`;
                });
            });
        }
    }

    // ===== SCROLL EFFECTS =====
    function setupScrollEffects() {
        // Header scroll effect
        const header = document.querySelector('.header');
        if (header) {
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset > 100;
                
                if (scrolled !== isScrolled) {
                    isScrolled = scrolled;
                    header.classList.toggle('scrolled', scrolled);
                }
            });
        }

        // Back to top button
        const backToTopBtn = document.getElementById('backToTop');
        if (backToTopBtn) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            });
        }

        // Progress bar
        const progressBar = document.querySelector('.scroll-progress');
        if (progressBar) {
            window.addEventListener('scroll', function() {
                const scrolled = (window.pageYOffset / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
                progressBar.style.width = scrolled + '%';
            });
        }
    }

    // ===== COUPON POPUP FUNCTIONALITY =====
    function showCouponPopup(data) {
        currentCoupon = data;
        
        // Update modal content
        const modal = document.getElementById('couponModal');
        if (modal) {
            const couponCode = modal.querySelector('#couponCode');
            const couponLink = modal.querySelector('#couponLink');
            
            if (couponCode) couponCode.textContent = data.code || 'COUPON';
            if (couponLink) {
                couponLink.href = data.link || '#';
                couponLink.setAttribute('data-coupon-id', data.id);
            }
            
            // Show modal
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
            
            // Track coupon view
            trackCouponView(data.id);
        }
    }

    function handleShare(platform) {
        if (!currentCoupon) return;
        
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent(`Check out this amazing coupon: ${currentCoupon.code}`);
        
        let shareUrl = '';
        
        switch (platform) {
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                break;
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${text}`;
                break;
            case 'whatsapp':
                shareUrl = `https://wa.me/?text=${text}%20${url}`;
                break;
            case 'copy':
                navigator.clipboard.writeText(currentCoupon.code).then(() => {
                    showToast('Coupon code copied to clipboard!', 'success');
                });
                return;
        }
        
        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
    }

    // ===== UTILITY FUNCTIONS =====
    function showToast(message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Fallback toast
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    }

    function trackCouponView(couponId) {
        // Send analytics data
        if (typeof gtag !== 'undefined') {
            gtag('event', 'coupon_view', {
                'coupon_id': couponId
            });
        }
        
        // Send to backend
        fetch('/api/coupons/' + couponId + '/view', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).catch(console.error);
    }

    function handleNewsletterSignup(form) {
        const email = form.querySelector('input[type="email"]').value;
        
        if (!email) {
            showToast('Please enter your email address', 'error');
            return;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
        submitBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            showToast('Thank you for subscribing!', 'success');
            form.reset();
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 1000);
    }

    function enhanceSearchForm(form) {
        const searchInput = form.querySelector('input[name="q"]');
        const searchValue = searchInput.value.trim();
        
        if (searchValue.length < 2) {
            showToast('Please enter at least 2 characters to search', 'warning');
            return false;
        }
        
        // Add search analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'search', {
                'search_term': searchValue
            });
        }
        
        return true;
    }

    // ===== PERFORMANCE OPTIMIZATIONS =====
    
    // Debounce function for scroll events
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Throttle function for resize events
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Optimize scroll events
    const optimizedScrollHandler = debounce(function() {
        setupScrollEffects();
    }, 16); // ~60fps

    window.addEventListener('scroll', optimizedScrollHandler);

    // Optimize resize events
    const optimizedResizeHandler = throttle(function() {
        // Handle responsive adjustments
        const isMobile = window.innerWidth < 768;
        document.body.classList.toggle('is-mobile', isMobile);
    }, 250);

    window.addEventListener('resize', optimizedResizeHandler);

    // ===== ACCESSIBILITY ENHANCEMENTS =====
    
    // Keyboard navigation for dropdowns
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close all open dropdowns
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });

    // Focus management for modals
    document.addEventListener('shown.bs.modal', function(e) {
        const modal = e.target;
        const firstFocusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (firstFocusable) {
            firstFocusable.focus();
        }
    });

    // ===== ERROR HANDLING =====
    window.addEventListener('error', function(e) {
        console.error('JavaScript error:', e.error);
        // Send error to analytics if available
        if (typeof gtag !== 'undefined') {
            gtag('event', 'exception', {
                'description': e.error.message,
                'fatal': false
            });
        }
    });

    // ===== SERVICE WORKER REGISTRATION =====
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('SW registered: ', registration);
                })
                .catch(function(registrationError) {
                    console.log('SW registration failed: ', registrationError);
                });
        });
    }

    // ===== EXPOSE FUNCTIONS TO GLOBAL SCOPE =====
    window.CouponDealsCMS = {
        showCouponPopup: showCouponPopup,
        handleShare: handleShare,
        showToast: showToast,
        trackCouponView: trackCouponView
    };

})();