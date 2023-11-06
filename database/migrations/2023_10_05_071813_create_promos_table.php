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
        Schema::create('promos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->unsignedDecimal('discount_amount',  $precision = 19, $scale = 2)->nullable();
            $table->integer('discount_percent', $precision = 5, $scale = 2)->default(0)->min(0)->max(100);
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime');
            $table->boolean('is_cancelled')->default(false);
            $table->uuidMorphs('promoable');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
