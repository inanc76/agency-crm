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
        Schema::create('storage_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider')->default('MINIO');
            $table->string('endpoint');
            $table->integer('port')->default(443);
            $table->boolean('use_ssl')->default(true);
            $table->string('access_key');
            $table->string('secret_key');
            $table->string('bucket_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_settings');
    }
};
