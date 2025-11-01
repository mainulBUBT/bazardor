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
        Schema::create('market_information', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('market_id');
            $table->boolean('is_non_veg')->default(true);
            $table->boolean('is_halal')->default(false);
            $table->boolean('is_parking')->default(false);
            $table->boolean('is_restroom')->default(false);
            $table->boolean('is_home_delivery')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_information');
    }
};
