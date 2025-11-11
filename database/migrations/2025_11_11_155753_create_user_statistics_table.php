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
        Schema::create('user_statistics', function (Blueprint $table) {
            $table->id('user_id')->primary();
            $table->integer('price_updates_count')->default(0);
            $table->integer('reviews_count')->default(0);
            $table->integer('products_added_count')->default(0);
            $table->integer('accurate_contributions_count')->default(0);
            $table->integer('inaccurate_contributions_count')->default(0);
            $table->decimal('reputation_score', 8, 2)->default(0);
            $table->string('tier')->default('bronze'); 
            $table->timestamp('last_price_update_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('reputation_score');
            $table->index('tier');
            $table->index('last_price_update_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_statistics');
    }   
};
