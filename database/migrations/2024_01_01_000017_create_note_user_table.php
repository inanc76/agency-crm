<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * note_user pivot table: Notların görünürlük kontrolü için
     * Bir not, birden fazla kullanıcı tarafından görülebilir.
     */
    public function up(): void
    {
        Schema::create('note_user', function (Blueprint $table) {
            $table->foreignUuid('note_id')->constrained('notes')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['note_id', 'user_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_user');
    }
};
