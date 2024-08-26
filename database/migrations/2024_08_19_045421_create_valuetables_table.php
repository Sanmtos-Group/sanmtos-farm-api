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
        Schema::create('valuetables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('value_id')->contrained('values')->cascadeOnUpdate()->cascadeOnDelete();
            $table->uuidMorphs('valuetable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valuetables');
    }
};
