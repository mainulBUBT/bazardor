<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();

            $table->uuid('product_id');
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('brand')->nullable();

            // Composite unique: one translation per locale per product
            $table->unique(['product_id', 'locale']);

            // Index for lookups by locale (e.g., "all Bengali products")
            $table->index(['locale', 'name']);

            // Full-text friendly index for name searches within a locale
            $table->index(['product_id', 'locale', 'name']);

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });

        // Migrate existing default-locale data
        $locale = 'en';
        $products = DB::table('products')->select('id', 'name', 'description', 'brand')->get();
        foreach ($products as $product) {
            DB::table('product_translations')->insert([
                'product_id' => $product->id,
                'locale' => $locale,
                'name' => $product->name ?? '',
                'description' => $product->description,
                'brand' => $product->brand,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_translations');
    }
};
