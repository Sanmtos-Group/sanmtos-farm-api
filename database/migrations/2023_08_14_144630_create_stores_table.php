<?php

use App\Models\User;
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
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->foreignUuid('user_id')->contrained('users')->cascadeOnUpdate()->cascadeOnDelete();              
            $table->timestamp('verified_at', $precision = 0)->nullable();
            $table->foreignUuid('verifier_id')->nullable()->contrained('users')->cascadeOnUpdate()->nullOnDelete();             
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
