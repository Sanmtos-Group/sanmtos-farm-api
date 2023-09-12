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
        Schema::create('permission_role', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('role_id')->contrained('roles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('permission_id')->contrained('permissions')->cascadeOnUpdate()->cascadeOnDelete();             
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
