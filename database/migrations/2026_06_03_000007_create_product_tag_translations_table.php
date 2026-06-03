<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_tag_translations', function (Blueprint $table) {
            $table->id();

            $table->uuid('product_tag_id');
            $table->string('locale', 10)->index();
            $table->string('tag');

            $table->unique(['product_tag_id', 'locale']);
            $table->index(['locale', 'tag']);

            $table->foreign('product_tag_id')
                ->references('id')
                ->on('product_tags')
                ->onDelete('cascade');
        });

        $locale = 'en';
        $tags = DB::table('product_tags')->select('id', 'tag')->get();
        foreach ($tags as $tag) {
            DB::table('product_tag_translations')->insert([
                'product_tag_id' => $tag->id,
                'locale' => $locale,
                'tag' => $tag->tag ?? '',
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_tag_translations');
    }
};
