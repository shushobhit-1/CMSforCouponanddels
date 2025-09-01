@extends('admin.layouts.app')

@section('title', 'Create New Coupon')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Create New Coupon</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Coupons
                </a>
            </div>
        </div>
    </div>

    <!-- Create Coupon Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Coupon Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.coupons.store') }}" method="POST" enctype="multipart/form-data" id="coupon-form">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Coupon Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="code" class="form-label">Coupon Code</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}" placeholder="e.g., SAVE20">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="store_id" class="form-label">Store *</label>
                                    <select class="form-control @error('store_id') is-invalid @enderror" 
                                            id="store_id" name="store_id" required>
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('store_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="type" class="form-label">Coupon Type *</label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage Discount</option>
                                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount Discount</option>
                                        <option value="free_shipping" {{ old('type') == 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                                        <option value="free_delivery" {{ old('type') == 'free_delivery' ? 'selected' : '' }}>Free Delivery</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="currency" class="form-label">Currency *</label>
                                    <select class="form-control @error('currency') is-invalid @enderror" 
                                            id="currency" name="currency" required>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                        <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                        <option value="CAD" {{ old('currency') == 'CAD' ? 'selected' : '' }}>CAD (C$)</option>
                                        <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>AUD (A$)</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Discount Fields (Dynamic based on type) -->
                        <div id="discount-fields">
                            <div class="row" id="percentage-fields" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="discount_percentage" class="form-label">Discount Percentage</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                                   id="discount_percentage" name="discount_percentage" 
                                                   value="{{ old('discount_percentage') }}" min="0" max="100" step="0.01">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('discount_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="fixed-fields" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="discount_amount" class="form-label">Discount Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="currency-symbol">$</span>
                                            <input type="number" class="form-control @error('discount_amount') is-invalid @enderror" 
                                                   id="discount_amount" name="discount_amount" 
                                                   value="{{ old('discount_amount') }}" min="0" step="0.01">
                                        </div>
                                        @error('discount_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="minimum_purchase" class="form-label">Minimum Purchase</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="min-currency-symbol">$</span>
                                        <input type="number" class="form-control @error('minimum_purchase') is-invalid @enderror" 
                                               id="minimum_purchase" name="minimum_purchase" 
                                               value="{{ old('minimum_purchase') }}" min="0" step="0.01">
                                    </div>
                                    @error('minimum_purchase')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="maximum_discount" class="form-label">Maximum Discount</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="max-currency-symbol">$</span>
                                        <input type="number" class="form-control @error('maximum_discount') is-invalid @enderror" 
                                               id="maximum_discount" name="maximum_discount" 
                                               value="{{ old('maximum_discount') }}" min="0" step="0.01">
                                    </div>
                                    @error('maximum_discount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date" class="form-label">Start Date *</label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="end_date" class="form-label">End Date *</label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                      id="short_description" name="short_description" rows="2" 
                                      placeholder="Brief description of the coupon offer">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Full Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Detailed description of the coupon, terms, and conditions">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="affiliate_link" class="form-label">Affiliate Link *</label>
                                    <input type="url" class="form-control @error('affiliate_link') is-invalid @enderror" 
                                           id="affiliate_link" name="affiliate_link" value="{{ old('affiliate_link') }}" 
                                           placeholder="https://example.com/affiliate-link" required>
                                    @error('affiliate_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tracking_id" class="form-label">Tracking ID</label>
                                    <input type="text" class="form-control @error('tracking_id') is-invalid @enderror" 
                                           id="tracking_id" name="tracking_id" value="{{ old('tracking_id') }}" 
                                           placeholder="Optional tracking identifier">
                                    @error('tracking_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Button Customization -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="button_text" class="form-label">Button Text</label>
                                    <input type="text" class="form-control @error('button_text') is-invalid @enderror" 
                                           id="button_text" name="button_text" value="{{ old('button_text', 'Get Coupon') }}" 
                                           placeholder="e.g., Get Coupon">
                                    @error('button_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="button_color" class="form-label">Button Color</label>
                                    <input type="color" class="form-control form-control-color @error('button_color') is-invalid @enderror" 
                                           id="button_color" name="button_color" value="{{ old('button_color', '#007bff') }}">
                                    @error('button_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="button_hover_effect" class="form-label">Hover Effect</label>
                                    <select class="form-control @error('button_hover_effect') is-invalid @enderror" 
                                            id="button_hover_effect" name="button_hover_effect">
                                        <option value="scale" {{ old('button_hover_effect') == 'scale' ? 'selected' : '' }}>Scale</option>
                                        <option value="glow" {{ old('button_hover_effect') == 'glow' ? 'selected' : '' }}>Glow</option>
                                        <option value="slide" {{ old('button_hover_effect') == 'slide' ? 'selected' : '' }}>Slide</option>
                                        <option value="bounce" {{ old('button_hover_effect') == 'bounce' ? 'selected' : '' }}>Bounce</option>
                                        <option value="none" {{ old('button_hover_effect') == 'none' ? 'selected' : '' }}>None</option>
                                    </select>
                                    @error('button_hover_effect')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status Options -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                               value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Featured
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular" 
                                               value="1" {{ old('is_popular') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_popular">
                                            Popular
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" 
                                               value="1" {{ old('is_verified') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_verified">
                                            Verified
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Coupon
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- SEO Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">SEO Settings</h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                               value="{{ old('meta_title') }}" placeholder="SEO title for search engines">
                        <small class="form-text text-muted">Leave empty to use coupon title</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" 
                                  rows="3" placeholder="SEO description for search engines">{{ old('meta_description') }}</textarea>
                        <small class="form-text text-muted">Leave empty to use short description</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                               value="{{ old('meta_keywords') }}" placeholder="Keywords separated by commas">
                    </div>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Coupon Images</h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="images" class="form-label">Upload Images</label>
                        <input type="file" class="form-control" id="images" name="images[]" 
                               accept="image/*" multiple>
                        <small class="form-text text-muted">
                            Upload one or more images. Supported formats: JPEG, PNG, WebP. Max size: 2MB each.
                        </small>
                    </div>
                    
                    <div id="image-preview" class="mt-3"></div>
                </div>
            </div>

            <!-- Help & Tips -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Help & Tips</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Creating Effective Coupons</h6>
                        <ul class="mb-0">
                            <li>Use clear, descriptive titles</li>
                            <li>Set realistic expiration dates</li>
                            <li>Include terms and conditions</li>
                            <li>Use high-quality images</li>
                            <li>Test affiliate links before publishing</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize form validation
    $('#coupon-form').validate({
        rules: {
            title: {
                required: true,
                minlength: 3,
                maxlength: 255
            },
            store_id: {
                required: true
            },
            type: {
                required: true
            },
            currency: {
                required: true
            },
            start_date: {
                required: true
            },
            end_date: {
                required: true
            },
            affiliate_link: {
                required: true,
                url: true
            }
        },
        messages: {
            title: {
                required: "Please enter a coupon title",
                minlength: "Title must be at least 3 characters long",
                maxlength: "Title cannot exceed 255 characters"
            },
            store_id: {
                required: "Please select a store"
            },
            type: {
                required: "Please select a coupon type"
            },
            currency: {
                required: "Please select a currency"
            },
            start_date: {
                required: "Please select a start date"
            },
            end_date: {
                required: "Please select an end date"
            },
            affiliate_link: {
                required: "Please enter an affiliate link",
                url: "Please enter a valid URL"
            }
        },
        errorElement: 'span',
        errorClass: 'invalid-feedback',
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        }
    });

    // Handle coupon type changes
    $('#type').change(function() {
        const type = $(this).val();
        
        // Hide all discount fields
        $('#discount-fields .row').hide();
        
        // Show relevant fields based on type
        if (type === 'percentage') {
            $('#percentage-fields').show();
        } else if (type === 'fixed') {
            $('#fixed-fields').show();
        }
    });

    // Handle currency changes
    $('#currency').change(function() {
        const currency = $(this).val();
        const symbols = {
            'USD': '$',
            'EUR': '€',
            'GBP': '£',
            'INR': '₹',
            'CAD': 'C$',
            'AUD': 'A$'
        };
        
        const symbol = symbols[currency] || '$';
        $('#currency-symbol, #min-currency-symbol, #max-currency-symbol').text(symbol);
    });

    // Date validation
    $('#end_date').change(function() {
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($(this).val());
        
        if (endDate <= startDate) {
            $(this).addClass('is-invalid');
            if (!$('#end_date').next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">End date must be after start date</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Image preview
    $('#images').change(function() {
        const files = this.files;
        const preview = $('#image-preview');
        preview.empty();
        
        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.append(`
                            <div class="image-preview-item mb-2">
                                <img src="${e.target.result}" class="img-thumbnail" width="100" height="100">
                                <small class="d-block text-muted">${file.name}</small>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(file);
                }
            }
        }
    });

    // Auto-generate meta title from coupon title
    $('#title').on('input', function() {
        const title = $(this).val();
        if (title && !$('#meta_title').val()) {
            $('#meta_title').val(title + ' - Coupon Code');
        }
    });

    // Auto-generate meta description from short description
    $('#short_description').on('input', function() {
        const desc = $(this).val();
        if (desc && !$('#meta_description').val()) {
            $('#meta_description').val(desc);
        }
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush

@push('styles')
<style>
.form-actions {
    padding-top: 1rem;
    border-top: 1px solid #dee2e6;
    margin-top: 2rem;
}

.image-preview-item {
    text-align: center;
}

.image-preview-item img {
    object-fit: cover;
}

.form-control-color {
    width: 100%;
    height: 38px;
}

.alert ul {
    padding-left: 1.2rem;
}

.alert li {
    margin-bottom: 0.25rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.form-text {
    font-size: 0.875rem;
}

.invalid-feedback {
    display: block;
}
</style>
@endpush