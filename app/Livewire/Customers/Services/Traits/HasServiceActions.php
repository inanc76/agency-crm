<?php

namespace App\Livewire\Customers\Services\Traits;

use App\Livewire\Traits\HasServiceCalculations;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * TRAIT      : HasServiceActions
 * SORUMLULUK : Müşteri hizmetlerinin (Service) CRUD operasyonlarını, toplu
 *              hizmet ekleme ve proje entegrasyon süreçlerini yönetir.
 *
 * BAĞIMLILIKLAR:
 * - App\Livewire\Traits\HasServiceCalculations (Hesaplama ve Veri Yükleme)
 * - Mary\Traits\Toast (Bileşen seviyesinde)
 *
 * METODLAR:
 * - save(): Yeni hizmet(ler) oluşturur veya mevcut olanı günceller.
 * - cancel(): İşlemi durdurur ve geri yönlendirir.
 * - toggleEditMode(): Görüntüleme ve düzenleme modları arasında geçiş yapar.
 * - delete(): Hizmeti sistemden siler (Arşivleme).
 * - addService(): Forma yeni bir hizmet satırı ekler (Toplu kayıt için).
 * - removeService(): Formdan bir hizmet satırını çıkartır.
 * -------------------------------------------------------------------------
 */
trait HasServiceActions
{
    use HasServiceCalculations;

    /**
     * Hizmet verilerini valide eder ve veritabanına kaydeder.
     * İş Kuralı: Tekli güncelleme veya toplu oluşturma (Bulk Insert) destekler.
     */
    public function save(): void
    {
        // 🔐 Security: Yetki denetimi operasyon tipine göre yapılır
        $this->authorize($this->serviceId ? 'services.edit' : 'services.create');

        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'asset_id' => 'required|exists:assets,id',
            'start_date' => 'required|date',
            'services' => 'required|array|min:1',
            'services.*.category' => 'required|string',
            'services.*.service_name' => 'required|string|max:200',
            'services.*.service_price' => 'required|numeric|min:0',
            'services.*.service_currency' => 'required|string|size:3',
            'services.*.service_duration' => 'required|string',
            'services.*.status' => 'required|string',
        ]);

        $startDate = Carbon::parse($this->start_date);

        if ($this->serviceId) {
            $this->updateSingleService($startDate);
        } else {
            $this->createMultipleServices($startDate);
        }
    }

    /**
     * Tek bir hizmet kaydını günceller.
     * İş Kuralı: Bitiş tarihi süre tanımına göre otomatik hesaplanır.
     */
    private function updateSingleService(Carbon $startDate): void
    {
        $service = Service::findOrFail($this->serviceId);
        $endDate = $this->calculateEndDate($startDate, $this->services[0]['service_duration']);

        $service->update([
            'customer_id' => $this->customer_id,
            'asset_id' => $this->asset_id,
            'project_id' => $this->services[0]['project_id'] ?: null,
            'project_phase_id' => $this->services[0]['project_phase_id'] ?: null,
            'price_definition_id' => $this->services[0]['price_definition_id'],
            'service_name' => $this->services[0]['service_name'],
            'service_category' => $this->services[0]['category'],
            'service_duration' => $this->services[0]['service_duration'],
            'service_price' => $this->services[0]['service_price'],
            'service_currency' => $this->services[0]['service_currency'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'description' => $this->services[0]['description'],
            'status' => $this->services[0]['status'],
            'is_active' => $this->services[0]['status'] === 'ACTIVE',
        ]);

        $this->success('Başarılı', 'Hizmet güncellendi.');
        $this->dispatch('service-saved');
        $this->isViewMode = true;
    }

    /**
     * Birden fazla hizmet kaydını tek bir işlemde (Transaction) oluşturur.
     * Performans: Bulk insert ile DB yükü optimize edilmiştir.
     */
    private function createMultipleServices(Carbon $startDate): void
    {
        DB::transaction(function () use ($startDate) {
            $servicesToInsert = [];

            foreach ($this->services as $serviceData) {
                $endDate = $this->calculateEndDate($startDate, $serviceData['service_duration']);

                $servicesToInsert[] = [
                    'id' => Str::uuid()->toString(),
                    'customer_id' => $this->customer_id,
                    'asset_id' => $this->asset_id,
                    'project_id' => ($serviceData['project_id'] ?? '') ?: null,
                    'project_phase_id' => ($serviceData['project_phase_id'] ?? '') ?: null,
                    'price_definition_id' => $serviceData['price_definition_id'],
                    'service_name' => $serviceData['service_name'],
                    'service_category' => $serviceData['category'],
                    'service_duration' => $serviceData['service_duration'],
                    'service_price' => $serviceData['service_price'],
                    'service_currency' => $serviceData['service_currency'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'description' => $serviceData['description'],
                    'status' => $serviceData['status'],
                    'is_active' => $serviceData['status'] === 'ACTIVE',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Service::insert($servicesToInsert);
        });

        $this->success('Başarılı', count($this->services) . ' hizmet oluşturuldu.');
        $this->dispatch('service-saved');
        $this->redirect('/dashboard/customers?tab=services');
    }

    /**
     * Hizmet süresine göre (Ay/Yıl) bitiş tarihini hesaplar.
     */
    private function calculateEndDate(Carbon $startDate, string $duration): Carbon
    {
        return str_contains(strtolower($duration), 'month')
            ? $startDate->copy()->addMonth()
            : $startDate->copy()->addYear();
    }

    /**
     * İşlemi iptal eder ve ilgili sekmeye döner.
     */
    public function cancel(): void
    {
        if ($this->serviceId) {
            $this->loadServiceData();
        } else {
            $this->redirect('/dashboard/customers?tab=services', navigate: true);
        }
    }

    /**
     * Düzenleme moduna geçiş yetkisini kontrol eder.
     */
    public function toggleEditMode(): void
    {
        $this->authorize('services.edit');
        $this->isViewMode = false;
    }

    /**
     * Hizmet kaydını siler.
     */
    public function delete(): void
    {
        $this->authorize('services.delete');
        if ($this->serviceId) {
            Service::where('id', $this->serviceId)->delete();
            $this->success('Silindi', 'Hizmet arşive taşındı.');
            $this->redirect('/dashboard/customers?tab=services');
        }
    }

    /**
     * Forma yeni bir boş hizmet satırı ekler (Maksimum 5 satır).
     */
    public function addService(): void
    {
        if (count($this->services) < 5) {
            $this->services[] = [
                'category' => '',
                'service_name' => '',
                'price_definition_id' => '',
                'status' => !empty($this->serviceStatuses) ? $this->serviceStatuses[0]['key'] : 'ACTIVE',
                'service_price' => 0.0,
                'description' => '',
                'service_duration' => '',
                'service_currency' => 'TRY',
                'services_list' => [],
                'project_id' => '',
                'project_phase_id' => '',
                'phases_list' => [],
            ];
        }
    }

    /**
     * Formdaki bir hizmet satırını siler (En az 1 satır kalmalı).
     */
    public function removeService(int $index): void
    {
        if (count($this->services) > 1) {
            unset($this->services[$index]);
            $this->services = array_values($this->services);
        }
    }
}
