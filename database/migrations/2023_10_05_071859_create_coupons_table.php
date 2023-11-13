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
            $table->unsignedDecimal('discount', $precision = 5, $scale = 2);
            $table->timestamp('start_datetime');
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_cancelled')->default(false);
            $table->uuidMorphs('couponable');
            $table->index(['code', 'couponable_id']);
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
