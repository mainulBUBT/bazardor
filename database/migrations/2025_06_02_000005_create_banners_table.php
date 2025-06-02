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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_path');
            $table->string('url')->nullable();
            $table->enum('type', ['general', 'featured'])->default('general');
            $table->text('description')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('badge_color')->nullable();
            $table->string('badge_background_color')->nullable();
            $table->string('badge_icon')->nullable();
            $table->string('button_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('position')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('type');
            $table->index('is_active');
            $table->index('position');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
