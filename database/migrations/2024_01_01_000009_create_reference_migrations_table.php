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
        Schema::create('reference_migrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('migration_type');
            $table->string('source_identifier');
            $table->string('target_category_key');
            $table->integer('items_migrated')->default(0);
            $table->integer('references_updated')->default(0);
            $table->jsonb('rollback_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_migrations');
    }
};
