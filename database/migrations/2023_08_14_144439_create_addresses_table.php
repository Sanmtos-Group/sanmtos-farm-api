<?php

use App\Models\Country;
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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('dialing_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address');
            $table->string('zip_code')->nullable();
            $table->foreignUuid('country_id')->contrained('countries')->cascadeOnUpdate()->cascadeOnDelete(); 
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->uuid('addressable_id');
            $table->string('addressable_type');
            $table->boolean('is_preferred')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
