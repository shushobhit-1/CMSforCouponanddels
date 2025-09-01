@extends('admin.layouts.app')

@section('title', 'Manage Coupons')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Manage Coupons</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Coupons</li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Coupon
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.coupons.index') }}" id="filters-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search coupons...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="store_id">Store</label>
                            <select class="form-control" id="store_id" name="store_id">
                                <option value="">All Stores</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="featured">Featured</label>
                            <select class="form-control" id="featured" name="featured">
                                <option value="">All</option>
                                <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured</option>
                                <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Not Featured</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Coupons Table Card -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">Coupons ({{ $coupons->total() }})</h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            Bulk Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item bulk-action" href="#" data-action="activate">Activate Selected</a></li>
                            <li><a class="dropdown-item bulk-action" href="#" data-action="deactivate">Deactivate Selected</a></li>
                            <li><a class="dropdown-item bulk-action" href="#" data-action="feature">Feature Selected</a></li>
                            <li><a class="dropdown-item bulk-action" href="#" data-action="unfeature">Unfeature Selected</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item bulk-action text-danger" href="#" data-action="delete">Delete Selected</a></li>
                        </ul>
                    </div>
                    <a href="{{ route('admin.coupons.export') }}" class="btn btn-outline-success ms-2">
                        <i class="fas fa-download"></i> Export
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="coupons-table">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>Coupon</th>
                            <th>Store</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Performance</th>
                            <th>Created</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input coupon-checkbox" value="{{ $coupon->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="coupon-image me-3">
                                        <img src="{{ $coupon->image_url }}" alt="{{ $coupon->title }}" 
                                             class="rounded" width="50" height="50">
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $coupon->title }}</h6>
                                        @if($coupon->code)
                                            <small class="text-muted">Code: {{ $coupon->code }}</small>
                                        @endif
                                        <div class="mt-1">
                                            @if($coupon->is_featured)
                                                <span class="badge bg-warning">Featured</span>
                                            @endif
                                            @if($coupon->is_popular)
                                                <span class="badge bg-info">Popular</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $coupon->store->logo_url }}" alt="{{ $coupon->store->name }}" 
                                         class="rounded me-2" width="30" height="30">
                                    <span>{{ $coupon->store->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($coupon->category)
                                    <span class="badge bg-secondary">{{ $coupon->category->name }}</span>
                                @else
                                    <span class="text-muted">Uncategorized</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ ucfirst($coupon->type) }}</span>
                                <div class="mt-1">
                                    <strong>{{ $coupon->discount_text }}</strong>
                                </div>
                            </td>
                            <td>
                                @if($coupon->is_expired)
                                    <span class="badge bg-danger">Expired</span>
                                @elseif($coupon->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                                <div class="mt-1">
                                    <small class="text-muted">
                                        {{ $coupon->start_date->format('M d') }} - {{ $coupon->end_date->format('M d') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="performance-stats">
                                    <div class="d-flex justify-content-between">
                                        <span>Clicks:</span>
                                        <strong>{{ number_format($coupon->click_count) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Conversions:</span>
                                        <strong>{{ number_format($coupon->conversion_count) }}</strong>
                                    </div>
                                    @if($coupon->revenue > 0)
                                    <div class="d-flex justify-content-between">
                                        <span>Revenue:</span>
                                        <strong class="text-success">${{ number_format($coupon->revenue, 2) }}</strong>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $coupon->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.coupons.show', $coupon) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-success toggle-status" 
                                            data-id="{{ $coupon->id }}" title="Toggle Status">
                                        <i class="fas fa-toggle-{{ $coupon->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning toggle-featured" 
                                            data-id="{{ $coupon->id }}" title="Toggle Featured">
                                        <i class="fas fa-star{{ $coupon->is_featured ? '' : '-o' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-coupon" 
                                            data-id="{{ $coupon->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                    <h5>No coupons found</h5>
                                    <p class="text-muted">Get started by creating your first coupon.</p>
                                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Coupon
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($coupons->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $coupons->firstItem() }} to {{ $coupons->lastItem() }} of {{ $coupons->total() }} results
                </div>
                <div>
                    {{ $coupons->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this coupon? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all functionality
    $('#select-all').change(function() {
        $('.coupon-checkbox').prop('checked', $(this).is(':checked'));
    });

    // Bulk actions
    $('.bulk-action').click(function(e) {
        e.preventDefault();
        
        const action = $(this).data('action');
        const selectedIds = $('.coupon-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('Warning', 'Please select at least one coupon.', 'warning');
            return;
        }

        let confirmMessage = '';
        let confirmButtonColor = 'primary';

        switch (action) {
            case 'delete':
                confirmMessage = 'Are you sure you want to delete the selected coupons? This action cannot be undone.';
                confirmButtonColor = 'danger';
                break;
            case 'activate':
                confirmMessage = 'Are you sure you want to activate the selected coupons?';
                break;
            case 'deactivate':
                confirmMessage = 'Are you sure you want to deactivate the selected coupons?';
                break;
            case 'feature':
                confirmMessage = 'Are you sure you want to feature the selected coupons?';
                break;
            case 'unfeature':
                confirmMessage = 'Are you sure you want to unfeature the selected coupons?';
                break;
        }

        Swal.fire({
            title: 'Confirm Action',
            text: confirmMessage,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmButtonColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction(action, selectedIds);
            }
        });
    });

    // Toggle status
    $('.toggle-status').click(function() {
        const couponId = $(this).data('id');
        const button = $(this);
        
        $.ajax({
            url: `/admin/coupons/${couponId}/toggle-status`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success', response.message, 'success');
                    
                    // Update button icon
                    const icon = button.find('i');
                    if (response.is_active) {
                        icon.removeClass('fa-toggle-off').addClass('fa-toggle-on');
                        button.removeClass('btn-outline-success').addClass('btn-outline-success');
                    } else {
                        icon.removeClass('fa-toggle-on').addClass('fa-toggle-off');
                        button.removeClass('btn-outline-success').addClass('btn-outline-secondary');
                    }
                    
                    // Reload page after a short delay to update status badges
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to update coupon status.', 'error');
            }
        });
    });

    // Toggle featured
    $('.toggle-featured').click(function() {
        const couponId = $(this).data('id');
        const button = $(this);
        
        $.ajax({
            url: `/admin/coupons/${couponId}/toggle-featured`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success', response.message, 'success');
                    
                    // Update button icon
                    const icon = button.find('i');
                    if (response.is_featured) {
                        icon.removeClass('fa-star-o').addClass('fa-star');
                        button.removeClass('btn-outline-warning').addClass('btn-outline-warning');
                    } else {
                        icon.removeClass('fa-star').addClass('fa-star-o');
                        button.removeClass('btn-outline-warning').addClass('btn-outline-secondary');
                    }
                    
                    // Reload page after a short delay to update badges
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to update featured status.', 'error');
            }
        });
    });

    // Delete coupon
    $('.delete-coupon').click(function() {
        const couponId = $(this).data('id');
        
        Swal.fire({
            title: 'Confirm Delete',
            text: 'Are you sure you want to delete this coupon? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/admin/coupons/${couponId}`;
            }
        });
    });

    // Perform bulk action
    function performBulkAction(action, couponIds) {
        $.ajax({
            url: '{{ route("admin.coupons.bulk-action") }}',
            method: 'POST',
            data: {
                action: action,
                coupon_ids: couponIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success', response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to perform bulk action.', 'error');
            }
        });
    }

    // Auto-submit filters on change
    $('#store_id, #category_id, #status, #featured').change(function() {
        $('#filters-form').submit();
    });

    // Clear filters
    $('.clear-filters').click(function() {
        window.location.href = '{{ route("admin.coupons.index") }}';
    });
});
</script>
@endpush

@push('styles')
<style>
.coupon-image img {
    object-fit: cover;
}

.performance-stats {
    font-size: 0.875rem;
}

.performance-stats .d-flex {
    margin-bottom: 0.25rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
}

.empty-state i {
    color: #6c757d;
}

.btn-group .btn {
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}
</style>
@endpush