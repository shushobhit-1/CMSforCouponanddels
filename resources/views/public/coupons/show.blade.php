@extends('layouts.public')

@section('title', $coupon->title . ' - ' . $coupon->store->name . ' Coupon')
@section('meta_description', $coupon->description)

@push('styles')
<style>
    .coupon-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
    }
    
    .coupon-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    
    .coupon-card-main {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        position: relative;
        margin-top: -100px;
        z-index: 2;
    }
    
    .store-logo-large {
        width: 100px;
        height: 100px;
        object-fit: contain;
        border-radius: 15px;
        background: white;
        padding: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .discount-badge {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
        padding: 15px 25px;
        border-radius: 30px;
        font-size: 1.2rem;
        font-weight: bold;
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        display: inline-block;
    }
    
    .coupon-code-display {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        padding: 20px;
        border-radius: 15px;
        text-align: center;
        margin: 2rem 0;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .coupon-code-display:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 40px rgba(0, 123, 255, 0.3);
    }
    
    .coupon-code-display::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .coupon-code-display:hover::before {
        left: 100%;
    }
    
    .share-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin: 2rem 0;
    }
    
    .share-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 1.2rem;
    }
    
    .share-btn:hover {
        transform: translateY(-5px) scale(1.1);
        color: white;
    }
    
    .share-btn.facebook { background: #3b5998; }
    .share-btn.twitter { background: #1da1f2; }
    .share-btn.whatsapp { background: #25d366; }
    .share-btn.copy { background: #6c757d; }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        margin: 2rem 0;
    }
    
    .stat-card {
        text-align: center;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 15px;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #007bff;
        margin-bottom: 0.5rem;
    }
    
    .related-coupons {
        margin-top: 4rem;
    }
    
    .related-coupon-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .related-coupon-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 2rem;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        font-weight: bold;
        color: rgba(255,255,255,0.7);
    }
    
    .breadcrumb-item a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
    }
    
    .breadcrumb-item.active {
        color: white;
    }
    
    .store-info-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: none;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .terms-section {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem 0;
    }
    
    .get-deal-btn {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        border-radius: 25px;
        padding: 15px 40px;
        color: white;
        font-weight: bold;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
    }
    
    .get-deal-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(40, 167, 69, 0.4);
        color: white;
    }
</style>
@endpush

