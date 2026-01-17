@extends('layouts.app')

@section('content')

<br><br>
<div class="container-fluid d-flex justify-content-center align-items-center">
    <div class="row g-4 align-items-stretch justify-content-center" style="max-width: 1200px; width: 100%;">

        {{-- LEFT COLUMN: Dashboard --}}
        <div class="col-lg-4 d-flex flex-column">

            {{-- Welcome Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white overflow-hidden flex-shrink-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1">
                        {{ __('Welcome Back, ') . Auth::user()->name }}
                    </h4>
                    <p class="mb-0 opacity-100">Overview of your EZExchange activity.</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="row g-3 flex-grow-1">

                {{-- Inventory --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-primary h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3"
                                 style="width: 50px; height: 50px; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-box-seam fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Your Inventory</h6>
                                <h3 class="fw-bold text-dark mb-0">{{ $totalInventoryItems }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Explore Items --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-warning h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3"
                                 style="width: 50px; height: 50px; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-globe2 fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Explore Items</h6>
                                <h3 class="fw-bold text-dark mb-0">{{ $totalExploreItems }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Exchange History --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-info h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-3 me-3"
                                style="width: 50px; height: 50px; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-arrow-repeat fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Exchange History</h6>
                                <h3 class="fw-bold text-dark mb-0">{{ $totalExchanges }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="col-12 mt-auto">
                    <p class="text-muted small fst-italic ps-2 mb-0 text-center">
                        You have <strong>{{ $totalInventoryItems }}</strong> item(s) and can explore 
                        <strong>{{ $totalExploreItems }}</strong> item(s). <br>
                        Total exchanges completed: <strong>{{ $totalExchanges }}</strong>.
                    </p>
                </div>

            </div>
        </div>

        {{-- RIGHT COLUMN: MAP --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 pt-3 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Nearby Items On Map
                    </h5>
                </div><br>
                <div class="card-body p-0 position-relative h-100">
                    <div id="map" style="height: 100%; min-height: 500px; width: 100%;"></div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    var map = L.map('map');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var redIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
        iconSize: [35, 35],
        iconAnchor: [17, 34],
        popupAnchor: [0, -30]
    });

    var markers = L.markerClusterGroup(); // default cluster style
    var bounds = [];
    var nearbyItems = @json($nearbyItems);

    nearbyItems.forEach(function(item) {
        if(item.latitude && item.longitude){
            var latlng = [item.latitude, item.longitude];
            markers.addLayer(
                L.marker(latlng, { icon: redIcon })
                    .bindPopup('<b>' + item.item_name + '</b>')
            );
            bounds.push(latlng);
        }
    });

    map.addLayer(markers);

    // User location
    map.locate({ enableHighAccuracy: true });

    map.on('locationfound', function(e) {
        L.circleMarker(e.latlng, {
            radius: 8,
            color: 'blue',
            fillColor: '#30f',
            fillOpacity: 0.6
        }).addTo(map).bindPopup("You are here");

        bounds.push([e.latitude, e.longitude]);

        if (bounds.length > 0) map.fitBounds(bounds, { padding: [40, 40] });
    });

    map.on('locationerror', function() {
        if (bounds.length > 0) map.fitBounds(bounds, { padding: [40, 40] });
        else map.setView([3.0738, 101.6069], 15);
    });

    setTimeout(() => map.invalidateSize(), 200);

});
</script>

@endsection
