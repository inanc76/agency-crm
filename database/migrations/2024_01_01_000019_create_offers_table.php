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
        Schema::create('offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number')->unique();
            $table->foreignUuid('customer_id')->constrained('customers');
            $table->string('status')->default('DRAFT');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->double('total_amount');
            $table->double('original_amount')->nullable();
            $table->double('discount_percentage')->nullable()->default(0);
            $table->double('discounted_amount')->nullable();
            $table->string('currency');
            $table->timestamp('valid_until');
            $table->string('pdf_url')->nullable();
            $table->string('tracking_token')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
