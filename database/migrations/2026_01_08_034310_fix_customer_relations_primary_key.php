<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite doesn't support dropping primary keys, so we need to recreate the table
            Schema::dropIfExists('customer_relations');
            Schema::create('customer_relations', function (Blueprint $table) {
                $table->uuid('customer_id');
                $table->uuid('related_customer_id');
                $table->string('relation_type')->nullable();
                $table->timestamps();

                $table->primary(['customer_id', 'related_customer_id']);
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                $table->foreign('related_customer_id')->references('id')->on('customers')->onDelete('cascade');
            });
        } else {
            // PostgreSQL and MySQL support dropping columns
            Schema::table('customer_relations', function (Blueprint $table) {
                $table->dropColumn('id');
                $table->primary(['customer_id', 'related_customer_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // Recreate with id column for SQLite
            Schema::dropIfExists('customer_relations');
            Schema::create('customer_relations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('customer_id');
                $table->uuid('related_customer_id');
                $table->string('relation_type')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                $table->foreign('related_customer_id')->references('id')->on('customers')->onDelete('cascade');
            });
        } else {
            Schema::table('customer_relations', function (Blueprint $table) {
                $table->dropPrimary(['customer_id', 'related_customer_id']);
                $table->uuid('id')->primary();
            });
        }
    }
};
