@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Footer Editor</h1>
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

    <form action="{{ route('admin.appearance.footer.save') }}" method="POST" class="card">
        @csrf
        <div class="card-body">
            <label class="form-label">Custom Footer HTML</label>
            <textarea name="html" class="form-control" rows="8" placeholder="&lt;p&gt;Copyright...&lt;/p&gt;">{{ old('html', $footer) }}</textarea>
            <small class="text-muted">Safe HTML is recommended. This will render above the default footer.</small>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save Footer</button>
        </div>
    </form>
</div>
@endsection

