<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_contribution_votes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('price_contribution_id');
            $table->foreignUuid('user_id');
            $table->boolean('is_upvote');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['price_contribution_id', 'user_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('price_contribution_votes');
    }
};
