<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Subcategory; // ✅ Import the model

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ Get subcategories dynamically for a given category
Route::get('/subcategories/{category}', function ($categoryId) {
    return Subcategory::where('category_id', $categoryId)
        ->orderBy('name')
        ->get(['id', 'name']); // only return id + name
});
