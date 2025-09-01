@extends('layouts.app')

@section('title', 'Latest Coupons & Discount Codes')
@section('meta_description', 'Discover the best coupons, discount codes, and deals from top online stores. Save money on your favorite products with our exclusive offers.')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title display-4 fw-bold mb-3">
                    Save Big with <span class="text-warning">Exclusive Coupons</span>
                </h1>
                <p class="hero-subtitle lead mb-4">
                    Discover thousands of verified coupons, discount codes, and deals from your favorite online stores. 
                    Start saving money today!
                </p>
                <div class="hero-actions">
                    <a href="#featured-coupons" class="btn btn-warning btn-lg me-3">
                        <i class="fas fa-tags me-2"></i>Browse Coupons
                    </a>
                    <a href="#stores" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-store me-2"></i>View Stores
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image">
                    <i class="fas fa-gift display-1 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search and Filters Section -->
<section class="search-section py-4 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form action="{{ route('coupons.index') }}" method="GET" class="search-form">
                    <div class="input-group input-group-lg">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search for coupons, stores, or categories..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Quick Filters -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="quick-filters d-flex flex-wrap justify-content-center gap-2">
                    <a href="{{ route('coupons.index', ['featured' => 1]) }}" 
                       class="btn btn-outline-primary btn-sm {{ request('featured') ? 'active' : '' }}">
                        <i class="fas fa-star me-1"></i>Featured
                    </a>
                    <a href="{{ route('coupons.index', ['new' => 1]) }}" 
                       class="btn btn-outline-success btn-sm {{ request('new') ? 'active' : '' }}">
                        <i class="fas fa-fire me-1"></i>New Arrivals
                    </a>
                    <a href="{{ route('coupons.index', ['expiring' => 1]) }}" 
                       class="btn btn-outline-warning btn-sm {{ request('expiring') ? 'active' : '' }}">
                        <i class="fas fa-clock me-1"></i>Expiring Soon
                    </a>
                    <a href="{{ route('coupons.index', ['popular' => 1]) }}" 
                       class="btn btn-outline-info btn-sm {{ request('popular') ? 'active' : '' }}">
                        <i class="fas fa-thumbs-up me-1"></i>Popular
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Coupons Section -->
<section id="featured-coupons" class="featured-coupons py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title display-6 fw-bold mb-3">
                <i class="fas fa-star text-warning me-2"></i>
                Featured Coupons
            </h2>
            <p class="section-subtitle text-muted lead">
                Hand-picked deals and discounts you don't want to miss
            </p>
        </div>
        
        <div class="row">
            @forelse($featuredCoupons as $coupon)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="coupon-card featured h-100">
                    <div class="coupon-card-header">
                        <div class="store-logo">
                            @if($coupon->store && $coupon->store->logo_url)
                                <img src="{{ $coupon->store->logo_url }}" 
                                     alt="{{ $coupon->store->name }}" 
                                     class="store-logo-img">
                            @else
                                <div class="store-logo-placeholder">
                                    <i class="fas fa-store"></i>
                                </div>
                            @endif
                        </div>
                        <div class="featured-badge">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    
                    <div class="coupon-card-body">
                        <h3 class="coupon-title">
                            <a href="{{ route('coupons.show', $coupon->slug) }}" class="text-decoration-none">
                                {{ $coupon->title }}
                            </a>
                        </h3>
                        
                        <p class="store-name">{{ $coupon->store->name ?? 'Unknown Store' }}</p>
                        
                        <div class="coupon-meta">
                            @if($coupon->discount_percentage)
                                <span class="discount-badge large">
                                    {{ $coupon->discount_percentage }}% OFF
                                </span>
                            @endif
                            @if($coupon->discount_amount)
                                <span class="discount-badge large">
                                    ₹{{ $coupon->discount_amount }} OFF
                                </span>
                            @endif
                        </div>
                        
                        <div class="coupon-stats">
                            <div class="stat-item">
                                <i class="fas fa-users text-muted"></i>
                                <span>{{ $coupon->used_count ?? 0 }} used</span>
                            </div>
                            @if($coupon->end_date)
                            <div class="stat-item">
                                <i class="fas fa-clock text-warning"></i>
                                <span>{{ $coupon->remaining_days }} days left</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="coupon-card-footer">
                        <button class="btn btn-primary btn-reveal-coupon w-100" 
                                onclick="showCouponPopup({{ $coupon->id }})">
                            <i class="fas fa-eye me-2"></i>Reveal Code
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <div class="empty-state">
                    <i class="fas fa-tags display-1 text-muted mb-3"></i>
                    <h3>No Featured Coupons</h3>
                    <p class="text-muted">Check back later for amazing deals!</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- All Coupons Section -->
