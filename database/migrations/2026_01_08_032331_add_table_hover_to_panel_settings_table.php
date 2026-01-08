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
            $table->string('table_hover_bg_color')->default('#f8fafc')->after('card_border_radius');
            $table->string('table_hover_text_color')->default('#0f172a')->after('table_hover_bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn(['table_hover_bg_color', 'table_hover_text_color']);
        });
    }
};
