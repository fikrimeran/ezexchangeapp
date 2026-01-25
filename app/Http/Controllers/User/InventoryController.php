<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Item;
use App\Models\Category;

class InventoryController extends Controller
{
    /* -------------- LIST -------------- */
    public function index()
    {
        $items = Item::with('category')
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->get();

        $availableItems   = $items->where('is_available', true);
        $unavailableItems = $items->where('is_available', false);

        return view('user.inventory', compact('availableItems', 'unavailableItems'));
    }

    /* -------------- FORM -------------- */
    public function create()
    {
        $categories = Category::orderBy('category_name')->get();
        return view('user.items.create', compact('categories'));
    }

    /* -------------- STORE ------------- */
    public function store(Request $request)
    {
        $request->validate([
            'item_name'        => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'subcategory_id'   => 'nullable|exists:subcategories,id',
            'item_description' => 'required|string',
            'item_location'    => 'required|string|max:255',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'item_image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // 🟢 Upload to Cloudinary
        try {
            $uploadedFileUrl = Cloudinary::upload(
                $request->file('item_image')->getRealPath(),
                ['folder' => 'ezexchange/items']
            )->getSecurePath();
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload failed (store): ' . $e->getMessage());
            return back()->withErrors(['item_image' => 'Image upload failed.'])->withInput();
        }

        // ✅ Save item to database
        $item = Item::create([
            'user_id'         => Auth::id(),
            'category_id'     => $request->category_id,
            'subcategory_id'  => $request->subcategory_id,
            'item_name'       => $request->item_name,
            'item_description'=> $request->item_description,
            'item_location'   => $request->item_location,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
            'item_image'      => $uploadedFileUrl,
        ]);

        // ✅ Prepare Telegram message
        $categoryName = $item->category ? $item->category->category_name : 'Unknown';
        $message = "📢 <b>New Item Added!</b>\n\n"
                 . "📝 <b>Name:</b> {$item->item_name}\n"
                 . "📂 <b>Category:</b> {$categoryName}\n"
                 . "📍 <b>Location:</b> {$item->item_location}\n"
                 . "ℹ️ <b>Description:</b> {$item->item_description}\n"
                 . "🔗 <a href='" . url("/items/{$item->id}") . "'>View Item</a>";

        $this->sendTelegramMessage($message);

        return redirect()
               ->route('user.inventory')
               ->with('success', 'Item added successfully!');
    }

    /**
     * Helper to send Telegram message
     */
    private function sendTelegramMessage($message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        return \Illuminate\Support\Facades\Http::get($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ]);
    }

    /* -------------- SHOW -------------- */
    public function show(Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to view this item.');
        }

        $item->load('category', 'subcategory', 'user');

        return view('user.items.show', compact('item'));
    }

    /* -------------- EDIT -------------- */
    public function edit(Item $item)
    {
        if ($item->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $categories = Category::orderBy('category_name')->get();

        return view('user.items.edit', compact('item', 'categories'));
    }

    /* -------------- UPDATE -------------- */
    public function update(Request $request, Item $item)
    {
        if ($item->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'item_name'        => 'required|string|max:255',
            'item_description' => 'required|string',
            'item_location'    => 'required|string|max:255',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'category_id'      => 'required|exists:categories,id',
            'subcategory_id'   => 'nullable|exists:subcategories,id',
            'is_available'     => 'required|boolean',
            'item_image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('item_image')) {
            // 🟢 Delete old image (Cloudinary or local)
            if ($item->item_image) {
                if (str_contains($item->item_image, 'cloudinary.com')) {
                    try {
                        $publicId = basename(parse_url($item->item_image, PHP_URL_PATH));
                        $publicId = preg_replace('/\.[^.]+$/', '', $publicId);
                        Cloudinary::destroy('ezexchange/items/' . $publicId);
                    } catch (\Exception $e) {
                        \Log::error('Cloudinary deletion failed (update): ' . $e->getMessage());
                    }
                } else {
                    Storage::disk('public')->delete($item->item_image);
                }
            }

            // 🟢 Upload new image
            try {
                $uploadedFileUrl = Cloudinary::upload(
                    $request->file('item_image')->getRealPath(),
                    ['folder' => 'ezexchange/items']
                )->getSecurePath();

                $validated['item_image'] = $uploadedFileUrl;
            } catch (\Exception $e) {
                \Log::error('Cloudinary upload failed (update): ' . $e->getMessage());
            }
        }

        $item->update($validated);

        return redirect()
            ->route('user.inventory')
            ->with('success', 'Item updated successfully!');
    }

    /* -------------- DESTROY -------------- */
    public function destroy(Item $item)
    {
        if ($item->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // 🟢 Delete image from Cloudinary or local storage
        if ($item->item_image) {
            if (str_contains($item->item_image, 'cloudinary.com')) {
                try {
                    $publicId = basename(parse_url($item->item_image, PHP_URL_PATH));
                    $publicId = preg_replace('/\.[^.]+$/', '', $publicId);
                    Cloudinary::destroy('ezexchange/items/' . $publicId);
                } catch (\Exception $e) {
                    \Log::error('Cloudinary deletion failed (destroy): ' . $e->getMessage());
                }
            } else {
                Storage::disk('public')->delete($item->item_image);
            }
        }

        $item->delete();

        return redirect()
            ->route('user.inventory')
            ->with('success', 'Item deleted successfully!');
    }
}
