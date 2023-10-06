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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('number');
            $table->foreignUuid('user_id')->contrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedDecimal('price', $precision = 19, $scale = 2)->default(0); 
            $table->foreignUuid('address_id')->contrained('addresses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('delivery_fee')->default(0);
            $table->integer('total_price')->default(0);
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
