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
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('announcement');
            $table->string('target_audience')->default('all');
            $table->foreignId('zone_id')->nullable();
            $table->string('link_url')->nullable();
            $table->string('image')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->string('status')->default('sent');
            $table->integer('recipients_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
    }
};
