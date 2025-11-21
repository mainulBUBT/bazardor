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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('category_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('unit_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('image_path')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->string('brand')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('country_of_origin')->nullable();
            $table->enum('added_by', ['admin', 'user'])->default('admin');
            $table->uuid('added_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();


            $table->index('name');
            $table->index('status');
            $table->index('is_visible');
            $table->index('is_featured');
            $table->index('brand');
            $table->index('base_price');
            $table->index('added_by');
            $table->index('added_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
