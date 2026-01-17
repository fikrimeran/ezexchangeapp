<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('id', 'asc')->paginate(10); // ✅ 10 per page
        return view('auth.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('auth.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        Category::create([
            'category_name' => $request->category_name,
        ]);

        return redirect()->route('auth.categories.index')
                         ->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        return view('auth.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('auth.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $category->update([
            'category_name' => $request->category_name,
        ]);

        return redirect()->route('auth.categories.index')
                         ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('auth.categories.index')
                         ->with('success', 'Category deleted successfully.');
    }
}
