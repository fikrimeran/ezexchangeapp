@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex align-items-center">
      <h3 class="card-title mb-0">Edit Item</h3>
      <a href="{{ route('auth.items.index') }}" class="btn btn-secondary btn-sm ml-auto">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>

    <div class="card-body">
      {{-- Validation Errors --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <strong>Whoops!</strong> Please fix the following:
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('auth.items.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Read-only fields --}}
        <div class="form-group">
          <label>Item Name</label>
          <input type="text" class="form-control" value="{{ $item->item_name }}" readonly>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea class="form-control" rows="3" readonly>{{ $item->item_description }}</textarea>
        </div>

        <div class="form-group">
          <label>Location</label>
          <input type="text" class="form-control" value="{{ $item->item_location }}" readonly>
        </div>

        <div class="form-group">
          <label>Latitude</label>
          <input type="text" class="form-control" value="{{ $item->latitude }}" readonly>
        </div>

        <div class="form-group">
          <label>Longitude</label>
          <input type="text" class="form-control" value="{{ $item->longitude }}" readonly>
        </div>

        <div class="form-group">
          <label>User</label>
          <input type="text" class="form-control" value="{{ $item->user->name ?? 'N/A' }}" readonly>
        </div>

        <div class="form-group">
          <label>Available</label>
          <input type="text" class="form-control" value="{{ $item->is_available ? 'Yes' : 'No' }}" readonly>
        </div>

        <div class="form-group">
          <label>Item Image</label>
          <div>
            @if($item->item_image)
              @php
                  $imageUrl = Str::startsWith($item->item_image, ['http://','https://'])
                      ? $item->item_image
                      : asset('storage/' . $item->item_image);
              @endphp
              <img src="{{ $imageUrl }}" alt="{{ $item->item_name }}" style="max-width:200px; border-radius:8px;">
            @else
              <span class="text-muted">No Image</span>
            @endif
          </div>
        </div>

        {{-- Editable fields: Category & Subcategory --}}
        <div class="form-group">
          <label for="category_id">Category</label>
          <select name="category_id" id="category_id" class="form-control">
            @foreach($categories as $category)
              <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                {{ $category->category_name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="subcategory_id">Subcategory</label>
          <select name="subcategory_id" id="subcategory_id" class="form-control">
            @foreach($subcategories as $subcategory)
              <option value="{{ $subcategory->id }}" {{ $item->subcategory_id == $subcategory->id ? 'selected' : '' }}>
                {{ $subcategory->name }}
              </option>
            @endforeach
          </select>
        </div>

        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Update
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
