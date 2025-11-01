<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_thresholds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id');
            $table->decimal('min_price', 10, 2);
            $table->decimal('max_price', 10, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->index('product_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('price_thresholds');
    }
};
