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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->jsonb('phones')->nullable(); // Prisma (String[])
            $table->text('address')->nullable();
            $table->foreignUuid('city_id')->nullable()->constrained('cities');
            $table->foreignUuid('country_id')->nullable()->constrained('countries');
            $table->string('tax_number')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('website')->nullable();
            $table->jsonb('websites')->nullable(); // Prisma (String[])
            $table->string('current_code')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('customer_type')->default('POTENTIAL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
