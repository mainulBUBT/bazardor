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
        Schema::table('price_contributions', function (Blueprint $table) {
            $table->foreignUuid('user_id')->nullable()->change();
        });

        Schema::table('price_contributions_history', function (Blueprint $table) {
            $table->foreignUuid('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_contributions', function (Blueprint $table) {
            $table->foreignUuid('user_id')->nullable(false)->change();
        });

        Schema::table('price_contributions_history', function (Blueprint $table) {
            $table->foreignUuid('user_id')->nullable(false)->change();
        });
    }
};
