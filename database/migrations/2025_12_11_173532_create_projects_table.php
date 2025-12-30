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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->string('status')->default('planning');
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->decimal('progress', 5, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('team_id');
            $table->index('status');
            $table->index('planned_start_date');
            $table->index('planned_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
