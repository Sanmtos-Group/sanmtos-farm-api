<?php

use App\Models\Category;
use App\Models\Store;
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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('short_description')->nullable();
            $table->unsignedDecimal('price', $precision = 19, $scale = 2)->default(0.01);
            $table->string('currency')->nullable();
            $table->unsignedDecimal('regular_price', $precision = 19, $scale = 2)->nullable();
            $table->unsignedDecimal('discount',  $precision = 5, $scale = 2)->default(0)->min(0)->max(100);
            $table->foreignUuid('category_id')->contrained('categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('store_id')->contrained('stores')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('products', function (Blueprint $table){
            $table->dropForeign(['category_id']);
            $table->dropForeign(['store_id']);
            $table->dropForeign(['verifier_id']);

        });
    }
};
