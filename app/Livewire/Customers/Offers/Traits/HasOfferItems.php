<?php

namespace App\Livewire\Customers\Offers\Traits;

use Illuminate\Support\Str;

trait HasOfferItems
{
    /**
     * @trait HasOfferItems
     * @purpose Hizmet kalemleri (items), manuel giriş ve kalem açıklamalarını yönetir.
     * @methods openManualEntryModal(), addManualItemRow(), removeManualItemRow(), saveManualItems(), updatedModalCategory(), addServiceFromExisting(), addServiceFromPriceDefinition(), removeItem(), openItemDescriptionModal(), saveItemDescription()
     */
    // Offer Items
    public $items = [];

    // Manual Entry Modal State
    public $showManualEntryModal = false;
    public $manualItems = [];

    // Item Description Modal State
    public $showItemDescriptionModal = false;
    public $editingItemIndex = null;
    public $itemDescriptionTemp = '';

    // Service Modal State (related vars)
    public $modalCategory = '';
    public $modalServiceName = '';

    public function openManualEntryModal(): void
    {
        $this->manualItems = [
            [
                'service_name' => '',
                'description' => '',
                'duration' => null,
                'price' => 0,
                'quantity' => 1
            ]
        ];
        $this->showManualEntryModal = true;
    }

    public function closeManualEntryModal(): void
    {
        $this->showManualEntryModal = false;
        $this->manualItems = [];
    }

    public function addManualItemRow(): void
    {
        $this->manualItems[] = [
            'service_name' => '',
            'description' => '',
            'duration' => null,
            'price' => 0,
            'quantity' => 1
        ];
    }

    public function removeManualItemRow(int $index): void
    {
        unset($this->manualItems[$index]);
        $this->manualItems = array_values($this->manualItems);
    }

    public function saveManualItems(): void
    {
        $this->validate([
            'manualItems.*.service_name' => 'required|string|max:255',
            'manualItems.*.description' => 'nullable|string',
            'manualItems.*.duration' => 'nullable|integer|min:1',
            'manualItems.*.price' => 'required|numeric|min:0',
            'manualItems.*.quantity' => 'required|integer|min:1',
        ], [
            'manualItems.*.service_name.required' => 'Hizmet adı zorunludur.',
            'manualItems.*.price.required' => 'Fiyat zorunludur.',
        ]);

        $count = count($this->manualItems);

        foreach ($this->manualItems as $item) {
            $this->items[] = [
                'service_id' => null, // Manual item
                'service_name' => $item['service_name'],
                'description' => $item['description'] ?? '',
                'price' => (float) $item['price'],
                'currency' => $this->currency,
                'duration' => $item['duration'] ? (int) $item['duration'] : null,
                'quantity' => (int) $item['quantity'],
            ];
        }

        $this->showManualEntryModal = false;
        $this->manualItems = [];
        $this->success('Başarılı', $count . ' kalem eklendi.');
    }

    public function updatedModalCategory(): void
    {
        $this->modalServiceName = '';
    }

    public function addServiceFromExisting(string $serviceId): void
    {
        $service = collect($this->customerServices)->firstWhere('id', $serviceId);

        if ($service) {
            // Currency sync & validation
            if (count($this->items) > 0) {
                if ($service['service_currency'] !== $this->currency) {
                    $this->error('Para Birimi Uyumsuzluğu', "Bu teklif {$this->currency} cinsindendir. {$service['service_currency']} birimli bir hizmet ekleyemezsiniz.");
                    return;
                }
            } else {
                $this->currency = $service['service_currency'];
            }

            $this->items[] = [
                'service_id' => $service['id'],
                'service_name' => $service['service_name'],
                'description' => ($service['description'] ?? '') . " (Uzatma)",
                'price' => $service['service_price'],
                'currency' => $service['service_currency'],
                'duration' => $service['service_duration'] ?? 1,
                'quantity' => 1,
            ];

            $this->success('Başarılı', 'Hizmet uzatma kalemi eklendi.');
            $this->closeServiceModal();
        }
    }

    public function addServiceFromPriceDefinition(): void
    {
        if (!$this->modalServiceName) {
            $this->error('Uyarı', 'Lütfen bir hizmet seçin.');
            return;
        }

        $priceDef = collect($this->priceDefinitions)
            ->where('category', $this->modalCategory)
            ->firstWhere('name', $this->modalServiceName);

        if ($priceDef) {
            // Currency sync & validation
            if (count($this->items) > 0) {
                if ($priceDef['currency'] !== $this->currency) {
                    $this->error('Para Birimi Uyumsuzluğu', "Bu teklif {$this->currency} cinsindendir. {$priceDef['currency']} birimli bir hizmet ekleyemezsiniz.");
                    return;
                }
            } else {
                $this->currency = $priceDef['currency'];
            }

            $this->items[] = [
                'service_id' => null,
                'service_name' => $priceDef['name'],
                'description' => $priceDef['description'] ?? '',
                'price' => $priceDef['price'],
                'currency' => $priceDef['currency'],
                'duration' => $priceDef['duration'] ?? 1,
                'quantity' => 1,
            ];

            $this->success('Başarılı', 'Hizmet eklendi.');
            $this->closeServiceModal();
        }
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function openItemDescriptionModal(int $index): void
    {
        $this->editingItemIndex = $index;
        $this->itemDescriptionTemp = $this->items[$index]['description'] ?? '';
        $this->showItemDescriptionModal = true;
    }

    public function saveItemDescription(): void
    {
        if ($this->editingItemIndex !== null) {
            $this->items[$this->editingItemIndex]['description'] = Str::limit($this->itemDescriptionTemp, 50, '');
            $this->showItemDescriptionModal = false;
            $this->editingItemIndex = null;
            $this->itemDescriptionTemp = '';
            $this->success('Başarılı', 'Açıklama güncellendi.');
        }
    }
}
