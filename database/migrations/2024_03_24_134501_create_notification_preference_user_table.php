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
        Schema::create('notification_preference_user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignUuid('notification_preference_id');
            $table->foreign('notification_preference_id')->references('id')->on('notification_preferences')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->index(['user_id','notification_preference_id'], 'unique_user_notification_preference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_preference');
    }
};
