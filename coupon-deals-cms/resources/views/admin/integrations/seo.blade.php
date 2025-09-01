@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">SEO Settings</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('admin.integrations.seo.save') }}" method="POST" class="card">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Default Title</label>
                    <input type="text" name="default_title" class="form-control" value="{{ old('default_title', $seo['default_title'] ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Default Keywords (comma separated)</label>
                    <input type="text" name="default_keywords" class="form-control" value="{{ old('default_keywords', $seo['default_keywords'] ?? '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Default Description</label>
                    <textarea name="default_description" class="form-control" rows="3" required>{{ old('default_description', $seo['default_description'] ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Open Graph Image URL</label>
                    <input type="url" name="og_image" class="form-control" value="{{ old('og_image', $seo['og_image'] ?? '') }}" placeholder="https://...">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Twitter Image URL</label>
                    <input type="url" name="twitter_image" class="form-control" value="{{ old('twitter_image', $seo['twitter_image'] ?? '') }}" placeholder="https://...">
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save SEO</button>
        </div>
    </form>
</div>
@endsection

