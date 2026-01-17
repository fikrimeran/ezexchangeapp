<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;
use App\Models\ExchangeRequest;

class HomeController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Total items in user's inventory
        $totalInventoryItems = Item::where('user_id', $userId)->count();

        // Total items available to explore from other users
        $totalExploreItems = Item::where('user_id', '!=', $userId)
            ->where('is_available', 1)
            ->count();

        // Total completed exchanges involving this user
        $totalExchanges = ExchangeRequest::where(function($q) use ($userId) {
                $q->where('from_user_id', $userId)
                  ->orWhere('to_user_id', $userId);
            })
            ->where('status', 'accepted') // adjust if your status uses a different value
            ->count();

        // Fetch other users' available items with lat/lng
        $nearbyItems = Item::where('user_id', '!=', $userId)
            ->where('is_available', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id','item_name','latitude','longitude']);

        return view('user.home', compact(
            'totalInventoryItems',
            'totalExploreItems',
            'totalExchanges',
            'nearbyItems'
        ));
    }
}
