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
            // Ekle (Create)
            $table->string('btn_create_bg_color')->default('#4f46e5'); // Indigo
            $table->string('btn_create_text_color')->default('#ffffff');
            $table->string('btn_create_hover_color')->default('#4338ca');

            // Düzenle (Edit)
            $table->string('btn_edit_bg_color')->default('#f59e0b'); // Amber/Orange
            $table->string('btn_edit_text_color')->default('#ffffff');
            $table->string('btn_edit_hover_color')->default('#d97706');

            // Sil (Delete)
            $table->string('btn_delete_bg_color')->default('#ef4444'); // Red
            $table->string('btn_delete_text_color')->default('#ffffff');
            $table->string('btn_delete_hover_color')->default('#dc2626');

            // İptal (Cancel)
            $table->string('btn_cancel_bg_color')->default('#94a3b8'); // Slate
            $table->string('btn_cancel_text_color')->default('#ffffff');
            $table->string('btn_cancel_hover_color')->default('#64748b');

            // Kaydet (Save)
            $table->string('btn_save_bg_color')->default('#10b981'); // Emerald
            $table->string('btn_save_text_color')->default('#ffffff');
            $table->string('btn_save_hover_color')->default('#059669');

            // Drop old generic buttons
            $table->dropColumn([
                'primary_button_bg_color',
                'primary_button_text_color',
                'primary_button_hover_color',
                'secondary_button_border_color',
                'secondary_button_text_color',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->string('primary_button_bg_color')->nullable();
            $table->string('primary_button_text_color')->nullable();
            $table->string('primary_button_hover_color')->nullable();
            $table->string('secondary_button_border_color')->nullable();
            $table->string('secondary_button_text_color')->nullable();

            $table->dropColumn([
                'btn_create_bg_color',
                'btn_create_text_color',
                'btn_create_hover_color',
                'btn_edit_bg_color',
                'btn_edit_text_color',
                'btn_edit_hover_color',
                'btn_delete_bg_color',
                'btn_delete_text_color',
                'btn_delete_hover_color',
                'btn_cancel_bg_color',
                'btn_cancel_text_color',
                'btn_cancel_hover_color',
                'btn_save_bg_color',
                'btn_save_text_color',
                'btn_save_hover_color',
            ]);
        });
    }
};
