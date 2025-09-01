<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title') - {{ config('app.name', 'Coupon Deals CMS') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Coupon CMS</span>
                    </a>
                </div>
                <button type="button" id="sidebarCollapse" class="btn btn-link d-lg-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="sidebar-content">
                <ul class="list-unstyled components">
                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="menu-section">
                        <span class="menu-section-text">Content Management</span>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.coupons.index') }}">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Coupons</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.deals.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.deals.index') }}">
                            <i class="fas fa-tags"></i>
                            <span>Deals</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box"></i>
                            <span>Products</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.stores.index') }}">
                            <i class="fas fa-store"></i>
                            <span>Stores</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.categories.index') }}">
                            <i class="fas fa-folder"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    
                    <li class="menu-section">
                        <span class="menu-section-text">User Management</span>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}">
                            <i class="fas fa-user-shield"></i>
                            <span>Roles & Permissions</span>
                        </a>
                    </li>
                    
                    <li class="menu-section">
                        <span class="menu-section-text">Appearance</span>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.theme.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.theme.index') }}">
                            <i class="fas fa-palette"></i>
                            <span>Theme Settings</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.menus.index') }}">
                            <i class="fas fa-bars"></i>
                            <span>Menu Management</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.sliders.index') }}">
                            <i class="fas fa-images"></i>
                            <span>Sliders</span>
                        </a>
                    </li>
                    
                    <li class="menu-section">
                        <span class="menu-section-text">Settings</span>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.settings.index') }}">
                            <i class="fas fa-cog"></i>
                            <span>General Settings</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.affiliates.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.affiliates.index') }}">
                            <i class="fas fa-handshake"></i>
                            <span>Affiliate Settings</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.seo.index') }}">
                            <i class="fas fa-search"></i>
                            <span>SEO Settings</span>
                        </a>
                    </li>
                    
                    <li class="menu-section">
                        <span class="menu-section-text">Analytics</span>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.analytics') }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.reports') }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content" class="content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white top-navbar">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-link d-lg-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="navbar-nav ms-auto">
                        <!-- Search -->
                        <div class="nav-item dropdown me-3">
                            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-search"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end search-dropdown">
                                <form class="p-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search...">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Notifications -->
                        <div class="nav-item dropdown me-3">
                            <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                                <div class="dropdown-header">
                                    <h6 class="mb-0">Notifications</h6>
                                    <a href="#" class="text-muted">Mark all as read</a>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-ticket-alt text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-0">New coupon added</p>
                                            <small class="text-muted">2 minutes ago</small>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-user text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-0">New user registered</p>
                                            <small class="text-muted">5 minutes ago</small>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="#">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar_url }}" alt="User Avatar" class="rounded-circle me-2" width="32" height="32">
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                                    <i class="fas fa-cog me-2"></i>Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/admin.js') }}"></script>
    
    @stack('scripts')
    
    <script>
        // Sidebar toggle
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            
            if (window.innerWidth <= 991 && 
                !sidebar.contains(event.target) && 
                !sidebarCollapse.contains(event.target)) {
                sidebar.classList.remove('active');
                document.getElementById('content').classList.remove('active');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>