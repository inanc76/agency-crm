<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('panel_settings', 'dl_logo_path')) {
                $table->string('dl_logo_path')->nullable()->after('pdf_show_logo');
            }
            if (! Schema::hasColumn('panel_settings', 'dl_logo_height')) {
                $table->integer('dl_logo_height')->default(50)->after('dl_logo_path');
            }
            if (! Schema::hasColumn('panel_settings', 'dl_header_bg_color')) {
                $table->string('dl_header_bg_color')->default('#4f46e5')->after('dl_logo_height');
            }
            if (! Schema::hasColumn('panel_settings', 'dl_header_text_color')) {
                $table->string('dl_header_text_color')->default('#ffffff')->after('dl_header_bg_color');
            }
            if (! Schema::hasColumn('panel_settings', 'introduction_files')) {
                $table->json('introduction_files')->nullable()->after('dl_header_text_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn([
                'dl_logo_path',
                'dl_logo_height',
                'dl_header_bg_color',
                'dl_header_text_color',
                'introduction_files',
            ]);
        });
    }
};
