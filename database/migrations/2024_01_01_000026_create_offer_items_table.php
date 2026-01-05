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
        Schema::create('offer_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignUuid('service_id')->nullable()->constrained('services');
            $table->string('service_name');
            $table->text('description');
            $table->double('price');
            $table->string('currency');
            $table->integer('duration');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_items');
    }
};
