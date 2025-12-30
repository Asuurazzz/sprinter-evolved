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
        Schema::create('reactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reactable_type');
            $table->uuid('reactable_id');
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('emoji');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['reactable_type', 'reactable_id']);
            $table->index('user_id');
            $table->unique(['reactable_type', 'reactable_id', 'user_id', 'emoji']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
