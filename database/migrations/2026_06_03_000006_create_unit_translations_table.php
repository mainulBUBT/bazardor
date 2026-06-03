<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_translations', function (Blueprint $table) {
            $table->id();

            $table->uuid('unit_id');
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->string('symbol')->nullable();

            $table->unique(['unit_id', 'locale']);
            $table->index(['locale', 'name']);

            $table->foreign('unit_id')
                ->references('id')
                ->on('units')
                ->onDelete('cascade');
        });

        $locale = 'en';
        $units = DB::table('units')->select('id', 'name', 'symbol')->get();
        foreach ($units as $unit) {
            DB::table('unit_translations')->insert([
                'unit_id' => $unit->id,
                'locale' => $locale,
                'name' => $unit->name ?? '',
                'symbol' => $unit->symbol ?? '',
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_translations');
    }
};
