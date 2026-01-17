<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notification::create([
            'user_id' => 3, // Make sure this user exists
            'exchangerequest_id' => 3, // Make sure this exchange request exists
            'notification_type' => 'Exchange Request',
            'notification_content' => 'Exchange request Iphone 11 to Iphone XR.',
        ]);
    }
}
