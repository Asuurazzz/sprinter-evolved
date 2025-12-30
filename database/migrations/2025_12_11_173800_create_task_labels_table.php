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
        Schema::create('task_labels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('label_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['task_id', 'label_id']);
            $table->index('label_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_labels');
    }
};
