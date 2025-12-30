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
        Schema::create('sprints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('objective')->nullable();
            $table->string('status')->default('planning');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('goal_points')->nullable();
            $table->integer('goal_tasks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('team_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprints');
    }
};
