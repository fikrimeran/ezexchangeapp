<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\ExchangeRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // =====================
        // TOP STATISTICS
        // =====================
        $totalUsers = User::count();
        $totalItems = Item::count();
        $totalExchanges = ExchangeRequest::count();

        // =====================
        // USERS STATISTICS
        // =====================

        // Weekly (last 7 days)
        $weeklyUsers = User::select(
                DB::raw('DATE(created_at) as label'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [now()->subDays(6), now()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('label')
            ->get();

        // Monthly (current year)
        $monthlyUsers = User::select(
                DB::raw('MONTH(created_at) as label'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('label')
            ->get();

        // Yearly: Monthly breakdown for this year
        $yearlyUsers = [];
        // Initialize 12 months with 0
        for ($m = 1; $m <= 12; $m++) {
            $yearlyUsers[$m] = 0;
        }
        foreach ($monthlyUsers as $u) {
            $yearlyUsers[$u->label] = $u->total;
        }

        // Format labels and data for charts
        $weeklyLabels = $weeklyUsers->pluck('label');
        $weeklyData   = $weeklyUsers->pluck('total');

        $monthlyLabels = $monthlyUsers->map(fn ($u) =>
            Carbon::create()->month($u->label)->format('M')
        );
        $monthlyData = $monthlyUsers->pluck('total');

        $yearlyLabels = collect(range(1,12))->map(fn($m) => Carbon::create()->month($m)->format('M'));
        $yearlyData = collect($yearlyUsers)->values();

        // =====================
        // ITEMS BY CATEGORY
        // =====================
        $itemsByCategory = Item::join('categories', 'items.category_id', '=', 'categories.id')
            ->select('categories.category_name', DB::raw('COUNT(items.id) as total'))
            ->groupBy('categories.category_name')
            ->get();

        // =====================
        // EXCHANGE STATUS
        // =====================
        $exchangeByStatus = ExchangeRequest::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        return view('auth.home', compact(
            'totalUsers',
            'totalItems',
            'totalExchanges',
            'itemsByCategory',
            'exchangeByStatus',
            'weeklyLabels',
            'weeklyData',
            'monthlyLabels',
            'monthlyData',
            'yearlyLabels',
            'yearlyData'
        ));
    }
}
