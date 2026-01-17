<?php

use Illuminate\Support\Facades\Route;

use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\User;

Route::get('/telegram/poll', function () {
    $updates = Telegram::getUpdates();

    foreach ($updates as $update) {
        $message = $update['message'] ?? null;
        if (!$message) continue;

        $chatId = $message['chat']['id'];
        $text = trim($message['text'] ?? '');

        // If user sends: link myemail@example.com
        if (str_starts_with($text, 'link ')) {
            $email = substr($text, 5); // remove 'link '
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->telegram_chat_id = $chatId;
                $user->save();

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "✅ Telegram linked successfully to {$user->email}",
                ]);
            } else {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ No user found with that email. Please try again.",
                ]);
            }
        } else {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "👋 Hi! To link your account, type:\nlink your@email.com",
            ]);
        }
    }

    return 'Polling complete.';
});


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();


Route::get('/verify-email', [App\Http\Controllers\Auth\EmailVerificationController::class, 'showForm'])->name('verify.email.form');
Route::post('/verify-email', [App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])->name('verify.email');


Route::get('auth/home', [App\Http\Controllers\Auth\HomeController::class, 'index'])->name('auth.home')->middleware('isAdmin');
Route::get('user/home', [App\Http\Controllers\User\HomeController::class, 'index'])->name('user.home')->middleware('auth'); 

Route::get('/user/inventory', [App\Http\Controllers\User\InventoryController::class, 'index'])->name('user.inventory');
Route::get('/user/items/create', [App\Http\Controllers\User\InventoryController::class, 'create'])->name('items.create');
Route::post('/user/items', [App\Http\Controllers\User\InventoryController::class, 'store'])->name('items.store');
Route::get('/user/items/{item}',  [App\Http\Controllers\User\InventoryController::class, 'show'])->name('items.show');
Route::get('/user/items/{item}/edit', [App\Http\Controllers\User\InventoryController::class, 'edit'])->name('items.edit');
Route::put('/user/items/{item}', [App\Http\Controllers\User\InventoryController::class, 'update'])->name('items.update');
Route::delete('/user/items/{item}', [App\Http\Controllers\User\InventoryController::class, 'destroy'])->name('items.destroy');

Route::get('/user/explore', [App\Http\Controllers\User\ExploreController::class, 'index'])->name('user.explore');
Route::get('/user/explore/{item}', [App\Http\Controllers\User\ExploreController::class, 'show'])->name('user.explore.show');
Route::get('/user/exchange/{item}/select', [App\Http\Controllers\User\ExchangeRequestController::class, 'select'])->name('exchange.select');
Route::post('/user/exchange/{item}', [App\Http\Controllers\User\ExchangeRequestController::class, 'store'])->name('exchange.store');
Route::get('/user/explore/{id}/estimate', [App\Http\Controllers\User\ExploreController::class, 'estimatePrice'])->name('item.estimate');
Route::get('/user/items/{id}/estimate', [App\Http\Controllers\User\ExploreController::class, 'estimatePrice'])->name('user.items.estimate');

Route::get('/user/notification', [App\Http\Controllers\User\NotificationController::class, 'index'])->name('user.notification');
Route::get('/user/notification/{exchangeRequest}', [App\Http\Controllers\User\NotificationController::class, 'show'])->name('user.notification.show');   
Route::post('/user/notification/{exchangeRequest}/accept', [App\Http\Controllers\User\NotificationController::class, 'accept'])->name('user.notification.accept');         
Route::post('/user/notification/{exchangeRequest}/decline', [App\Http\Controllers\User\NotificationController::class, 'decline'])->name('user.notification.decline');        

Route::get('/user/chat', [App\Http\Controllers\User\ChatController::class, 'index'])->name('user.chat');
Route::get('/chat/{exchange}', [App\Http\Controllers\User\ChatController::class, 'show'])->name('user.chat.show');
Route::post('/chat/{exchange}', [App\Http\Controllers\User\ChatController::class, 'store'])->name('user.chat.store');

Route::get('/user/about', [App\Http\Controllers\User\AboutController::class, 'index'])->name('about');

