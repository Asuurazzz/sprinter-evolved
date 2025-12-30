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
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('checklist_group_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->boolean('is_completed')->default(false);
            $table->foreignUuid('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['checklist_group_id', 'position']);
            $table->index('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
