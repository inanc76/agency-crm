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
        Schema::table('offers', function (Blueprint $table) {
            $table->decimal('vat_rate', 5, 2)->default(0)->after('discounted_amount');
            $table->decimal('vat_amount', 15, 2)->default(0)->after('vat_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['vat_rate', 'vat_amount']);
        });
    }
};
