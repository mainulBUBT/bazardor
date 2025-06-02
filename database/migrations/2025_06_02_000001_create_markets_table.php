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
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->json('opening_hours')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('rating_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('name');
            $table->index('location');
            $table->index('type');
            $table->index('is_active');
            $table->index('position');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
