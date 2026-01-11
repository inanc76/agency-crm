<?php

namespace App\Livewire\Variables\Traits;

use App\Models\ReferenceCategory;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasCategoryActions Trait (Category CRUD Operations)                                       ║
 * ║  🎯 ANA GÖREV: ReferenceCategory CRUD işlemleri ve modal yönetimi                                               ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • openCreateCategoryModal(): Yeni kategori oluşturma modalı                                                    ║
 * ║  • editCategory(): Mevcut kategori düzenleme                                                                    ║
 * ║  • saveCategory(): Kategori kaydetme (create/update)                                                            ║
 * ║  • deleteCategory(): Kategori ve bağlı öğeleri silme                                                            ║
 * ║  • resetCategoryForm(): Form alanlarını temizleme                                                               ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK KATMANLARI:                                                                                        ║
 * ║  • Key Uniqueness: Kategori anahtarlarının benzersizlik kontrolü                                                ║
 * ║  • Form Validation: Laravel validation rules                                                                    ║
 * ║  • Repository Pattern: Güvenli veri erişimi                                                                     ║
 * ║                                                                                                                  ║
 * ║  📊 STATE BAĞIMLILIKLARI:                                                                                       ║
 * ║  • $this->repository: ReferenceDataRepository instance (parent trait'ten)                                      ║
 * ║  • $this->selectedCategoryKey: Aktif seçili kategori                                                            ║
 * ║  • Modal form states: showCategoryModal, categoryId, categoryName, categoryKey, categoryDescription            ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasCategoryActions
{
    // Category Form State
    public bool $showCategoryModal = false;
    public string $categoryId = '';
    public string $categoryName = '';
    public string $categoryKey = '';
    public string $categoryDescription = '';

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
     * 🔗 Side Effects:
     *    - Yeni kategori: ReferenceCategory::create
     *    - Güncelleme: repository->updateCategory
     *    - Key değişirse: selectedCategoryKey senkronizasyonu
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
            // Key uniqueness check
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
                // Sync selectedCategoryKey if editing current selection
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
     * 🔗 Side Effects: Kategori silinirse ilişkili ReferenceItem'lar da silinir (cascade)
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
}
