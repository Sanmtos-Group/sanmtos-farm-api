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
        Schema::table('attributables', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('value');
            $table->foreignUuid('attribute_id')->nullable()->contrained('attributes')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attributables', function (Blueprint $table) {
            $table->dropColumn('attribute_id');
            $table->string('name')->nullable();
            $table->string('value')->nullable();
        });
    }
};
