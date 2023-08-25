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
            $table->id();
            $table->string('address');
            $table->string('zip_code')->nullable();
            $table->foreignIdFor(Country::class)->cascadeOnUpdate()->cascadeOnDelete();            
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->integer('addressable_id');
            $table->string('addressable_type');
            $table->boolean('is_preferred')->default(false);
            $table->timestamps();
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
