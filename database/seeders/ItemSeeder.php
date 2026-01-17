<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('items')->insert([
            [
                'user_id' => 3, // Make sure user with ID 1 exists
                'category_id' => 2, // Make sure category with ID 1 exists
                'item_image' => null, 
                'item_name' => 'Adidas Samba',
                'item_description' => 'Limited edition sneaker, size 10',
                'item_location' => 'UiTM Jasin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
