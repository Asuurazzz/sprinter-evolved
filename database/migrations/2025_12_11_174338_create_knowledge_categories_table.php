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
        Schema::create('knowledge_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable()->constrained('knowledge_categories')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('team_id');
            $table->index('parent_id');
            $table->index('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_categories');
    }
};
