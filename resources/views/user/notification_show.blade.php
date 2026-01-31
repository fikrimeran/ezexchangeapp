@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('user.notification') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle me-2"></i>Back
        </a>
    </div>

    <h1 class="mb-5 text-center">Exchange Request</h1>

    <div class="card shadow rounded-4 border-0 p-4">

        <!-- From User (Item Offered) -->
        <div class="mb-4 d-flex align-items-center">
            {{-- Offered Item Image --}}
            <div class="me-3">
                @php
                    if ($req->fromItem->item_image) {
                        $fromImage = Str::startsWith($req->fromItem->item_image, ['http://', 'https://'])
                            ? $req->fromItem->item_image
                            : asset('storage/' . $req->fromItem->item_image);
                    } else {
                        $fromImage = asset('images/placeholder.png');
                    }
                @endphp
                <img src="{{ $fromImage }}"
                     alt="Offered Item Image"
                     class="rounded border"
                     style="width:90px; height:90px; object-fit:cover;">
            </div>

            {{-- Offered Item Details --}}
            <div>
                <h5 class="mb-1">From: {{ $req->fromUser->name }}</h5>
                <p class="mb-0 text-muted">
                    Item Offered: {{ $req->fromItem->item_name }}
                </p>
            </div>
        </div>

        <!-- Requested Item -->
        <div class="mb-4 d-flex align-items-center">
            {{-- Requested Item Image --}}
            <div class="me-3">
                @php
                    if ($req->toItem->item_image) {
                        $toImage = Str::startsWith($req->toItem->item_image, ['http://', 'https://'])
                            ? $req->toItem->item_image
                            : asset('storage/' . $req->toItem->item_image);
                    } else {
                        $toImage = asset('images/placeholder.png');
                    }
                @endphp
                <img src="{{ $toImage }}"
                     alt="Requested Item Image"
                     class="rounded border"
                     style="width:90px; height:90px; object-fit:cover;">
            </div>

            {{-- Requested Item Details --}}
            <div>
                <h5 class="mb-1">Requested Item</h5>
                <p class="mb-0 text-muted">
                    {{ $req->toItem->item_name }}
                </p>
            </div>
        </div>

        <!-- Status -->
        <div class="mb-4 d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-info-circle fs-2 text-warning"></i>
            </div>
            <div>
                <h5 class="mb-1">Status</h5>
                @if($req->status === 'pending')
                    <span class="badge bg-warning text-dark fs-6">Pending</span>
                @elseif($req->status === 'accepted')
                    <span class="badge bg-success fs-6">Accepted</span>
                @elseif($req->status === 'declined')
                    <span class="badge bg-danger fs-6">Declined</span>
                @endif
            </div>
        </div>

        <!-- Actions -->
        @if($req->status === 'pending')
        <div class="d-flex justify-content-center gap-3 mt-3">
            <form action="{{ route('user.notification.accept', $req) }}" method="POST">
                @csrf
                <button class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle me-2"></i>Accept Exchange
                </button>
            </form>

            <form action="{{ route('user.notification.decline', $req) }}" method="POST">
                @csrf
                <button class="btn btn-outline-danger btn-lg">
                    <i class="bi bi-x-circle me-2"></i>Decline Exchange
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection
