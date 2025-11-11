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
        Schema::create('price_contributions_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id');
            $table->foreignUuid('market_id');
            $table->foreignUuid('user_id');
            $table->decimal('submitted_price', 10, 2);
            $table->string('proof_image')->nullable();
            $table->string('status')->default('validated');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'market_id']);
            $table->index(['user_id', 'validated_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_contributions_history');
    }
};
