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
        Schema::table('messages', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->after('body');
            $table->string('recipient_email')->nullable()->after('recipient_name');
            $table->foreignUuid('contact_id')->nullable()->after('recipient_email')->constrained('contacts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
            $table->dropColumn(['recipient_name', 'recipient_email', 'contact_id']);
        });
    }
};
