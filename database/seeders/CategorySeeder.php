<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category_seed = [
            ['id'=>'1','category_name'=>'Clothes'],
            ['id'=>'2','category_name'=>'Shoes'],
            ['id'=>'3','category_name'=>'Accessories'],
            ['id'=>'4','category_name'=>'Electronics'],
            ];

            foreach ($category_seed as $category_seed)
                    {
                        Category::firstOrCreate($category_seed);
                    }

    }
}
