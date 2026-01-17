<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStatusEnumOnExchangerequestsTable extends Migration
{
    public function up(): void
    {
        // If you haven't yet:   composer require doctrine/dbal
        Schema::table('exchangerequests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'declined'])
                  ->default('pending')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('exchangerequests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted'])
                  ->default('pending')
                  ->change();
        });
    }
}
