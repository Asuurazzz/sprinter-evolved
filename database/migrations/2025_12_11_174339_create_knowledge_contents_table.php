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
        Schema::create('knowledge_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained('knowledge_categories')->nullOnDelete();
            $table->foreignUuid('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('status')->default('draft');
            $table->json('tags')->nullable();
            $table->integer('version')->default(1);
            $table->foreignUuid('last_edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('team_id');
            $table->index('category_id');
            $table->index('author_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_contents');
    }
};
