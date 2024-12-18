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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('operation', ['insert', 'edit'])->default('insert');
            $table->string('comment');
            $table->foreignUuid('category_id')->nullable()->contrained('categories')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('sub_category_id')->nullable()->contrained('categories')->cascadeOnUpdate()->nullOnDelete();
            $table->integer('quantity')->nullable();
            $table->enum('period', ['day', 'week', 'month'])->default('day');
            $table->integer('duration')->default(1);
            $table->string('upload_file')->nullable();
            $table->foreignUuid('assignee_user_id')->nullable()->contrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('assigner_user_id')->nullable()->contrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->enum('status', ['pending', 'ongoing', 'done'])->default('pending');
            $table->foreignUuid('store_id')->nullable()->contrained('stores')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
