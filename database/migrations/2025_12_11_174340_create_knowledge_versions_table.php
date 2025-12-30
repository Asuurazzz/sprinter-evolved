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
        Schema::create('knowledge_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('content_id')->constrained('knowledge_contents')->cascadeOnDelete();
            $table->foreignUuid('editor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->integer('version_number');
            $table->timestamp('created_at')->useCurrent();

            $table->index('content_id');
            $table->index('version_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_versions');
    }
};
