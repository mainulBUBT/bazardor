<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();

            $table->uuid('category_id');
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['category_id', 'locale']);
            $table->index(['locale', 'name']);
            $table->index(['category_id', 'locale', 'name']);

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });

        $locale = 'en';
        $categories = DB::table('categories')->select('id', 'name', 'description')->get();
        foreach ($categories as $category) {
            DB::table('category_translations')->insert([
                'category_id' => $category->id,
                'locale' => $locale,
                'name' => $category->name ?? '',
                'description' => $category->description,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_translations');
    }
};
