@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Menu Builder</h1>
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

    <form action="{{ route('admin.appearance.menus.save') }}" method="POST" class="card">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Menu Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $menu->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $menu->location) }}" required>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="mb-2">Menu Items</h6>
            <div id="itemsWrapper" class="mb-3">
                @php $items = is_array($menu->items) ? $menu->items : []; @endphp
                @foreach($items as $i => $item)
                <div class="row g-2 align-items-end mb-2 menu-item">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="items[{{ $i }}][label]" placeholder="Label" value="{{ $item['label'] ?? '' }}" required>
                    </div>
                    <div class="col-md-6">
                        <input type="url" class="form-control" name="items[{{ $i }}][url]" placeholder="https://..." value="{{ $item['url'] ?? '' }}" required>
                    </div>
                    <div class="col-md-1">
                        <select class="form-select" name="items[{{ $i }}][target]">
                            <option value="_self" {{ ($item['target'] ?? '_self') === '_self' ? 'selected' : '' }}>Same</option>
                            <option value="_blank" {{ ($item['target'] ?? '') === '_blank' ? 'selected' : '' }}>New</option>
                        </select>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-outline-danger remove-item">Remove</button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary" id="addItemBtn"><i class="fa fa-plus me-1"></i> Add Item</button>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Save Menu</button>
        </div>
    </form>
</div>

<template id="itemTemplate">
    <div class="row g-2 align-items-end mb-2 menu-item">
        <div class="col-md-4">
            <input type="text" class="form-control" name="items[__INDEX__][label]" placeholder="Label" required>
        </div>
        <div class="col-md-6">
            <input type="url" class="form-control" name="items[__INDEX__][url]" placeholder="https://..." required>
        </div>
        <div class="col-md-1">
            <select class="form-select" name="items[__INDEX__][target]">
                <option value="_self">Same</option>
                <option value="_blank">New</option>
            </select>
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-outline-danger remove-item">Remove</button>
        </div>
    </div>
</template>

@push('scripts')
<script>
    (function(){
        let index = {{ is_array($menu->items) ? count($menu->items) : 0 }};
        const wrapper = document.getElementById('itemsWrapper');
        const tpl = document.getElementById('itemTemplate').innerHTML;
        document.getElementById('addItemBtn').addEventListener('click', function(){
            const html = tpl.replaceAll('__INDEX__', index++);
            const div = document.createElement('div');
            div.innerHTML = html;
            wrapper.appendChild(div);
        });
        wrapper.addEventListener('click', function(e){
            if (e.target.closest('.remove-item')) {
                e.preventDefault();
                e.target.closest('.menu-item').remove();
            }
        });
    })();
</script>
@endpush
@endsection

