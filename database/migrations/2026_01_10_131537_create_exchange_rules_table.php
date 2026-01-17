<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exchange_rules', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('price_gap_level');     // 1,2,3
            $table->boolean('same_category');            // 0,1
            $table->boolean('same_subcategory');         // 0,1
            $table->tinyInteger('distance_level');       // 1,2,3
            $table->string('recommendation');            // Highly Recommended, etc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rules');
    }
};
