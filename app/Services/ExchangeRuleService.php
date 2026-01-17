<?php

namespace App\Services;

use App\Models\ExchangeRule;
use App\Models\Item;

class ExchangeRuleService
{
    /**
     * Evaluate exchange suitability between two items
     */
    public static function evaluate(
        Item $receiverItem,
        Item $myItem,
        float $receiverPrice,
        float $myItemPrice
    ): array {

        /**
         * 1️⃣ PRICE GAP LEVEL (1–5)
         * Smaller gap = better match
         */
        $gapPercent = abs($receiverPrice - $myItemPrice)
            / max($receiverPrice, $myItemPrice) * 100;

        if ($gapPercent <= 5) {
            $priceGapLevel = 1;
        } elseif ($gapPercent <= 15) {
            $priceGapLevel = 2;
        } elseif ($gapPercent <= 30) {
            $priceGapLevel = 3;
        } elseif ($gapPercent <= 50) {
            $priceGapLevel = 4;
        } else {
            $priceGapLevel = 5;
        }

        /**
         * 2️⃣ CATEGORY & SUBCATEGORY MATCH
         */
        $sameCategory = ($receiverItem->category_id === $myItem->category_id) ? 1 : 0;

        $sameSubcategory = (
            $receiverItem->subcategory_id &&
            $receiverItem->subcategory_id === $myItem->subcategory_id
        ) ? 1 : 0;

        /**
         * 3️⃣ DISTANCE LEVEL (1–5 KM)
         */
        $distance = self::distanceKm(
            $receiverItem->latitude,
            $receiverItem->longitude,
            $myItem->latitude,
            $myItem->longitude
        );

        if ($distance <= 5) {
            $distanceLevel = 1;
        } elseif ($distance <= 15) {
            $distanceLevel = 2;
        } elseif ($distance <= 25) {
            $distanceLevel = 3;
        } elseif ($distance <= 40) {
            $distanceLevel = 4;
        } else {
            $distanceLevel = 5;
        }

        /**
         * 4️⃣ EXACT / NEAREST RULE MATCH (PURE DB-DRIVEN)
         * No weights, no priority math
         */
        $rule = ExchangeRule::where([
                'price_gap_level'  => $priceGapLevel,
                'same_category'    => $sameCategory,
                'same_subcategory' => $sameSubcategory,
                'distance_level'   => $distanceLevel,
            ])->first();

        /**
         * 5️⃣ Fallback: nearest rule if exact not found
         */
        if (!$rule) {
            $rule = ExchangeRule::orderByRaw("
                ABS(price_gap_level - ?) +
                ABS(same_category - ?) +
                ABS(same_subcategory - ?) +
                ABS(distance_level - ?)
            ", [
                $priceGapLevel,
                $sameCategory,
                $sameSubcategory,
                $distanceLevel
            ])->first();
        }

        return [
            'recommendation'   => $rule->recommendation ?? 'Not Recommended',
            'price_gap_level'  => $priceGapLevel,
            'distance_level'   => $distanceLevel,
            'same_category'    => $sameCategory,
            'same_subcategory' => $sameSubcategory,
            'distance_km'      => round($distance, 2),
        ];
    }

    /**
     * Haversine distance (KM)
     */
    private static function distanceKm($lat1, $lon1, $lat2, $lon2): float
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }

        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
