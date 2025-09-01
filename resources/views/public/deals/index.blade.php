@extends('layouts.public')

@section('title', 'Hot Deals & Limited Time Offers - Save Big Today')
@section('meta_description', 'Discover the hottest deals and limited time offers from top brands. Save big with our handpicked deals updated daily.')

@push('styles')
<style>
    .deals-hero {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
    }
    
    .deals-hero::before {
        content: '🔥';
        position: absolute;
        font-size: 20rem;
        opacity: 0.1;
        top: -50px;
        right: -50px;
        animation: pulse 4s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .deal-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
        background: white;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    }
    
    .deal-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    
    .deal-image {
        position: relative;
        overflow: hidden;
        height: 250px;
    }
    
    .deal-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s ease;
    }
    
    .deal-card:hover .deal-image img {
        transform: scale(1.1);
    }
    
    .deal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 107, 107, 0.8), rgba(238, 90, 36, 0.8));
        opacity: 0;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .deal-card:hover .deal-overlay {
        opacity: 1;
    }
    
    .quick-view-btn {
        background: white;
        color: #ff6b6b;
        border: none;
        padding: 12px 24px;
        border-radius: 25px;
        font-weight: bold;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }
    
    .deal-card:hover .quick-view-btn {
        transform: translateY(0);
    }
    
    .discount-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
        z-index: 2;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    
    .deal-timer {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: bold;
        z-index: 2;
    }
    
    .price-section {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 1rem 0;
    }
    
    .current-price {
        font-size: 1.5rem;
        font-weight: bold;
        color: #28a745;
    }
    
    .original-price {
        font-size: 1.1rem;
        color: #6c757d;
        text-decoration: line-through;
    }
    
    .savings-amount {
        background: #ffc107;
        color: #212529;
        padding: 4px 8px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .get-deal-btn {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        width: 100%;
    }
    
    .get-deal-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .get-deal-btn:hover:before {
        left: 100%;
    }
    
    .get-deal-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        color: white;
    }
    
    .deal-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }
    
    .deal-popularity {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .popularity-bar {
        width: 60px;
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
    }
    
    .popularity-fill {
        height: 100%;
        background: linear-gradient(45deg, #28a745, #20c997);
        border-radius: 2px;
        transition: width 0.3s ease;
    }
    
    .filters-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        position: sticky;
        top: 100px;
    }
    
    .countdown-timer {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 1rem 0;
    }
    
    .countdown-item {
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        text-align: center;
        min-width: 50px;
    }
    
    .countdown-number {
        font-weight: bold;
        font-size: 1.1rem;
    }
    
    .countdown-label {
        font-size: 0.7rem;
        opacity: 0.8;
    }
</style>
@endpush

@section('content')
<!-- Deals Hero Section -->
<section class="deals-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-3 fw-bold mb-3">🔥 Hot Deals</h1>
                <p class="lead mb-4">Limited time offers you can't afford to miss. Save big with our handpicked deals!</p>
                <div class="d-flex align-items-center gap-4">
                    <div class="text-center">
                        <h3 class="mb-0 text-warning" data-countup="{{ $deals->total() }}">0</h3>
                        <small>Active Deals</small>
                    </div>
                    <div class="text-center">
                        <h3 class="mb-0 text-warning" data-countup="75">0</h3>
                        <small>Avg. Discount %</small>
                    </div>
                    <div class="text-center">
                        <h3 class="mb-0 text-warning" data-countup="24">0</h3>
                        <small>Hours Left</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="countdown-timer">
                    <div class="countdown-item">
                        <div class="countdown-number" id="hours">24</div>
                        <div class="countdown-label">HRS</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-number" id="minutes">59</div>
                        <div class="countdown-label">MIN</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-number" id="seconds">59</div>
                        <div class="countdown-label">SEC</div>
                    </div>
                </div>
                <p class="mb-0 small">⚡ Flash Sale Ends Soon!</p>
            </div>
        </div>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="filters-card">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-filter me-2"></i>Filter Deals
                </h5>
                
                <!-- Price Range -->
                <div class="filter-section mb-4">
                    <h6 class="fw-bold mb-3">Price Range</h6>
                    <div class="mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2">
                            Under ₹1,000
                        </label>
                    </div>
                    <div class="mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2">
                            ₹1,000 - ₹5,000
                        </label>
                    </div>
                    <div class="mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2">
                            ₹5,000 - ₹10,000
                        </label>
                    </div>
                    <div class="mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2">
                            Above ₹10,000
                        </label>
                    </div>
                </div>
                
                <!-- Discount Range -->
                <div class="filter-section mb-4">
                    <h6 class="fw-bold mb-3">Discount</h6>
                    <div class="mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2">
                            50% or more
                        </label>
                    </div>
                    <div class="mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2">
                            40% - 50%
                        </label>
                    </div>
                    <div class="mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2">
                            30% - 40%
                        </label>
                    </div>
                </div>
                
                <!-- Categories -->
                <div class="filter-section mb-4">
                    <h6 class="fw-bold mb-3">Categories</h6>
                    @foreach($categories->take(6) as $category)
                    <div class="mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2" value="{{ $category->slug }}">
                            {{ $category->name }}
                        </label>
                    </div>
                    @endforeach
                </div>
                
                <!-- Stores -->
                <div class="filter-section">
                    <h6 class="fw-bold mb-3">Stores</h6>
                    @foreach($stores->take(6) as $store)
                    <div class="mb-2">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input me-2" value="{{ $store->slug }}">
                            {{ $store->name }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Deals Grid -->
        <div class="col-lg-9">
            <!-- Sort Options -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">{{ $deals->total() }} Deals Found</h4>
                    <small class="text-muted">Updated {{ now()->diffForHumans() }}</small>
                </div>
                <select class="form-select" style="width: auto;">
                    <option>Sort by Popularity</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                    <option>Discount: High to Low</option>
                    <option>Ending Soon</option>
                </select>
            </div>
            
            <!-- Deals Grid -->
            <div class="row g-4">
                @forelse($deals as $deal)
                <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="deal-card">
                        <div class="deal-image">
                            <img src="{{ $deal->getFirstMediaUrl('image') ?: 'https://via.placeholder.com/400x250?text=Deal+Image' }}" alt="{{ $deal->title }}">
                            
                            <!-- Overlay with Quick View -->
                            <div class="deal-overlay">
                                <button class="quick-view-btn" onclick="showDealDetails({{ $deal->id }})">
                                    <i class="fas fa-eye me-2"></i>Quick View
                                </button>
                            </div>
                            
                            <!-- Discount Badge -->
                            @if($deal->discount_percentage)
                            <div class="discount-badge">
                                {{ $deal->discount_percentage }}% OFF
                            </div>
                            @endif
                            
                            <!-- Timer Badge -->
                            @if($deal->expires_at)
                            <div class="deal-timer">
                                <i class="fas fa-clock me-1"></i>
                                {{ $deal->expires_at->diffForHumans(null, true) }} left
                            </div>
                            @endif
                        </div>
                        
                        <div class="card-body p-4">
                            <!-- Store Info -->
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ $deal->store->getFirstMediaUrl('logo') ?: 'https://via.placeholder.com/30x30?text=' . substr($deal->store->name, 0, 1) }}" 
                                     alt="{{ $deal->store->name }}" class="me-2" style="width: 30px; height: 30px; border-radius: 5px;">
                                <small class="text-muted">{{ $deal->store->name }}</small>
                                <button class="btn btn-sm btn-outline-danger ms-auto favorite-btn" 
                                        data-type="deal" data-id="{{ $deal->id }}" title="Add to favorites">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            
                            <!-- Deal Title -->
                            <h5 class="card-title fw-bold mb-2">{{ Str::limit($deal->title, 60) }}</h5>
                            <p class="card-text text-muted small mb-3">{{ Str::limit($deal->description, 80) }}</p>
                            
                            <!-- Price Section -->
                            <div class="price-section">
                                <div class="current-price">₹{{ number_format($deal->deal_price) }}</div>
                                @if($deal->original_price && $deal->original_price > $deal->deal_price)
                                <div class="original-price">₹{{ number_format($deal->original_price) }}</div>
                                <div class="savings-amount">Save ₹{{ number_format($deal->original_price - $deal->deal_price) }}</div>
                                @endif
                            </div>
                            
                            <!-- Get Deal Button -->
                            <button class="get-deal-btn" onclick="showDealPopup('{{ $deal->title }}', '{{ $deal->affiliate_url }}', {{ $deal->original_price }}, {{ $deal->deal_price }})">
                                <i class="fas fa-bolt me-2"></i>Get Deal
                            </button>
                            
                            <!-- Deal Stats -->
                            <div class="deal-stats">
                                <div class="deal-popularity">
                                    <span>{{ number_format($deal->views_count) }} views</span>
                                    <div class="popularity-bar">
                                        <div class="popularity-fill" style="width: {{ min(100, ($deal->clicks_count / max(1, $deal->views_count)) * 100) }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <small class="text-success">
                                        <i class="fas fa-check me-1"></i>{{ number_format($deal->clicks_count) }} people got this deal
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-fire fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No deals found</h4>
                    <p class="text-muted">Try adjusting your filters or check back later for new deals</p>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($deals->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $deals->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/countup@1.8.2/dist/countUp.min.js"></script>

<script>
// Countdown Timer
function updateCountdown() {
    const now = new Date().getTime();
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(0, 0, 0, 0);
    
    const distance = tomorrow.getTime() - now;
    
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
}

// Update countdown every second
setInterval(updateCountdown, 1000);
updateCountdown();

// Initialize counters
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('[data-countup]');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const endVal = parseInt(target.getAttribute('data-countup'));
                const countUp = new CountUp(target, 0, endVal, 0, 2);
                countUp.start();
                observer.unobserve(target);
            }
        });
    });
    
    counters.forEach(counter => observer.observe(counter));
});

