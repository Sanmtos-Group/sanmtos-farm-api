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
        Schema::create('currency_exchange_rates', function (Blueprint $table) {

            $table->uuid('id')->primary();
            $table->string('from')->comment('currency');
            $table->string('to')->comment('currency');
            $table->unsignedDecimal('value', $precision = 19, $scale = 6)->comment('1 from curr. - to curr.');
            $table->timestamps();
            
            $table->index(['from', 'to'], 'unique_curr_ex_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_exchange_rates');
    }
};
