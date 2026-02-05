<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Pagination\Paginator;

class ExploreController extends Controller
{
    public function boot()
    {
        Paginator::useBootstrap();
    }

    public function index(Request $request)
    {
        $query = Item::with(['user', 'category', 'subcategory']) // include relationships
            ->where('is_available', true)
            ->when(Auth::check(), fn($q) => $q->where('user_id', '!=', Auth::id()));

        // 🔍 Text search (item name, category, or subcategory)
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', '%'.$search.'%')
                ->orWhereHas('category', function($q2) use ($search) {
                    $q2->where('category_name', 'like', '%'.$search.'%');
                })
                ->orWhereHas('subcategory', function($q3) use ($search) {
                    $q3->where('name', 'like', '%'.$search.'%');
                });
            });
        }

        // 🏷️ Category filter
        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }

        // ✅ Subcategory filter
        if ($subcategoryId = $request->input('subcategory')) {
            $query->where('subcategory_id', $subcategoryId);
        }

        // ✅ Nearby filter by radius (no checkbox)
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');
        $radiusKm = $request->input('radius'); // will be 10/30/50 or null

        if ($radiusKm && $userLat && $userLng) {
            $query->selectRaw("items.*, 
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance", [$userLat, $userLng, $userLat])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
        }

        $items = $query->latest()->paginate(15); // 15 items per page
        $categories = Category::orderBy('category_name')->get();

        return view('user.explore', compact('items', 'categories'));
    }

    public function show(Item $item)
    {
        $item->load(['user', 'category', 'subcategory']); // ✅ load subcategory too
        return view('user.explore-show', compact('item'));
    }

public function estimatePrice($id)
{
    $item = Item::findOrFail($id);
    $searchQuery = trim($item->item_name . ' ' . Str::limit($item->item_description, 50));

    // Step 1: Get eBay Access Token
    $tokenResponse = Http::asForm()->withHeaders([
        'Authorization' => 'Basic ' . base64_encode(env('EBAY_CLIENT_ID') . ':' . env('EBAY_CLIENT_SECRET')),
        'Content-Type' => 'application/x-www-form-urlencoded',
    ])->post('https://api.ebay.com/identity/v1/oauth2/token', [
        'grant_type' => 'client_credentials',
        'scope' => 'https://api.ebay.com/oauth/api_scope',
    ]);

    $token = $tokenResponse->json()['access_token'] ?? null;

    if (! $token) {
        return response()->json(['error' => 'Failed to get eBay token'], 500);
    }

    // Step 2: Search for the item on eBay
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->get('https://api.ebay.com/buy/browse/v1/item_summary/search', [
        'q' => $searchQuery,
        'limit' => 5,
    ]);

    $data = $response->json();
    $summaries = $data['itemSummaries'] ?? [];

    $prices = [];
    foreach ($summaries as $entry) {
        if (isset($entry['price']['value'])) {
            $prices[] = (float) $entry['price']['value'];
        }
    }

    if (count($prices) === 0) {
        return response()->json(['error' => 'No results found'], 404);
    }

    // Step 3: Calculate average price (and convert to RM)
    $average = array_sum($prices) / count($prices);
    $estimatedMYR = $average * 4.7; // USD → RM conversion rate

    return response()->json([
        'search_query' => $searchQuery,
        'estimated_price' => 'RM ' . number_format($estimatedMYR, 2),
        'prices_found' => count($prices),
    ]);
}


}
