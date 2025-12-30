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
        Schema::create('knowledge_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('content_id')->constrained('knowledge_contents')->cascadeOnDelete();
            $table->foreignUuid('author_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('content_id');
            $table->index('author_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_comments');
    }
};
