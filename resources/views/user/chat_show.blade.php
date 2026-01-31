@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('user.chat') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle me-2"></i>Back
        </a>
    </div>
    
    {{-- Header --}}
    <div class="d-flex align-items-center mb-3">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($otherUser->name) }}&background=0D8ABC&color=fff&size=40"
             class="rounded-circle me-2 shadow-sm">
        <h4 class="m-0 fw-bold">{{ $otherUser->name }}</h4>
    </div>

    {{-- Chat history --}}
    <div class="chat-history mb-4 p-3 border rounded shadow-sm"
         style="max-height:65vh; overflow-y:auto; background:#f5f7fa; border-radius:15px;">

        @foreach ($messages as $msg)
            @php
                $isMe = $msg->from_user_id == auth()->id();
            @endphp

            <div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} mb-3">

                <div style="max-width:70%;">
                    <div class="p-3 rounded-4 shadow-sm
                        {{ $isMe ? 'bg-primary text-white' : 'bg-white border' }}"
                        style="{{ $isMe ? 'border-bottom-right-radius:5px;' : 'border-bottom-left-radius:5px;' }}">

                        <div class="small fw-bold mb-1">
                            {{ $isMe ? 'You' : $otherUser->name }}
                        </div>

                        <div class="fs-6">
                            {{ $msg->chat_message }}
                        </div>

                        <div class="small text-end mt-2 opacity-75">
                            {{ $msg->created_at->format('d M · h:i A') }}
                        </div>
                    </div>
                </div>

            </div>
        @endforeach

    </div>

    {{-- Send message --}}
    <form method="POST" action="{{ route('user.chat.store', $exchangeId) }}" id="chatForm"
          class="shadow-sm p-3 border rounded-4" style="background:white;">
        @csrf
        <input type="hidden" name="to_user_id" value="{{ $otherUser->id }}">

        <div class="input-group">

            <textarea name="chat_message" class="form-control p-3 rounded-4"
                      rows="2"
                      style="resize:none; border-radius:15px;"
                      placeholder="Type a message…" required></textarea>

            <button class="btn btn-primary px-4 ms-2 rounded-4 d-flex align-items-center"
                    type="submit" style="border-radius:12px;">
                <i class="bi bi-send-fill me-1"></i> Send
            </button>
        </div>
    </form>

</div>

{{-- Keep your existing JS --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.querySelector('.chat-history');

    console.log("Subscribing to private channel: chat.{{ auth()->id() }}");

    if (!window.Echo) {
        console.error("Echo is not loaded!");
        return;
    }

    window.Echo.private('chat.{{ auth()->id() }}')
        .listen('NewChatMessage', (e) => {
            console.log("NewChatMessage event received:", e);

            if (e.message.exchangerequest_id == {{ $exchangeId }}) {
                const isMe = e.message.from_user_id == {{ auth()->id() }};
                const messageDiv = document.createElement('div');

                messageDiv.classList.add(
                    'd-flex',
                    isMe ? 'justify-content-end' : 'justify-content-start',
                    'mb-3'
                );

                messageDiv.innerHTML = `
                    <div style="max-width:70%;">
                        <div class="p-3 rounded-4 shadow-sm ${isMe ? 'bg-primary text-white' : 'bg-white border'}"
                             style="${isMe ? 'border-bottom-right-radius:5px;' : 'border-bottom-left-radius:5px;'}">

                            <div class="small fw-bold mb-1">
                                ${isMe ? 'You' : '{{ $otherUser->name }}'}
                            </div>

                            <div>${e.message.chat_message}</div>

                            <div class="small text-end mt-2 opacity-75">
                                ${new Date(e.message.created_at).toLocaleString()}
                            </div>

                        </div>
                    </div>
                `;

                chatContainer.appendChild(messageDiv);
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });

    chatContainer.scrollTop = chatContainer.scrollHeight;

    document.getElementById('chatForm').addEventListener('submit', function() {
        setTimeout(() => {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }, 100);
    });
});
</script>
@endpush
@endsection
