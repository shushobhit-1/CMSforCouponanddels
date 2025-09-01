<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Coupon Deals CMS'))</title>
    <meta name="description" content="@yield('meta_description', 'Find the best coupons, deals, and affiliate products. Save money with exclusive discounts and offers.')">
    <meta name="keywords" content="@yield('meta_keywords', 'coupons, deals, discounts, affiliate products, savings, online shopping')">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', @yield('title', config('app.name')))">
    <meta property="og:description" content="@yield('og_description', @yield('meta_description'))">
    <meta property="og:image" content="@yield('og_image', asset('images/default-og.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', @yield('title', config('app.name')))">
    <meta name="twitter:description" content="@yield('twitter_description', @yield('meta_description'))">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/default-twitter.jpg'))">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
    
    <!-- Google Analytics -->
    @if(config('services.google.analytics_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google.analytics_id') }}');
    </script>
    @endif
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="fas fa-ticket-alt text-primary me-2"></i>
                    <span class="fw-bold">{{ config('app.name', 'Coupon Deals') }}</span>
                </a>
                
                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="fas fa-home me-1"></i>Home
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ticket-alt me-1"></i>Coupons
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('coupons.index') }}">All Coupons</a></li>
                                <li><a class="dropdown-item" href="{{ route('coupons.index', ['featured' => 1]) }}">Featured</a></li>
                                <li><a class="dropdown-item" href="{{ route('coupons.index', ['popular' => 1]) }}">Popular</a></li>
                                <li><a class="dropdown-item" href="{{ route('coupons.index', ['expiring' => 1]) }}">Expiring Soon</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-tags me-1"></i>Deals
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('deals.index') }}">All Deals</a></li>
                                <li><a class="dropdown-item" href="{{ route('deals.index', ['flash_sale' => 1]) }}">Flash Sales</a></li>
                                <li><a class="dropdown-item" href="{{ route('deals.index', ['exclusive' => 1]) }}">Exclusive</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-box me-1"></i>Products
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('products.index') }}">All Products</a></li>
                                <li><a class="dropdown-item" href="{{ route('products.index', ['featured' => 1]) }}">Featured</a></li>
                                <li><a class="dropdown-item" href="{{ route('products.index', ['on_sale' => 1]) }}">On Sale</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-store me-1"></i>Stores
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('stores.index') }}">All Stores</a></li>
                                <li><a class="dropdown-item" href="{{ route('stores.index', ['featured' => 1]) }}">Featured</a></li>
                                <li><a class="dropdown-item" href="{{ route('stores.index', ['verified' => 1]) }}">Verified</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('category.index') }}">
                                <i class="fas fa-folder me-1"></i>Categories
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Search Bar -->
                    <form class="d-flex me-3" role="search">
                        <div class="input-group">
                            <input class="form-control" type="search" placeholder="Search coupons, deals, products..." aria-label="Search">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- User Menu -->
                    <ul class="navbar-nav">
                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="User Avatar" class="rounded-circle me-2" width="32" height="32">
                                    <span>{{ auth()->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('user.favorites') }}">
                                        <i class="fas fa-heart me-2"></i>Favorites
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('user.profile') }}">
                                        <i class="fas fa-user me-2"></i>Profile
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn btn-primary text-white px-3" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>Sign Up
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-section">
                        <h5 class="text-white mb-3">
                            <i class="fas fa-ticket-alt text-primary me-2"></i>
                            {{ config('app.name', 'Coupon Deals') }}
                        </h5>
                        <p class="text-muted mb-3">
                            Discover the best coupons, deals, and affiliate products. Save money with exclusive discounts and offers from top brands and stores.
                        </p>
                        
                        <!-- Social Media Icons with Hover Effects -->
                        <div class="social-media-icons">
                            <h6 class="text-white mb-3">Follow Us</h6>
                            <div class="d-flex gap-3">
                                <a href="#" class="social-icon facebook" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-icon twitter" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-icon instagram" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-icon youtube" title="YouTube">
                                    <i class="fab fa-youtube"></i>
                                </a>
                                <a href="#" class="social-icon linkedin" title="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-icon pinterest" title="Pinterest">
                                    <i class="fab fa-pinterest-p"></i>
                                </a>
                                <a href="#" class="social-icon tiktok" title="TikTok">
                                    <i class="fab fa-tiktok"></i>
                                </a>
                                <a href="#" class="social-icon telegram" title="Telegram">
                                    <i class="fab fa-telegram-plane"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h6 class="text-white mb-3">Quick Links</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="{{ route('home') }}" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Home
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('coupons.index') }}" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Coupons
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('deals.index') }}" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Deals
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('products.index') }}" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Products
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('stores.index') }}" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Stores
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Categories -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h6 class="text-white mb-3">Categories</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="#" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Electronics
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Fashion
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Home & Garden
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Sports
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Books
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Support -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h6 class="text-white mb-3">Support</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="#" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Help Center
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Contact Us
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>FAQ
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Terms of Service
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-muted text-decoration-none hover-text-white">
                                    <i class="fas fa-chevron-right me-2 text-primary"></i>Privacy Policy
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Newsletter -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h6 class="text-white mb-3">Newsletter</h6>
                        <p class="text-muted mb-3">Get the latest deals and coupons delivered to your inbox!</p>
                        <form class="newsletter-form">
                            <div class="input-group mb-3">
                                <input type="email" class="form-control" placeholder="Your email" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom border-top border-secondary pt-4 mt-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Coupon Deals') }}. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="text-muted mb-0">
                            Made with <i class="fas fa-heart text-danger"></i> for saving money
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-to-top" id="scrollToTop" title="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    @stack('scripts')
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Scroll to Top functionality
        const scrollToTopBtn = document.getElementById('scrollToTop');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });
        
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Mobile menu improvements
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.querySelector('.navbar-collapse');
            
            // Close mobile menu when clicking on a link
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) {
                        navbarCollapse.classList.remove('show');
                    }
                });
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!navbarToggler.contains(event.target) && !navbarCollapse.contains(event.target)) {
                    navbarCollapse.classList.remove('show');
                }
            });
        });

        // Newsletter form submission
        document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            // You can implement newsletter subscription logic here
            alert('Thank you for subscribing to our newsletter!');
            this.reset();
        });
    </script>
</body>
</html>