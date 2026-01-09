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
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->string('page_bg_color')->default('#f8fafc')->after('font_family');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn('page_bg_color');
        });
    }
};
