@extends('layouts.public')

@section('title', 'Best Deals, Coupons & Affiliate Products')
@section('meta_description', 'Discover exclusive coupons, hot deals, and affiliate products from top stores. Save money with verified discount codes and promotional offers.')

@section('content')
<!-- Hero Section with Slider (if available) or Particles Background -->
@php
$homeSlider = $sliders->first();
@endphp
@if($homeSlider && is_array($homeSlider->slides) && count($homeSlider->slides))
<section class="position-relative overflow-hidden">
    <div id="homeCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($homeSlider->slides as $i => $slide)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                @if(!empty($slide['image']))
                    <img src="{{ $slide['image'] }}" class="d-block w-100" alt="{{ $slide['title'] ?? 'Slide' }}" style="height: 60vh; object-fit: cover;">
                @else
                    <div class="d-block w-100 bg-light" style="height: 60vh;"></div>
                @endif
                <div class="carousel-caption d-none d-md-block text-start">
                    @if(!empty($slide['title']))
                        <h5 class="fw-bold">{{ $slide['title'] }}</h5>
                    @endif
                    @if(!empty($slide['subtitle']))
                        <p>{{ $slide['subtitle'] }}</p>
                    @endif
                    @if(!empty($slide['cta_url']) && !empty($slide['cta_label']))
                        <a href="{{ $slide['cta_url'] }}" class="btn btn-primary btn-lg">{{ $slide['cta_label'] }}</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        <div class="carousel-indicators">
            @foreach($homeSlider->slides as $i => $slide)
                <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i===0 ? 'active' : '' }}" aria-current="{{ $i===0 ? 'true' : 'false' }}" aria-label="Slide {{ $i+1 }}"></button>
            @endforeach
        </div>
    </div>
