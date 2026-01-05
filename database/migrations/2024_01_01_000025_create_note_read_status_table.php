<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('note_read_status', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('note_id')->constrained('notes')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('read_at')->useCurrent();

            $table->unique(['note_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_read_status');
    }
};
