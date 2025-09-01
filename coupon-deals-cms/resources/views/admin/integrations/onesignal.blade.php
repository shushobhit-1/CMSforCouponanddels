@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">OneSignal</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('admin.integrations.onesignal.save') }}" method="POST" class="card">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">App ID</label>
                    <input type="text" name="app_id" class="form-control" value="{{ old('app_id', $onesignal['app_id'] ?? '') }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Safari Web ID</label>
                    <input type="text" name="safari_web_id" class="form-control" value="{{ old('safari_web_id', $onesignal['safari_web_id'] ?? '') }}">
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="enabled" value="1" id="onesignal_enabled" {{ !empty($onesignal['enabled']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="onesignal_enabled">Enabled</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save OneSignal</button>
        </div>
    </form>
</div>
@endsection

