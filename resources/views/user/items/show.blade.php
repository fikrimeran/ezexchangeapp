@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle me-2"></i>Back
        </a>
    </div>

    {{-- 🔹 Item display card --}}
    <div class="card shadow-sm p-4">
        <div class="row g-4 align-items-center">
            {{-- Left: Image --}}
            <div class="col-md-5 text-center">
                @php
                    // Determine correct image URL
                    if ($item->item_image) {
                        // If image path starts with "http", it's a Cloudinary URL
                        $imageUrl = Str::startsWith($item->item_image, ['http://', 'https://'])
                            ? $item->item_image
                            : asset('storage/' . $item->item_image);
                    } else {
                        // Fallback placeholder image
                        $imageUrl = asset('images/placeholder.png');
                    }
                @endphp
                <img src="{{ $imageUrl }}"
                     alt="Image of {{ $item->item_name }}"
                     class="img-fluid rounded border"
                     style="max-height: 350px; object-fit: contain; background:#f8f9fa; padding:10px;">
            </div>

            {{-- Right: Item details --}}
            <div class="col-md-7">
                <h3 class="card-title mb-3">{{ $item->item_name }}</h3>

                <p class="text-muted mb-2">
                    <strong><i class="bi bi-geo-alt-fill me-1"></i> Location:</strong>
                    {{ $item->item_location }}
                </p>

                @isset($item->category)
                    <p class="text-muted mb-2">
                        <strong><i class="bi bi-tags-fill me-1"></i> Category:</strong>
                        {{ $item->category->category_name }}
                        @if($item->subcategory)
                            → {{ $item->subcategory->name }}
                        @endif
                    </p>
                @endisset

                <p class="text-muted mb-3">
                    <strong><i class="bi bi-person-circle me-1"></i> Posted by:</strong>
                    {{ $item->user->name ?? 'You' }}
                </p>

                <p class="mb-0">
                    <strong><i class="bi bi-file-text me-1"></i> Description:</strong><br>
                    {{ $item->item_description }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
