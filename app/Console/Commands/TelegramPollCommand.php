<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\HttpClients\GuzzleHttpClient;
use GuzzleHttp\Client;
use Telegram\Bot\FileUpload\InputFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ExchangeRequest;
use App\Models\Chat;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Start auto-polling Telegram for messages';
    protected $telegram;
    protected $perPage = 5;

    public function __construct()
    {
        parent::__construct();
        // $client = new Client(['verify' => false]);
        // $httpClient = new GuzzleHttpClient($client);
        // $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'), false, $httpClient);
    }

    public function handle()
    {
        $this->info('🚀 Telegram auto-polling started...');
        $lastUpdateId = 0;

        while (true) {
            try {
                $updates = $this->telegram->getUpdates(['offset' => $lastUpdateId + 1]);

                foreach ($updates as $update) {
                    $this->handleUpdate($update);
                    $lastUpdateId = $update['update_id'];
                }

                sleep(2);
            } catch (\Exception $e) {
                $this->warn("⚠️ Error: {$e->getMessage()}");
                sleep(5);
            }
        }
    }

    protected function handleUpdate($update)
    {
        $message = $update['message'] ?? null;
        $callback = $update['callback_query'] ?? null;
        $chatId = $message['chat']['id'] ?? ($callback['message']['chat']['id'] ?? null);

        // -----------------------
        // Handle callback buttons
        // -----------------------
        if ($callback) {
            $data = $callback['data'];

            // Main navigation
            if ($data === 'explore_items') return $this->sendItemsPage($chatId, 1);
            if ($data === 'explore_nearby') {
                // Ask user to select radius first
                $keyboard = [
                    'keyboard' => [
                        [['text' => '10 km'], ['text' => '30 km'], ['text' => '50 km']],
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                ];
                Cache::put("tg_nearby_step_{$chatId}", 'awaiting_radius', 900); // 15 min
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "📏 Select a search radius first (10km, 30km, 50km).",
                    'reply_markup' => json_encode($keyboard),
                ]);
            }
            if ($data === 'explore_by_categories') return $this->sendCategoryList($chatId);

            // Category/Subcategory
            if (Str::startsWith($data, 'category_')) {
                if (Str::startsWith($data, 'category_all_')) {
                    $categoryId = (int) Str::after($data, 'category_all_');
                    return $this->sendItemsByCategory($chatId, $categoryId, 1);
                }
                $categoryId = (int) Str::after($data, 'category_');
                return $this->sendSubcategoryList($chatId, $categoryId);
            }

            if (Str::startsWith($data, 'subcategory_')) {
                $subcategoryId = (int) Str::after($data, 'subcategory_');
                return $this->sendItemsBySubcategory($chatId, $subcategoryId, 1);
            }

            // Pagination
            if (Str::startsWith($data, 'page_all_')) {
                $page = (int) Str::after($data, 'page_all_');
                return $this->sendItemsPage($chatId, $page);
            }
            if (Str::startsWith($data, 'page_subcat_')) {
                [$subcategoryId, $page] = explode('_p', Str::after($data, 'page_subcat_'));
                return $this->sendItemsBySubcategory($chatId, (int)$subcategoryId, (int)$page);
            }
            if (Str::startsWith($data, 'page_nearby_')) {
                [$lat, $lng, $page] = explode('_', Str::after($data, 'page_nearby_'));
                return $this->sendNearbyItems($chatId, (float)$lat, (float)$lng, (int)$page);
            }
            if (Str::startsWith($data, 'page_category_')) {
                [$categoryId, $page] = explode('_', Str::after($data, 'page_category_'));
                return $this->sendItemsByCategory($chatId, (int)$categoryId, (int)$page);
            }

            // -----------------------
            // Exchange request flow
            // -----------------------
            if (Str::startsWith($data, 'exchange_item_')) {
                $toItemId = (int) Str::after($data, 'exchange_item_');
                $toItem = Item::find($toItemId);
                $fromUser = User::where('telegram_chat_id', $chatId)->first();

                if (!$toItem || !$fromUser) {
                    return $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "❌ Invalid item or user."
                    ]);
                }

                $inventoryItems = Item::where('user_id', $fromUser->id)->where('is_available', true)->get();
                if ($inventoryItems->isEmpty()) {
                    return $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "😢 You have no items to offer."
                    ]);
                }

                $buttons = [];
                foreach ($inventoryItems as $item) {
                    $buttons[] = [['text' => $item->item_name, 'callback_data' => "select_offer_{$toItemId}_{$item->id}"]];
                }

                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Select one of your items to offer for exchange:",
                    'reply_markup' => json_encode(['inline_keyboard' => $buttons])
                ]);
            }

            if (Str::startsWith($data, 'select_offer_')) {
                [$toItemId, $fromItemId] = explode('_', Str::after($data, 'select_offer_'));
                $toItem = Item::find((int)$toItemId);
                $fromItem = Item::find((int)$fromItemId);
                $fromUser = User::where('telegram_chat_id', $chatId)->first();
                $toUser = $toItem->user;

                if (!$toItem || !$fromItem || !$fromUser) {
                    return $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "❌ Invalid selection."
                    ]);
                }

                // Create exchange request
                $exchangeRequest = ExchangeRequest::create([
                    'from_user_id' => $fromUser->id,
                    'to_user_id' => $toUser->id,
                    'from_item_id' => $fromItem->id,
                    'to_item_id' => $toItem->id,
                    'status' => 'pending'
                ]);

                // Store notification in Chat table
                Chat::create([
                    'exchangerequest_id' => $exchangeRequest->id,
                    'from_user_id' => $fromUser->id,
                    'to_user_id' => $toUser->id,
                    'chat_message' => "🔔 {$fromUser->name} wants to exchange '{$fromItem->item_name}' for '{$toItem->item_name}'"
                ]);

                // Send Telegram message to the recipient if they linked Telegram, with accept/decline buttons
                if ($toUser->telegram_chat_id) {
                    $keyboard = [
                        'inline_keyboard' => [
                            [
                                ['text' => '✅ Accept', 'callback_data' => "accept_request_{$exchangeRequest->id}"],
                                ['text' => '❌ Decline', 'callback_data' => "decline_request_{$exchangeRequest->id}"]
                            ]
                        ]
                    ];

                    $this->telegram->sendMessage([
                        'chat_id' => $toUser->telegram_chat_id,
                        'text' => "🔔 *New Exchange Request!*\n\n"
                            . "{$fromUser->name} wants to exchange:\n"
                            . "• Their item: *{$fromItem->item_name}*\n"
                            . "• For your item: *{$toItem->item_name}*",
                        'parse_mode' => 'Markdown',
                        'reply_markup' => json_encode($keyboard)
                    ]);
                }

                // Confirmation to sender
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "✅ Exchange request created!\nYou offered '{$fromItem->item_name}' for '{$toItem->item_name}'."
                ]);
            }
        
            // -----------------------
            // Handle exchange request acceptance/decline
            // -----------------------

            // ACCEPT
            if (Str::startsWith($data, 'accept_request_')) {
                $exchangeRequestId = (int) Str::after($data, 'accept_request_');
                $exchangeRequest = ExchangeRequest::find($exchangeRequestId);

                if (!$exchangeRequest || $exchangeRequest->status !== 'pending') {
                    return $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "⚠️ Invalid or already processed request."
                    ]);
                }

                $recipient = User::where('telegram_chat_id', $chatId)->first();
                if ($exchangeRequest->to_user_id !== $recipient->id) {
                    return $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "❌ You are not allowed to process this request."
                    ]);
                }

                // 🔹 Prevent accepting unavailable items
                if (
                    $exchangeRequest->fromItem->is_available == 0 ||
                    $exchangeRequest->toItem->is_available == 0
                ) {
                    return $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "⚠️ One or both items are no longer available."
                    ]);
                }

                // Update request status
                $exchangeRequest->update(['status' => 'accepted']);

                // 🔹 Mark both items as unavailable
                $exchangeRequest->fromItem->update([
                    'is_available' => 0,
                ]);

                $exchangeRequest->toItem->update([
                    'is_available' => 0,
                ]);

                // Save chat in DB
                Chat::create([
                    'exchangerequest_id' => $exchangeRequest->id,
                    'from_user_id'       => $recipient->id,
                    'to_user_id'         => $exchangeRequest->from_user_id,
                    'chat_message'       => "Hi {$exchangeRequest->fromUser->name}, "
                                        ."I’ve accepted your exchange request for "
                                        ."{$exchangeRequest->toItem->item_name}.",
                ]);

                // Telegram notification to requester
                if ($exchangeRequest->fromUser->telegram_chat_id) {
                    $this->telegram->sendMessage([
                        'chat_id' => $exchangeRequest->fromUser->telegram_chat_id,
                        'text' => "✅ {$recipient->name} has accepted your offer:\n"
                                . "• Their item: {$exchangeRequest->toItem->item_name}\n"
                                . "• Your item: {$exchangeRequest->fromItem->item_name}"
                    ]);
                }

                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "✅ You have accepted the exchange request."
                ]);
            }

            // DECLINE
            if (Str::startsWith($data, 'decline_request_')) {
                $exchangeRequestId = (int) Str::after($data, 'decline_request_');
                $exchangeRequest = ExchangeRequest::find($exchangeRequestId);

                if (!$exchangeRequest || $exchangeRequest->status !== 'pending') {
                    return $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "⚠️ Invalid or already processed request."
                    ]);
                }

                $recipient = User::where('telegram_chat_id', $chatId)->first();
                if ($exchangeRequest->to_user_id !== $recipient->id) {
                    return $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "❌ You are not allowed to process this request."
                    ]);
                }

                // Update request status
                $exchangeRequest->update(['status' => 'declined']);

                // Save chat in DB
                Chat::create([
                    'exchangerequest_id' => $exchangeRequest->id,
                    'from_user_id'       => $recipient->id,
                    'to_user_id'         => $exchangeRequest->from_user_id,
                    'chat_message'       => "Hi {$exchangeRequest->fromUser->name}, "
                                        ."I’ve declined your exchange request for "
                                        ."{$exchangeRequest->toItem->item_name}.",
                ]);

                // Telegram notification to requester
                if ($exchangeRequest->fromUser->telegram_chat_id) {
                    $this->telegram->sendMessage([
                        'chat_id' => $exchangeRequest->fromUser->telegram_chat_id,
                        'text' => "❌ {$recipient->name} has declined your offer:\n"
                                . "• Their item: {$exchangeRequest->toItem->item_name}\n"
                                . "• Your item: {$exchangeRequest->fromItem->item_name}"
                    ]);
                }

                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ You have declined the exchange request."
                ]);
            }

            return;
        }

        // -----------------------
        // Handle messages
        // -----------------------
        if (!$message) return;
        $text = $message['text'] ?? null;
        $textTrim = trim($text);

        // -----------------------
        // Nearby radius selection flow
        // -----------------------
        $nearbyStep = Cache::get("tg_nearby_step_{$chatId}");

        if ($nearbyStep === 'awaiting_radius' && in_array($textTrim, ['10 km', '30 km', '50 km'])) {
            $radius = (int) $textTrim;
            Cache::put("tg_nearby_radius_{$chatId}", $radius, 900); // store selected radius
            Cache::put("tg_nearby_step_{$chatId}", 'awaiting_location', 900);

            // Ask user to share location
            return $this->requestUserLocation($chatId);
        }

        if ($nearbyStep === 'awaiting_location' && isset($message['location'])) {
            $lat = $message['location']['latitude'];
            $lng = $message['location']['longitude'];
            $radius = Cache::get("tg_nearby_radius_{$chatId}") ?? 10;

            // Clear nearby flow cache
            Cache::forget("tg_nearby_step_{$chatId}");
            Cache::forget("tg_nearby_radius_{$chatId}");

            return $this->sendNearbyItems($chatId, $lat, $lng, 1, $radius);
        }
        // -----------------------
        // LINK FLOW
        // -----------------------
        if (Str::lower($textTrim) === 'link') {
            Cache::put("tg_link_step_{$chatId}", 'awaiting_email', 900);
            return $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "🔐 To link your account, please enter the email address associated with your account."
            ]);
        }

        if (Str::startsWith(Str::lower($textTrim), 'link ')) {
            $rest = trim(substr($textTrim, 5));
            $parts = preg_split('/\s+/', $rest, 2);
            $email = $parts[0] ?? null;
            $password = $parts[1] ?? null;

            if (!$email) {
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❗ Please provide an email after `link`. Example: `link you@example.com`"
                ]);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ No user found with email: {$email}"
                ]);
            }

            if ($user->telegram_chat_id && (string)$user->telegram_chat_id !== (string)$chatId) {
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "⚠️ This account is already linked to another Telegram. If this is your account and you lost access, unlink it first from the web app."
                ]);
            }

            if ($password !== null) {
                if (Hash::check($password, $user->password)) {
                    $user->telegram_chat_id = $chatId;
                    $user->save();
                    Cache::forget("tg_link_step_{$chatId}");
                    Cache::forget("tg_link_email_{$chatId}");
                    return $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "✅ Linked! Telegram is now connected to {$email}."
                    ]);
                } else {
                    return $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "❌ Incorrect password. Please try again. You can send `link {$email} <your_password>` or start with `link`."
                    ]);
                }
            }

            Cache::put("tg_link_step_{$chatId}", 'awaiting_password', 900);
            Cache::put("tg_link_email_{$chatId}", $email, 900);
            return $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "🔐 Email recognized as {$email}.\nNow please reply with your password to complete linking."
            ]);
        }

        // Mid-flow email/password handling
        $currentStep = Cache::get("tg_link_step_{$chatId}");
        if ($currentStep === 'awaiting_email') {
            $enteredEmail = trim($textTrim);
            $user = User::where('email', $enteredEmail)->first();
            if (!$user) {
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ No user found with email: {$enteredEmail}\nPlease enter a valid email or send `link <email>`."
                ]);
            }
            if ($user->telegram_chat_id && (string)$user->telegram_chat_id !== (string)$chatId) {
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "⚠️ This account is already linked to another Telegram. If this is your account and you lost access, unlink it first from the web app."
                ]);
            }
            Cache::put("tg_link_step_{$chatId}", 'awaiting_password', 900);
            Cache::put("tg_link_email_{$chatId}", $enteredEmail, 900);
            return $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "✅ Email received. Now please enter your password."
            ]);
        }

        if ($currentStep === 'awaiting_password') {
            $enteredPassword = $textTrim;
            $email = Cache::get("tg_link_email_{$chatId}");
            if (!$email) {
                Cache::forget("tg_link_step_{$chatId}");
                Cache::forget("tg_link_email_{$chatId}");
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❗ Something went wrong. Please start again with `link`."
                ]);
            }
            $user = User::where('email', $email)->first();
            if (!$user) {
                Cache::forget("tg_link_step_{$chatId}");
                Cache::forget("tg_link_email_{$chatId}");
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ No user found with email: {$email}. Please start again with `link`."
                ]);
            }
            if (!Hash::check($enteredPassword, $user->password)) {
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ Incorrect password. Please try again."
                ]);
            }

            $user->telegram_chat_id = $chatId;
            $user->save();
            Cache::forget("tg_link_step_{$chatId}");
            Cache::forget("tg_link_email_{$chatId}");
            return $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "✅ Success! Telegram is now linked to {$email}."
            ]);
        }

        // /start
        if ($text === '/start') {
            $user = User::where('telegram_chat_id', $chatId)->first();
            if (!$user) {
                return $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "👋 Welcome! Link your account:\nSend `link` then follow the prompts (email + password).",
                    'parse_mode' => 'Markdown'
                ]);
            }

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🛍️ Explore All Items', 'callback_data' => 'explore_items']],
                    [['text' => '🗂️ Explore by Categories', 'callback_data' => 'explore_by_categories']],
                    [['text' => '📍 Explore Nearby Items', 'callback_data' => 'explore_nearby']],
                ]
            ];

            return $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "👋 Welcome back, *{$user->name}!* Choose an option:",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode($keyboard)
            ]);
        }

        // Unknown command
        return $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "❓ Unknown command. Try /start."
        ]);
    }

    // -----------------------
    // Helper Methods
    // -----------------------

    protected function requestUserLocation($chatId)
    {
        $keyboard = [
            'keyboard' => [
                [['text' => '📍 Share My Location', 'request_location' => true]],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ];

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "📍 Now please share your location to find nearby items.",
            'reply_markup' => json_encode($keyboard),
        ]);
    }

    protected function sendNearbyItems($chatId, $lat, $lng, $page = 1, $radius = 10)
    {
        $user = User::where('telegram_chat_id', $chatId)->first();

        $query = Item::selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$lat, $lng, $lat]
        )
            ->having('distance', '<', $radius)
            ->where('is_available', true)
            ->when($user, fn($q) => $q->where('user_id', '!=', $user->id))
            ->with(['user', 'category', 'subcategory']);

        $total = $query->count();
        $items = $query->skip(($page - 1) * $this->perPage)->take($this->perPage)->get();
        $totalPages = ceil($total / $this->perPage);

        if ($items->isEmpty()) {
            return $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "😢 No nearby items found within {$radius} km.",
            ]);
        }

        foreach ($items as $item) {
            $photo = Str::startsWith($item->item_image, ['http', 'https'])
                ? $item->item_image
                : env('APP_URL') . '/storage/' . ltrim($item->item_image, '/');

            $caption = "📦 *{$item->item_name}*\n"
                . "👤 Owner: {$item->user->name}\n"
                . "🏷️ {$item->category->category_name}\n"
                . "📂 " . ($item->subcategory->name ?? '-') . "\n"
                . "📍 {$item->item_location}\n"
                . "📏 " . number_format($item->distance, 2) . " km";

            $this->telegram->sendPhoto([
                'chat_id' => $chatId,
                'photo' => InputFile::create($photo),
                'caption' => $caption,
                'parse_mode' => 'Markdown',
            ]);

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Send Exchange Request', 'callback_data' => "exchange_item_{$item->id}"]],
                ]
            ];

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Would you like to exchange this item?",
                'reply_markup' => json_encode($keyboard),
            ]);
        }

        // Pagination
        $buttons = [];
        if ($page > 1) $buttons[] = ['text' => '← Previous', 'callback_data' => "page_nearby_{$lat}_{$lng}_" . ($page - 1)];
        if ($page < $totalPages) $buttons[] = ['text' => 'Next →', 'callback_data' => "page_nearby_{$lat}_{$lng}_" . ($page + 1)];

        if (!empty($buttons)) {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "📄 Page {$page} of {$totalPages}",
                'reply_markup' => json_encode(['inline_keyboard' => [$buttons]]),
            ]);
        }
    }

    protected function sendCategoryList($chatId)
    {
        $categories = Category::all();
        $buttons = [];
        foreach ($categories as $cat) $buttons[] = [['text' => $cat->category_name, 'callback_data' => 'category_' . $cat->id]];

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "📂 Choose a category:",
            'reply_markup' => json_encode(['inline_keyboard' => $buttons]),
        ]);
    }

    protected function sendSubcategoryList($chatId, $categoryId)
    {
        $subcats = Subcategory::where('category_id', $categoryId)->get();
        $buttons = [];

        // Add "All items in this category" button
        $buttons[] = [['text' => '📦 All items in this category', 'callback_data' => 'category_all_' . $categoryId]];

        // Add subcategory buttons
        foreach ($subcats as $sub) {
            $buttons[] = [['text' => $sub->name, 'callback_data' => 'subcategory_' . $sub->id]];
        }

        $this->telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "📁 Choose a subcategory or view all items in this category:",
            'reply_markup' => json_encode(['inline_keyboard' => $buttons]),
        ]);
    }

    protected function sendItemsByCategory($chatId, $categoryId, $page = 1)
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        $query = Item::where('is_available', true)
            ->where('category_id', $categoryId)
            ->when($user, fn($q) => $q->where('user_id', '!=', $user->id))
            ->with(['user', 'category', 'subcategory'])
            ->latest();

        $total = $query->count();
        $items = $query->skip(($page - 1) * $this->perPage)->take($this->perPage)->get();
        $totalPages = ceil($total / $this->perPage);

        if ($items->isEmpty()) {
            return $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "😢 No items in this category.",
            ]);
        }

        foreach ($items as $item) {
            $photo = Str::startsWith($item->item_image, ['http', 'https'])
                ? $item->item_image
                : env('APP_URL') . '/storage/' . ltrim($item->item_image, '/');

            $caption = "📦 *{$item->item_name}*\n"
                . "👤 Owner: {$item->user->name}\n"
                . "🏷️ {$item->category->category_name}\n"
                . "📂 " . ($item->subcategory->name ?? '-') . "\n"
                . "📍 {$item->item_location}";

            $this->telegram->sendPhoto([
                'chat_id' => $chatId,
                'photo' => InputFile::create($photo),
                'caption' => $caption,
                'parse_mode' => 'Markdown',
            ]);

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Send Exchange Request', 'callback_data' => "exchange_item_{$item->id}"]],
                ]
            ];

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Would you like to exchange this item?",
                'reply_markup' => json_encode($keyboard),
            ]);
        }

        // Pagination
        $buttons = [];
        if ($page > 1) $buttons[] = ['text' => '← Previous', 'callback_data' => "page_category_{$categoryId}_" . ($page - 1)];
        if ($page < $totalPages) $buttons[] = ['text' => 'Next →', 'callback_data' => "page_category_{$categoryId}_" . ($page + 1)];

        if (!empty($buttons)) {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "📄 Page {$page} of {$totalPages}",
                'reply_markup' => json_encode(['inline_keyboard' => [$buttons]]),
            ]);
        }
    }
    
    protected function sendItemsBySubcategory($chatId, $subId, $page = 1)
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        $query = Item::where('is_available', true)
            ->where('subcategory_id', $subId)
            ->when($user, fn($q) => $q->where('user_id', '!=', $user->id))
            ->with(['user', 'category', 'subcategory'])
            ->latest();

        $total = $query->count();
        $items = $query->skip(($page - 1) * $this->perPage)->take($this->perPage)->get();
        $totalPages = ceil($total / $this->perPage);

        if ($items->isEmpty()) {
            return $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "😢 No items in this subcategory.",
            ]);
        }

        foreach ($items as $item) {
            $photo = Str::startsWith($item->item_image, ['http', 'https'])
                ? $item->item_image
                : env('APP_URL') . '/storage/' . ltrim($item->item_image, '/');

            $caption = "📦 *{$item->item_name}*\n"
                . "👤 Owner: {$item->user->name}\n"
                . "🏷️ {$item->category->category_name}\n"
                . "📍 {$item->item_location}";

            $this->telegram->sendPhoto([
                'chat_id' => $chatId,
                'photo' => InputFile::create($photo),
                'caption' => $caption,
                'parse_mode' => 'Markdown',
            ]);

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Send Exchange Request', 'callback_data' => "exchange_item_{$item->id}"]],
                ]
            ];

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Would you like to exchange this item?",
                'reply_markup' => json_encode($keyboard),
            ]);
        }

        // Pagination
        $buttons = [];
        if ($page > 1) $buttons[] = ['text' => '← Previous', 'callback_data' => "page_subcat_{$subId}_p" . ($page - 1)];
        if ($page < $totalPages) $buttons[] = ['text' => 'Next →', 'callback_data' => "page_subcat_{$subId}_p" . ($page + 1)];

        if (!empty($buttons)) {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "📄 Page {$page} of {$totalPages}",
                'reply_markup' => json_encode(['inline_keyboard' => [$buttons]]),
            ]);
        }
    }

    protected function sendItemsPage($chatId, $page = 1)
    {
        $user = User::where('telegram_chat_id', $chatId)->first();
        $query = Item::where('is_available', true)
            ->when($user, fn($q) => $q->where('user_id', '!=', $user->id))
            ->with(['user', 'category', 'subcategory'])
            ->latest();

        $total = $query->count();
        $items = $query->skip(($page - 1) * $this->perPage)->take($this->perPage)->get();
        $totalPages = ceil($total / $this->perPage);

        if ($items->isEmpty()) {
            return $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "😢 No available items.",
            ]);
        }

        foreach ($items as $item) {
            $photo = Str::startsWith($item->item_image, ['http', 'https'])
                ? $item->item_image
                : env('APP_URL') . '/storage/' . ltrim($item->item_image, '/');

            $caption = "📦 *{$item->item_name}*\n"
                . "👤 Owner: {$item->user->name}\n"
                . "🏷️ {$item->category->category_name}\n"
                . "📂 " . ($item->subcategory->name ?? '-') . "\n"
                . "📍 {$item->item_location}";

            $this->telegram->sendPhoto([
                'chat_id' => $chatId,
                'photo' => InputFile::create($photo),
                'caption' => $caption,
                'parse_mode' => 'Markdown',
            ]);

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🔄 Send Exchange Request', 'callback_data' => "exchange_item_{$item->id}"]],
                ]
            ];

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "Would you like to exchange this item?",
                'reply_markup' => json_encode($keyboard),
            ]);
        }

        // Pagination
        $buttons = [];
        if ($page > 1) $buttons[] = ['text' => '← Previous', 'callback_data' => "page_all_" . ($page - 1)];
        if ($page < $totalPages) $buttons[] = ['text' => 'Next →', 'callback_data' => "page_all_" . ($page + 1)];

        if (!empty($buttons)) {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "📄 Page {$page} of {$totalPages}",
                'reply_markup' => json_encode(['inline_keyboard' => [$buttons]]),
            ]);
        }
    }
}
