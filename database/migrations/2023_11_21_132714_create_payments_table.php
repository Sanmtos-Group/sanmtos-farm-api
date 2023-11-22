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
        Schema::create('payments', function (Blueprint $table) {
            $table->foreignUuid('id');
            $table->foreignUuid('user_id')->constrained('users');
            $table->string('payment_gate_way');
            $table->string('payment_method')->nullable();
            $table->string('payment_type');
            $table->unsignedDecimal('amount', $precision = 19, $scale = 2)->default(0.01);
            $table->foreignUuid('product_id')->constrained('products');
            $table->string('transaction_reference');
            $table->enum('transaction_status', ['pending','successful'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
