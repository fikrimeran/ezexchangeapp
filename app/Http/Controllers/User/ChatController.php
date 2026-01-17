<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use App\Events\NewChatMessage;

class ChatController extends Controller
{
    /** List page */
    public function index()
    {
        $userId = auth()->id();

        // Get all latest chats per exchange request, regardless of sender/receiver
        $allChats = Chat::with([
                'fromUser',
                'toUser',
                'exchangeRequest.fromItem',
                'exchangeRequest.toItem'
            ])
            ->whereIn('id', function ($q) use ($userId) {
                $q->selectRaw('MAX(id)')
                ->from('chats')
                ->where(function ($query) use ($userId) {
                    $query->where('from_user_id', $userId)
                            ->orWhere('to_user_id', $userId);
                })
                ->groupBy('exchangerequest_id');
            })
            ->orderByDesc('created_at')
            ->get();

        return view('user.chat', compact('allChats'));
    }


    /** Single conversation */
    public function show($exchangeId)
    {
        $userId = auth()->id();

        $messages = Chat::with(['fromUser', 'toUser'])
            ->where('exchangerequest_id', $exchangeId)
            ->where(function ($q) use ($userId) {
                $q->where('from_user_id', $userId)
                  ->orWhere('to_user_id', $userId);
            })
            ->orderBy('created_at')
            ->get();

        abort_if($messages->isEmpty(), 404);

        $otherUser = $messages->first()->from_user_id == $userId
            ? $messages->first()->toUser
            : $messages->first()->fromUser;

        return view('user.chat_show', compact('messages', 'otherUser', 'exchangeId'));
    }

    /** Save new message */
    public function store(Request $request, $exchangeId)
    {
        $request->validate([
            'chat_message' => 'required|string|max:1000',
            'to_user_id'   => 'required|exists:users,id',
        ]);

        $chat = Chat::create([
            'exchangerequest_id' => $exchangeId,
            'from_user_id'       => auth()->id(),
            'to_user_id'         => $request->to_user_id,
            'chat_message'       => $request->chat_message,
        ]);

        event(new NewChatMessage($chat));

        return back();
    }
}
