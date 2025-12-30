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
        Schema::create('checklist_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['task_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_groups');
    }
};
