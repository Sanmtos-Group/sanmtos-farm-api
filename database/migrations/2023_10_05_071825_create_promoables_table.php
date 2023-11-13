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
        Schema::create('promoables', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('promo_id')->contrained('promos')->cascadeOnUpdate()->cascadeOnDelete();
            $table->uuidMorphs('promoable');
            $table->index(['promo_id', 'promoable_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promoable');
    }
};
