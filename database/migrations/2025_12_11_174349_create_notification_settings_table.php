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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('event_type');
            $table->boolean('email_enabled')->default(true);
            $table->boolean('app_enabled')->default(true);
            $table->string('frequency')->default('immediate');
            $table->timestamps();

            $table->index('user_id');
            $table->index('team_id');
            $table->index('event_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
