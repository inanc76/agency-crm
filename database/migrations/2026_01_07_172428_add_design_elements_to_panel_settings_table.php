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
            // Typography
            $table->string('font_family')->default('Inter')->nullable();
            $table->string('base_text_color')->default('#475569')->nullable(); // slate-600
            $table->string('heading_color')->default('#0f172a')->nullable(); // slate-900

            // Input & Validation
            $table->string('input_focus_ring_color')->default('#6366f1')->nullable(); // indigo-500
            $table->string('input_border_color')->default('#cbd5e1')->nullable(); // slate-300
            $table->string('input_error_ring_color')->default('#ef4444')->nullable(); // red-500
            $table->string('input_error_border_color')->default('#ef4444')->nullable();
            $table->string('input_error_text_color')->default('#ef4444')->nullable();
            $table->string('input_vertical_padding')->default('0.5rem')->nullable();
            $table->string('input_border_radius')->default('0.375rem')->nullable(); // rounded-md

            // Buttons
            $table->string('primary_button_bg_color')->default('#4f46e5')->nullable(); // indigo-600
            $table->string('primary_button_text_color')->default('#ffffff')->nullable();
            $table->string('primary_button_hover_color')->default('#4338ca')->nullable(); // indigo-700

            $table->string('secondary_button_border_color')->default('#e2e8f0')->nullable(); // slate-200
            $table->string('secondary_button_text_color')->default('#475569')->nullable(); // slate-600

            $table->string('action_link_color')->default('#4f46e5')->nullable(); // indigo-600

            // Cards
            $table->string('card_bg_color')->default('#eff4ff')->nullable();
            $table->string('card_border_color')->default('#bfdbfe')->nullable();
            $table->string('card_border_radius')->default('0.75rem')->nullable(); // rounded-xl
        });
    }

    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn([
                'font_family',
                'base_text_color',
                'heading_color',
                'input_focus_ring_color',
                'input_border_color',
                'input_error_ring_color',
                'input_error_border_color',
                'input_error_text_color',
                'input_vertical_padding',
                'input_border_radius',
                'primary_button_bg_color',
                'primary_button_text_color',
                'primary_button_hover_color',
                'secondary_button_border_color',
                'secondary_button_text_color',
                'action_link_color',
                'card_bg_color',
                'card_border_color',
                'card_border_radius'
            ]);
        });
    }
};
