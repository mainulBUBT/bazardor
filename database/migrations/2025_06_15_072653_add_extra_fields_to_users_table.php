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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('last_name');
            $table->date('dob')->nullable()->after('username');
            $table->string('image_path')->nullable()->after('dob');
            $table->string('phone')->nullable()->after('image_path');
            $table->string('gender')->nullable()->after('phone');
            $table->text('address')->nullable()->after('gender');
            $table->string('city')->nullable()->after('address');
            $table->string('division')->nullable()->after('city');
            $table->boolean('is_active')->default(1)->after('division');
            $table->boolean('subscribed_to_newsletter')->default(0)->after('is_active');
            $table->string('referral_code')->nullable()->after('subscribed_to_newsletter');
            $table->string('referred_by')->nullable()->after('referral_code');
            $table->string('status')->default('pending')->after('referred_by');
            $table->string('user_type')->default('user');
            $table->foreignId('zone_id')->nullable();
            $table->timestamp('last_login_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('dob');
            $table->dropColumn('image_path');
            $table->dropColumn('phone');
            $table->dropColumn('gender');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('division');
            $table->dropColumn('is_active');
            $table->dropColumn('subscribed_to_newsletter');
            $table->dropColumn('referral_code');
            $table->dropColumn('referred_by');
            $table->dropColumn('user_type');
            $table->dropColumn('zone_id');
            $table->dropColumn('last_login_at');
        });
    }
};
