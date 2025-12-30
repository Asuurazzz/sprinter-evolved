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
        Schema::create('automations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('board_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('trigger_type');
            $table->json('trigger_config')->nullable();
            $table->json('conditions')->nullable();
            $table->json('actions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('creator_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('team_id');
            $table->index('board_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automations');
    }
};
