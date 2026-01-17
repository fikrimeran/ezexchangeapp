<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of subcategories.
     */
    public function index()
    {
        // Load subcategories with their parent category
        $subcategories = Subcategory::with('category')
            ->orderBy('created_at', 'asc')
            ->paginate(10); // ✅ 10 per page

        return view('auth.subcategories.index', compact('subcategories'));
    }

    /**
     * Show the form for creating a new subcategory.
     */
    public function create()
    {
        // Get all categories for the dropdown
        $categories = Category::orderBy('category_name')->get();

        return view('auth.subcategories.create', compact('categories'));
    }

    /**
     * Store a newly created subcategory in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        Subcategory::create([
            'name'        => $request->name,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('auth.subcategories.index')
                         ->with('success', 'Subcategory created successfully.');
    }

    /**
     * Display the specified subcategory.
     */
    public function show(Subcategory $subcategory)
    {
        $subcategory->load('category'); // eager load category
        return view('auth.subcategories.show', compact('subcategory'));
    }

    /**
     * Show the form for editing the specified subcategory.
     */
    public function edit(Subcategory $subcategory)
    {
        $categories = Category::orderBy('category_name')->get();
        return view('auth.subcategories.edit', compact('subcategory', 'categories'));
    }

    /**
     * Update the specified subcategory in storage.
     */
    public function update(Request $request, Subcategory $subcategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory->update([
            'name'        => $request->name,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('auth.subcategories.index')
                         ->with('success', 'Subcategory updated successfully.');
    }

    /**
     * Remove the specified subcategory from storage.
     */
    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();

        return redirect()->route('auth.subcategories.index')
                         ->with('success', 'Subcategory deleted successfully.');
    }
}
