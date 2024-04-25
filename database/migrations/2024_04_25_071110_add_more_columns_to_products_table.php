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
        Schema::table('products', function (Blueprint $table) {

            $table->unsignedDecimal('length',8,2)->default(0.00)->comment('in CM')->after('weight');
            $table->unsignedDecimal('width',8,2)->default(0.00)->comment('in CM')->after('length');
            $table->unsignedDecimal('height',8,2)->default(0.00)->comment('in CM')->after('width');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['length', 'width', 'height']);
        });
    }
};
