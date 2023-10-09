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
        Schema::create('coupon_product', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('coupon_id')->contrained('coupons')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('product_id')->contrained('products')->cascadeOnUpdate()->cascadeOnDelete();

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