@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle me-2"></i>Back
        </a>
    </div>
    <h1 class="mb-5 text-center">Exchange Request</h1>

    <div class="card shadow rounded-4 border-0 p-4">
        <!-- From User -->
        <div class="mb-4 d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-person-circle fs-2 text-primary"></i>
            </div>
            <div>
                <h5 class="mb-1">From: {{ $req->fromUser->name }}</h5>
                <p class="mb-0 text-muted">Item Offered: {{ $req->fromItem->item_name }}</p>
            </div>
        </div>

        <!-- Requested Item -->
        <div class="mb-4 d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-gift fs-2 text-success"></i>
            </div>
            <div>
                <h5 class="mb-1">Requested Item</h5>
                <p class="mb-0 text-muted">{{ $req->toItem->item_name }}</p>
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
