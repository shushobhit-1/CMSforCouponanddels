@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Header Editor</h1>
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

    <form action="{{ route('admin.appearance.header.save') }}" method="POST" class="card">
        @csrf
        <div class="card-body">
            <label class="form-label">Custom Header HTML</label>
            <textarea name="html" class="form-control" rows="8" placeholder="&lt;div&gt;Custom header content...&lt;/div&gt;">{{ old('html', $header) }}</textarea>
            <small class="text-muted">Safe HTML is recommended. This will render at the top of the page.</small>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save Header</button>
        </div>
    </form>
</div>
@endsection

