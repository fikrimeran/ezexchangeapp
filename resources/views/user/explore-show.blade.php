@extends('layouts.app')

@section('content')
<div class="container">

    {{-- 🔹 Top bar with back and exchange buttons --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('user.explore') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle me-2"></i>Back
        </a>

        <a href="{{ route('exchange.select', $item->id) }}" class="btn btn-primary">
            <i class="bi bi-arrow-left-right me-1"></i> Request
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
                    <strong><i class="bi bi-tags-fill me-1"></i> Category:</strong>
                    {{ $item->category->category_name ?? '-' }}
                    @if($item->subcategory)
                        → {{ $item->subcategory->name }}
                    @endif
                </p>

                <p class="text-muted mb-3">
                    <strong><i class="bi bi-person-circle me-1"></i> Posted by:</strong>
                    {{ $item->user->name ?? 'Unknown' }}
                </p>

                <p class="mb-0">
                    <strong><i class="bi bi-file-text me-1"></i> Description:</strong><br>
                    {{ $item->item_description }}
                </p>

                {{-- 📍 Location + Estimated Market Value (below description) --}}
                <p class="text-muted mt-3 mb-1" id="distance-info">
                    Calculating distance...
                </p>

                <p class="text-muted mb-0" id="estimateResult">
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

    // ✅ Auto-fetch estimated price on page load
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

    // ✅ Distance calculation (frontend geolocation)
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;

                const itemLat = {{ $item->latitude ?? 'null' }};
                const itemLng = {{ $item->longitude ?? 'null' }};

                const distanceElem = document.getElementById('distance-info');

                if (itemLat && itemLng) {
                    const distance = getDistance(userLat, userLng, itemLat, itemLng);
                    distanceElem.textContent = `📍 ${distance.toFixed(2)} km away from you`;
                } else {
                    distanceElem.textContent = "📍 Location not available for this item";
                }
            },
            function() {
                document.getElementById('distance-info').textContent =
                    "📍 Unable to get your location";
            }
        );
    } else {
        document.getElementById('distance-info').textContent =
            "📍 Geolocation not supported by your browser";
    }

    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // km
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a =
            Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) ** 2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }
    function toRad(value) {
        return value * Math.PI / 180;
    }
});
</script>
@endpush
