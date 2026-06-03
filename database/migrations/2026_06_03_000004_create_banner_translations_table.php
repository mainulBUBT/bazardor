<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banner_translations', function (Blueprint $table) {
            $table->id();

            $table->uuid('banner_id');
            $table->string('locale', 10)->index();
            $table->string('title');

            $table->unique(['banner_id', 'locale']);
            $table->index(['locale', 'title']);

            $table->foreign('banner_id')
                ->references('id')
                ->on('banners')
                ->onDelete('cascade');
        });

        $locale = 'en';
        $banners = DB::table('banners')->select('id', 'title')->get();
        foreach ($banners as $banner) {
            DB::table('banner_translations')->insert([
                'banner_id' => $banner->id,
                'locale' => $locale,
                'title' => $banner->title ?? '',
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_translations');
    }
};
