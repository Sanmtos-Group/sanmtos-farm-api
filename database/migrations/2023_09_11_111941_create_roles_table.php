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
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignUuid('store_id')->nullable()->contrained('stores')->cascadeOnUpdate()->casecadeOnDelete();            
            $table->foreignUuid('creator_id')->nullable()->contrained('users')->cascadeOnUpdate()->nullOnDelete();            
            $table->timestamps();

            $table->index(['name', 'store_id'], 'unique_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropIndex('unique_role');
        Schema::dropIfExists('roles');
    }
};
