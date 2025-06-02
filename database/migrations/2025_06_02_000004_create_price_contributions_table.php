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
        Schema::create('price_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('market_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->decimal('submitted_price', 10, 2);
            $table->string('proof_image')->nullable(); // Optional image proof
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['product_id', 'market_id', 'status']);
            $table->index('user_id');
            $table->index('created_at');
        });

        // Create table for tracking user votes on price contributions
        Schema::create('price_contribution_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_contribution_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->boolean('is_upvote');
            $table->timestamps();

            // Prevent multiple votes from same user
            $table->unique(['price_contribution_id', 'user_id']);
        });

        // Create table for price thresholds
        Schema::create('price_thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->decimal('min_price', 10, 2);
            $table->decimal('max_price', 10, 2);
            $table->timestamps();

            // Index
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_contribution_votes');
        Schema::dropIfExists('price_contributions');
        Schema::dropIfExists('price_thresholds');
    }
};
