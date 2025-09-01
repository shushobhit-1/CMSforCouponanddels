@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Admin Dashboard</h1>
        <div>
            <a href="#" class="btn btn-primary">Quick Action</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold text-muted">Users</div>
                            <div class="fs-4">—</div>
                        </div>
                        <i class="fa-solid fa-users fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold text-muted">Coupons</div>
                            <div class="fs-4">—</div>
                        </div>
                        <i class="fa-solid fa-ticket fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold text-muted">Deals</div>
                            <div class="fs-4">—</div>
                        </div>
                        <i class="fa-solid fa-tags fs-3 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold text-muted">Products</div>
                            <div class="fs-4">—</div>
                        </div>
                        <i class="fa-solid fa-box fs-3 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

