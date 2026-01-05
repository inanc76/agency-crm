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
        Schema::create('price_definitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('category');
            $table->string('duration');
            $table->double('price');
            $table->string('currency')->default('TRY');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_definitions');
    }
};
