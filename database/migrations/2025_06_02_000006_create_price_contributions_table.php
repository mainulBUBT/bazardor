<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_contributions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id');
            $table->foreignUuid('market_id');
            $table->foreignUuid('user_id');
            $table->decimal('submitted_price', 10, 2);
            $table->string('proof_image')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['product_id', 'market_id', 'status']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('price_contributions');
    }
};
