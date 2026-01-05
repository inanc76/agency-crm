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
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider');
            $table->boolean('is_active')->default(false);
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->boolean('smtp_secure')->nullable();
            $table->string('smtp_from_email')->nullable();
            $table->string('smtp_from_name')->nullable();
            $table->string('mailgun_api_key')->nullable();
            $table->string('mailgun_domain')->nullable();
            $table->string('mailgun_from_email')->nullable();
            $table->string('mailgun_from_name')->nullable();
            $table->string('mailgun_region')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_settings');
    }
};
