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
        Schema::create('preferences', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('language_code')->default('USD');
            $table->string('currency_code')->default('en');
            $table->uuidMorphs('preferenceable');
            $table->timestamps();

            $table->index(['preferenceable_id', 'preferenceable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferences');
    }
};
