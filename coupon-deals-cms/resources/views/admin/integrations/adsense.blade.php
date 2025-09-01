@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Google AdSense</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('admin.integrations.adsense.save') }}" method="POST" class="card">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Publisher ID (ca-pub-XXXXXXXXXXXX)</label>
                    <input type="text" name="publisher_id" class="form-control" value="{{ old('publisher_id', $adsense['publisher_id'] ?? '') }}">
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="enabled" value="1" id="enabled" {{ !empty($adsense['enabled']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="enabled">Enable AdSense</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="auto_ads" value="1" id="auto_ads" {{ !empty($adsense['auto_ads']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_ads">Enable Auto Ads</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save AdSense</button>
        </div>
    </form>
</div>
@endsection

