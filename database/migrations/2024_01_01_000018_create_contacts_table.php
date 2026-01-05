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
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->jsonb('emails')->nullable(); // Prisma (String[])
            $table->string('phone')->nullable();
            $table->jsonb('phones')->nullable(); // Prisma (String[])
            $table->string('position')->nullable();
            $table->string('status')->nullable()->default('WORKING');
            $table->string('gender')->nullable();
            $table->timestamp('birth_date')->nullable();
            $table->jsonb('social_profiles')->nullable();
            $table->jsonb('extensions')->nullable(); // Prisma (String[])
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
