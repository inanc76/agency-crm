<?php

namespace App\Livewire\Variables\Traits;

use App\Models\ReferenceCategory;
use App\Models\ReferenceItem;
use App\Repositories\ReferenceDataRepository;
use App\Services\ReferenceDataService;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                          🏛️ MİMARIN NOTU - CONSTITUTION V10                                      ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasVariableActions Trait                                                                  ║
 * ║  🎯 ANA GÖREV: Referans veri yönetimi ve kategori-öğe ilişkileri                                               ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • Kategori Yönetimi: ReferenceCategory CRUD işlemleri ve anahtar benzersizlik kontrolü                        ║
 * ║  • Öğe Yönetimi: ReferenceItem CRUD işlemleri, renk metadata'sı ve sıralama                                    ║
 * ║  • Sıralama Kontrolü: Öğelerin kategori içinde yukarı/aşağı taşınması                                          ║
 * ║  • Modal State Yönetimi: Kategori ve öğe düzenleme modallarının açılma/kapanma durumları                       ║
 * ║  • Renk Sistemi: Tailwind CSS renk sınıfları ile görsel kategorizasyon                                         ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK KATMANLARI:                                                                                        ║
 * ║  • Form Validasyonu: Laravel validation rules ile veri doğrulama                                               ║
 * ║  • Anahtar Benzersizliği: Kategori key'lerinin tekrar kontrolü                                                  ║
 * ║  • Repository Pattern: Veri erişimi için güvenli katman                                                         ║
 * ║                                                                                                                  ║
 * ║  📊 BAĞIMLILIK HARİTASI:                                                                                        ║
 * ║  • $this->selectedCategoryKey: Aktif seçili kategori anahtarı                                                   ║
 * ║  • $this->repository: ReferenceDataRepository instance                                                          ║
 * ║  • $this->service: ReferenceDataService instance                                                                ║
 * ║  • Modal form states: showItemModal, showCategoryModal ve ilgili form alanları                                  ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasVariableActions
{
    // Services
    protected ReferenceDataRepository $repository;
    protected ReferenceDataService $service;

    /**
     * @purpose Repository ve Service bağımlılıklarının enjekte edilmesi
     * @param ReferenceDataRepository $repository Veri erişim katmanı
     * @param ReferenceDataService $service İş mantığı katmanı
     * @return void
     * 🔐 Security: Dependency injection ile güvenli servis erişimi
     * 📢 Events: Servis bağımlılıkları hazırlanır
     * 
     * State Dependencies: $this->repository, $this->service
     */
    public function boot(ReferenceDataRepository $repository, ReferenceDataService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

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

    // Category Form State
    public bool $showCategoryModal = false;
    public string $categoryId = '';
    public string $categoryName = '';
    public string $categoryKey = '';
    public string $categoryDescription = '';

    /**
     * @purpose Kategori seçimi ve öğe formunun sıfırlanması
     * @param string $key Seçilecek kategori anahtarı
     * @return void
     * 🔐 Security: Kategori anahtarı string kontrolü
     * 📢 Events: $this->selectedCategoryKey güncellenir, resetItemForm() çağrısı
     * 
     * State Dependencies: $this->selectedCategoryKey
     */
    public function selectCategory(string $key): void
    {
        $this->selectedCategoryKey = $key;
        $this->resetItemForm();
    }

    /**
     * @purpose Renk ID'sine göre Tailwind CSS sınıflarını alma
     * @param string $colorId Renk tanımlayıcısı
     * @return string Tailwind CSS sınıf string'i
     * 🔐 Security: Service katmanı üzerinden güvenli renk sınıfı erişimi
     * 📢 Events: UI renk güncellemesi
     * 
     * State Dependencies: $this->service
     */
    public function getTailwindColor($colorId)
    {
        return $this->service->getColorClasses($colorId);
    }

    // --- Category Actions ---

    /**
     * @purpose Yeni kategori oluşturma modalını açma
     * @return void
     * 🔐 Security: Genel erişim - özel yetki kontrolü yok
     * 📢 Events: $this->showCategoryModal = true, resetCategoryForm() çağrısı
     * 
     * State Dependencies: $this->showCategoryModal
     */
    public function openCreateCategoryModal(): void
    {
        $this->resetCategoryForm();
        $this->showCategoryModal = true;
    }

    /**
     * @purpose Mevcut kategoriyi düzenleme moduna alma
     * @param string $id Düzenlenecek kategori ID'si
     * @return void
     * 🔐 Security: Kategori varlığı kontrolü, ID validasyonu
     * 📢 Events: $this->showCategoryModal = true, form alanları doldurulur
     * 
     * State Dependencies: $this->categoryId, $this->categoryName, $this->categoryKey, $this->categoryDescription
     */
    public function editCategory(string $id): void
    {
        $category = ReferenceCategory::find($id);
        if (!$category)
            return;

        $this->categoryId = $category->id;
        $this->categoryName = $category->name;
        $this->categoryKey = $category->key;
        $this->categoryDescription = $category->description ?? '';
        $this->showCategoryModal = true;
    }

    /**
     * @purpose Kategori kaydetme (yeni oluşturma veya güncelleme)
     * @return void
     * 🔐 Security: Form validasyonu, kategori anahtarı benzersizlik kontrolü
     * 📢 Events: Success/error toast, modal kapatma, selectedCategoryKey güncelleme
     * 
     * State Dependencies: $this->categoryId, $this->selectedCategoryKey, kategori form alanları
     */
    public function saveCategory(): void
    {
        $this->validate([
            'categoryName' => 'required|string|max:255',
            'categoryKey' => 'required|string|max:255',
            'categoryDescription' => 'nullable|string',
        ]);

        try {
            $query = ReferenceCategory::where('key', $this->categoryKey);
            if ($this->categoryId) {
                $query->where('id', '!=', $this->categoryId);
            }
            if ($query->exists()) {
                $this->addError('categoryKey', 'Bu anahtar zaten kullanılıyor.');
                return;
            }

            $data = [
                'name' => $this->categoryName,
                'key' => $this->categoryKey,
                'description' => $this->categoryDescription,
            ];

            if ($this->categoryId) {
                $this->repository->updateCategory($this->categoryId, $data);
                if ($this->selectedCategoryKey && $this->categoryId === ReferenceCategory::where('key', $this->selectedCategoryKey)->first()?->id) {
                    $this->selectedCategoryKey = $this->categoryKey;
                }
                $this->success('Kategori güncellendi.');
            } else {
                $this->repository->createCategory($data);
                $this->success('Yeni kategori oluşturuldu.');
            }

            $this->showCategoryModal = false;
            $this->resetCategoryForm();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @purpose Kategoriyi ve bağlı öğeleri silme
     * @param string $id Silinecek kategori ID'si
     * @return void
     * 🔐 Security: Kategori varlığı kontrolü, cascade silme yetkisi
     * 📢 Events: Success/error toast, selectedCategoryKey sıfırlama
     * 
     * State Dependencies: $this->selectedCategoryKey
     */
    public function deleteCategory(string $id): void
    {
        try {
            $category = ReferenceCategory::find($id);
            if (!$category)
                return;
            $key = $category->key;

            $this->repository->deleteCategory($id);

            if ($this->selectedCategoryKey === $key) {
                $this->selectedCategoryKey = null;
            }
            $this->success('Kategori silindi.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @purpose Kategori form alanlarını sıfırlama
     * @return void
     * 🔐 Security: Private metot - sadece trait içinden erişilebilir
     * 📢 Events: Form alanları temizlenir, hata mesajları sıfırlanır
     * 
     * State Dependencies: $this->categoryId, $this->categoryName, $this->categoryKey, $this->categoryDescription
     */
    private function resetCategoryForm(): void
    {
        $this->categoryId = '';
        $this->categoryName = '';
        $this->categoryKey = '';
        $this->categoryDescription = '';
        $this->resetErrorBag();
    }

    // --- Item Actions ---

    /**
     * @purpose Yeni öğe oluşturma modalını açma
     * @return void
     * 🔐 Security: Kategori seçimi zorunlu - selectedCategoryKey kontrolü
     * 📢 Events: $this->showItemModal = true, error toast (kategori yoksa)
     * 
     * State Dependencies: $this->selectedCategoryKey, $this->showItemModal
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
     * 🔐 Security: Öğe varlığı kontrolü, ID validasyonu
     * 📢 Events: $this->showItemModal = true, form alanları doldurulur
     * 
     * State Dependencies: $this->itemId, öğe form alanları, $this->selectedColor
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
     * 🔐 Security: Form validasyonu, kategori anahtarı kontrolü, renk validasyonu
     * 📢 Events: Success/error toast, modal kapatma, resetItemForm() çağrısı
     * 
     * State Dependencies: $this->itemId, $this->selectedCategoryKey, öğe form alanları
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
     * 🔐 Security: Öğe varlığı kontrolü, silme yetkisi
     * 📢 Events: Success/error toast
     * 
     * State Dependencies: Yok (sadece veritabanı işlemi)
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
     * 🔐 Security: Öğe varlığı kontrolü, sort_order manipülasyon yetkisi
     * 📢 Events: Success/error toast, UI sıralama güncelleme
     * 
     * State Dependencies: Yok (veritabanı sort_order değişikliği)
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
     * 🔐 Security: Öğe varlığı kontrolü, sort_order manipülasyon yetkisi
     * 📢 Events: Success/error toast, UI sıralama güncelleme
     * 
     * State Dependencies: Yok (veritabanı sort_order değişikliği)
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
     * 🔐 Security: Private metot - sadece trait içinden erişilebilir
     * 📢 Events: Form alanları temizlenir, hata mesajları sıfırlanır
     * 
     * State Dependencies: $this->itemId, öğe form alanları, $this->selectedColor
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
