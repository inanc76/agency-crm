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
        Schema::table('services', function (Blueprint $table) {
            $table->foreignUuid('project_id')->nullable()->after('asset_id')->constrained('projects')->nullOnDelete();
            $table->foreignUuid('project_phase_id')->nullable()->after('project_id')->constrained('project_phases')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['project_phase_id']);
            $table->dropColumn(['project_id', 'project_phase_id']);
        });
    }
};
