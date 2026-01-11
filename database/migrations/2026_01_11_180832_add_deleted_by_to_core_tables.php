<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: Blameable Delete Migration                                                                ║
 * ║  🎯 ANA GÖREV: Temel tablolara deleted_by kolonu ekleyerek silen kullanıcıyı izleme                             ║
 * ║                                                                                                                  ║
 * ║  🔧 ETKİLENEN TABLOLAR:                                                                                         ║
 * ║  • users, customers, contacts, assets, services, offers, offer_items                                            ║
 * ║                                                                                                                  ║
 * ║  📊 İŞ MANTIĞI:                                                                                                 ║
 * ║  • deleted_by: SoftDelete yapıldığında, silme işlemini yapan kullanıcının UUID'si                               ║
 * ║  • HasBlameable trait ile otomatik doldurulur                                                                   ║
 * ║  • Audit trail ve veri güvenliği için kritik                                                                    ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
return new class extends Migration {
    /**
     * Tablolara deleted_by kolonu ekle
     */
    public function up(): void
    {
        $tables = ['users', 'customers', 'contacts', 'assets', 'services', 'offers', 'offer_items'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_by')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->uuid('deleted_by')->nullable()->after('deleted_at');
                    $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
                });
            }
        }
    }

    /**
     * deleted_by kolonlarını kaldır
     */
    public function down(): void
    {
        $tables = ['users', 'customers', 'contacts', 'assets', 'services', 'offers', 'offer_items'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_by')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    // Drop foreign key first
                    $blueprint->dropForeign([$table . '_deleted_by_foreign']);
                    $blueprint->dropColumn('deleted_by');
                });
            }
        }
    }
};
