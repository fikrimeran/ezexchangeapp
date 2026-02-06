@extends('layouts.app')

@section('content')

<style>
    .pagination .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>

<div class="container">

    {{-- 🔝 Top bar: Back button + Proceed button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('user.explore') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle me-2"></i>Back
        </a>

        <form action="{{ route('exchange.store', $receiverItem->id) }}" method="POST" class="mb-0">
            @csrf
            <input type="hidden" name="from_item_id" id="top_from_item_id">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i> Proceed Request
            </button>
        </form>
    </div>

    <h2 class="mb-4">Send Exchange Request</h2>

    <h5 class="text-muted">You want:</h5>
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4 mb-4">
            @php
                $imageUrl = $receiverItem->item_image
                    ? (Str::startsWith($receiverItem->item_image, ['http://','https://'])
                        ? $receiverItem->item_image
                        : asset('storage/' . $receiverItem->item_image))
                    : asset('images/placeholder.png');
            @endphp

            <div class="card shadow-sm border-dark">
                <img src="{{ $imageUrl }}" class="card-img-top" style="height:150px; object-fit:cover;">
                <div class="card-body p-3">
                    <h6 class="mb-1 fw-semibold">{{ $receiverItem->item_name }}</h6>
                    <p class="small text-muted mb-2">
                        {{ Str::limit($receiverItem->item_description, 80) }}
                    </p>
                    <div class="small text-muted">
                        <i class="bi bi-cash-stack me-1"></i>
                        RM {{ number_format($receiverPrice, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    {{-- Loading --}}
    <div id="loading" class="text-center my-4">
        <div class="spinner-border text-success"></div>
        <p class="mt-2 text-muted">Finding best exchange matches...</p>
    </div>

    {{-- Results --}}
    <div id="results" style="display:none;">
        @php
            use Illuminate\Pagination\LengthAwarePaginator;

            $perPage = 5;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            $currentItems = collect($results)
                ->slice(($currentPage - 1) * $perPage, $perPage)
                ->values();

            $paginatedResults = new LengthAwarePaginator(
                $currentItems,
                count($results),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        @endphp

        <form action="{{ route('exchange.store', $receiverItem->id) }}" method="POST">
            @csrf

            <h5 class="mb-3">Select one of your items to offer:</h5>

            @foreach($paginatedResults as $index => $data)
                @php
                    $myItem = $data['item'];
                    $recommendation = $data['recommendation'];
                    $distance = $data['distance_km'];

                    $badgeClass = $recommendation === 'Highly Recommended' ? 'bg-success'
                                  : ($recommendation === 'Recommended' ? 'bg-warning' : 'bg-secondary');

                    $borderClass = $recommendation === 'Highly Recommended' ? 'border-success' : '';

                    $imgMy = Str::startsWith($myItem->item_image, ['http','https'])
                        ? $myItem->item_image
                        : asset('storage/'.$myItem->item_image);
                @endphp

                <div class="card shadow-sm mb-3 {{ $borderClass }}">
                    @if($currentPage === 1 && $index === 0 && $recommendation === 'Highly Recommended')
                        <div class="card-header bg-success text-white fw-bold">
                            ⭐ Best Match ({{ $recommendation }})
                        </div>
                    @endif

                    <label class="d-flex gap-3 p-3">
                        <input type="radio" name="from_item_id"
                               class="form-check-input mt-2"
                               value="{{ $myItem->id }}" required
                               onchange="document.getElementById('top_from_item_id').value=this.value;">

                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $myItem->item_name }}</h5>

                            <span class="badge {{ $badgeClass }}">
                                {{ $recommendation }}
                            </span>

                            <p class="small text-muted mb-1">
                                {{ Str::limit($myItem->item_description, 120) }}
                            </p>

                            <div class="small text-muted">
                                <i class="bi bi-cash-stack me-1"></i>
                                {{ $data['formatted_price'] }}
                            </div>

                            <div class="small text-muted">
                                <i class="bi bi-geo-alt-fill me-1"></i>
                                Distance: {{ $distance }} km
                            </div>

                            <div class="small text-muted">
                                <i class="bi bi-tags-fill me-1"></i>
                                {{ $myItem->category->category_name ?? '' }}
                                @if($myItem->subcategory) → {{ $myItem->subcategory->name }}@endif
                            </div>
                        </div>

                        <img src="{{ $imgMy }}" class="rounded"
                             style="width:90px;height:90px;object-fit:cover;">
                    </label>
                </div>
            @endforeach

            {{-- Pagination info --}}
            @if($paginatedResults->total() > 0)
                <p class="mb-2 text-muted text-center">
                    Showing {{ $paginatedResults->firstItem() }}
                    to {{ $paginatedResults->lastItem() }}
                    of {{ $paginatedResults->total() }} items —
                    Page {{ $paginatedResults->currentPage() }}
                    of {{ $paginatedResults->lastPage() }}
                </p>
            @endif

            {{-- Pagination --}}
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-3">

                    @if($paginatedResults->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginatedResults->previousPageUrl() }}">
                                Previous
                            </a>
                        </li>
                    @endif

                    @foreach ($paginatedResults->getUrlRange(1, $paginatedResults->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $paginatedResults->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    @if($paginatedResults->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginatedResults->nextPageUrl() }}">
                                Next
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>

        </form>
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('results').style.display = 'block';
    });
</script>
@endsection
