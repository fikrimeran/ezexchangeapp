@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">

                    <h2 class="mb-4 text-center fw-bold text-success">
                        <i class="bi bi-box-seam"></i> Add New Item
                    </h2>

                    <form method="POST"
                          action="{{ route('items.store') }}"
                          enctype="multipart/form-data"
                          class="row g-4">

                        @csrf

                        {{-- ✅ Image Upload with Preview --}}
                        <div class="col-12 text-center">
                            <label for="item_image" class="form-label fw-semibold d-block">Item Image</label>

                            {{-- Preview Image --}}
                            <img id="image_preview"
                                 src="{{ asset('images/placeholder.png') }}"
                                 alt="Preview"
                                 class="img-fluid rounded-3 mb-3 shadow-sm"
                                 style="max-width: 250px; display: none;">

                            <input class="form-control @error('item_image') is-invalid @enderror"
                                   type="file"
                                   name="item_image"
                                   id="item_image"
                                   accept="image/*"
                                   required
                                   onchange="previewImage(event)">
                            @error('item_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- ✅ Item Name --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Item Name</label>
                            <input type="text"
                                   name="item_name"
                                   value="{{ old('item_name') }}"
                                   class="form-control @error('item_name') is-invalid @enderror"
                                   placeholder="Enter item name"
                                   required>
                            @error('item_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- ✅ Category (moved to next row) --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category_id"
                                    id="category_id"
                                    class="form-select @error('category_id') is-invalid @enderror"
                                    required>
                                <option value="" disabled selected>Choose…</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                            {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- ✅ Subcategory --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Subcategory (optional)</label>
                            <select name="subcategory_id"
                                    id="subcategory_id"
                                    class="form-select @error('subcategory_id') is-invalid @enderror">
                                <option value="">Choose a subcategory…</option>
                            </select>
                            @error('subcategory_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- ✅ Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="item_description"
                                      rows="4"
                                      class="form-control @error('item_description') is-invalid @enderror"
                                      placeholder="Describe your item"
                                      required>{{ old('item_description') }}</textarea>
                            @error('item_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- ✅ Location --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Location</label>
                            <div class="input-group">
                                <input type="text"
                                       id="item_location"
                                       name="item_location"
                                       value="{{ old('item_location') }}"
                                       class="form-control @error('item_location') is-invalid @enderror"
                                       placeholder="Click 'Get My Location'"
                                       required>

                                <button type="button" class="btn btn-outline-success" onclick="getCurrentLocation()">
                                    <i class="bi bi-geo-alt"></i> Get My Location
                                </button>
                            </div>
                            @error('item_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Hidden lat/lng --}}
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">

                        {{-- ✅ Map Preview --}}
                        <div id="map" style="width: 100%; height: 300px; display: none; margin-top: 10px;" class="rounded-3 border shadow-sm"></div>

                        {{-- ✅ Buttons --}}
                        <div class="col-12 text-end mt-4">
                            <a href="{{ route('user.inventory') }}" class="btn btn-secondary px-4 me-2">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle"></i> Save Item
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Image Preview Script --}}
<script>
function previewImage(event) {
    const image = document.getElementById('image_preview');
    image.src = URL.createObjectURL(event.target.files[0]);
    image.style.display = 'block';
}
</script>

@push('scripts')
{{-- ✅ Keep your existing map + subcategory scripts --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKJO5SLUm8Ym2QPHtWbWy3qPonDaWYZaY&libraries=places"></script>

<script>
let map;
let marker;
let geocoder;

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                let lat = position.coords.latitude;
                let lng = position.coords.longitude;

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                initMap(lat, lng);
                reverseGeocode(lat, lng);
            },
            function(error) {
                alert("Error getting location: " + error.message);
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function initMap(lat, lng) {
    document.getElementById("map").style.display = "block";
    const latlng = { lat: lat, lng: lng };
    geocoder = new google.maps.Geocoder();

    map = new google.maps.Map(document.getElementById("map"), {
        center: latlng,
        zoom: 15
    });

    marker = new google.maps.Marker({
        position: latlng,
        map: map,
        draggable: true
    });

    marker.addListener("dragend", function() {
        const pos = marker.getPosition();
        const newLat = pos.lat();
        const newLng = pos.lng();
        document.getElementById('latitude').value = newLat;
        document.getElementById('longitude').value = newLng;
        reverseGeocode(newLat, newLng);
    });
}

function reverseGeocode(lat, lng) {
    const latlng = { lat: lat, lng: lng };

    geocoder.geocode({ location: latlng }, function(results, status) {
        if (status === "OK" && results[0]) {
            let components = results[0].address_components;
            let area = "", city = "", state = "";

            components.forEach(c => {
                if (c.types.includes("sublocality") || c.types.includes("neighborhood")) {
                    area = c.long_name;
                }
                if (c.types.includes("locality")) {
                    city = c.long_name;
                }
                if (c.types.includes("administrative_area_level_1")) {
                    state = c.long_name;
                }
            });

            let finalLocation = [area, city, state].filter(Boolean).join(", ");
            document.getElementById("item_location").value = finalLocation || (lat + ", " + lng);
        } else {
            document.getElementById("item_location").value = lat + ", " + lng;
            alert("Unable to get address. Using coordinates instead.");
        }
    });
}

// ✅ Subcategory fetcher
document.addEventListener("DOMContentLoaded", function() {
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');

    categorySelect.addEventListener('change', function() {
        let categoryId = this.value;
        subcategorySelect.innerHTML = '<option value="">Loading…</option>';

        if (categoryId) {
            fetch(`/api/subcategories/${categoryId}`)
                .then(res => res.json())
                .then(data => {
                    subcategorySelect.innerHTML = '<option value="">Choose a subcategory…</option>';
                    data.forEach(sub => {
                        subcategorySelect.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
                    });
                });
        } else {
            subcategorySelect.innerHTML = '<option value="">Choose a subcategory…</option>';
        }
    });
});
</script>
@endpush
@endsection
