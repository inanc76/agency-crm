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
        Schema::table('project_modules', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->integer('estimated_hours')->nullable()->after('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_modules', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn('estimated_hours');
        });
    }
};
