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
            $table->string('code');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('discount_in_value')->nullable();
            $table->integer('discount_in_percent')->default(0)->min(0)->max(100);
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('has_ended')->default(false);
            $table->uuidMorphs('promoable');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'promoable_id']);

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
