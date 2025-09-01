@extends('layouts.admin')

@section('title', 'Affiliate Networks Management')

@push('styles')
<style>
    .affiliate-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .affiliate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }
    
    .affiliate-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        position: relative;
    }
    
    .affiliate-logo {
        width: 60px;
        height: 60px;
        background: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        color: #667eea;
    }
    
    .status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .status-active {
        background: #28a745;
        color: white;
    }
    
    .status-inactive {
        background: #dc3545;
        color: white;
    }
    
    .status-testing {
        background: #ffc107;
        color: #212529;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin: 1rem 0;
    }
    
    .stat-item {
        text-align: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #007bff;
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .btn-test {
        background: linear-gradient(45deg, #17a2b8, #20c997);
        border: none;
        color: white;
    }
    
    .btn-sync {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        color: white;
    }
    
    .commission-rate {
        font-size: 1.1rem;
        font-weight: bold;
        color: #28a745;
    }
    
    .last-sync {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .add-network-card {
        border: 2px dashed #dee2e6;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 300px;
        border-radius: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .add-network-card:hover {
        border-color: #007bff;
        background: #e3f2fd;
    }
    
    .network-logos {
        display: flex;
        gap: 10px;
        margin: 1rem 0;
    }
    
    .network-logo {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: bold;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Affiliate Networks</h1>
            <p class="mb-0 text-muted">Manage your affiliate network integrations and API keys</p>
        </div>
        <div>
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addNetworkModal">
                <i class="fas fa-plus me-2"></i>Add Network
            </button>
            <button class="btn btn-success" onclick="syncAllNetworks()">
                <i class="fas fa-sync me-2"></i>Sync All
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Networks
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $affiliates->where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-network-wired fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Product::whereNotNull('affiliate_id')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                This Month Clicks
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\AffiliateClick::whereMonth('created_at', now()->month)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mouse-pointer fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg Commission
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($affiliates->avg('commission_rate'), 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Affiliate Networks Grid -->
    <div class="row">
        @foreach($affiliates as $affiliate)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="affiliate-card card h-100">
                <div class="affiliate-header">
                    <div class="status-badge status-{{ $affiliate->is_active ? 'active' : 'inactive' }}">
                        {{ $affiliate->is_active ? 'Active' : 'Inactive' }}
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="affiliate-logo me-3">
                            {{ strtoupper(substr($affiliate->name, 0, 2)) }}
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $affiliate->name }}</h5>
                            <p class="mb-0 opacity-75">{{ Str::limit($affiliate->description, 50) }}</p>
                        </div>
                    </div>
                    
                    <div class="commission-rate">
                        Commission: {{ $affiliate->commission_rate }}%
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value">{{ $affiliate->products_count ?? 0 }}</div>
                            <div class="stat-label">Products</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $affiliate->clicks_count ?? 0 }}</div>
                            <div class="stat-label">Clicks</div>
                        </div>
                    </div>
                    
                    <!-- API Status -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">API Status:</span>
                            <span class="badge bg-{{ $affiliate->api_status ?? 'secondary' }}">
                                {{ ucfirst($affiliate->api_status ?? 'Unknown') }}
                            </span>
                        </div>
                        @if($affiliate->last_sync_at)
                        <div class="last-sync">
                            Last sync: {{ $affiliate->last_sync_at->diffForHumans() }}
                        </div>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button class="btn btn-test btn-sm" onclick="testConnection({{ $affiliate->id }})">
                            <i class="fas fa-plug me-1"></i>Test
                        </button>
                        <button class="btn btn-sync btn-sm" onclick="syncNetwork({{ $affiliate->id }})">
                            <i class="fas fa-sync me-1"></i>Sync
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="editNetwork({{ $affiliate->id }})">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        <button class="btn btn-info btn-sm" onclick="viewStats({{ $affiliate->id }})">
                            <i class="fas fa-chart-bar me-1"></i>Stats
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        <!-- Add New Network Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="add-network-card" data-bs-toggle="modal" data-bs-target="#addNetworkModal">
                <div class="text-center">
                    <i class="fas fa-plus fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Add New Network</h5>
                    <p class="text-muted">Connect with popular affiliate networks</p>
                    
                    <div class="network-logos">
                        <div class="network-logo">VC</div>
                        <div class="network-logo">CL</div>
                        <div class="network-logo">OM</div>
                        <div class="network-logo">INR</div>
                        <div class="network-logo">AMZ</div>
                        <div class="network-logo">FK</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Network Modal -->
<div class="modal fade" id="addNetworkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Affiliate Network</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addNetworkForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Network Name</label>
                                <select class="form-select" name="network_type" required>
                                    <option value="">Select Network</option>
                                    <option value="vcommission">vCommission</option>
                                    <option value="cuelinks">Cuelinks</option>
                                    <option value="optimisemedia">OptimiseMedia</option>
                                    <option value="inrdeals">INR Deals</option>
                                    <option value="amazon">Amazon Associates</option>
                                    <option value="flipkart">Flipkart</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Display Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">API Key</label>
                                <input type="text" class="form-control" name="api_key" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">API Secret (Optional)</label>
                                <input type="text" class="form-control" name="api_secret">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Publisher ID</label>
                                <input type="text" class="form-control" name="publisher_id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Commission Rate (%)</label>
                                <input type="number" class="form-control" name="commission_rate" step="0.1" min="0" max="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Website URL</label>
                        <input type="url" class="form-control" name="website_url">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">API URL</label>
                        <input type="url" class="form-control" name="api_url">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                        <label class="form-check-label" for="isActive">
                            Activate immediately
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Network</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Network Modal -->
<div class="modal fade" id="editNetworkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Affiliate Network</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editNetworkForm">
                <div class="modal-body">
                    <!-- Form fields will be populated dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Network</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stats Modal -->
<div class="modal fade" id="statsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Network Statistics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="statsContent">
                    <!-- Stats content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Test connection
async function testConnection(affiliateId) {
    try {
        const response = await fetch(`/admin/affiliates/${affiliateId}/test-connection`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire('Success!', 'Connection test successful', 'success');
        } else {
            Swal.fire('Failed!', result.message || 'Connection test failed', 'error');
        }
    } catch (error) {
        Swal.fire('Error!', 'Failed to test connection', 'error');
    }
}

// Sync network
async function syncNetwork(affiliateId) {
    Swal.fire({
        title: 'Syncing...',
        text: 'This may take a few minutes',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch(`/admin/affiliates/${affiliateId}/sync`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire('Success!', `Synced ${result.synced_count} products`, 'success');
            location.reload();
        } else {
            Swal.fire('Failed!', result.message || 'Sync failed', 'error');
        }
    } catch (error) {
        Swal.fire('Error!', 'Failed to sync network', 'error');
    }
}

// Sync all networks
async function syncAllNetworks() {
    Swal.fire({
        title: 'Syncing All Networks...',
        text: 'This may take several minutes',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch('/admin/affiliates/sync-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            let message = 'Sync completed:\n';
            Object.entries(result.results).forEach(([network, data]) => {
                message += `${network}: ${data.synced_count || 0} products\n`;
            });
            Swal.fire('Success!', message, 'success');
            location.reload();
        } else {
            Swal.fire('Failed!', 'Some networks failed to sync', 'warning');
        }
    } catch (error) {
        Swal.fire('Error!', 'Failed to sync networks', 'error');
    }
}

// Edit network
function editNetwork(affiliateId) {
    // Load network data and populate edit form
    fetch(`/admin/affiliates/${affiliateId}`)
        .then(response => response.json())
        .then(data => {
            // Populate edit form with data
            const modal = new bootstrap.Modal(document.getElementById('editNetworkModal'));
            modal.show();
        });
}

// View stats
function viewStats(affiliateId) {
    // Load and display network statistics
    const modal = new bootstrap.Modal(document.getElementById('statsModal'));
    modal.show();
    
    // Load stats content
    document.getElementById('statsContent').innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';
    
    fetch(`/admin/affiliates/${affiliateId}/stats`)
        .then(response => response.json())
        .then(data => {
            // Display stats
            document.getElementById('statsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="clicksChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="conversionsChart"></canvas>
                    </div>
                </div>
            `;
        });
}

// Add network form submission
document.getElementById('addNetworkForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/admin/affiliates', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        });
        
        if (response.ok) {
            Swal.fire('Success!', 'Network added successfully', 'success');
            location.reload();
        } else {
            Swal.fire('Error!', 'Failed to add network', 'error');
        }
    } catch (error) {
        Swal.fire('Error!', 'Failed to add network', 'error');
    }
});
</script>
@endpush