@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Create Slider</h1>
        <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data" class="card">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="is_active">
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h6 class="mb-3">Slides</h6>
            <div id="slidesWrapper"></div>
            <button type="button" class="btn btn-outline-primary" id="addSlideBtn"><i class="fa fa-plus me-1"></i> Add Slide</button>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Create Slider</button>
        </div>
    </form>
</div>

<template id="slideTemplate">
    <div class="card mb-3 slide-item">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="slides[__INDEX__][title]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subtitle</label>
                    <input type="text" class="form-control" name="slides[__INDEX__][subtitle]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">CTA Label</label>
                    <input type="text" class="form-control" name="slides[__INDEX__][cta_label]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">CTA URL</label>
                    <input type="url" class="form-control" name="slides[__INDEX__][cta_url]" placeholder="https://...">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Image (1600x600 recommended)</label>
                    <input type="file" class="form-control" name="slides[__INDEX__][image]" accept="image/*">
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-outline-danger remove-slide">Remove</button>
                </div>
            </div>
        </div>
    </div>
    </template>

@push('scripts')
<script>
    (function(){
        let index = 0;
        const wrapper = document.getElementById('slidesWrapper');
        const tpl = document.getElementById('slideTemplate').innerHTML;
        document.getElementById('addSlideBtn').addEventListener('click', function(){
            const html = tpl.replaceAll('__INDEX__', index++);
            const div = document.createElement('div');
            div.innerHTML = html;
            wrapper.appendChild(div);
        });
        wrapper.addEventListener('click', function(e){
            if (e.target.closest('.remove-slide')) {
                e.preventDefault();
                e.target.closest('.slide-item').remove();
            }
        });
    })();
    </script>
@endpush
@endsection

