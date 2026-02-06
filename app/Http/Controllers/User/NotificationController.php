<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRequest;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /** Notification list */
    public function index()
    {
        $notifications = ExchangeRequest::with(['fromUser', 'fromItem', 'toItem'])
            ->where('to_user_id', auth()->id())
            ->latest()
            ->get();

        return view('user.notification', compact('notifications'));
    }

    /** View single exchange request */
    public function show(ExchangeRequest $exchangeRequest)
    {
        abort_unless($exchangeRequest->to_user_id === auth()->id(), 403);

        return view('user.notification_show', [
            'req' => $exchangeRequest->load(['fromUser', 'fromItem', 'toItem']),
        ]);
    }

    /** Accept exchange request */
    public function accept(ExchangeRequest $exchangeRequest)
    {
        DB::transaction(function () use ($exchangeRequest) {

            // Authorization + status check
            $this->updateStatus($exchangeRequest, 'accepted');

            // Prevent accepting unavailable items
            if (
                $exchangeRequest->fromItem->is_available == 0 ||
                $exchangeRequest->toItem->is_available == 0
            ) {
                abort(409, 'One or both items are no longer available.');
            }

            // Mark both items as unavailable
            $exchangeRequest->fromItem->update(['is_available' => 0]);
            $exchangeRequest->toItem->update(['is_available' => 0]);

            // Send chat notification to accepted user
            Chat::create([
                'exchangerequest_id' => $exchangeRequest->id,
                'from_user_id'       => auth()->id(), // acceptor
                'to_user_id'         => $exchangeRequest->from_user_id,
                'chat_message'       =>
                    "Hi {$exchangeRequest->fromUser->name}, "
                    ."I’ve accepted your exchange request for "
                    ."{$exchangeRequest->toItem->item_name}. "
                    ."Let’s arrange the swap!",
            ]);

            // Automatically decline other pending requests for the same item
            $otherRequests = ExchangeRequest::where('to_user_id', auth()->id())
                ->where('to_item_id', $exchangeRequest->to_item_id)
                ->where('status', 'pending')
                ->where('id', '!=', $exchangeRequest->id)
                ->get();

            foreach ($otherRequests as $req) {
                $this->updateStatus($req, 'declined');

                // Optional: send chat notification to other users
                Chat::create([
                    'exchangerequest_id' => $req->id,
                    'from_user_id'       => auth()->id(), // owner who declined
                    'to_user_id'         => $req->from_user_id,
                    'chat_message'       =>
                        "Hi {$req->fromUser->name}, "
                        ."your exchange request for "
                        ."{$req->toItem->item_name} has been declined "
                        ."because the item has already been traded.",
                ]);
            }
        });

        return back()->with(
            'success',
            'Exchange accepted. Other requests for this item have been declined automatically.'
        );
    }

    /** Decline exchange request */
    public function decline(ExchangeRequest $exchangeRequest)
    {
        DB::transaction(function () use ($exchangeRequest) {

            // Authorization + status check
            $this->updateStatus($exchangeRequest, 'declined');

            // Send chat notification
            Chat::create([
                'exchangerequest_id' => $exchangeRequest->id,
                'from_user_id'       => auth()->id(), // decliner
                'to_user_id'         => $exchangeRequest->from_user_id,
                'chat_message'       =>
                    "Hi {$exchangeRequest->fromUser->name}, "
                    ."I’m sorry, but I have to decline your "
                    ."exchange request for "
                    ."{$exchangeRequest->toItem->item_name}. "
                    ."Thanks for understanding.",
            ]);
        });

        return back()->with(
            'success',
            'Exchange declined and a chat has send to the requester.'
        );
    }

    /** Shared status update logic */
    private function updateStatus(ExchangeRequest $exchangeRequest, string $status)
    {
        abort_unless($exchangeRequest->to_user_id === auth()->id(), 403);

        if ($exchangeRequest->status !== 'pending') {
            abort(409, 'Request already processed.');
        }

        $exchangeRequest->update(['status' => $status]);
    }
}
