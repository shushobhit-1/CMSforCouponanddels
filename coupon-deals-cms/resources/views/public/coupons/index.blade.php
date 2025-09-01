@extends('layouts.public')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Latest Coupons</h1>
    </div>

    <div class="row g-3">
        @forelse ($coupons as $coupon)
            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title mb-2 text-truncate" title="{{ $coupon->title }}">{{ $coupon->title }}</h6>
                        <div class="small text-muted mb-3">{{ $coupon->discount_text ?? 'Special Offer' }}</div>
                        <div class="mt-auto d-grid">
                            <button
                                class="btn btn-primary btn-show-coupon"
                                data-title="{{ $coupon->title }}"
                                data-code="{{ $coupon->code }}"
                                data-affiliate="{{ $coupon->affiliate_url }}"
                            >
                                <i class="fa-solid fa-ticket me-1"></i> Show Coupon
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">No coupons available.</div></div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $coupons->links() }}
    </div>
</div>

<script type="module">
    function shareLinks(url, title){
        const encodedUrl = encodeURIComponent(url);
        const encodedTitle = encodeURIComponent(title);
        return {
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
            twitter: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`,
            whatsapp: `https://api.whatsapp.com/send?text=${encodedTitle}%20${encodedUrl}`,
        };
    }

    document.addEventListener('click', async (e) => {
        const trigger = e.target.closest('.btn-show-coupon');
        if(!trigger) return;
        e.preventDefault();

        const title = trigger.dataset.title || 'Coupon';
        const code = trigger.dataset.code || '';
        const affiliate = trigger.dataset.affiliate || '#';
        const links = shareLinks(affiliate, title);

        const result = await window.Swal.fire({
            title: title,
            html: `
                <div class="mb-3">Copy the code and use it at checkout.</div>
                <div class="input-group mb-3">
                    <input readonly class="form-control" value="${code}" id="coupon-code-input" />
                    <button class="btn btn-outline-secondary" id="copy-code-btn"><i class="fa-regular fa-copy"></i></button>
                </div>
                <div class="d-flex align-items-center mb-2">Share:</div>
                <div class="d-flex align-items-center">
                    <a class="share-circle facebook me-2" href="${links.facebook}" target="_blank" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a class="share-circle twitter me-2" href="${links.twitter}" target="_blank" title="Share on X"><i class="fab fa-x-twitter"></i></a>
                    <a class="share-circle whatsapp me-2" href="${links.whatsapp}" target="_blank" title="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <button class="share-circle copy" type="button" id="copy-link-btn" title="Copy Link"><i class="fa-regular fa-link"></i></button>
                </div>
            `,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: 'Go to Store',
            cancelButtonText: 'Close',
            didOpen: () => {
                const copyBtn = document.getElementById('copy-code-btn');
                if(copyBtn){
                    copyBtn.addEventListener('click', async () => {
                        await navigator.clipboard.writeText(code);
                        window.Swal.showValidationMessage('Code copied');
                        setTimeout(() => window.Swal.resetValidationMessage(), 1200);
                    }, { once: true });
                }
                const copyLinkBtn = document.getElementById('copy-link-btn');
                if(copyLinkBtn){
                    copyLinkBtn.addEventListener('click', async () => {
                        await navigator.clipboard.writeText(affiliate);
                        window.Swal.showValidationMessage('Link copied');
                        setTimeout(() => window.Swal.resetValidationMessage(), 1200);
                    }, { once: true });
                }
            }
        });

        if(result.isConfirmed){
            window.open(affiliate, '_blank');
        }
    });
</script>
@endsection

