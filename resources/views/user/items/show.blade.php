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
                    if ($item->item_image) {
                        $imageUrl = Str::startsWith($item->item_image, ['http://', 'https://'])
                            ? $item->item_image
                            : asset('storage/' . $item->item_image);
                    } else {
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

                {{-- 💰 Estimated Price Section --}}
                <p class="text-muted mt-3 mb-0" id="estimateResult">
                    ⏳ Calculating estimated value...
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const result = document.getElementById('estimateResult');

    // ✅ Fetch estimated price
    fetch("{{ route('item.estimate', $item->id) }}")
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                result.innerHTML = "⚠️ " + data.error;
            } else {
                result.innerHTML =
                    "💰 Estimated Value: <strong>" + data.estimated_price + "</strong><br>" +
                    "(Based on " + data.prices_found + " listings)";
            }
        })
        .catch(error => {
            result.textContent = "⚠️ Error fetching estimate.";
            console.error(error);
        });
});
</script>
@endpush