<section class="all-coupons py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <div class="filters-sidebar">
                    <div class="filter-card">
                        <h5 class="filter-title">
                            <i class="fas fa-filter me-2"></i>Filters
                        </h5>
                        
                        <!-- Store Filter -->
                        <div class="filter-group mb-3">
                            <label class="form-label fw-bold">Store</label>
                            <select name="store_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Stores</option>
                                @foreach($stores as $store)
                                <option value="{{ $store->id }}" 
                                        {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="filter-group mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Discount Filter -->
                        <div class="filter-group mb-3">
                            <label class="form-label fw-bold">Minimum Discount</label>
                            <select name="min_discount" class="form-select" onchange="this.form.submit()">
                                <option value="">Any Discount</option>
                                <option value="10" {{ request('min_discount') == '10' ? 'selected' : '' }}>10%+</option>
                                <option value="20" {{ request('min_discount') == '20' ? 'selected' : '' }}>20%+</option>
                                <option value="30" {{ request('min_discount') == '30' ? 'selected' : '' }}>30%+</option>
                                <option value="50" {{ request('min_discount') == '50' ? 'selected' : '' }}>50%+</option>
                            </select>
                        </div>
                        
                        <!-- Sort Options -->
                        <div class="filter-group mb-3">
                            <label class="form-label fw-bold">Sort By</label>
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="discount" {{ request('sort') == 'discount' ? 'selected' : '' }}>Highest Discount</option>
                                <option value="expiring" {{ request('sort') == 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Coupons Grid -->
            <div class="col-lg-9">
                <div class="coupons-header d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">
                        <i class="fas fa-tags me-2"></i>
                        All Coupons 
                        <span class="text-muted">({{ $coupons->total() }})</span>
                    </h3>
                    
                    <div class="view-options">
                        <button class="btn btn-outline-secondary btn-sm active" data-view="grid">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
                
                <div class="coupons-grid" id="couponsGrid">
                    @forelse($coupons as $coupon)
                    <div class="coupon-card h-100" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="coupon-card-header">
                            <div class="store-logo">
                                @if($coupon->store && $coupon->store->logo_url)
                                    <img src="{{ $coupon->store->logo_url }}" 
                                         alt="{{ $coupon->store->name }}" 
                                         class="store-logo-img">
                                @else
                                    <div class="store-logo-placeholder">
                                        <i class="fas fa-store"></i>
                                    </div>
                                @endif
                            </div>
                            
                            @if($coupon->featured)
                            <div class="featured-badge">
                                <i class="fas fa-star"></i>
                            </div>
                            @endif
                            
                            @if($coupon->popular)
                            <div class="popular-badge">
                                <i class="fas fa-fire"></i>
                            </div>
                            @endif
                        </div>
                        
                        <div class="coupon-card-body">
                            <h3 class="coupon-title">
                                <a href="{{ route('coupons.show', $coupon->slug) }}" class="text-decoration-none">
                                    {{ $coupon->title }}
                                </a>
                            </h3>
                            
                            <p class="store-name">{{ $coupon->store->name ?? 'Unknown Store' }}</p>
                            
                            <div class="coupon-meta">
                                @if($coupon->discount_percentage)
                                    <span class="discount-badge">
                                        {{ $coupon->discount_percentage }}% OFF
                                    </span>
                                @endif
                                @if($coupon->discount_amount)
                                    <span class="discount-badge">
                                        ₹{{ $coupon->discount_amount }} OFF
                                    </span>
                                @endif
                            </div>
                            
                            @if($coupon->short_description)
                            <p class="coupon-description">{{ Str::limit($coupon->short_description, 80) }}</p>
                            @endif
                            
                            <div class="coupon-stats">
                                <div class="stat-item">
                                    <i class="fas fa-users text-muted"></i>
                                    <span>{{ $coupon->used_count ?? 0 }} used</span>
                                </div>
                                @if($coupon->end_date)
                                <div class="stat-item">
                                    <i class="fas fa-clock text-warning"></i>
                                    <span>{{ $coupon->remaining_days }} days left</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="coupon-card-footer">
                            <button class="btn btn-primary btn-reveal-coupon w-100" 
                                    onclick="showCouponPopup({{ $coupon->id }})">
                                <i class="fas fa-eye me-2"></i>Reveal Code
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <div class="empty-state">
                            <i class="fas fa-search display-1 text-muted mb-3"></i>
                            <h3>No Coupons Found</h3>
                            <p class="text-muted">Try adjusting your search criteria or check back later.</p>
                            <a href="{{ route('coupons.index') }}" class="btn btn-primary">
                                <i class="fas fa-refresh me-2"></i>Reset Filters
                            </a>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($coupons->hasPages())
                <div class="pagination-wrapper mt-5">
                    {{ $coupons->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Stores Section -->
<section id="stores" class="stores-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title display-6 fw-bold mb-3">
                <i class="fas fa-store text-primary me-2"></i>
                Popular Stores
            </h2>
            <p class="section-subtitle text-muted lead">
                Discover amazing deals from your favorite online stores
            </p>
        </div>
        
        <div class="row">
            @foreach($popularStores as $store)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="store-card text-center h-100">
                    <div class="store-logo-wrapper mb-3">
                        @if($store->logo_url)
                            <img src="{{ $store->logo_url }}" 
                                 alt="{{ $store->name }}" 
                                 class="store-logo">
                        @else
                            <div class="store-logo-placeholder">
                                <i class="fas fa-store"></i>
                            </div>
                        @endif
                    </div>
                    
                    <h4 class="store-name">{{ $store->name }}</h4>
                    
                    <div class="store-stats">
                        <div class="stat">
                            <span class="stat-number">{{ $store->coupons_count ?? 0 }}</span>
                            <span class="stat-label">Coupons</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">{{ $store->deals_count ?? 0 }}</span>
                            <span class="stat-label">Deals</span>
                        </div>
                    </div>
                    
                    <div class="store-rating mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= ($store->rating ?? 0))
                                <i class="fas fa-star text-warning"></i>
                            @else
                                <i class="far fa-star text-muted"></i>
                            @endif
                        @endfor
                        <span class="rating-text ms-2">{{ number_format($store->rating ?? 0, 1) }}</span>
                    </div>
                    
                    <a href="{{ route('stores.show', $store->slug) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-2"></i>View Store
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('stores.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-store me-2"></i>View All Stores
            </a>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="mb-3">
                    <i class="fas fa-envelope me-2"></i>
                    Never Miss a Deal!
                </h3>
                <p class="lead mb-4">
                    Subscribe to our newsletter and get the latest coupons and deals delivered to your inbox.
                </p>
                
                <form class="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <div class="input-group input-group-lg">
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="Enter your email address"
                               required>
                        <button class="btn btn-warning" type="submit">
                            <i class="fas fa-paper-plane me-2"></i>Subscribe
                        </button>
                    </div>
                </form>
                
                <p class="small mt-3 opacity-75">
                    We respect your privacy. Unsubscribe at any time.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Include Coupon Popup Component -->
@foreach($coupons as $coupon)
    <x-coupon-popup 
        :coupon="$coupon" 
        :show="false"
        :position="$coupon->popup_position ?? 'center'"
        :animation="$coupon->popup_animation ?? 'fadeIn'"
        :delay="$coupon->popup_delay ?? 0"
        :style="$coupon->popup_style ?? 'default'" />
@endforeach

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/coupon-popup.css') }}">
<style>
/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hero-title {
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.hero-image {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* Coupon Cards */
.coupon-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f0f0f0;
    overflow: hidden;
}

.coupon-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
}

.coupon-card.featured {
    border: 2px solid #ffc107;
    box-shadow: 0 8px 30px rgba(255, 193, 7, 0.2);
}

.coupon-card-header {
    padding: 20px 20px 0;
    position: relative;
}

.store-logo {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #f0f0f0;
}

.store-logo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.store-logo-placeholder {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 24px;
}

.featured-badge,
.popular-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.featured-badge {
    background: linear-gradient(135deg, #ffc107, #ff8f00);
}

.popular-badge {
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
}

.coupon-card-body {
    padding: 20px;
}

.coupon-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
    line-height: 1.4;
}

.coupon-title a {
    color: #212529;
}

.coupon-title a:hover {
    color: #007bff;
}

.store-name {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 12px;
    font-weight: 500;
}

.coupon-meta {
    margin-bottom: 16px;
}

.discount-badge {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
    margin-right: 8px;
    margin-bottom: 8px;
}

.discount-badge.large {
    font-size: 14px;
    padding: 8px 16px;
}

.coupon-description {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 16px;
}

.coupon-stats {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6c757d;
}

.coupon-card-footer {
    padding: 0 20px 20px;
}

.btn-reveal-coupon {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    padding: 12px 24px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-reveal-coupon:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
}

/* Store Cards */
.store-card {
    background: white;
    border-radius: 16px;
    padding: 24px 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.store-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.store-logo-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto;
}

.store-logo {
    width: 100%;
    height: 100%;
    border-radius: 16px;
    object-fit: cover;
    border: 2px solid #f0f0f0;
}

.store-logo-placeholder {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 32px;
    border: 2px solid #f0f0f0;
}

.store-name {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 16px;
    color: #212529;
}

.store-stats {
    display: flex;
    justify-content: center;
    gap: 24px;
    margin-bottom: 16px;
}

.stat {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 20px;
    font-weight: 700;
    color: #007bff;
}

.stat-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.store-rating {
    margin-bottom: 16px;
}

.rating-text {
    font-size: 14px;
    color: #6c757d;
    font-weight: 600;
}

/* Filters Sidebar */
.filters-sidebar {
    position: sticky;
    top: 20px;
}

.filter-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
}

.filter-title {
    color: #212529;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f0f0;
}

.filter-group {
    margin-bottom: 20px;
}

.form-label {
    color: #495057;
    margin-bottom: 8px;
}

.form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Quick Filters */
.quick-filters {
    margin-top: 16px;
}

.quick-filters .btn {
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.quick-filters .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.quick-filters .btn.active {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

/* Empty State */
.empty-state {
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    opacity: 0.5;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .coupon-card {
        margin-bottom: 20px;
    }
    
    .filters-sidebar {
        position: static;
        margin-bottom: 30px;
    }
    
    .quick-filters {
        flex-direction: column;
        align-items: center;
    }
    
    .quick-filters .btn {
        width: 100%;
        max-width: 200px;
    }
}

/* Animation Classes */
[data-aos] {
    opacity: 0;
    transition-property: opacity, transform;
}

[data-aos].aos-animate {
    opacity: 1;
}

[data-aos="fade-up"] {
    transform: translate3d(0, 30px, 0);
}

[data-aos="fade-up"].aos-animate {
    transform: translate3d(0, 0, 0);
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS animations
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true
    });
    
    // View toggle functionality
    const viewButtons = document.querySelectorAll('.view-options .btn');
    const couponsGrid = document.getElementById('couponsGrid');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Update active button
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Update grid layout
            if (view === 'list') {
                couponsGrid.classList.add('list-view');
            } else {
                couponsGrid.classList.remove('list-view');
            }
        });
    });
    
    // Auto-submit form on filter change
    const filterSelects = document.querySelectorAll('.filters-sidebar select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});

// Function to show coupon popup
function showCouponPopup(couponId) {
    const popup = document.getElementById(`coupon-popup-${couponId}`);
    if (popup) {
        popup.style.display = 'block';
        setTimeout(() => {
            popup.classList.add('show');
        }, 10);
        
        // Track popup view
        trackCouponPopupView(couponId);
    }
}

// Function to track coupon popup view
function trackCouponPopupView(couponId) {
    fetch('/api/coupons/' + couponId + '/track-popup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).catch(error => {
        console.error('Error tracking popup view:', error);
    });
}
</script>
@endpush