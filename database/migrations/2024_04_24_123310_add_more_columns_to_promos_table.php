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
        Schema::table('promos', function (Blueprint $table) {
            $table->boolean('requires_min_purchase')->default(false)->after('discount_type_id');
            $table->unsignedDecimal('min_purchase_price',  $precision = 19, $scale = 2)->default(0)->after('requires_min_purchase');
            $table->boolean('is_for_first_purchase_only')->default(false)->after('min_purchase_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn(['requires_min_purchase', 'min_purchase_price', 'is_for_first_purchase_only']);

        });
    }
};
