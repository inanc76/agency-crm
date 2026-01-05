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
        Schema::create('offer_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable()->default('Default Template');
            $table->string('header_bg_color')->default('#1e3a8a');
            $table->string('header_text_color')->default('#ffffff');
            $table->string('header_logo')->nullable();
            $table->integer('logo_height')->default(100);
            $table->string('customer_info_title_color')->default('#1e3a8a');
            $table->string('items_table_title_color')->default('#1e3a8a');
            $table->string('description_title_color')->default('#1e3a8a');
            $table->text('footer_content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_templates');
    }
};
