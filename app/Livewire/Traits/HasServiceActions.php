<?php

/**
 * 🛡️ ZIRHLI MODÜL: Service Management
 * ---------------------------------------------------------
 * DURUM: %100 Test Pass (Pest)
 * YETKİ: [services.create, services.edit, services.delete]
 * TEST: tests/Feature/Customers/ServiceCreateTest.php
 * MİMARİ: Volt + Trait + Bulk Insert (Performance)
 * ---------------------------------------------------------
 */

namespace App\Livewire\Traits;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait HasServiceActions
{
    /**
     * Save service (create or update)
     * Uses DB Transaction and Bulk Insert for optimal performance.
     * 
     * @return void
     */
    public function save(): void
    {
        $this->validate([
            'customer_id' => 'required',
            'asset_id' => 'required',
            'start_date' => 'required|date',
            'services.*.category' => 'required',
            'services.*.service_name' => 'required',
        ]);

        $startDate = Carbon::parse($this->start_date);

        if ($this->serviceId) {
            if (!auth()->user()->can('services.edit')) {
                abort(403, 'Bu işlem için yetkiniz yok.');
            }
            // Edit mode - update single service
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
            $this->isViewMode = true;
        } else {
            if (!auth()->user()->can('services.create')) {
                abort(403, 'Bu işlem için yetkiniz yok.');
            }
            // Create mode - create multiple services with BULK INSERT
            DB::transaction(function () use ($startDate) {
                $servicesToInsert = [];

                foreach ($this->services as $serviceData) {
                    $endDate = $this->calculateEndDate($startDate, $serviceData['service_duration']);

                    $servicesToInsert[] = [
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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // 🚀 BULK INSERT: Single query for all services
                Service::insert($servicesToInsert);
            });

            $count = count($this->services);
            $this->success('İşlem Başarılı', "{$count} adet hizmet başarıyla oluşturuldu.");
            $this->redirect('/dashboard/customers?tab=services');
        }
    }

    /**
     * Toggle edit mode
     * 🔐 AUTHORIZATION: services.edit permission required
     */
    public function toggleEditMode(): void
    {
        // Authorization Check
        if (!auth()->user()->can('services.edit')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $this->isViewMode = false;
    }

    /**
     * Cancel edit mode
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
     * Delete service
     * 
     * @return void
     */
    public function delete(): void
    {
        // Authorization Check
        if (!auth()->user()->can('services.delete')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        if ($this->serviceId) {
            $service = Service::findOrFail($this->serviceId);
            $customer_id = $service->customer_id;
            $service->delete();
            $this->success('Hizmet Silindi', 'Hizmet kaydı başarıyla silindi.');
            $this->redirect('/dashboard/customers/' . $customer_id . '?tab=services');
        }
    }

    /**
     * Add service to array
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
     * Remove service from array
     */
    public function removeService(int $index): void
    {
        if (count($this->services) > 1) {
            unset($this->services[$index]);
            $this->services = array_values($this->services);
        }
    }

    /**
     * Calculate end date based on duration
     */
    private function calculateEndDate(Carbon $startDate, string $duration): Carbon
    {
        $endDate = $startDate->copy()->addYear(); // Default fallback

        if (str_contains(strtolower($duration), 'month')) {
            $endDate = $startDate->copy()->addMonth();
        }

        return $endDate;
    }
}
