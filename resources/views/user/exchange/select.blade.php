@extends('layouts.app')

@section('content')
<div class="container">

    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-link">&larr; Back</a>
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

            <div class="card shadow-sm border-dark small-card">
                <img src="{{ $imageUrl }}" class="card-img-top" style="height:150px; object-fit:cover; border-bottom:1px solid #ddd;">
                <div class="card-body p-3">
                    <h6 class="mb-1 fw-semibold">{{ $receiverItem->item_name }}</h6>
                    <p class="small text-muted mb-2" style="font-size:0.8rem;">
                        {{ Str::limit($receiverItem->item_description, 80) }}
                    </p>

                    <div class="small text-muted mt-1">
                        <i class="bi bi-cash-stack me-1"></i>
                        RM {{ number_format($receiverPrice, 2) }}
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 p-2" style="font-size:0.75rem;">
                    <div class="text-muted">
                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $receiverItem->item_location }}
                    </div>
                    <div class="text-muted">
                        <i class="bi bi-tags-fill me-1"></i>
                        {{ $receiverItem->category->category_name ?? 'No category' }}
                        @if($receiverItem->subcategory) → {{ $receiverItem->subcategory->name }}@endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    {{-- 🔄 LOADING SPINNER --}}
    <div id="loading" class="text-center my-4">
        <div class="spinner-border text-success" role="status"></div>
        <p class="mt-2 text-muted">Finding best exchange matches...</p>
    </div>

    {{-- 🔽 RESULTS (hidden initially) --}}
    <div id="results" style="display:none;">
        <form action="{{ route('exchange.store', $receiverItem->id) }}" method="POST">
            @csrf
            <h5 class="mb-3">Select one of your items to offer:</h5>

            <div id="my-items-container">
                @foreach($results as $index => $data)
                    @php
                        $myItem = $data['item'];
                        $recommendation = $data['recommendation'];
                        $distance = $data['distance_km'];
                        $badgeClass = $recommendation === 'Highly Recommended' ? 'bg-success' :
                                      ($recommendation === 'Recommended' ? 'bg-warning' : 'bg-secondary');
                        $borderClass = $recommendation === 'Highly Recommended' ? 'border-success' : '';
                        $imgMy = Str::startsWith($myItem->item_image, ['http','https'])
                            ? $myItem->item_image
                            : asset('storage/'.$myItem->item_image);
                    @endphp

                    <div class="card shadow-sm mb-3 {{ $borderClass }}">
                        @if($index === 0 && $recommendation === 'Highly Recommended')
                            <div class="card-header bg-success text-white fw-bold">
                                ⭐ Best Match ({{ $recommendation }})
                            </div>
                        @endif

                        <label class="d-flex gap-3 p-3">
                            <input type="radio" name="from_item_id"
                                   class="form-check-input mt-2"
                                   value="{{ $myItem->id }}" required>

                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $myItem->item_name }}</h5>

                                <span class="badge {{ $badgeClass }}">
                                    {{ $recommendation }}
                                </span>

                                <p class="small text-muted mb-1">
                                    {{ $myItem->item_description }}
                                </p>

                                <div class="small text-muted mt-1">
                                    <i class="bi bi-cash-stack me-1"></i>
                                    {{ $data['formatted_price'] }}
                                </div>

                                <div class="small text-muted mt-1">
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
            </div>

            <button type="submit" class="btn btn-success mt-2">
                <i class="bi bi-check-circle me-1"></i> Proceed Request
            </button>
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
