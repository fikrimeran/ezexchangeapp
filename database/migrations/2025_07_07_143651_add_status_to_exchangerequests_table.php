<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exchangerequests', function (Blueprint $table) {
            // Add the column after to_item_id for readability
            $table
                ->enum('status', ['pending', 'accepted', 'rejected'])
                ->default('pending')
                ->after('to_item_id');
        });
    }

    public function down(): void
    {
        Schema::table('exchangerequests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
