@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">My Items</h1>
        <a href="{{ route('items.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i> Add New Item
        </a>
    </div>

    {{-- ✅ AVAILABLE ITEMS --}}
    <h3 class="mb-3 text-success">Available Items</h3>
    <div class="row gy-4">
        @forelse ($availableItems as $item)
            <div class="col-md-6 col-lg-4 mb-3 pb-1">
                @include('user.partials.item-card', ['item' => $item])

                <div class="card-footer bg-transparent border-top-0 p-2 d-flex gap-2">
                    <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline-success w-100">
                        <i class="bi bi-eye"></i> View
                    </a>

                    <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-pencil"></i> Edit
                    </a>

                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="w-100 d-inline" onsubmit="return confirm('Delete this item?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted">No available items.</p>
        @endforelse
    </div>

    <hr class="my-5">

    {{-- ❌ UNAVAILABLE ITEMS --}}
    <h3 class="mb-3 text-danger">Unavailable Items</h3>
    <div class="row gy-4">
        @forelse ($unavailableItems as $item)
            <div class="col-md-6 col-lg-4 mb-3 pb-1">
                @include('user.partials.item-card', ['item' => $item])

                <div class="card-footer bg-transparent border-top-0 p-2 d-flex gap-2">
                    <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline-success w-100">
                        <i class="bi bi-eye"></i> View
                    </a>

                    <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-pencil"></i> Edit
                    </a>

                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="w-100 d-inline" onsubmit="return confirm('Delete this item?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted">No unavailable items.</p>
        @endforelse
    </div>
</div>
@endsection
