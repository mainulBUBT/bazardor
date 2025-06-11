<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_opening_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('market_opening_hours');
    }
};
