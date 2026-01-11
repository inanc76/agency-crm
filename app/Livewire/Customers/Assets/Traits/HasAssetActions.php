<?php

namespace App\Livewire\Customers\Assets\Traits;

use App\Models\Asset;
use App\Models\Customer;
use Illuminate\Support\Str;

trait HasAssetActions
{
    // Varlık Bilgileri
    public string $customer_id = '';
    public string $name = '';
    public string $type = '';
    public string $url = '';

    // State Management
    public bool $isViewMode = false;
    public ?string $assetId = null;
    public string $activeTab = 'info';

    // Reference Data
    public $customers = [];
    public $assetTypes = [
        ['id' => 'WEBSITE', 'name' => 'Web Sitesi'],
        ['id' => 'SOCIAL_MEDIA', 'name' => 'Sosyal Medya'],
        ['id' => 'HOSTING', 'name' => 'Hosting'],
        ['id' => 'DOMAIN', 'name' => 'Domain'],
        ['id' => 'SERVER', 'name' => 'Sunucu'],
        ['id' => 'OTHER', 'name' => 'Diğer'],
    ];

    public function mount(?string $asset = null): void
    {
        // Load Customers
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // If asset ID is provided, load data
        if ($asset) {
            $this->assetId = $asset;
            $this->loadAssetData();

            // Set active tab from URL if present
            $this->activeTab = request()->query('tab', 'info');
        } else {
            // Check for customer query parameter
            $customerId = request()->query('customer');
            if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
                $this->customer_id = $customerId;
            }
        }
    }

    private function loadAssetData(): void
    {
        $asset = Asset::findOrFail($this->assetId);

        $this->customer_id = $asset->customer_id;
        $this->name = $asset->name;
        $this->type = $asset->type;
        $this->url = $asset->url ?? '';

        $this->isViewMode = true;
    }

    public function save(): void
    {
        $this->validate([
            'customer_id' => 'required',
            'name' => 'required|min:2',
            'type' => 'required',
        ]);

        $data = [
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'type' => $this->type,
            'url' => $this->url,
        ];

        if ($this->assetId) {
            $asset = Asset::findOrFail($this->assetId);
            $asset->update($data);
            $message = 'Varlık bilgileri güncellendi.';
        } else {
            $this->assetId = Str::uuid()->toString();
            $data['id'] = $this->assetId;
            Asset::create($data);
            $message = 'Yeni varlık başarıyla oluşturuldu.';
        }

        $this->success('İşlem Başarılı', $message);
        $this->isViewMode = true;

        // Dispatch event
        $this->dispatch('asset-saved');
    }

    public function cancel(): void
    {
        if ($this->assetId) {
            $this->loadAssetData();
        } else {
            $this->redirect('/dashboard/customers/' . $this->customer_id . '?tab=assets', navigate: true);
        }
    }

    public function toggleEditMode(): void
    {
        $this->isViewMode = false;
    }

    public function delete(): void
    {
        if ($this->assetId) {
            $asset = Asset::findOrFail($this->assetId);
            $customer_id = $asset->customer_id;
            $asset->delete();
            $this->success('Varlık Silindi', 'Varlık kaydı başarıyla silindi.');
            $this->redirect('/dashboard/customers/' . $customer_id . '?tab=assets');
        }
    }

    // Auto-prefix URL with https://
    public function updatedUrl()
    {
        $val = trim($this->url);
        if ($val && !preg_match('/^https?:\/\//', $val)) {
            $this->url = 'https://' . $val;
        }
    }
}
