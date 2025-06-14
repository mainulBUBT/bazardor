<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_creators', function (Blueprint $table) {
            $table->id();
            // User who created the entity
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Polymorphic relation fields: creatable_type & creatable_id
            $table->morphs('creatable');

            $table->timestamps();

            // Indexes for quick lookup
            $table->index(['user_id']);
            $table->index(['creatable_id', 'creatable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_creators');
    }
}; 