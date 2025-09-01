@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Google Site Kit</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('admin.integrations.google.save') }}" method="POST" class="card">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Site Kit ID</label>
                    <input type="text" name="site_kit_id" class="form-control" value="{{ old('site_kit_id', $sitekit['site_kit_id'] ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Analytics ID (G-XXXX)</label>
                    <input type="text" name="analytics_id" class="form-control" value="{{ old('analytics_id', $sitekit['analytics_id'] ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tag Manager ID (GTM-XXXX)</label>
                    <input type="text" name="tag_manager_id" class="form-control" value="{{ old('tag_manager_id', $sitekit['tag_manager_id'] ?? '') }}">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="search_console_verified" value="1" id="scv" {{ !empty($sitekit['search_console_verified']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="scv">Search Console Verified</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>
@endsection

