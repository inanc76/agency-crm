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
            $table->string('table_header_bg_color')->nullable()->default('#f8fafc');
            $table->string('table_header_text_color')->nullable()->default('#1e293b');
            $table->string('table_divide_color')->nullable()->default('#f1f5f9');
            $table->string('table_item_name_size')->nullable()->default('13px');
            $table->string('table_item_name_weight')->nullable()->default('500');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_settings', function (Blueprint $table) {
            $table->dropColumn([
                'table_header_bg_color',
                'table_header_text_color',
                'table_divide_color',
                'table_item_name_size',
                'table_item_name_weight',
            ]);
        });
    }
};
