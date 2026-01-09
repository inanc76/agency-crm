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
            // Sidebar Settings
            $table->string('sidebar_bg_color')->default('#3D3373')->nullable()->after('menu_text_color');
            $table->string('sidebar_text_color')->default('#ffffff')->nullable()->after('sidebar_bg_color');
            $table->string('sidebar_hover_bg_color')->default('#4338ca')->nullable()->after('sidebar_text_color');
            $table->string('sidebar_hover_text_color')->default('#ffffff')->nullable()->after('sidebar_hover_bg_color');
            $table->string('sidebar_active_item_bg_color')->default('#4f46e5')->nullable()->after('sidebar_hover_text_color');
            $table->string('sidebar_active_item_text_color')->default('#ffffff')->nullable()->after('sidebar_active_item_bg_color');

            // Header Active Items
            $table->string('header_active_item_bg_color')->default('#ffffff')->nullable()->after('header_icon_color');
            $table->string('header_active_item_text_color')->default('#4f46e5')->nullable()->after('header_active_item_bg_color');

            // Dashboard Colors
            $table->string('dashboard_card_bg_color')->default('#eff4ff')->nullable()->after('card_border_radius'); // Defaulting to existing card_bg_color logic if needed but user asked specific
            $table->string('dashboard_card_text_color')->default('#475569')->nullable()->after('dashboard_card_bg_color');

            $table->string('dashboard_stats_1_color')->default('#3b82f6')->nullable()->after('dashboard_card_text_color'); // Blue
            $table->string('dashboard_stats_2_color')->default('#14b8a6')->nullable()->after('dashboard_stats_1_color'); // Teal
            $table->string('dashboard_stats_3_color')->default('#f59e0b')->nullable()->after('dashboard_stats_2_color'); // Amber

            // User Menu / Header Dropdown
            $table->string('avatar_gradient_start_color')->default('#c084fc')->nullable()->after('header_active_item_text_color');
            $table->string('avatar_gradient_end_color')->default('#9333ea')->nullable()->after('avatar_gradient_start_color');

            $table->string('dropdown_header_bg_start_color')->default('#f5f3ff')->nullable()->after('avatar_gradient_end_color');
            $table->string('dropdown_header_bg_end_color')->default('#eef2ff')->nullable()->after('dropdown_header_bg_start_color');

            $table->string('notification_badge_color')->default('#ef4444')->nullable()->after('dropdown_header_bg_end_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn([
                'sidebar_bg_color',
                'sidebar_text_color',
                'sidebar_hover_bg_color',
                'sidebar_hover_text_color',
                'sidebar_active_item_bg_color',
                'sidebar_active_item_text_color',
                'header_active_item_bg_color',
                'header_active_item_text_color',
                'dashboard_card_bg_color',
                'dashboard_card_text_color',
                'dashboard_stats_1_color',
                'dashboard_stats_2_color',
                'dashboard_stats_3_color',
                'avatar_gradient_start_color',
                'avatar_gradient_end_color',
                'dropdown_header_bg_start_color',
                'dropdown_header_bg_end_color',
                'notification_badge_color'
            ]);
        });
    }
};
