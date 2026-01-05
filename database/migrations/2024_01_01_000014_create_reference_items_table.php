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
        Schema::create('reference_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category_key');
            $table->string('key');
            $table->string('display_label');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->foreign('category_key')->references('key')->on('reference_categories');
            $table->unique(['category_key', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_items');
    }
};
