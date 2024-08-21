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
        Schema::create('product_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('product_id')->contrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('attribute_id')->contrained('attributes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('value_id')->contrained('values')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_value');
    }
};
