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
            $table->string('btn_create_border_color')->nullable()->after('btn_create_hover_color');
            $table->string('btn_edit_border_color')->nullable()->after('btn_edit_hover_color');
            $table->string('btn_delete_border_color')->nullable()->after('btn_delete_hover_color');
            $table->string('btn_cancel_border_color')->nullable()->after('btn_cancel_hover_color');
            $table->string('btn_save_border_color')->nullable()->after('btn_save_hover_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn([
                'btn_create_border_color',
                'btn_edit_border_color',
                'btn_delete_border_color',
                'btn_cancel_border_color',
                'btn_save_border_color',
            ]);
        });
    }
};
