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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('chat_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('author_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('reply_to_id')->nullable()->constrained('chat_messages')->nullOnDelete();
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('chat_id');
            $table->index('author_id');
            $table->index('reply_to_id');
            $table->index('is_pinned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
