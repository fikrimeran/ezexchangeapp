<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chat;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Chat::create([
            'exchangerequest_id' => 3, 
            'from_user_id' => 2,       
            'to_user_id' => 3,         
            'chat_message' => 'Hi! I’m interested in this exchange. Can we discuss more?',
        ]);
    }
}
