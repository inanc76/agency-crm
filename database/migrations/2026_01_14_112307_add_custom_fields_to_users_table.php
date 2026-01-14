<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 📋 Migration: Add custom_fields to users table
 * -----------------------------------------------
 * Purpose: Enable storing external user flags and other metadata
 *
 * Usage: {"is_external": true} for invited external users
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('custom_fields')->nullable()->after('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
