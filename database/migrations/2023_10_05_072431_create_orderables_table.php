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
        Schema::create('orderables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->contrained('orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->uuidMorphs('orderable');
            $table->integer('quantity');
            $table->unsignedDecimal('price', $precision = 19, $scale = 2); 
            $table->unsignedDecimal('total_price', $precision = 19, $scale = 2); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderables');
    }
};
