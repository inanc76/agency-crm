<?php

namespace App\Livewire\Settings\Traits;

use App\Models\PriceDefinition;
use Illuminate\Support\Facades\DB;

use Mary\Traits\Toast;

trait HasPriceSettings
{
    use Toast;
    // Filters & Search
    public string $search = '';
    public string $filterCategory = '';
    public string $filterDuration = '';

    // Modal State
    public bool $showModal = false;
    public ?string $selectedId = null;

    // Form State
    public string $name = '';
    public string $category = '';
    public string $duration = '';
    public float $price = 0;
    public string $currency = 'TRY';
    public string $description = '';
    public bool $is_active = true;

    public function openCreatePriceModal(): void
    {
        $this->resetPriceForm();
        $this->showModal = true;
    }

    public function editPrice(string $id): void
    {
        $price = PriceDefinition::findOrFail($id);
        $this->selectedId = $price->id;
        $this->name = $price->name;
        $this->category = $price->category;
        $this->duration = $price->duration;
        $this->price = $price->price;
        $this->currency = $price->currency;
        $this->description = $price->description ?? '';
        $this->is_active = $price->is_active;

        $this->showModal = true;
    }

    public function savePrice(): void
    {
        $this->validate($this->priceRules());

        try {
            DB::transaction(function () {
                $data = [
                    'name' => $this->name,
                    'category' => $this->category,
                    'duration' => $this->duration,
                    'price' => $this->price,
                    'currency' => $this->currency,
                    'description' => $this->description,
                    'is_active' => $this->is_active,
                ];

                if ($this->selectedId) {
                    PriceDefinition::findOrFail($this->selectedId)->update($data);
                    $this->success('Fiyat tanımı güncellendi.');
                } else {
                    PriceDefinition::create($data);
                    $this->success('Yeni fiyat tanımı oluşturuldu.');
                }
            });

            $this->showModal = false;
            $this->resetPriceForm();
        } catch (\Exception $e) {
            $this->error('Bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function togglePriceStatus(string $id): void
    {
        $price = PriceDefinition::findOrFail($id);
        $price->update(['is_active' => !$price->is_active]);
        $this->success('Durum güncellendi.');
    }

    public function deletePrice(string $id): void
    {
        PriceDefinition::findOrFail($id)->delete();
        $this->success('Fiyat tanımı silindi.');
    }

    public function clearPriceFilters(): void
    {
        $this->reset(['search', 'filterCategory', 'filterDuration']);
    }

    protected function resetPriceForm(): void
    {
        $this->reset(['selectedId', 'name', 'category', 'duration', 'price', 'currency', 'description', 'is_active']);
        $this->currency = 'TRY';
        $this->is_active = true;
        $this->price = 0;
    }

    protected function priceRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'duration' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'description' => 'nullable|string',
        ];
    }
}