@section('content')
<!-- Coupon Hero Section -->
<section class="coupon-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item {{ $breadcrumb['url'] ? '' : 'active' }}">
                    @if($breadcrumb['url'])
                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
                    @else
                        {{ $breadcrumb['name'] }}
                    @endif
                </li>
                @endforeach
            </ol>
        </nav>
        
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">{{ $coupon->title }}</h1>
                <p class="lead mb-4">{{ $coupon->description }}</p>
                <div class="d-flex align-items-center gap-3">
                    <span class="discount-badge">
                        @if($coupon->discount_percentage)
                            {{ $coupon->discount_percentage }}% OFF
                        @elseif($coupon->discount_amount)
                            ₹{{ $coupon->discount_amount }} OFF
                        @else
                            SPECIAL DEAL
                        @endif
                    </span>
                    @if($coupon->is_verified)
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i>Verified
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <img src="{{ $coupon->store->getFirstMediaUrl('logo') ?: 'https://via.placeholder.com/100x100?text=' . substr($coupon->store->name, 0, 1) }}" 
                     alt="{{ $coupon->store->name }}" class="store-logo-large">
                <h4 class="mt-3 fw-bold">{{ $coupon->store->name }}</h4>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <!-- Main Coupon Card -->
    <div class="coupon-card-main">
        <div class="row">
            <div class="col-lg-8">
                @if($coupon->type === 'code')
                <!-- Coupon Code Display -->
                <div class="coupon-code-display" onclick="copyToClipboard('{{ $coupon->code }}')">
                    <h2 class="mb-2">{{ $coupon->code }}</h2>
                    <p class="mb-0">Click to copy code</p>
                </div>
                @endif
                
                <!-- Share Buttons -->
                <div class="share-buttons">
                    <a href="#" class="share-btn facebook" onclick="shareOnFacebook('{{ $coupon->title }}', '{{ $coupon->code ?? '' }}')" title="Share on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="share-btn twitter" onclick="shareOnTwitter('{{ $coupon->title }}', '{{ $coupon->code ?? '' }}')" title="Share on Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="share-btn whatsapp" onclick="shareOnWhatsApp('{{ $coupon->title }}', '{{ $coupon->code ?? '' }}')" title="Share on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="#" class="share-btn copy" onclick="copyToClipboard('{{ $coupon->code ?? $coupon->affiliate_url }}')" title="Copy to clipboard">
                        <i class="fas fa-copy"></i>
                    </a>
                </div>
                
                <!-- Action Button -->
                <div class="text-center">
                    @if($coupon->type === 'code')
                        <a href="{{ $coupon->affiliate_url }}" target="_blank" class="get-deal-btn text-decoration-none"
                           onclick="trackAffiliateClick('{{ $coupon->affiliate_url }}')">
                            <i class="fas fa-external-link-alt me-2"></i>Go to Store
                        </a>
                    @else
                        <a href="{{ $coupon->affiliate_url }}" target="_blank" class="get-deal-btn text-decoration-none"
                           onclick="trackAffiliateClick('{{ $coupon->affiliate_url }}')">
                            <i class="fas fa-bolt me-2"></i>Get Deal
                        </a>
                    @endif
                </div>
                
                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value">{{ number_format($coupon->views_count) }}</div>
                        <div class="stat-label">Views</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ number_format($coupon->clicks_count) }}</div>
                        <div class="stat-label">Used</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ $coupon->success_rate ?? 95 }}%</div>
                        <div class="stat-label">Success Rate</div>
                    </div>
                    @if($coupon->expires_at)
                    <div class="stat-card">
                        <div class="stat-value">{{ $coupon->expires_at->format('M d') }}</div>
                        <div class="stat-label">Expires</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Store Information -->
                <div class="store-info-card">
                    <h5 class="fw-bold mb-3">About {{ $coupon->store->name }}</h5>
                    <p class="text-muted mb-3">{{ Str::limit($coupon->store->description, 150) }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Category:</span>
                        <span class="fw-bold">{{ $coupon->category->name ?? 'General' }}</span>
                    </div>
                    
                    @if($coupon->expires_at)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Valid Until:</span>
                        <span class="fw-bold text-{{ $coupon->expires_at->isPast() ? 'danger' : 'success' }}">
                            {{ $coupon->expires_at->format('F j, Y') }}
                        </span>
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Added:</span>
                        <span class="fw-bold">{{ $coupon->created_at->diffForHumans() }}</span>
                    </div>
                    
                    <a href="{{ route('stores.show', $coupon->store->slug) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-store me-2"></i>View All {{ $coupon->store->name }} Coupons
                    </a>
                    
                    <button class="btn btn-outline-danger w-100 mt-2 favorite-btn" 
                            data-type="coupon" data-id="{{ $coupon->id }}">
                        <i class="far fa-heart me-2"></i>Add to Favorites
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Terms and Conditions -->
    @if($coupon->terms)
    <div class="terms-section">
        <h5 class="fw-bold mb-3">
            <i class="fas fa-file-contract me-2"></i>Terms & Conditions
        </h5>
        <div class="text-muted">
            {!! nl2br(e($coupon->terms)) !!}
        </div>
    </div>
    @endif
    
    <!-- How to Use -->
    <div class="terms-section">
        <h5 class="fw-bold mb-3">
            <i class="fas fa-question-circle me-2"></i>How to Use This Coupon
        </h5>
        <ol class="text-muted">
            @if($coupon->type === 'code')
            <li>Click the "Copy Code" button above to copy the coupon code</li>
            <li>Click "Go to Store" to visit {{ $coupon->store->name }}</li>
            <li>Add products to your cart and proceed to checkout</li>
            <li>Paste the coupon code in the discount/promo code field</li>
            <li>Complete your purchase and enjoy the savings!</li>
            @else
            <li>Click the "Get Deal" button above</li>
            <li>You'll be redirected to {{ $coupon->store->name }}</li>
            <li>The discount will be automatically applied</li>
            <li>Complete your purchase and enjoy the savings!</li>
            @endif
        </ol>
    </div>
    
    <!-- Related Coupons -->
    @if($relatedCoupons->count() > 0)
    <div class="related-coupons">
        <h3 class="fw-bold mb-4">Related Coupons</h3>
        <div class="row">
            @foreach($relatedCoupons as $related)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="related-coupon-card card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $related->store->getFirstMediaUrl('logo') ?: 'https://via.placeholder.com/50x50?text=' . substr($related->store->name, 0, 1) }}" 
                                 alt="{{ $related->store->name }}" class="me-3" style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px;">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $related->store->name }}</h6>
                                <small class="text-muted">{{ $related->category->name ?? 'General' }}</small>
                            </div>
                        </div>
                        <h6 class="card-title">{{ Str::limit($related->title, 50) }}</h6>
                        <p class="card-text text-muted small">{{ Str::limit($related->description, 60) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">
                                @if($related->discount_percentage)
                                    {{ $related->discount_percentage }}% OFF
                                @else
                                    DEAL
                                @endif
                            </span>
                            <a href="{{ route('coupons.show', $related->slug) }}" class="btn btn-sm btn-outline-primary">
                                View Deal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Track page view
document.addEventListener('DOMContentLoaded', function() {
    // This would typically be handled by analytics
    console.log('Coupon viewed: {{ $coupon->id }}');
});
</script>
@endpush