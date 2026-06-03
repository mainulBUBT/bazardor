<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_translations', function (Blueprint $table) {
            $table->id();

            $table->uuid('market_id');
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address')->nullable();

            $table->unique(['market_id', 'locale']);
            $table->index(['locale', 'name']);
            $table->index(['market_id', 'locale', 'name']);

            $table->foreign('market_id')
                ->references('id')
                ->on('markets')
                ->onDelete('cascade');
        });

        $locale = 'en';
        $markets = DB::table('markets')->select('id', 'name', 'description', 'address')->get();
        foreach ($markets as $market) {
            DB::table('market_translations')->insert([
                'market_id' => $market->id,
                'locale' => $locale,
                'name' => $market->name ?? '',
                'description' => $market->description,
                'address' => $market->address,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('market_translations');
    }
};
