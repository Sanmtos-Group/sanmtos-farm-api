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
            $table->uuid()->primary();
            $table->string('code');
            $table->integer('discount');
            $table->boolean('is_universal')->default(false);
            $table->timestamp('valid_until');
            $table->boolean('is_cancel')->default(false);
            $table->foreignUuid('store_id')->contrained('stores')->cascadeOnUpdate()->cascadeOnDelete();
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
