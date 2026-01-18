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
        Schema::table('offer_download_logs', function (Blueprint $table) {
            $table->string('downloader_email')->nullable()->after('offer_id');
            $table->string('file_name')->nullable()->after('downloaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_download_logs', function (Blueprint $table) {
            $table->dropColumn(['downloader_email', 'file_name']);
        });
    }
};
