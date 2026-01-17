<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    // Display list of all items with pagination
    public function index()
    {
        $items = Item::with(['user', 'category', 'subcategory'])
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        return view('auth.items.index', compact('items'));
    }

    // Show details of a specific item
    public function show(Item $item)
    {
        // Load relations if not eager loaded
        $item->load(['user', 'category', 'subcategory']);
        
        return view('auth.items.show', compact('item'));
    }

    // Delete an item
    public function destroy(Item $item)
    {
        // Optional: Delete image file if you handle uploads here

        $item->delete();

        return redirect()->route('auth.items.index')
                         ->with('success', 'Item deleted successfully.');
    }
}