// Show deal details modal
function showDealDetails(dealId) {
    // This could show a detailed modal with more deal information
    console.log('Show details for deal:', dealId);
}

// Enhanced deal popup
function showDealPopup(dealTitle, dealUrl, originalPrice, dealPrice) {
    const savings = originalPrice - dealPrice;
    const discountPercentage = Math.round((savings / originalPrice) * 100);
    
    Swal.fire({
        title: dealTitle,
        html: `
            <div class="deal-popup-content text-center">
                <div class="price-display mb-3">
                    <span class="h3 text-success">₹${dealPrice.toLocaleString()}</span>
                    ${originalPrice > dealPrice ? `<span class="text-muted text-decoration-line-through ms-2">₹${originalPrice.toLocaleString()}</span>` : ''}
                </div>
                ${savings > 0 ? `
                <div class="savings-badge bg-warning text-dark rounded-pill px-3 py-2 mb-3 d-inline-block">
                    <i class="fas fa-tag me-1"></i>
                    Save ₹${savings.toLocaleString()} (${discountPercentage}% OFF)
                </div>` : ''}
                <p class="mb-3">Don't miss out on this amazing deal!</p>
                <div class="share-buttons mb-3">
                    <button class="btn btn-sm btn-outline-primary me-2" onclick="shareOnFacebook('${dealTitle}', 'Amazing deal!')">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info me-2" onclick="shareOnTwitter('${dealTitle}', 'Check out this deal!')">
                        <i class="fab fa-twitter"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="shareOnWhatsApp('${dealTitle}', 'Great deal alert!')">
                        <i class="fab fa-whatsapp"></i>
                    </button>
                </div>
                <div class="countdown-mini d-flex justify-content-center gap-2 mb-3">
                    <small class="text-danger">
                        <i class="fas fa-clock me-1"></i>
                        Hurry! Limited time offer
                    </small>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-bolt me-2"></i>Get Deal Now',
        cancelButtonText: 'Close',
        confirmButtonColor: '#ff6b6b',
        width: '500px'
    }).then((result) => {
        if (result.isConfirmed) {
            trackAffiliateClick(dealUrl);
        }
    });
}

// Track affiliate click for deals
async function trackAffiliateClick(affiliateUrl) {
    try {
        await fetch('/api/track-affiliate-click', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ affiliate_url: affiliateUrl })
        });
        
        window.open(affiliateUrl, '_blank');
    } catch (error) {
        console.error('Tracking error:', error);
        window.open(affiliateUrl, '_blank');
    }
}
</script>
@endpush