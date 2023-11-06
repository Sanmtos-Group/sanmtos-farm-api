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
        Schema::create('order_product', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('order_id')->contrained('orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('product_id')->contrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('quantity');
            $table->unsignedDecimal('ordered_price', $precision = 19, $scale = 2); 
            $table->unsignedDecimal('total_price', $precision = 19, $scale = 2); 
            $table->foreignUuid('promo_id')->contrained('promos')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('coupon_id')->contrained('coupons')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product');
    }
};
