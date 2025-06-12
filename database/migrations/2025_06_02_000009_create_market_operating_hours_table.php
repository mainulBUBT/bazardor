<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_operating_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id');
            $table->string('day'); // e.g. Monday, Tuesday
            $table->time('opening')->nullable();
            $table->time('closing')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            $table->index(['market_id', 'day']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('market_operating_hours');
    }
};
