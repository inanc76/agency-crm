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
            $table->string('table_avatar_bg_color')->default('#f1f5f9')->nullable();
            $table->string('table_avatar_border_color')->default('#e2e8f0')->nullable();
            $table->string('table_avatar_text_color')->default('#475569')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn(['table_avatar_bg_color', 'table_avatar_border_color', 'table_avatar_text_color']);
        });
    }
};