</section>
@else
<section class="hero-section position-relative overflow-hidden" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); min-height: 90vh;">
    <div id="particles-js" class="position-absolute w-100 h-100"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="text-white">
                    <h1 class="display-3 fw-bold mb-4">
                        Save Big with <span class="text-warning">Exclusive Deals</span>
                    </h1>
                    <p class="lead mb-4 fs-5">
                        Discover thousands of verified coupons, hot deals, and affiliate products from your favorite stores. 
                        Start saving money today with our exclusive discount codes!
                    </p>
                    
                    <!-- Stats -->
                    <div class="row mb-4">
                        <div class="col-4">
                            <div class="text-center">
                                <h3 class="fw-bold text-warning mb-0" data-countup="{{ $stats['total_coupons'] }}">0</h3>
                                <small class="text-white-50">Active Coupons</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <h3 class="fw-bold text-warning mb-0" data-countup="{{ $stats['total_stores'] }}">0</h3>
                                <small class="text-white-50">Partner Stores</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <h3 class="fw-bold text-warning mb-0" data-countup="{{ $stats['money_saved'] }}">0</h3>
                                <small class="text-white-50">Money Saved</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CTA Buttons -->
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('coupons.index') }}" class="btn btn-warning btn-lg px-4 py-3">
                            <i class="fas fa-search me-2"></i>Browse Coupons
                        </a>
                        <a href="{{ route('deals.index') }}" class="btn btn-outline-light btn-lg px-4 py-3">
                            <i class="fas fa-fire me-2"></i>Hot Deals
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <div class="position-relative text-center">
                    <!-- Floating Cards Animation -->
                    <div class="position-relative">
                        <div class="card shadow-lg animate__animated animate__bounceInDown" style="animation-delay: 0.5s; transform: rotate(-10deg); margin: 20px;">
                            <div class="card-body text-center">
                                <h5 class="text-primary">50% OFF</h5>
                                <p class="mb-0">Electronics</p>
                            </div>
                        </div>
                        <div class="card shadow-lg animate__animated animate__bounceInUp" style="animation-delay: 1s; transform: rotate(10deg); margin: -50px 0 0 100px;">
                            <div class="card-body text-center">
                                <h5 class="text-success">Buy 1 Get 1</h5>
                                <p class="mb-0">Fashion</p>
                            </div>
                        </div>
                        <div class="card shadow-lg animate__animated animate__bounceInLeft" style="animation-delay: 1.5s; transform: rotate(5deg); margin: -30px 0 0 -50px;">
                            <div class="card-body text-center">
                                <h5 class="text-warning">Free Shipping</h5>
                                <p class="mb-0">All Orders</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Down Indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x text-white mb-4">
        <div class="text-center">
            <small>Scroll to explore</small>
            <div class="animate__animated animate__bounce animate__infinite">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Search Section -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="input-group input-group-lg">
                    <input type="search" class="form-control" placeholder="Search for coupons, deals, stores..." id="searchInput">
                    <button class="btn btn-primary px-4" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div id="searchSuggestions" class="position-absolute w-100 bg-white shadow-lg rounded-bottom d-none" style="z-index: 1000; max-height: 300px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold mb-3">Shop by Category</h2>
            <p class="lead text-muted">Explore deals across all your favorite categories</p>
        </div>
        
        <div class="row g-4">
            @foreach($categories->take(8) as $category)
                <div class="col-lg-3 col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                    <a href="{{ route('categories.show', $category->slug) }}" class="text-decoration-none">
                        <div class="card category-card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="category-icon mb-3">
                                    @switch($category->name)
                                        @case('Electronics')
                                            <i class="fas fa-laptop text-primary" style="font-size: 3rem;"></i>
                                            @break
                                        @case('Fashion')
                                            <i class="fas fa-tshirt text-danger" style="font-size: 3rem;"></i>
                                            @break
                                        @case('Home & Kitchen')
                                            <i class="fas fa-home text-success" style="font-size: 3rem;"></i>
                                            @break
                                        @case('Travel')
                                            <i class="fas fa-plane text-info" style="font-size: 3rem;"></i>
                                            @break
                                        @default
                                            <i class="fas fa-shopping-bag text-secondary" style="font-size: 3rem;"></i>
                                    @endswitch
                                </div>
                                <h5 class="card-title">{{ $category->name }}</h5>
                                <p class="text-muted small mb-3">{{ $category->coupons_count + $category->deals_count }} offers</p>
                                <span class="btn btn-outline-primary btn-sm">Explore Deals</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Coupons Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-up">
            <div>
                <h2 class="display-5 fw-bold mb-2">
                    <i class="fas fa-ticket-alt text-primary me-3"></i>Featured Coupons
                </h2>
                <p class="lead text-muted mb-0">Handpicked deals that save you the most</p>
            </div>
            <a href="{{ route('coupons.index') }}" class="btn btn-outline-primary">
                View All Coupons <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="row g-4">
            @foreach($featuredCoupons->take(8) as $coupon)
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="coupon-card card h-100">
                        <div class="card-body">
                            <!-- Store Logo & Info -->
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $coupon->store->logo_url ?? '/images/default-store.png' }}" 
                                     alt="{{ $coupon->store->name ?? 'Store' }}" 
                                     class="store-logo me-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold">{{ $coupon->store->name ?? 'Store' }}</h6>
                                    <small class="text-muted">{{ $coupon->category->name ?? 'Category' }}</small>
                                </div>
                                <span class="badge bg-warning">
                                    <i class="fas fa-star me-1"></i>Featured
                                </span>
                            </div>
                            
                            <!-- Coupon Title -->
                            <h6 class="card-title mb-3">{{ Str::limit($coupon->title, 50) }}</h6>
                            
                            <!-- Discount Display -->
                            <div class="discount-display bg-gradient text-center p-3 rounded mb-3" 
                                 style="background: linear-gradient(45deg, #007bff, #0056b3);">
                                <div class="text-white">
                                    <div class="fw-bold fs-5">{{ $coupon->discount_text }}</div>
                                    <small>Discount Available</small>
                                </div>
                            </div>
                            
                            <!-- Expiry -->
                            @if($coupon->expires_at)
                                <div class="expiry-info mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Expires: {{ $coupon->expires_at->format('M d, Y') }}
                                    </small>
                                </div>
                            @endif
                            
                            <!-- Get Coupon Button -->
                            <div class="d-grid">
                                <button class="coupon-code btn btn-primary"
                                        onclick="showCouponPopup(
                                            '{{ addslashes($coupon->title) }}',
                                            '{{ $coupon->code }}',
                                            '{{ $coupon->affiliate_url }}',
                                            '{{ addslashes($coupon->store->name ?? 'Store') }}'
                                        )">
                                    <i class="fas fa-ticket-alt me-2"></i>Get Code
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Hot Deals Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-up">
            <div>
                <h2 class="display-5 fw-bold mb-2">
                    <i class="fas fa-fire text-danger me-3"></i>Hot Deals
                </h2>
                <p class="lead text-muted mb-0">Limited time offers you don't want to miss</p>
            </div>
            <a href="{{ route('deals.index') }}" class="btn btn-outline-danger">
                View All Deals <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="row g-4">
            @forelse($featuredDeals->take(6) as $deal)
                <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="coupon-card card h-100">
                        <div class="position-relative">
                            <img src="{{ $deal->image_url }}" class="card-img-top" alt="{{ $deal->title }}" style="height: 200px; object-fit: cover;">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-danger fs-6">
                                    {{ $deal->discount_percentage }}% OFF
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ $deal->store->logo_url ?? '/images/default-store.png' }}" 
                                     alt="{{ $deal->store->name ?? 'Store' }}" 
                                     class="store-logo me-2" style="width: 30px; height: 30px;">
                                <small class="text-muted">{{ $deal->store->name ?? 'Store' }}</small>
                            </div>
                            
                            <h6 class="card-title">{{ Str::limit($deal->title, 60) }}</h6>
                            
                            <div class="price-info mb-3">
                                <span class="h5 text-success fw-bold">${{ $deal->deal_price }}</span>
                                @if($deal->original_price)
                                    <span class="text-muted text-decoration-line-through ms-2">${{ $deal->original_price }}</span>
                                @endif
                            </div>
                            
                            <div class="d-grid">
                                <button class="deal-btn btn btn-success"
                                        onclick="showDealPopup(
                                            '{{ addslashes($deal->title) }}',
                                            '{{ $deal->affiliate_url }}',
                                            '{{ addslashes($deal->store->name ?? 'Store') }}'
                                        )">
                                    <i class="fas fa-shopping-cart me-2"></i>Get Deal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-4">
                        <p class="text-muted">No featured deals available at the moment.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Popular Stores Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold mb-3">Popular Stores</h2>
            <p class="lead text-muted">Shop from your favorite brands and discover new ones</p>
        </div>
        
        <div class="row g-4">
            @foreach($popularStores->take(12) as $store)
                <div class="col-lg-2 col-md-3 col-sm-4 col-6" data-aos="flip-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <a href="{{ route('stores.show', $store->slug) }}" class="text-decoration-none">
                        <div class="card store-card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-3">
                                <img src="{{ $store->logo_url ?? '/images/default-store.png' }}" 
                                     alt="{{ $store->name }}" 
                                     class="img-fluid mb-2" 
                                     style="height: 60px; object-fit: contain;">
                                <h6 class="card-title mb-1">{{ $store->name }}</h6>
                                <small class="text-muted">{{ $store->coupons_count + $store->deals_count }} offers</small>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- App Download Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h3 class="display-6 fw-bold mb-3">Get Our Mobile App</h3>
                <p class="lead mb-4">
                    Never miss a deal! Download our mobile app for instant notifications 
                    about new coupons and exclusive mobile-only offers.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-light btn-lg">
                        <i class="fab fa-apple me-2"></i>App Store
                    </a>
                    <a href="#" class="btn btn-outline-light btn-lg">
                        <i class="fab fa-google-play me-2"></i>Google Play
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="text-center">
                    <div class="position-relative d-inline-block">
                        <i class="fas fa-mobile-alt" style="font-size: 200px; opacity: 0.2;"></i>
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <div class="bg-white rounded-circle p-4 shadow">
                                <i class="fas fa-bell text-primary" style="font-size: 40px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h3 class="display-6 fw-bold mb-3">Stay Updated</h3>
                <p class="lead text-muted mb-4">
                    Subscribe to our newsletter and be the first to know about new deals, 
                    exclusive coupons, and money-saving tips.
                </p>
                <form class="newsletter-form" id="newsletterForm">
                    <div class="row g-2 justify-content-center">
                        <div class="col-md-6">
                            <input type="email" class="form-control form-control-lg" 
                                   placeholder="Enter your email address" required>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-paper-plane me-2"></i>Subscribe
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<!-- Particles.js -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<!-- CountUp.js -->
<script src="https://cdn.jsdelivr.net/npm/countup@1.9.3/dist/countUp.min.js"></script>
<!-- Typed.js -->
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Particles.js
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: "#ffffff" },
                shape: { type: "circle" },
                opacity: { value: 0.5, random: false },
                size: { value: 3, random: true },
                line_linked: { enable: true, distance: 150, color: "#ffffff", opacity: 0.4, width: 1 },
                move: { enable: true, speed: 6, direction: "none", random: false, straight: false, out_mode: "out", bounce: false }
            },
            interactivity: {
                detect_on: "canvas",
                events: { onhover: { enable: true, mode: "repulse" }, onclick: { enable: true, mode: "push" }, resize: true },
                modes: { grab: { distance: 400, line_linked: { opacity: 1 } }, bubble: { distance: 400, size: 40, duration: 2, opacity: 8, speed: 3 }, repulse: { distance: 200, duration: 0.4 }, push: { particles_nb: 4 }, remove: { particles_nb: 2 } }
            },
            retina_detect: true
        });
        
        // Initialize CountUp animations
        const countUpElements = document.querySelectorAll('[data-countup]');
        countUpElements.forEach(el => {
            const target = parseInt(el.dataset.countup);
            const countUp = new CountUp(el, target, {
                duration: 2.5,
                useEasing: true,
                useGrouping: true,
                separator: ','
            });
            
            // Start animation when element is in viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        countUp.start();
                        observer.unobserve(entry.target);
                    }
                });
            });
            observer.observe(el);
        });
        
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const suggestionsContainer = document.getElementById('searchSuggestions');
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                suggestionsContainer.classList.add('d-none');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`/api/search-suggestions?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(suggestions => {
                        if (suggestions.length > 0) {
                            const html = suggestions.map(item => 
                                `<a href="${item.url}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-${item.type === 'coupon' ? 'ticket-alt' : 'store'} me-2 text-muted"></i>
                                    ${item.title}
                                    <small class="text-muted ms-2">(${item.type})</small>
                                </a>`
                            ).join('');
                            suggestionsContainer.innerHTML = html;
                            suggestionsContainer.classList.remove('d-none');
                        } else {
                            suggestionsContainer.classList.add('d-none');
                        }
                    });
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.classList.add('d-none');
            }
        });
        
        // Newsletter form
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            fetch('/api/newsletter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Subscribed!',
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                    this.reset();
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to subscribe. Please try again.',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        });
        
        // PWA Installation Prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install banner after 30 seconds
            setTimeout(() => {
                Swal.fire({
                    title: 'Install App',
                    text: 'Install our app for a better experience and offline access!',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Install',
                    cancelButtonText: 'Maybe Later'
                }).then((result) => {
                    if (result.isConfirmed && deferredPrompt) {
                        deferredPrompt.prompt();
                        deferredPrompt.userChoice.then((choiceResult) => {
                            deferredPrompt = null;
                        });
                    }
                });
            }, 30000);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .hero-section {
        position: relative;
        overflow: hidden;
    }
    
    #particles-js {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
    }
    
    .category-card:hover {
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }
    
    .store-card:hover {
        transform: scale(1.05);
        transition: all 0.3s ease;
    }
    
    .newsletter-form input:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-color: #86b7fe;
    }
    
    .min-vh-75 {
        min-height: 75vh;
    }
    
    @media (max-width: 768px) {
        .display-3 {
            font-size: 2.5rem;
        }
        
        .hero-section {
            min-height: 70vh;
        }
    }
</style>
@endpush