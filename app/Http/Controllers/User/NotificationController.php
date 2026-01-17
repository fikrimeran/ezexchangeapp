<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRequest;
use App\Models\Chat;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = ExchangeRequest::with(['fromUser', 'fromItem', 'toItem'])
            ->where('to_user_id', auth()->id())
            ->latest()
            ->get();

        return view('user.notification', compact('notifications'));
    }

    public function show(ExchangeRequest $exchangeRequest)
    {
        abort_unless($exchangeRequest->to_user_id === auth()->id(), 403);

        return view('user.notification_show', [
            'req' => $exchangeRequest->load(['fromUser', 'fromItem', 'toItem']),
        ]);
    }

    public function accept(ExchangeRequest $exchangeRequest)
    {
        $this->updateStatus($exchangeRequest, 'accepted');

        // Automatically send a chat message to the requester
        Chat::create([
            'exchangerequest_id' => $exchangeRequest->id,
            'from_user_id'       => auth()->id(), // acceptor
            'to_user_id'         => $exchangeRequest->from_user_id,
            'chat_message'       => "Hi {$exchangeRequest->fromUser->name}, "
                                   ."I’ve accepted your exchange request for "
                                   ."{$exchangeRequest->toItem->item_name}. "
                                   ."Let’s arrange the swap!",
        ]);

        return back()->with('success', 'Exchange accepted and the requester has been notified.');
    }

    public function decline(ExchangeRequest $exchangeRequest)
    {
        $this->updateStatus($exchangeRequest, 'declined');

        // Automatically send a chat message to the requester
        Chat::create([
            'exchangerequest_id' => $exchangeRequest->id,
            'from_user_id'       => auth()->id(), // decliner
            'to_user_id'         => $exchangeRequest->from_user_id,
            'chat_message'       => "Hi {$exchangeRequest->fromUser->name}, "
                                   ."I’m sorry, but I have to decline your "
                                   ."exchange request for "
                                   ."{$exchangeRequest->toItem->item_name}. "
                                   ."Thanks for understanding.",
        ]);

        return back()->with('success', 'Exchange declined and the requester has been notified.');
    }

    private function updateStatus(ExchangeRequest $exchangeRequest, string $status)
    {
        abort_unless($exchangeRequest->to_user_id === auth()->id(), 403);

        if ($exchangeRequest->status !== 'pending') {
            abort(409, 'Request already processed.');
        }

        $exchangeRequest->update(['status' => $status]);
    }
}
