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
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->contrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedDecimal('amount', $precision = 19, $scale = 2);
            $table->uuidMorphs('paymentable'); // the item paid for e.g orders, subscriptions etc. 

            $table->foreignUuid('gateway_id')->contrained('payment_gateways')->cascadeOnUpdate()->cascadeOnDelete(); // gateway e.g paystack, flutterwave, 
            $table->string('method')->nullable(); // method/channel e.g card, transfer, ussd
            $table->string('currency')->nullable(); // e.g NGN, USD
            $table->ipAddress('ip_address')->nullable();
            $table->string('gateway_checkout_url')->nullable(); // generated gateway checkout url   

            $table->string('transaction_reference')->nullable();
            $table->enum('transaction_status', ['pending','failed','successful'])->default('pending');
            $table->jsonb('metadata')->nullable(); // the gateway payment response full data in json format 
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignUuid('verifier_id')->nullable()->contrained('users')->cascadeOnUpdate()->nullOnDelete(); // for manual verification
            
            $table->timestamps();
            $table->softDeletes();

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
