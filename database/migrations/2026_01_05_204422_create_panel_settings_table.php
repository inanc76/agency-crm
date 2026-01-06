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
        Schema::create('panel_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('site_name')->default('MEDIACLICK');
            $table->string('favicon_path')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('header_bg_color')->default('#3D3373');
            $table->string('menu_bg_color')->default('rgba(255, 255, 255, 0.1)');
            $table->string('menu_text_color')->default('#ffffff');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panel_settings');
    }
};
