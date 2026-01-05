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
        Schema::create('cities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('name');
            $table->string('plate_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['country_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
