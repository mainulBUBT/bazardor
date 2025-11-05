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
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->tinyInteger('otp_hit_count')->default('0');
            $table->boolean('is_temp_blocked')->default('0');
            $table->timestamp('temp_blocked_at')->nullable();
            $table->timestamp('expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn('otp_hit_count');
            $table->dropColumn('is_temp_blocked');
            $table->dropColumn('temp_blocked_at');
            $table->dropColumn('expires_at');
        });
    }
};
