@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Edit Slider</h1>
        <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">Back</a>
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

    <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data" class="card">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $slider->title) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $slider->slug) }}" required>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $slider->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="mb-3">Slides</h6>
            <div id="slidesWrapper">
                @php $slides = is_array($slider->slides) ? $slider->slides : []; @endphp
                @foreach($slides as $i => $slide)
                <div class="card mb-3 slide-item">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="slides[{{ $i }}][title]" value="{{ $slide['title'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Subtitle</label>
                                <input type="text" class="form-control" name="slides[{{ $i }}][subtitle]" value="{{ $slide['subtitle'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">CTA Label</label>
                                <input type="text" class="form-control" name="slides[{{ $i }}][cta_label]" value="{{ $slide['cta_label'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">CTA URL</label>
                                <input type="url" class="form-control" name="slides[{{ $i }}][cta_url]" value="{{ $slide['cta_url'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Image (1600x600)</label>
                                <input type="hidden" name="slides[{{ $i }}][existing_image]" value="{{ $slide['image'] ?? '' }}">
                                <input type="file" class="form-control" name="slides[{{ $i }}][image]" accept="image/*">
                                @if(!empty($slide['image']))
                                    <img src="{{ $slide['image'] }}" class="img-fluid mt-2 rounded" style="max-height: 120px; object-fit: cover;" alt="slide">
                                @endif
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-outline-danger remove-slide">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary" id="addSlideBtn"><i class="fa fa-plus me-1"></i> Add Slide</button>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save Changes</button>
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
                    <label class="form-label">Image (1600x600)</label>
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
        let index = {{ is_array($slider->slides) ? count($slider->slides) : 0 }};
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

