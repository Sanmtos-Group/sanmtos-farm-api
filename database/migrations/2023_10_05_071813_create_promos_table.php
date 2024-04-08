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
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedDecimal('discount',  $precision = 19, $scale = 2)->default(0);
            $table->foreignUuid('discount_type_id')->nullable()->contrained('coupon_types')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('free_delivery')->default(false);
            $table->boolean('free_advert')->default(false);
            $table->timestamp('start_datetime')->nullable();
            $table->timestamp('end_datetime')->nullable();
            $table->boolean('is_unlimited')->default(false);
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('cancellation_reason')->nullable();
            $table->foreignUuid('store_id')->nullable()->contrained('stores')->cascadeOnUpdate()->casecadeOnDelete();

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
