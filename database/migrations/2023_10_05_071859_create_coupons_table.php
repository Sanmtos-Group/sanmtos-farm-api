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
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->foreignUuid('discount_type_id')->nullable()->contrained('coupon_types')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedDecimal('discount',  $precision = 19, $scale = 2)->default(0);
            $table->boolean('requires_min_purchase')->default(false);
            $table->unsignedDecimal('min_purchase_price',  $precision = 19, $scale = 2)->default(0);
            $table->boolean('is_for_first_purchase_only')->default(false);
            $table->integer('max_usage')->default(1);
            $table->boolean('unlimited_usage')->default(false);
            $table->timestamp('expiration_date');
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignUuid('store_id')->nullable()->contrained('stores')->cascadeOnUpdate()->nullOnDelete();            
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
