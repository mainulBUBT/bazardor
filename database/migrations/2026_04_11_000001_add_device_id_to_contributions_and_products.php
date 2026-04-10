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
            $table->string('device_id', 255)->nullable()->after('user_id');
            $table->index('device_id');
        });

        Schema::table('price_contributions_history', function (Blueprint $table) {
            $table->string('device_id', 255)->nullable()->after('user_id');
            $table->index('device_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('device_id', 255)->nullable()->after('added_by_id');
            $table->index('device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['device_id']);
            $table->dropColumn('device_id');
        });

        Schema::table('price_contributions_history', function (Blueprint $table) {
            $table->dropIndex(['device_id']);
            $table->dropColumn('device_id');
        });

        Schema::table('price_contributions', function (Blueprint $table) {
            $table->dropIndex(['device_id']);
            $table->dropColumn('device_id');
        });
    }
};
