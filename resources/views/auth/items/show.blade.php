@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title mb-0">Item Details</h3>
            <a href="{{ route('auth.items.index') }}" class="btn btn-secondary btn-sm ml-auto">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Item Name:</dt>
                <dd class="col-sm-9">{{ $item->item_name }}</dd>

                <dt class="col-sm-3">Description:</dt>
                <dd class="col-sm-9">{{ $item->item_description }}</dd>

                <dt class="col-sm-3">Location:</dt>
                <dd class="col-sm-9">{{ $item->item_location }}</dd>

                <dt class="col-sm-3">Latitude:</dt>
                <dd class="col-sm-9">{{ $item->latitude }}</dd>

                <dt class="col-sm-3">Longitude:</dt>
                <dd class="col-sm-9">{{ $item->longitude }}</dd>

                <dt class="col-sm-3">User:</dt>
                <dd class="col-sm-9">{{ $item->user->name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Category:</dt>
                <dd class="col-sm-9">{{ $item->category->category_name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Subcategory:</dt>
                <dd class="col-sm-9">{{ $item->subcategory->name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Available:</dt>
                <dd class="col-sm-9">{{ $item->is_available ? 'Yes' : 'No' }}</dd>

                <dt class="col-sm-3">Item Image:</dt>
                <dd class="col-sm-9">
                    @if($item->item_image)
                        @php
                            // ✅ Detect if image is Cloudinary or local
                            $imageUrl = Str::startsWith($item->item_image, ['http://', 'https://'])
                                ? $item->item_image
                                : asset('storage/' . $item->item_image);
                        @endphp

                        <img src="{{ $imageUrl }}" 
                            alt="{{ $item->item_name }}" 
                            style="max-width: 200px; border-radius: 8px;">
                    @else
                        <span class="text-muted">No Image</span>
                    @endif
                </dd>
            </dl>

            {{-- Action buttons --}}
            <a href="{{ route('auth.items.edit', $item->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>

            <form action="{{ route('auth.items.destroy', $item->id) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this item?');" 
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
