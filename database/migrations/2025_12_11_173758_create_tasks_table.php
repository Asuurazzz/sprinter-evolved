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
            $table->foreignUuid('board_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('stage_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('sprint_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('creator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('parent_task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->integer('task_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium');
            $table->string('status')->default('active');
            $table->integer('story_points')->nullable();
            $table->integer('estimated_minutes')->nullable();
            $table->integer('tracked_minutes')->default(0);
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('position')->default(0);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable();
            $table->json('recurrence_config')->nullable();
            $table->date('recurrence_start_date')->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->boolean('has_active_blocker')->default(false);
            $table->text('blocker_reason')->nullable();
            $table->timestamp('blocker_created_at')->nullable();
            $table->foreignUuid('last_modified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('board_id');
            $table->index('stage_id');
            $table->index(['project_id', 'sprint_id']);
            $table->index('creator_id');
            $table->index('task_number');
            $table->index('priority');
            $table->index('status');
            $table->index('due_date');
            $table->index('has_active_blocker');
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
