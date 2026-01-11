<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: SoftDeletes Migration                                                                     ║
 * ║  🎯 ANA GÖREV: Temel tablolara deleted_at kolonu ekleyerek soft delete desteği sağlamak                          ║
 * ║                                                                                                                  ║
 * ║  🔧 ETKİLENEN TABLOLAR:                                                                                         ║
 * ║  • users: Kullanıcı kayıtları                                                                                   ║
 * ║  • customers: Müşteri kayıtları                                                                                 ║
 * ║  • contacts: Kişi kayıtları                                                                                     ║
 * ║  • assets: Varlık kayıtları                                                                                     ║
 * ║  • services: Hizmet kayıtları                                                                                   ║
 * ║  • offers: Teklif kayıtları                                                                                     ║
 * ║  • offer_items: Teklif kalemi kayıtları                                                                         ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK: Kalıcı silme yerine soft delete ile veri güvenliği                                                ║
 * ║  📊 ROLLBACK: down() metodu ile geri alınabilir                                                                 ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
return new class extends Migration {
    /**
     * Tablolara deleted_at kolonu ekle
     */
    public function up(): void
    {
        $tables = ['users', 'customers', 'contacts', 'assets', 'services', 'offers', 'offer_items'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * deleted_at kolonlarını kaldır
     */
    public function down(): void
    {
        $tables = ['users', 'customers', 'contacts', 'assets', 'services', 'offers', 'offer_items'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
