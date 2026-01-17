<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExchangeRequest;

class ExchangeRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExchangeRequest::create([
            'from_user_id' => 3,       
            'to_user_id' => 2,         
            'from_item_id' => 6,       
            'to_item_id' => 3,        
        ]);
    }
}
