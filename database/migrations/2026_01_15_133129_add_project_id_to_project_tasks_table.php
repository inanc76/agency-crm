<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add project_id to project_tasks for direct project-based task assignment
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->foreignUuid('project_id')->nullable()->after('module_id')
                ->constrained('projects')->onDelete('cascade');

            // Make module_id nullable since tasks can now be project-based
            $table->uuid('module_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
