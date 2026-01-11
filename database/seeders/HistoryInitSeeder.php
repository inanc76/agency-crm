<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Note;
use App\Models\Offer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: History Init Seeder                                                                       ║
 * ║  🎯 ANA GÖREV: Mevcut kayıtlar için başlangıç notları oluşturma (Polymorphic Note sistemi)                      ║
 * ║                                                                                                                  ║
 * ║  🔧 ETKİLENEN MODELLER:                                                                                         ║
 * ║  • Customer, Contact, Asset, Service, Offer                                                                     ║
 * ║                                                                                                                  ║
 * ║  📊 ÇALIŞMA MANTIĞI:                                                                                            ║
 * ║  • Her kayıt için created_at tarihiyle "Kayıt oluşturuldu (Migration)" notu oluşturulur                         ║
 * ║  • Zaten notu olan kayıtlar atlanır (idempotent)                                                                ║
 * ║  • author_id olarak sistemdeki ilk kullanıcı kullanılır                                                         ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK: Bu seeder manuel çalıştırılmalıdır (DatabaseSeeder'a eklenmedi)                                   ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
class HistoryInitSeeder extends Seeder
{
    /**
     * Mevcut kayıtlar için başlangıç notları oluştur
     */
    public function run(): void
    {
        $author = User::first();

        if (!$author) {
            $this->command->error('❌ Sistemde kullanıcı bulunamadı. Önce bir kullanıcı oluşturun.');
            return;
        }

        $this->command->info('🚀 History Init Seeder başlatılıyor...');
        $this->command->info("📝 Author: {$author->name} ({$author->email})");

        $stats = [
            'customers' => 0,
            'contacts' => 0,
            'assets' => 0,
            'services' => 0,
            'offers' => 0,
            'skipped' => 0,
        ];

        // Customers
        Customer::all()->each(function ($record) use ($author, &$stats) {
            if ($this->createHistoryNote($record, 'App\Models\Customer', $author)) {
                $stats['customers']++;
            } else {
                $stats['skipped']++;
            }
        });

        // Contacts
        Contact::all()->each(function ($record) use ($author, &$stats) {
            if ($this->createHistoryNote($record, 'App\Models\Contact', $author)) {
                $stats['contacts']++;
            } else {
                $stats['skipped']++;
            }
        });

        // Assets
        Asset::all()->each(function ($record) use ($author, &$stats) {
            if ($this->createHistoryNote($record, 'App\Models\Asset', $author)) {
                $stats['assets']++;
            } else {
                $stats['skipped']++;
            }
        });

        // Services
        Service::all()->each(function ($record) use ($author, &$stats) {
            if ($this->createHistoryNote($record, 'App\Models\Service', $author)) {
                $stats['services']++;
            } else {
                $stats['skipped']++;
            }
        });

        // Offers
        Offer::all()->each(function ($record) use ($author, &$stats) {
            if ($this->createHistoryNote($record, 'App\Models\Offer', $author)) {
                $stats['offers']++;
            } else {
                $stats['skipped']++;
            }
        });

        // Özet Rapor
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('📊 HISTORY INIT SEEDER RAPORU');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->line("  Customers:  {$stats['customers']} not oluşturuldu");
        $this->command->line("  Contacts:   {$stats['contacts']} not oluşturuldu");
        $this->command->line("  Assets:     {$stats['assets']} not oluşturuldu");
        $this->command->line("  Services:   {$stats['services']} not oluşturuldu");
        $this->command->line("  Offers:     {$stats['offers']} not oluşturuldu");
        $this->command->line("  Atlandı:    {$stats['skipped']} (zaten notu var)");
        $this->command->info('═══════════════════════════════════════════');

        $total = array_sum($stats) - $stats['skipped'];
        $this->command->info("✅ Toplam {$total} başlangıç notu oluşturuldu.");
    }

    /**
     * Bir kayıt için başlangıç notu oluştur
     */
    private function createHistoryNote($record, string $entityType, User $author): bool
    {
        // Zaten notu var mı kontrol et
        $exists = Note::where('entity_type', $entityType)
            ->where('entity_id', $record->id)
            ->exists();

        if ($exists) {
            return false;
        }

        // Model adından readable isim oluştur
        $modelName = class_basename($entityType);
        $readableNames = [
            'Customer' => 'Müşteri',
            'Contact' => 'Kişi',
            'Asset' => 'Varlık',
            'Service' => 'Hizmet',
            'Offer' => 'Teklif',
        ];
        $readableName = $readableNames[$modelName] ?? $modelName;

        // Not oluştur
        Note::create([
            'id' => Str::uuid()->toString(),
            'entity_type' => $entityType,
            'entity_id' => $record->id,
            'content' => "{$readableName} kaydı oluşturuldu. (Sistem Migration)",
            'author_id' => $author->id,
            'created_at' => $record->created_at,
            'updated_at' => $record->created_at,
        ]);

        return true;
    }
}
