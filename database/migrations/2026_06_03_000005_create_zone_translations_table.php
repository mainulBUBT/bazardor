<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zone_translations', function (Blueprint $table) {
            $table->id();

            $table->uuid('zone_id');
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['zone_id', 'locale']);
            $table->index(['locale', 'name']);
            $table->index(['zone_id', 'locale', 'name']);

            $table->foreign('zone_id')
                ->references('id')
                ->on('zones')
                ->onDelete('cascade');
        });

        $locale = 'en';
        $zones = DB::table('zones')->select('id', 'name')->get();
        foreach ($zones as $zone) {
            DB::table('zone_translations')->insert([
                'zone_id' => $zone->id,
                'locale' => $locale,
                'name' => $zone->name ?? '',
                'description' => null,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('zone_translations');
    }
};