Route::get('/user/profile', [App\Http\Controllers\User\ProfileController::class, 'index'])->name('profile');
Route::get('profile/edit', [App\Http\Controllers\User\ProfileController::class, 'edit'])->name('profile.edit');
Route::put('profile', [App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');


//Admin Routes

//list of users
Route::get('auth/users', [App\Http\Controllers\Auth\UserController::class, 'index'])->name('auth.users.index');
Route::get('auth/users/create',[App\Http\Controllers\Auth\UserController::class, 'create'])->name('auth.users.create');
Route::post('auth/users', [App\Http\Controllers\Auth\UserController::class, 'store'])->name('auth.users.store');
Route::get('auth/users/{user}', [App\Http\Controllers\Auth\UserController::class, 'show'])->name('auth.users.show');
Route::get('auth/users/{user}/edit', [App\Http\Controllers\Auth\UserController::class, 'edit'])->name('auth.users.edit');
Route::put('auth/users/{user}', [App\Http\Controllers\Auth\UserController::class, 'update'])->name('auth.users.update');
Route::delete('auth/users/{user}', [App\Http\Controllers\Auth\UserController::class, 'destroy'])->name('auth.users.destroy');

//list of categories
Route::get('auth/categories', [App\Http\Controllers\Auth\CategoryController::class, 'index'])->name('auth.categories.index');
Route::get('auth/categories/create',[App\Http\Controllers\Auth\CategoryController::class, 'create'])->name('auth.categories.create');
Route::post('auth/categories', [App\Http\Controllers\Auth\CategoryController::class, 'store'])->name('auth.categories.store');
Route::get('auth/categories/{category}', [App\Http\Controllers\Auth\CategoryController::class, 'show'])->name('auth.categories.show');
Route::get('auth/categories/{category}/edit', [App\Http\Controllers\Auth\CategoryController::class, 'edit'])->name('auth.categories.edit');
Route::put('auth/categories/{category}', [App\Http\Controllers\Auth\CategoryController::class, 'update'])->name('auth.categories.update');
Route::delete('auth/categories/{category}', [App\Http\Controllers\Auth\CategoryController::class, 'destroy'])->name('auth.categories.destroy');

//list of subcategories
Route::get('auth/subcategories', [App\Http\Controllers\Auth\SubcategoryController::class, 'index'])->name('auth.subcategories.index');
Route::get('auth/subcategories/create', [App\Http\Controllers\Auth\SubcategoryController::class, 'create'])->name('auth.subcategories.create');
Route::post('auth/subcategories', [App\Http\Controllers\Auth\SubcategoryController::class, 'store'])->name('auth.subcategories.store');
Route::get('auth/subcategories/{subcategory}', [App\Http\Controllers\Auth\SubcategoryController::class, 'show'])->name('auth.subcategories.show');
Route::get('auth/subcategories/{subcategory}/edit', [App\Http\Controllers\Auth\SubcategoryController::class, 'edit'])->name('auth.subcategories.edit');
Route::put('auth/subcategories/{subcategory}', [App\Http\Controllers\Auth\SubcategoryController::class, 'update'])->name('auth.subcategories.update');
Route::delete('auth/subcategories/{subcategory}', [App\Http\Controllers\Auth\SubcategoryController::class, 'destroy'])->name('auth.subcategories.destroy');

//list of Exchange History
Route::get('auth/exchangerequests', [App\Http\Controllers\Auth\ExchangeRequestController::class, 'index'])->name('auth.exchangerequests.index');
Route::get('auth/exchangerequests/{exchangerequest}', [App\Http\Controllers\Auth\ExchangeRequestController::class, 'show'])->name('auth.exchangerequests.show');
Route::get('auth/exchangerequests/{exchangerequest}/edit', [App\Http\Controllers\Auth\ExchangeRequestController::class, 'edit'])->name('auth.exchangerequests.edit');
Route::put('auth/exchangerequests/{exchangerequest}', [App\Http\Controllers\Auth\ExchangeRequestController::class, 'update'])->name('auth.exchangerequests.update');
Route::delete('auth/exchangerequests/{exchangerequest}', [App\Http\Controllers\Auth\ExchangeRequestController::class, 'destroy'])->name('auth.exchangerequests.destroy');

//list of Items 
Route::get('auth/items', [App\Http\Controllers\Auth\ItemController::class, 'index'])->name('auth.items.index');
Route::get('auth/items/{item}', [App\Http\Controllers\Auth\ItemController::class, 'show'])->name('auth.items.show');
Route::delete('auth/items/{item}', [App\Http\Controllers\Auth\ItemController::class, 'destroy'])->name('auth.items.destroy');
