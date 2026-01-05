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
        Schema::create('offer_download_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->timestamp('downloaded_at')->useCurrent();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_read_log')->default(false);

            $table->index('offer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_download_logs');
    }
};
