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
            $table->integer('label_font_size')->default(14)->after('heading_color');
            $table->integer('input_font_size')->default(16)->after('label_font_size');
            $table->integer('heading_font_size')->default(18)->after('input_font_size');
            $table->integer('error_font_size')->default(12)->after('heading_font_size');
            $table->integer('helper_font_size')->default(12)->after('error_font_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn([
                'label_font_size',
                'input_font_size',
                'heading_font_size',
                'error_font_size',
                'helper_font_size'
            ]);
        });
    }
};
