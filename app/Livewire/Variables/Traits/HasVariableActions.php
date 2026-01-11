<?php

namespace App\Livewire\Variables\Traits;

use App\Models\ReferenceItem;
use App\Repositories\ReferenceDataRepository;
use App\Services\ReferenceDataService;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11 (SLIM)                                     ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasVariableActions Trait (Main Coordinator)                                               ║
 * ║  🎯 ANA GÖREV: Referans veri yönetimi koordinasyonu ve Item CRUD işlemleri                                      ║
 * ║                                                                                                                  ║
 * ║  📦 TRAIT BAĞIMLILIKLARI (Composition):                                                                         ║
 * ║  • HasCategoryActions: Kategori CRUD işlemleri (openCreateCategoryModal, saveCategory, etc.)                   ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • boot(): Dependency injection (Repository & Service)                                                          ║
 * ║  • selectCategory(): Kategori seçimi                                                                            ║
 * ║  • Item CRUD: openCreateModal, editItem, saveItem, deleteItem                                                   ║
 * ║  • Sorting: moveItemUp, moveItemDown                                                                            ║
 * ║  • getTailwindColor(): Renk sınıfı dönüşümü                                                                     ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK KATMANLARI:                                                                                        ║
 * ║  • Form Validasyonu: Laravel validation rules                                                                   ║
 * ║  • Repository Pattern: Güvenli veri erişimi                                                                     ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasVariableActions
{
    use HasCategoryActions; // 📁 Kategori CRUD işlemleri

    // Services
    protected ReferenceDataRepository $repository;
    protected ReferenceDataService $service;

    // State
    public string $search = '';
    public ?string $selectedCategoryKey = null;

    // Item Form State
    public bool $showItemModal = false;
    public string $itemId = '';
    public string $key = '';
    public string $display_label = '';
    public string $description = '';
    public bool $is_default = false;
    public string $selectedColor = 'gray';

    /**
     * @purpose Repository ve Service bağımlılıklarının enjekte edilmesi
     * @return void
     * 🔐 Security: Dependency injection ile güvenli servis erişimi
     */
    public function boot(ReferenceDataRepository $repository, ReferenceDataService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @purpose Kategori seçimi ve öğe formunun sıfırlanması
     * @param string $key Seçilecek kategori anahtarı
     * @return void
     */
    public function selectCategory(string $key): void
    {
        $this->selectedCategoryKey = $key;
        $this->resetItemForm();
    }

    /**
     * @purpose Renk ID'sine göre Tailwind CSS sınıflarını alma
     * @return string Tailwind CSS sınıf string'i
     */
    public function getTailwindColor($colorId)
    {
        return $this->service->getColorClasses($colorId);
    }

    // --- Item Actions ---

    /**
     * @purpose Yeni öğe oluşturma modalını açma
     * @return void
     * 🔐 Security: Kategori seçimi zorunlu - selectedCategoryKey kontrolü
     */
    public function openCreateModal(): void
    {
        if (!$this->selectedCategoryKey) {
            $this->error('Lütfen önce bir kategori seçiniz.');
            return;
        }
        $this->resetItemForm();
        $this->showItemModal = true;
    }

    /**
     * @purpose Mevcut öğeyi düzenleme moduna alma
     * @param string $id Düzenlenecek öğe ID'si
     * @return void
     * 🔐 Security: Öğe varlığı kontrolü
     */
    public function editItem(string $id): void
    {
        $item = ReferenceItem::find($id);
        if (!$item)
            return;

        $this->itemId = $item->id;
        $this->key = $item->key;
        $this->display_label = $item->display_label;
        $this->description = $item->description ?? '';
        $this->is_default = $item->is_default;
        $this->selectedColor = $item->metadata['color'] ?? 'gray';
        $this->showItemModal = true;
    }

    /**
     * @purpose Referans öğesi kaydetme (yeni oluşturma veya güncelleme)
     * @return void
     * 🔐 Security: Form validasyonu, kategori kontrolü
     * 📢 Events: Success/error toast, modal kapatma
     */
    public function saveItem(): void
    {
        $this->validate([
            'key' => 'required|string|max:255',
            'display_label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'selectedColor' => 'required|string',
        ]);

        try {
            $data = [
                'category_key' => $this->selectedCategoryKey,
                'key' => $this->key,
                'display_label' => $this->display_label,
                'description' => $this->description,
                'is_default' => $this->is_default,
                'is_active' => true,
                'metadata' => ['color' => $this->selectedColor],
            ];

            if ($this->itemId) {
                $this->repository->updateItem($this->itemId, $data);
                $this->success('Öğe güncellendi.');
            } else {
                $this->repository->createItem($data);
                $this->success('Yeni öğe oluşturuldu.');
            }

            $this->showItemModal = false;
            $this->resetItemForm();
        } catch (\Exception $e) {
            $this->addError('key', $e->getMessage());
        }
    }

    /**
     * @purpose Referans öğesini silme
     * @param string $id Silinecek öğe ID'si
     * @return void
     * 🔐 Security: Öğe varlığı kontrolü
     */
    public function deleteItem(string $id): void
    {
        try {
            $this->repository->deleteItem($id);
            $this->success('Öğe silindi.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @purpose Öğeyi sıralamada yukarı taşıma
     * @param string $id Taşınacak öğe ID'si
     * @return void
     * 🔐 Security: sort_order manipülasyon yetkisi
     */
    public function moveItemUp(string $id): void
    {
        try {
            $item = ReferenceItem::findOrFail($id);
            $previousItem = ReferenceItem::where('category_key', $item->category_key)
                ->where('sort_order', '<', $item->sort_order)
                ->orderBy('sort_order', 'desc')
                ->first();

            if ($previousItem) {
                $tempOrder = $item->sort_order;
                $item->sort_order = $previousItem->sort_order;
                $previousItem->sort_order = $tempOrder;
                $item->save();
                $previousItem->save();
                $this->success('Sıralama güncellendi.');
            }
        } catch (\Exception $e) {
            $this->error('Sıralama güncellenemedi.');
        }
    }

    /**
     * @purpose Öğeyi sıralamada aşağı taşıma
     * @param string $id Taşınacak öğe ID'si
     * @return void
     * 🔐 Security: sort_order manipülasyon yetkisi
     */
    public function moveItemDown(string $id): void
    {
        try {
            $item = ReferenceItem::findOrFail($id);
            $nextItem = ReferenceItem::where('category_key', $item->category_key)
                ->where('sort_order', '>', $item->sort_order)
                ->orderBy('sort_order', 'asc')
                ->first();

            if ($nextItem) {
                $tempOrder = $item->sort_order;
                $item->sort_order = $nextItem->sort_order;
                $nextItem->sort_order = $tempOrder;
                $item->save();
                $nextItem->save();
                $this->success('Sıralama güncellendi.');
            }
        } catch (\Exception $e) {
            $this->error('Sıralama güncellenemedi.');
        }
    }

    /**
     * @purpose Öğe form alanlarını sıfırlama
     * @return void
     */
    private function resetItemForm(): void
    {
        $this->itemId = '';
        $this->key = '';
        $this->display_label = '';
        $this->description = '';
        $this->is_default = false;
        $this->selectedColor = 'gray';
        $this->resetErrorBag();
    }
}
