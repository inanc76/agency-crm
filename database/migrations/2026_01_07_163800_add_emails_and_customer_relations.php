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
        // Add emails JSONB field to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->jsonb('emails')->nullable()->after('email');
        });

        // Create customer_relations pivot table for related companies
        Schema::create('customer_relations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignUuid('related_customer_id')->constrained('customers')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['customer_id', 'related_customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_relations');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('emails');
        });
    }
};
