@extends('layouts.admin')

@section('title', 'Affiliate Networks')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Affiliate Networks</h1>
            <p class="text-muted">Manage your affiliate network integrations and API keys</p>
        </div>
        <a href="{{ route('admin.affiliates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Network
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Active Networks</h6>
                            <h3 class="mb-0">{{ $networks->where('is_active', true)->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-network-wired fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Clicks</h6>
                            <h3 class="mb-0">{{ number_format(rand(10000, 50000)) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-mouse-pointer fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Conversions</h6>
                            <h3 class="mb-0">{{ number_format(rand(500, 2000)) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Revenue</h6>
                            <h3 class="mb-0">${{ number_format(rand(25000, 100000)) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supported Networks Overview -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Supported Affiliate Networks
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($supportedNetworks as $slug => $config)
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold">{{ $config['name'] }}</h6>
                            <small class="text-muted">{{ $config['endpoint'] }}</small>
                            <div class="mt-2">
                                <span class="badge bg-light text-dark">
                                    {{ count($config['requires']) }} API fields required
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Networks Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Configured Networks
            </h5>
        </div>
        <div class="card-body">
            @if($networks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Network</th>
                                <th>Status</th>
                                <th>Commission</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($networks as $network)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @switch($network->slug)
                                                    @case('amazon')
                                                        <div class="bg-warning rounded p-2">
                                                            <i class="fab fa-amazon text-white"></i>
                                                        </div>
                                                        @break
                                                    @case('flipkart')
                                                        <div class="bg-primary rounded p-2">
                                                            <i class="fas fa-shopping-cart text-white"></i>
                                                        </div>
                                                        @break
                                                    @default
                                                        <div class="bg-secondary rounded p-2">
                                                            <i class="fas fa-link text-white"></i>
                                                        </div>
                                                @endswitch
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $network->name }}</h6>
                                                <small class="text-muted">{{ $network->slug }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($network->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-pause me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($network->commission_rate)
                                            {{ $network->commission_rate }}%
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span data-bs-toggle="tooltip" title="{{ $network->updated_at }}">
                                            {{ $network->updated_at->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.affiliates.show', $network) }}" 
                                               class="btn btn-outline-primary" data-bs-toggle="tooltip" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.affiliates.edit', $network) }}" 
                                               class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-info" 
                                                    onclick="testConnection({{ $network->id }})" 
                                                    data-bs-toggle="tooltip" title="Test Connection">
                                                <i class="fas fa-wifi"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="syncProducts({{ $network->id }})" 
                                                    data-bs-toggle="tooltip" title="Sync Products">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.affiliates.destroy', $network) }}" 
                                                  class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" 
                                                        data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $networks->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-network-wired fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No Affiliate Networks</h5>
                    <p class="text-muted">Get started by adding your first affiliate network integration.</p>
                    <a href="{{ route('admin.affiliates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add First Network
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function testConnection(networkId) {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        
        fetch(`/admin/affiliates/${networkId}/test-connection`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: data.success ? 'success' : 'error',
                title: data.success ? 'Connection Successful' : 'Connection Failed',
                text: data.message,
                timer: 3000,
                showConfirmButton: false,
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to test connection',
                timer: 3000,
                showConfirmButton: false,
            });
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
    
    function syncProducts(networkId) {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        
        fetch(`/admin/affiliates/${networkId}/sync-products`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: data.success ? 'success' : 'error',
                title: data.success ? 'Sync Successful' : 'Sync Failed',
                text: data.message,
                timer: 3000,
                showConfirmButton: false,
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to sync products',
                timer: 3000,
                showConfirmButton: false,
            });
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
    
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush