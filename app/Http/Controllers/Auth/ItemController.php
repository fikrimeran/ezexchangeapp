<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Subcategory;

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

    // Show edit form (Admin only)
    public function edit(Item $item)
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();

        return view('auth.items.edit', compact('item', 'categories', 'subcategories'));
    }

    // Update category and subcategory (Admin only)
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        $item->update([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
        ]);

        return redirect()->route('auth.items.show', $item->id)
                         ->with('success', 'Item updated successfully.');
    }
}
