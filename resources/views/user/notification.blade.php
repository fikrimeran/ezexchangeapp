@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Notifications</h1>

    @forelse ($notifications as $req)
        <div class="card mb-3 
            {{ $req->status === 'pending' ? 'border-warning shadow-sm' : 'opacity-75' }}">
            
            <div class="card-body">
                {{-- NEW / pending badge --}}
                @if($req->status === 'pending')
                    <span class="badge bg-warning text-dark mb-2">ACTION REQUIRED</span>
                @endif

                <p class="mb-2">
                    <strong>{{ $req->fromUser->name }}</strong>
                    wants to trade
                    <strong>{{ $req->fromItem->item_name }}</strong>
                    for your
                    <strong>{{ $req->toItem->item_name }}</strong>.
                </p>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('user.notification.show', $req) }}"
                       class="btn btn-outline-secondary btn-sm">
                        View
                    </a>

                    @if ($req->status === 'pending')
                        <form action="{{ route('user.notification.accept', $req) }}" method="POST">
                            @csrf
                            <button class="btn btn-primary btn-sm">Proceed Exchange</button>
                        </form>

                        <form action="{{ route('user.notification.decline', $req) }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-danger btn-sm">Decline Exchange</button>
                        </form>
                    @endif
                </div>

                <small class="text-muted d-block mt-2">
                    {{ ucfirst($req->status) }} • {{ $req->created_at->diffForHumans() }}
                </small>
            </div>
        </div>
    @empty
        <div class="alert alert-info">You have no exchange requests right now.</div>
    @endforelse
</div>
@endsection
