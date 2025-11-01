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
        Schema::create('product_market_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id');
            $table->foreignUuid('market_id');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->timestamp('price_date')->useCurrent();
            $table->timestamps();
            
            // Adding indexes and constraints
            $table->unique(['product_id', 'market_id', 'price_date']);
            $table->index('price');
            $table->index('price_date');
            $table->index(['product_id', 'market_id']); // Composite index for frequent queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_market_prices');
    }
};
