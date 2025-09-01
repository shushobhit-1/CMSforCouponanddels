@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Dashboard</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ul>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" onclick="refreshStats()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="exportReport()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Users Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-widget-one">
                        <div class="stat-icon">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-text">Total Users</div>
                            <div class="stat-digit">{{ number_format($totalUsers) }}</div>
                            <div class="stat-change {{ $userGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-{{ $userGrowth >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($userGrowth), 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coupons Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-widget-one">
                        <div class="stat-icon">
                            <i class="fas fa-ticket-alt text-success"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-text">Active Coupons</div>
                            <div class="stat-digit">{{ number_format($activeCoupons) }}</div>
                            <div class="stat-change text-warning">
                                <i class="fas fa-clock"></i>
                                {{ $expiringCoupons }} expiring soon
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-widget-one">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign text-warning"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-text">Monthly Revenue</div>
                            <div class="stat-digit">${{ number_format($monthlyRevenue, 2) }}</div>
                            <div class="stat-change {{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-{{ $revenueGrowth >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($revenueGrowth), 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-widget-one">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line text-info"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-text">Conversion Rate</div>
                            <div class="stat-digit">{{ number_format($conversionRate, 2) }}%</div>
                            <div class="stat-change text-info">
                                <i class="fas fa-eye"></i>
                                {{ number_format($totalClicks) }} clicks
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Monthly Statistics Chart -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Monthly Statistics</h4>
                    <div class="card-tools">
                        <select class="form-select form-select-sm" id="chartType">
                            <option value="users">Users</option>
                            <option value="coupons">Coupons</option>
                            <option value="deals">Deals</option>
                            <option value="revenue">Revenue</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Daily Activity Chart -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daily Activity (Last 30 Days)</h4>
                </div>
                <div class="card-body">
                    <canvas id="dailyChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Overview -->
    <div class="row">
        <!-- Recent Coupons -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Coupons</h4>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Store</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCoupons as $coupon)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="text-primary">
                                            {{ Str::limit($coupon->title, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ $coupon->store->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $coupon->is_active ? 'success' : 'danger' }}">
                                            {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $coupon->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No coupons found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Deals -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Deals</h4>
                    <a href="{{ route('admin.deals.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Store</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDeals as $deal)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.deals.edit', $deal->id) }}" class="text-primary">
                                            {{ Str::limit($deal->title, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ $deal->store->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $deal->is_active ? 'success' : 'danger' }}">
                                            {{ $deal->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $deal->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No deals found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row">
        <!-- Top Performing Coupons -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Top Performing Coupons</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Coupon</th>
                                    <th>Store</th>
                                    <th>Clicks</th>
                                    <th>Conversions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCoupons as $coupon)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="text-primary">
                                            {{ Str::limit($coupon->title, 25) }}
                                        </a>
                                    </td>
                                    <td>{{ $coupon->store->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($coupon->click_count) }}</td>
                                    <td>{{ number_format($coupon->conversion_count) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performing Stores -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Top Performing Stores</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Store</th>
                                    <th>Rating</th>
                                    <th>Clicks</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topStores as $store)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.stores.edit', $store->id) }}" class="text-primary">
                                            {{ Str::limit($store->name, 25) }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="text-warning me-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $store->rating ? '' : '-o' }}"></i>
                                                @endfor
                                            </span>
                                            <span class="ms-1">({{ $store->rating }})</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($store->click_count) }}</td>
                                    <td>${{ number_format($store->revenue, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">System Health</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="health-item">
                                <div class="health-icon">
                                    <i class="fas fa-hdd text-{{ $systemHealth['disk_usage'] > 80 ? 'danger' : 'success' }}"></i>
                                </div>
                                <div class="health-info">
                                    <h6>Disk Usage</h6>
                                    <p class="mb-0">{{ $systemHealth['disk_usage'] }}%</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="health-item">
                                <div class="health-icon">
                                    <i class="fas fa-memory text-{{ $systemHealth['memory_usage'] > 100 ? 'warning' : 'success' }}"></i>
                                </div>
                                <div class="health-info">
                                    <h6>Memory Usage</h6>
                                    <p class="mb-0">{{ $systemHealth['memory_usage'] }} MB</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="health-item">
                                <div class="health-icon">
                                    <i class="fas fa-database text-success"></i>
                                </div>
                                <div class="health-info">
                                    <h6>Database</h6>
                                    <p class="mb-0">{{ $systemHealth['database_connections'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="health-item">
                                <div class="health-icon">
                                    <i class="fas fa-code text-info"></i>
                                </div>
                                <div class="health-info">
                                    <h6>PHP Version</h6>
                                    <p class="mb-0">{{ $systemHealth['php_version'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Modal -->
<div class="modal fade" id="quickActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-outline-primary w-100 mb-3">
                            <i class="fas fa-plus"></i> Add Coupon
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.deals.create') }}" class="btn btn-outline-success w-100 mb-3">
                            <i class="fas fa-plus"></i> Add Deal
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.stores.create') }}" class="btn btn-outline-info w-100 mb-3">
                            <i class="fas fa-plus"></i> Add Store
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-warning w-100 mb-3">
                            <i class="fas fa-plus"></i> Add User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Statistics Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: @json($monthlyStats->pluck('month')),
        datasets: [{
            label: 'Users',
            data: @json($monthlyStats->pluck('users')),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Daily Activity Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
const dailyChart = new Chart(dailyCtx, {
    type: 'bar',
    data: {
        labels: @json($dailyStats->pluck('date')),
        datasets: [{
            label: 'Clicks',
            data: @json($dailyStats->pluck('clicks')),
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
        }, {
            label: 'Conversions',
            data: @json($dailyStats->pluck('conversions')),
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Chart Type Change Handler
document.getElementById('chartType').addEventListener('change', function() {
    const type = this.value;
    const data = @json($monthlyStats);
    
    monthlyChart.data.datasets[0].data = data.map(item => item[type]);
    monthlyChart.data.datasets[0].label = type.charAt(0).toUpperCase() + type.slice(1);
    monthlyChart.update();
});

// Functions
function refreshStats() {
    location.reload();
}

function exportReport() {
    // Implement export functionality
    alert('Export functionality will be implemented here');
}

// Auto-refresh every 5 minutes
setInterval(function() {
    // You can implement AJAX refresh here instead of full page reload
}, 300000);
</script>
@endpush

@push('styles')
<style>
.stat-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-widget-one {
    display: flex;
    align-items: center;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
}

.stat-icon i {
    font-size: 24px;
}

.stat-content {
    flex: 1;
}

.stat-text {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 5px;
}

.stat-digit {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.stat-change {
    font-size: 12px;
    font-weight: 600;
}

.health-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 10px;
    background: rgba(0,0,0,0.02);
}

.health-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.health-icon i {
    font-size: 20px;
}

.health-info h6 {
    margin: 0;
    font-size: 14px;
    color: #6c757d;
}

.health-info p {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
}

.badge {
    font-size: 11px;
    padding: 5px 10px;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.08);
}

.card-header {
    background: transparent;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 20px 25px;
}

.card-body {
    padding: 25px;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
}

.form-select {
    border-radius: 8px;
}
</style>
@endpush