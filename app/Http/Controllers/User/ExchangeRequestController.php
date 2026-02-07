<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Item;
use App\Models\ExchangeRequest;
use App\Services\ExchangeRuleService;

class ExchangeRequestController extends Controller
{
    /**
     * STEP 1 – Show current user’s items with exchange recommendations
     */
    public function select(Item $item)
    {
        abort_if($item->user_id == Auth::id(), 403);

        $myItems = Item::with(['category', 'subcategory'])
            ->where('user_id', Auth::id())
            ->where('is_available', 1)
            ->get();

        // Cached estimated price for receiver item
        $receiverPrice = $this->getEstimatedPriceValue($item);

        $results = [];

        foreach ($myItems as $myItem) {
            $myItemPrice = $this->getEstimatedPriceValue($myItem);

            $ruleResult = ExchangeRuleService::evaluate(
                $item,
                $myItem,
                $receiverPrice,
                $myItemPrice
            );

            $results[] = [
                'item'             => $myItem,
                'recommendation'   => $ruleResult['recommendation'],
                'distance_km'      => $ruleResult['distance_km'],
                'price_value'      => $myItemPrice,
                'formatted_price'  => 'RM ' . number_format($myItemPrice, 2),
                'price_gap_level'  => $ruleResult['price_gap_level'],
                'distance_level'   => $ruleResult['distance_level'],
                'same_category'    => $ruleResult['same_category'],
                'same_subcategory' => $ruleResult['same_subcategory'],
            ];
        }

        /**
         * SORTING STRATEGY
         */
        $order = [
            'Highly Recommended' => 1,
            'Recommended'        => 2,
            'Not Recommended'    => 3,
        ];

        usort($results, function ($a, $b) use ($order) {
            return
                ($order[$a['recommendation']] ?? 99) <=> ($order[$b['recommendation']] ?? 99)
                ?: $a['price_gap_level'] <=> $b['price_gap_level']
                ?: $a['distance_level'] <=> $b['distance_level']
                ?: $a['distance_km'] <=> $b['distance_km'];
        });

        return view('user.exchange.select', [
            'receiverItem'  => $item,
            'receiverPrice' => $receiverPrice,
            'results'       => $results,
        ]);
    }

    /**
     * STEP 2 – Save the exchange request
     */
    public function store(Request $request, Item $item)
    {
        $data = $request->validate([
            'from_item_id' => 'required|exists:items,id',
        ]);

        abort_if(
            Item::where('id', $data['from_item_id'])
                ->where('user_id', Auth::id())
                ->doesntExist(),
            403
        );

        // 🔹 CHANGE 1: store the created request in a variable
        $exchangeRequest = ExchangeRequest::create([
            'from_user_id' => Auth::id(),
            'to_user_id'   => $item->user_id,
            'from_item_id' => $data['from_item_id'],
            'to_item_id'   => $item->id,
        ]);

        // 🔹 CHANGE 2: send email notification (safe)
        try {
            $receiver = $exchangeRequest->toUser;
            $sender   = Auth::user();

            Mail::raw(
                "Hello {$receiver->name},\n\n" .
                "{$sender->name} has sent you an exchange request on EZExchange.\n\n" .
                "Your Item: {$exchangeRequest->toItem->item_name}\n" .
                "Offered Item: {$exchangeRequest->fromItem->item_name}\n\n" .
                "To respond to this exchange request, please visit:\n" .
                "https://www.ezexchange.me\n\n" .
                "Thank you,\n" .
                "EZExchange Team",
                function ($message) use ($receiver) {
                    $message->to($receiver->email)
                            ->subject('New Exchange Request Received');
                }
            );
        } catch (\Exception $e) {
            // Email failed — exchange request still works
        }

        return redirect()
            ->route('user.explore.show', $item->id)
            ->with('success', 'Exchange request sent!');
    }

    /**
     * Estimate item price using eBay API (CACHED)
     */
    private function getEstimatedPriceValue(Item $item): float
    {
        return Cache::remember(
            'estimated_price_item_' . $item->id,
            now()->addHours(12), // cache for 12 hours
            function () use ($item) {

                $price = 0;

                try {
                    $searchQuery = trim(
                        $item->item_name . ' ' . Str::limit($item->item_description, 50)
                    );

                    $tokenResponse = Http::asForm()->withHeaders([
                        'Authorization' => 'Basic ' . base64_encode(
                            env('EBAY_CLIENT_ID') . ':' . env('EBAY_CLIENT_SECRET')
                        ),
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ])->post('https://api.ebay.com/identity/v1/oauth2/token', [
                        'grant_type' => 'client_credentials',
                        'scope'      => 'https://api.ebay.com/oauth/api_scope',
                    ]);

                    $token = $tokenResponse->json()['access_token'] ?? null;

                    if ($token) {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $token,
                        ])->get('https://api.ebay.com/buy/browse/v1/item_summary/search', [
                            'q'     => $searchQuery,
                            'limit' => 5,
                        ]);

                        $summaries = $response->json()['itemSummaries'] ?? [];

                        $prices = [];
                        foreach ($summaries as $entry) {
                            if (isset($entry['price']['value'])) {
                                $prices[] = (float) $entry['price']['value'];
                            }
                        }

                        if (count($prices) > 0) {
                            $price = (array_sum($prices) / count($prices)) * 4.7;
                        }
                    }
                } catch (\Exception $e) {
                    // silent fallback
                }

                return $price ?: rand(50, 200);
            }
        );
    }
}
