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
        Schema::create('mail_queue', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers');
            $table->foreignUuid('contact_id')->nullable()->constrained('contacts');
            $table->foreignUuid('template_id')->nullable()->constrained('mail_templates');
            $table->foreignUuid('offer_id')->nullable()->constrained('offers');
            $table->string('subject');
            $table->text('content');
            $table->string('status')->default('DRAFT');
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_queue');
    }
};
