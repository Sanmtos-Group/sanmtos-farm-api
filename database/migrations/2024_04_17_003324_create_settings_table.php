<?php

use App\Enums\HtmlInputTypeEnums;
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
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->nullable()->cascadeOnUpdate()->casecadeOnDelete();
            $table->enum('html_input_type', array_merge(HtmlInputTypeEnums::values()))->nullable();
            $table->text('select_options')->nullable();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('group_name')->nullable();
            $table->nullableUuidMorphs('settable');
            $table->text('allowed_editor_roles')->nullable();
            $table->text('allowed_view_roles')->nullable();
            $table->string('owner_feature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
