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
            $table->string('list_card_bg_color')->nullable()->default('#ffffff');
            $table->string('list_card_border_color')->nullable()->default('#e2e8f0');
            $table->string('list_card_link_color')->nullable()->default('#4f46e5');
            $table->string('list_card_hover_color')->nullable()->default('#f8fafc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn([
                'list_card_bg_color',
                'list_card_border_color',
                'list_card_link_color',
                'list_card_hover_color',
            ]);
        });
    }
};
