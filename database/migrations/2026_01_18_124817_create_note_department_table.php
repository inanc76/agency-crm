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
        Schema::create('note_department', function (Blueprint $table) {
            $table->foreignUuid('note_id')->constrained('notes')->onDelete('cascade');
            // department_id points to reference_items(id)
            $table->foreignUuid('department_id')->constrained('reference_items')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['note_id', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_department');
    }
};
