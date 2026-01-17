<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExchangeRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('exchange_rules')->truncate();

        $rules = [];

        for ($priceGap = 1; $priceGap <= 5; $priceGap++) {
            for ($sameCat = 0; $sameCat <= 1; $sameCat++) {
                for ($sameSub = 0; $sameSub <= 1; $sameSub++) {
                    for ($distLevel = 1; $distLevel <= 5; $distLevel++) {

                        // Default recommendation
                        $recommendation = 'Not Recommended';

                        // ⭐ HIGHLY RECOMMENDED: Low price gap AND low distance
                        if ($priceGap <= 2 && $distLevel <= 2) {
                            $recommendation = 'Highly Recommended';
                        }
                        // 👍 RECOMMENDED: Medium matches
                        elseif (
                            ($priceGap <= 3 && $sameCat === 1 && $sameSub === 1 && $distLevel <= 3) ||
                            ($priceGap <= 3 && $sameCat === 1 && $sameSub === 0 && $distLevel <= 2)
                        ) {
                            $recommendation = 'Recommended';
                        }

                        $rules[] = [
                            'price_gap_level'  => $priceGap,
                            'same_category'    => $sameCat,
                            'same_subcategory' => $sameSub,
                            'distance_level'   => $distLevel,
                            'recommendation'   => $recommendation,
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ];
                    }
                }
            }
        }

        DB::table('exchange_rules')->insert($rules);
    }
}
