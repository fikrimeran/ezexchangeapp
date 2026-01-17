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

<div class="card shadow-sm border-dark">
    <img src="{{ $imageUrl }}"
         class="card-img-top"
         alt="Image of {{ $item->item_name }}"
         style="height: 200px; object-fit: cover;">

    <div class="card-body p-4">
        <h5 class="card-title">{{ $item->item_name }}</h5>
        <p class="card-text">{{ $item->item_description }}</p>
    </div>

    <div class="card-footer bg-white border-top-0 p-3">
        <small class="text-muted d-block">
            <i class="bi bi-geo-alt-fill me-1"></i> {{ $item->item_location }}
        </small>

        @isset($item->category)
            <small class="text-muted d-block">
                <i class="bi bi-tags-fill me-1"></i>
                {{ $item->category->category_name }}
                @if($item->subcategory)
                    → {{ $item->subcategory->name }}
                @endif
            </small>
        @endisset
    </div>
</div>
