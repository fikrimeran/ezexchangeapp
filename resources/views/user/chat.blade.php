@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Messages</h1>

    @forelse ($allChats as $chat)
        @php
            $exchange = $chat->exchangeRequest;

            // Item names
            $itemA = optional($exchange->fromItem)->item_name ?? 'Unknown Item';
            $itemB = optional($exchange->toItem)->item_name ?? 'Unknown Item';

            // Determine chat partner
            $isSender = $chat->from_user_id == auth()->id();
            $partner = $isSender ? $chat->toUser : $chat->fromUser;
            $partnerName = optional($partner)->name ?? 'Unknown User';
        @endphp

        <div class="card mb-3 shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        {{-- Partner name --}}
                        <h5 class="card-title mb-1">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ $partnerName }}
                        </h5>

                        {{-- Item exchange: A <-> B with bootstrap icon --}}
                        <p class="text-primary fw-bold mb-2">
                            {{ $itemA }}
                            <i class="bi bi-arrow-repeat mx-1"></i>
                            {{ $itemB }}
                        </p>

                        {{-- Last message --}}
                        <p class="card-text mb-2">{{ $chat->chat_message }}</p>

                        {{-- Time --}}
                        <small class="text-muted">
                            {{ $chat->created_at->format('d M Y · h:i A') }}
                        </small>
                    </div>

                    {{-- View button --}}
                    <a href="{{ route('user.chat.show', $chat->exchangerequest_id) }}"
                       class="btn btn-sm btn-outline-primary">
                        View
                    </a>

                </div>

            </div>
        </div>

    @empty
        <div class="alert alert-info">No messages yet.</div>
    @endforelse
</div>
@endsection
