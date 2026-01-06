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
            $table->string('header_icon_color')->default('#ffffff')->after('menu_text_color');
            $table->string('header_border_color')->default('transparent')->after('header_icon_color');
            $table->integer('header_border_width')->default(0)->after('header_border_color'); // in pixels
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn(['header_icon_color', 'header_border_color', 'header_border_width']);
        });
    }
};
