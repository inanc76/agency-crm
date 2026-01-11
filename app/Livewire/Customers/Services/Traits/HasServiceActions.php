<?php

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11 (SLIM)                                     ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasServiceActions Trait (Core CRUD Operations)                                            ║
 * ║  🎯 ANA GÖREV: Hizmet yaşam döngüsü yönetimi - Create, Update, Delete                                           ║
 * ║                                                                                                                  ║
 * ║  📦 TRAIT BAĞIMLILIKLARI (Composition):                                                                         ║
 * ║  • HasServiceDataLoader: Veri yükleme (mount, loadServiceData, loadAssets, watchers)                           ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • save(): Çoklu hizmet oluşturma veya tekli güncelleme (DB Transaction)                                       ║
 * ║  • cancel(): İptal ve yönlendirme                                                                               ║
 * ║  • toggleEditMode(): Görüntüleme ↔ Düzenleme modu                                                               ║
 * ║  • delete(): Kalıcı silme                                                                                       ║
 * ║  • addService/removeService: Servis array yönetimi                                                              ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK KATMANLARI:                                                                                        ║
 * ║  • services.create: Yeni hizmet oluşturma                                                                       ║
 * ║  • services.edit: Mevcut hizmet düzenleme                                                                       ║
 * ║  • services.delete: Hizmet silme                                                                                ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */

namespace App\Livewire\Customers\Services\Traits;

use App\Livewire\Traits\HasServiceCalculations;
use App\Models\Customer;
use App\Models\PriceDefinition;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasServiceActions
{
    use HasServiceCalculations; // 📊 Veri yükleme ve hesaplama trait'i

    // State Fields
    public string $customer_id = '';
    public string $asset_id = '';
    public ?string $start_date = null;
    public array $services = [];
    public bool $isViewMode = false;
    public ?string $serviceId = null;
    public string $activeTab = 'info';

    // Reference Data
    public $customers = [];
    public $assets = [];
    public $categories = [];

    /**
     * @purpose Livewire bileşeninin başlatılması
     * @param string|null $service Düzenlenecek hizmet ID'si
     * @return void
     * 🔐 Security: Genel erişim
     */
    public function mount(?string $service = null): void
    {
        // Load Customers
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // Load Categories from PriceDefinition
        $this->categories = PriceDefinition::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->toArray();

        // Default start date
        $this->start_date = Carbon::now()->format('Y-m-d');

        if ($service) {
            $this->serviceId = $service;
            $this->loadServiceData(); // From HasServiceCalculations
            $this->activeTab = request()->query('tab', 'info');
        } else {
            $customerId = request()->query('customer');
            if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
                $this->customer_id = $customerId;
                $this->loadAssets();
            }
            $this->addService();
        }
    }

    /**
     * @purpose Yeni boş hizmet satırı ekleme (max 5)
     * @return void
     */
    public function addService(): void
    {
        if (count($this->services) < 5) {
            $this->services[] = [
                'category' => '',
                'service_name' => '',
                'price_definition_id' => '',
                'status' => 'ACTIVE',
                'service_price' => 0.0,
                'description' => '',
                'service_duration' => '',
                'service_currency' => 'TRY',
                'services_list' => [],
            ];
        }
    }

    /**
     * @purpose Hizmet satırını kaldırma
     * @param int $index Kaldırılacak satır indeksi
     * @return void
     */
    public function removeService(int $index): void
    {
        if (count($this->services) > 1) {
            unset($this->services[$index]);
            $this->services = array_values($this->services);
        }
    }

    /**
     * @purpose Hizmet kaydetme (yeni oluşturma veya güncelleme)
     * @return void
     * 🔐 Security: services.create (new) or services.edit (existing)
     * 📢 Events: Success toast, 'service-saved' dispatch
     * 🔗 Side Effects: Bulk insert for new services, atomic transaction
     */
    public function save(): void
    {
        // 🔐 Security Check
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
            'services.*.status' => 'required|in:ACTIVE,PASSIVE,EXPIRED',
            'services.*.description' => 'nullable|string|max:1000',
        ]);

        $startDate = Carbon::parse($this->start_date);

        if ($this->serviceId) {
            $this->updateSingleService($startDate);
        } else {
            $this->createMultipleServices($startDate);
        }
    }

    /**
     * @purpose Tekli hizmet güncelleme
     * @param Carbon $startDate Başlangıç tarihi
     * @return void
     */
    private function updateSingleService(Carbon $startDate): void
    {
        $service = Service::findOrFail($this->serviceId);
        $endDate = $this->calculateEndDate($startDate, $this->services[0]['service_duration']);

        $service->update([
            'customer_id' => $this->customer_id,
            'asset_id' => $this->asset_id,
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

        $this->success('İşlem Başarılı', 'Hizmet bilgileri güncellendi.');
        $this->dispatch('service-saved');
        $this->isViewMode = true;
    }

    /**
     * @purpose Çoklu hizmet oluşturma (Bulk Insert)
     * @param Carbon $startDate Başlangıç tarihi
     * @return void
     * 🔗 Side Effects: DB Transaction, redirect on success
     */
    private function createMultipleServices(Carbon $startDate): void
    {
        DB::transaction(function () use ($startDate) {
            foreach ($this->services as $serviceData) {
                $endDate = $this->calculateEndDate($startDate, $serviceData['service_duration']);

                Service::create([
                    'id' => Str::uuid()->toString(),
                    'customer_id' => $this->customer_id,
                    'asset_id' => $this->asset_id,
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
                ]);
            }
        });

        $count = count($this->services);
        $this->success('İşlem Başarılı', "{$count} adet hizmet başarıyla oluşturuldu.");
        $this->dispatch('service-saved');
        $this->redirect('/dashboard/customers?tab=services');
    }

    /**
     * @purpose Bitiş tarihi hesaplama
     * @param Carbon $startDate Başlangıç tarihi
     * @param string $duration Süre string'i
     * @return Carbon Bitiş tarihi
     */
    private function calculateEndDate(Carbon $startDate, string $duration): Carbon
    {
        return str_contains(strtolower($duration), 'month')
            ? $startDate->copy()->addMonth()
            : $startDate->copy()->addYear();
    }

    /**
     * @purpose İptal işlemi
     * @return void
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
     * @purpose Düzenleme moduna geçiş
     * @return void
     * 🔐 Security: services.edit
     */
    public function toggleEditMode(): void
    {
        $this->authorize('services.edit');
        $this->isViewMode = false;
    }

    /**
     * @purpose Hizmeti silme
     * @return void
     * 🔐 Security: services.delete
     */
    public function delete(): void
    {
        $this->authorize('services.delete');

        if ($this->serviceId) {
            $service = Service::findOrFail($this->serviceId);
            $customer_id = $service->customer_id;
            $service->delete();
            $this->success('Hizmet Silindi', 'Hizmet kaydı başarıyla silindi.');
            $this->redirect('/dashboard/customers/' . $customer_id . '?tab=services');
        }
    }
}
