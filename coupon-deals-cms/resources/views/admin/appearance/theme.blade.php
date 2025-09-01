@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Theme Settings</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.appearance.theme.update') }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Primary Color</label>
                    <input type="color" name="primary_color" class="form-control form-control-color" value="{{ $theme['primary_color'] ?? '#007bff' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Secondary Color</label>
                    <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ $theme['secondary_color'] ?? '#6c757d' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Font Family</label>
                    <input type="text" name="font_family" class="form-control" value="{{ $theme['font_family'] ?? 'Inter, sans-serif' }}" placeholder="Inter, sans-serif">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="rounded" value="1" id="rounded" {{ !empty($theme['rounded']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="rounded">Use rounded corners</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save Theme</button>
        </div>
    </form>
</div>
@endsection

