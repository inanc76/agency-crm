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
        Schema::table('customer_relations', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->primary(['customer_id', 'related_customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_relations', function (Blueprint $table) {
            $table->dropPrimary(['customer_id', 'related_customer_id']);
            $table->uuid('id')->primary();
        });
    }
};
