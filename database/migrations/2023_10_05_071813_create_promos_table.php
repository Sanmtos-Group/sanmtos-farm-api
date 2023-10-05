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
            $table->integer('discount');
            $table->boolean('is_universal')->default(false);
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->boolean('is_cancel')->default(false);
            $table->foreignUuid('store_id')->constrained();
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
