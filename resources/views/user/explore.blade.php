@extends('layouts.app')

@section('content')

<style>
    .pagination .page-link {
        padding: 0.25rem 0.5rem;  /* smaller padding */
        font-size: 0.875rem;      /* smaller font */
    }

    .pagination .page-item.disabled .page-link {
        opacity: 0.5;             /* dim disabled buttons */
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;  /* optional: keep Bootstrap primary color */
        border-color: #0d6efd;
    }
</style>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Explore Items</h1>
    </div>

    {{-- 🔍 Search + Category + Subcategory + Nearby filter --}}
    <form method="GET" action="{{ route('user.explore') }}" class="mb-4">
        <div class="card shadow-sm p-3">
            <div class="row g-2 align-items-center">

                {{-- Search box --}}
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by item name…"
                            value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Category dropdown --}}
                <div class="col-md-3">
                    <select name="category" id="category_filter" class="form-select">
                        <option value="">All categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string) $cat->id === request('category') ? 'selected' : '' }}>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Subcategory dropdown --}}
                <div class="col-md-3">
                    <select name="subcategory" id="subcategory_filter" class="form-select">
                        <option value="">All subcategories</option>
                    </select>
                </div>

                {{-- Nearby radius dropdown --}}
                <div class="col-md-2">
                    <select name="radius" id="radius_filter" class="form-select">
                        <option value="">Nearby</option>
                        <option value="10" {{ request('radius') == 10 ? 'selected' : '' }}>10 km</option>
                        <option value="30" {{ request('radius') == 30 ? 'selected' : '' }}>30 km</option>
                        <option value="50" {{ request('radius') == 50 ? 'selected' : '' }}>50 km</option>
                    </select>
                </div>

                {{-- Submit button --}}
                <div class="col-12 col-md-2 col-lg-1 mt-2 mt-md-0 d-grid">
                    <br><button class="btn btn-primary w-100" type="submit">
                        <i class="bi bi-filter-circle"></i> Filter
                    </button>
                </div>

                {{-- Hidden lat/lng --}}
                <input type="hidden" name="lat" id="user_lat" value="{{ request('lat') }}">
                <input type="hidden" name="lng" id="user_lng" value="{{ request('lng') }}">
            </div>
        </div>
    </form>

    {{-- Items --}}
    <div class="row gy-4">
        @forelse ($items as $item)
            <div class="col-md-6 col-lg-4 mb-3 pb-1">
                {{-- Reusable card --}}
                @include('user.partials.item-card', ['item' => $item])

                {{-- Action buttons --}}
                <div class="card-footer bg-transparent border-top-0 p-2 d-flex gap-2">
                    <a href="{{ route('user.explore.show', $item->id) }}"
                       class="btn btn-outline-success btn-sm w-100">
                        <i class="bi bi-eye"></i> View
                    </a>

                    <a href="{{ route('exchange.select', $item->id) }}"
                       class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-arrow-left-right me-1"></i>
                        Request
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">No items available right now. Check back later!</p>
            </div>
        @endforelse
    </div>
    
    <div class="mt-4 text-center">
        {{-- Page indicator --}}
        @if($items->total() > 0)
            <p class="mb-2 text-muted">
                Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} items —
                Page {{ $items->currentPage() }} of {{ $items->lastPage() }}
            </p>
        @endif

        {{-- Pagination links --}}
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                @if($items->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">Previous</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $items->previousPageUrl() }}">Previous</a></li>
                @endif

                @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $items->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                @if($items->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $items->nextPageUrl() }}">Next</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">Next</span></li>
                @endif
            </ul>
        </nav>
    </div>
    
</div><br>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const latField = document.getElementById("user_lat");
    const lngField = document.getElementById("user_lng");
    const radiusSelect = document.getElementById("radius_filter");

    // Fetch user location when nearby radius is selected
    radiusSelect.addEventListener("change", function() {
        if (this.value && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    latField.value = pos.coords.latitude;
                    lngField.value = pos.coords.longitude;
                },
                err => {
                    alert("Could not get your location: " + err.message);
                    this.value = ""; // reset if location fails
                }
            );
        }
    });

    // Subcategory dynamic loading
    const categoryFilter = document.getElementById('category_filter');
    const subcategoryFilter = document.getElementById('subcategory_filter');

    function loadSubcategories(categoryId, preselected = "") {
        subcategoryFilter.innerHTML = '<option value="">Loading…</option>';

        if (categoryId) {
            fetch(`/api/subcategories/${categoryId}`)
                .then(res => res.json())
                .then(data => {
                    subcategoryFilter.innerHTML = '<option value="">All subcategories</option>';
                    data.forEach(sub => {
                        let selected = (sub.id == preselected) ? "selected" : "";
                        subcategoryFilter.innerHTML += `<option value="${sub.id}" ${selected}>${sub.name}</option>`;
                    });
                });
        } else {
            subcategoryFilter.innerHTML = '<option value="">All subcategories</option>';
        }
    }

    // When category changes → fetch subcategories
    categoryFilter.addEventListener('change', function() {
        loadSubcategories(this.value);
    });

    // Auto-load subcategories if already selected
    const preselectedCategory = categoryFilter.value;
    const preselectedSubcategory = "{{ request('subcategory') }}";
    if (preselectedCategory) {
        loadSubcategories(preselectedCategory, preselectedSubcategory);
    }
});
</script>
@endpush
