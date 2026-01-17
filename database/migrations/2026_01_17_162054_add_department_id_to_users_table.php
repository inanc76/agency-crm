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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('department_id')->nullable()->after('role_id');

            // Assuming reference_items table stores departments
            // We won't enforce a strict foreign key here if ReferenceItem uses a different ID strategy or to allow soft flexibility, 
            // but usually it points to 'id' of reference_items.
            // Let's add a loose foreign key or just the column for now as Reference data can be complex.
            // Based on user request "ReferenceData variables DEPARTMENT", it implies ReferenceItem.
            $table->foreign('department_id')->references('id')->on('reference_items')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
