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
            $table->foreignUuid('order_id')->constrained();
            $table->foreignUuid('product_id')->constrained();
            $table->integer('quantity');
            $table->integer('ordered_price');
            $table->integer('total_price');
            $table->foreignUuid('promo_id')->constrained();
            $table->foreignUuid('coupon_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
