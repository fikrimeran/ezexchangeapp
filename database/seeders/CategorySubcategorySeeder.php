<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class CategorySubcategorySeeder extends Seeder
{
    public function run(): void
    {
        // 🥾 SHOES
        $shoes = Category::firstOrCreate(['category_name' => 'Shoes']);
        Subcategory::firstOrCreate(['name' => 'Sneakers', 'category_id' => $shoes->id]);
        Subcategory::firstOrCreate(['name' => 'Boots', 'category_id' => $shoes->id]);
        Subcategory::firstOrCreate(['name' => 'Sandals', 'category_id' => $shoes->id]);
        Subcategory::firstOrCreate(['name' => 'Formal Shoes', 'category_id' => $shoes->id]);

        // 👕 CLOTHES
        $clothes = Category::firstOrCreate(['category_name' => 'Clothes']);
        Subcategory::firstOrCreate(['name' => 'T-Shirts', 'category_id' => $clothes->id]);
        Subcategory::firstOrCreate(['name' => 'Jackets', 'category_id' => $clothes->id]);
        Subcategory::firstOrCreate(['name' => 'Pants', 'category_id' => $clothes->id]);
        Subcategory::firstOrCreate(['name' => 'Dresses', 'category_id' => $clothes->id]);

        // 💻 ELECTRONICS
        $electronics = Category::firstOrCreate(['category_name' => 'Electronics']);
        Subcategory::firstOrCreate(['name' => 'Mobile Phones', 'category_id' => $electronics->id]);
        Subcategory::firstOrCreate(['name' => 'Laptops', 'category_id' => $electronics->id]);
        Subcategory::firstOrCreate(['name' => 'Tablets', 'category_id' => $electronics->id]);
        Subcategory::firstOrCreate(['name' => 'Cameras', 'category_id' => $electronics->id]);

        // 🎒 ACCESSORIES
        $accessories = Category::firstOrCreate(['category_name' => 'Accessories']);
        Subcategory::firstOrCreate(['name' => 'Watches', 'category_id' => $accessories->id]);
        Subcategory::firstOrCreate(['name' => 'Bags', 'category_id' => $accessories->id]);
        Subcategory::firstOrCreate(['name' => 'Belts', 'category_id' => $accessories->id]);
        Subcategory::firstOrCreate(['name' => 'Jewelry', 'category_id' => $accessories->id]);

        $this->command->info('✅ Categories & Subcategories seeded successfully!');
    }
}
