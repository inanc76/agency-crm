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
            // PDF Header
            if (! Schema::hasColumn('panel_settings', 'pdf_logo_path')) {
                $table->string('pdf_logo_path')->nullable();
            }
            if (! Schema::hasColumn('panel_settings', 'pdf_header_bg_color')) {
                $table->string('pdf_header_bg_color')->default('#4f46e5');
            }
            if (! Schema::hasColumn('panel_settings', 'pdf_header_text_color')) {
                $table->string('pdf_header_text_color')->default('#ffffff');
            }

            // PDF Content
            if (! Schema::hasColumn('panel_settings', 'pdf_font_family')) {
                $table->string('pdf_font_family')->default('Segoe UI');
            }
            if (! Schema::hasColumn('panel_settings', 'pdf_primary_color')) {
                $table->string('pdf_primary_color')->default('#4f46e5');
            }
            if (! Schema::hasColumn('panel_settings', 'pdf_secondary_color')) {
                $table->string('pdf_secondary_color')->default('#6b7280');
            }

            // Footer - Change to text if exists, or add
            if (Schema::hasColumn('panel_settings', 'pdf_footer_text')) {
                // We can't easily change type in SQLite/some drivers without installing doctrine/dbal
                // Assuming pgsql based on previous error
                $table->text('pdf_footer_text')->nullable()->change();
            } else {
                $table->text('pdf_footer_text')->nullable();
            }

            // Specific Colors
            if (! Schema::hasColumn('panel_settings', 'pdf_discount_color')) {
                $table->string('pdf_discount_color')->default('#16a34a');
            }
            if (! Schema::hasColumn('panel_settings', 'pdf_total_color')) {
                $table->string('pdf_total_color')->default('#4f46e5');
            }

            // Table Colors
            if (! Schema::hasColumn('panel_settings', 'pdf_table_header_bg_color')) {
                $table->string('pdf_table_header_bg_color')->default('#f9fafb');
            }
            if (! Schema::hasColumn('panel_settings', 'pdf_table_header_text_color')) {
                $table->string('pdf_table_header_text_color')->default('#6b7280');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping columns safely
        Schema::table('panel_settings', function (Blueprint $table) {
            $columns = [
                'pdf_logo_path',
                'pdf_header_bg_color',
                'pdf_header_text_color',
                'pdf_font_family',
                'pdf_discount_color',
                'pdf_total_color',
                'pdf_table_header_bg_color',
                'pdf_table_header_text_color',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('panel_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
