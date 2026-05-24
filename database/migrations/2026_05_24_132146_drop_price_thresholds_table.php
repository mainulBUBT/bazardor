<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('price_thresholds');
    }

    public function down(): void
    {
        // Intentionally left empty — price thresholds are no longer part of the price flow.
    }
};
