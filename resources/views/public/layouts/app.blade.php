<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Coupon Deals & Affiliate Products'))</title>
    <meta name="description" content="@yield('description', 'Find the best coupons, deals, and affiliate products. Save money with exclusive offers and discounts.')">
    <meta name="keywords" content="@yield('keywords', 'coupons, deals, discounts, affiliate products, savings, offers')">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', @yield('title', config('app.name')))">
    <meta property="og:description" content="@yield('og_description', @yield('description'))">
    <meta property="og:image" content="@yield('og_image', asset('images/default-og.jpg'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('twitter_title', @yield('title', config('app.name')))">
    <meta property="twitter:description" content="@yield('twitter_description', @yield('description'))">
    <meta property="twitter:image" content="@yield('twitter_image', asset('images/default-twitter.jpg'))">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@10.0.0/swiper-bundle.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.0/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/public.css') }}" rel="stylesheet">
    @stack('styles')

    <!-- OneSignal -->
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" defer></script>
    <script>
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "{{ config('services.onesignal.app_id') }}",
                notifyButton: {
                    enable: true,
                },
                allowLocalhostAsSecureOrigin: true,
            });
        });
    </script>
</head>
<body class="public-layout">
    <!-- Preloader -->
    <div id="preloader" class="preloader">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <!-- Top Bar -->
        <div class="top-bar bg-primary text-white py-2">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="top-bar-left">
                            <span class="me-3"><i class="fas fa-phone me-1"></i> {{ $settings['contact_phone'] ?? '+1-800-123-4567' }}</span>
                            <span><i class="fas fa-envelope me-1"></i> {{ $settings['contact_email'] ?? 'info@example.com' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="top-bar-right text-end">
                            @auth
                                <a href="{{ route('user.dashboard') }}" class="text-white me-3">Dashboard</a>
                                <a href="{{ route('logout') }}" class="text-white" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            @else
                                <a href="{{ route('login') }}" class="text-white me-3">Login</a>
                                <a href="{{ route('register') }}" class="text-white">Register</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" height="40">
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation -->
                <div class="collapse navbar-collapse" id="navbarMain">
                    <!-- Search Bar -->
                    <form class="d-flex mx-auto" action="{{ route('search') }}" method="GET">
                        <div class="input-group" style="max-width: 500px;">
                            <input class="form-control" type="search" name="q" placeholder="Search coupons, deals, products..." value="{{ request('q') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Navigation Menu -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Coupons
                            </a>
                            <ul class="dropdown-menu mega-menu">
                                <li><a class="dropdown-item" href="{{ route('coupons') }}">All Coupons</a></li>
                                <li><a class="dropdown-item" href="{{ route('coupons', ['featured' => 1]) }}">Featured</a></li>
                                <li><a class="dropdown-item" href="{{ route('coupons', ['expiring' => 1]) }}">Expiring Soon</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach($categories->take(5) as $category)
                                    <li><a class="dropdown-item" href="{{ route('category', $category->slug) }}">{{ $category->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Deals
                            </a>
                            <ul class="dropdown-menu mega-menu">
                                <li><a class="dropdown-item" href="{{ route('deals') }}">All Deals</a></li>
                                <li><a class="dropdown-item" href="{{ route('deals', ['featured' => 1]) }}">Featured</a></li>
                                <li><a class="dropdown-item" href="{{ route('deals', ['expiring' => 1]) }}">Expiring Soon</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach($categories->take(5) as $category)
                                    <li><a class="dropdown-item" href="{{ route('category', $category->slug) }}">{{ $category->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Products
                            </a>
                            <ul class="dropdown-menu mega-menu">
                                <li><a class="dropdown-item" href="{{ route('products') }}">All Products</a></li>
                                <li><a class="dropdown-item" href="{{ route('products', ['featured' => 1]) }}">Featured</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach($categories->take(5) as $category)
                                    <li><a class="dropdown-item" href="{{ route('category', $category->slug) }}">{{ $category->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Stores
                            </a>
                            <ul class="dropdown-menu mega-menu">
                                <li><a class="dropdown-item" href="{{ route('stores') }}">All Stores</a></li>
                                <li><a class="dropdown-item" href="{{ route('stores', ['featured' => 1]) }}">Featured</a></li>
                                <li><a class="dropdown-item" href="{{ route('stores', ['verified' => 1]) }}">Verified</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('about') }}">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact') }}">Contact</a>
                        </li>
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
    <footer class="footer bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <!-- Company Info -->
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">{{ config('app.name') }}</h5>
                    <p class="text-muted">{{ $settings['footer_description'] ?? 'Find the best coupons, deals, and affiliate products. Save money with exclusive offers and discounts.' }}</p>
                    <div class="social-links">
                        @if($settings['social_facebook'])
                            <a href="{{ $settings['social_facebook'] }}" class="text-white me-3" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if($settings['social_twitter'])
                            <a href="{{ $settings['social_twitter'] }}" class="text-white me-3" target="_blank"><i class="fab fa-twitter"></i></a>
                        @endif
                        @if($settings['social_instagram'])
                            <a href="{{ $settings['social_instagram'] }}" class="text-white me-3" target="_blank"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if($settings['social_linkedin'])
                            <a href="{{ $settings['social_linkedin'] }}" class="text-white me-3" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-muted">Home</a></li>
                        <li><a href="{{ route('coupons') }}" class="text-muted">Coupons</a></li>
                        <li><a href="{{ route('deals') }}" class="text-muted">Deals</a></li>
                        <li><a href="{{ route('products') }}" class="text-muted">Products</a></li>
                        <li><a href="{{ route('stores') }}" class="text-muted">Stores</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        @foreach($categories->take(6) as $category)
                            <li><a href="{{ route('category', $category->slug) }}" class="text-muted">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Support -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('about') }}" class="text-muted">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="text-muted">Contact</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-muted">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="text-muted">Terms of Service</a></li>
                        <li><a href="{{ route('sitemap') }}" class="text-muted">Sitemap</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Newsletter</h6>
                    <p class="text-muted small">Get the latest deals and coupons</p>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control form-control-sm" placeholder="Your email">
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="my-4">

            <!-- Bottom Footer -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0 text-muted">
                        Powered by <a href="#" class="text-muted">Laravel</a> | 
                        <a href="#" class="text-muted">Privacy</a> | 
                        <a href="#" class="text-muted">Terms</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-primary back-to-top" title="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Coupon Popup Modal -->
    <div class="modal fade" id="couponModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Coupon Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="coupon-code-display mb-3">
                        <span id="couponCode" class="coupon-code"></span>
                    </div>
                    <p class="text-muted">Click the button below to get your discount!</p>
                    <a href="#" id="couponLink" class="btn btn-primary btn-lg" target="_blank">
                        Get Coupon <i class="fas fa-external-link-alt ms-2"></i>
                    </a>
                </div>
                <div class="modal-footer justify-content-center">
                    <div class="share-buttons">
                        <span class="text-muted me-2">Share:</span>
                        <a href="#" class="btn btn-outline-primary btn-sm share-btn" data-platform="facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-outline-info btn-sm share-btn" data-platform="twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-success btn-sm share-btn" data-platform="whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="#" class="btn btn-outline-secondary btn-sm share-btn" data-platform="copy">
                            <i class="fas fa-copy"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10.0.0/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lazysizes@5.3.2/lazysizes.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.8.3/dist/lazyload.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.16/dist/typed.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/countup.js@2.6.0/dist/countUp.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.0/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.0/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="{{ asset('js/public.js') }}"></script>
    @stack('scripts')

    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>

    <!-- Hide Preloader -->
    <script>
        window.addEventListener('load', function() {
            document.getElementById('preloader').style.display = 'none';
        });
    </script>
</body>
</html>