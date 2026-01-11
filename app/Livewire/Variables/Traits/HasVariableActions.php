<?php

namespace App\Livewire\Variables\Traits;

use App\Models\ReferenceCategory;
use App\Models\ReferenceItem;
use App\Repositories\ReferenceDataRepository;
use App\Services\ReferenceDataService;

trait HasVariableActions
{
    // Services
    protected ReferenceDataRepository $repository;
    protected ReferenceDataService $service;

    /**
     * Boot services
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
     * Toggle selected category
     */
    public function selectCategory(string $key): void
    {
        $this->selectedCategoryKey = $key;
        $this->resetItemForm();
    }

    /**
     * Get tailwind classes for colors
     */
    public function getTailwindColor($colorId)
    {
        return $this->service->getColorClasses($colorId);
    }

    // --- Category Actions ---

    public function openCreateCategoryModal(): void
    {
        $this->resetCategoryForm();
        $this->showCategoryModal = true;
    }

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
     * Save category (Create/Update)
     * TABLE: reference_categories
     * NOTIFY: success/error toast
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
     * Delete category
     * TABLE: reference_categories (+ cascades to items)
     * NOTIFY: success/error toast
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

    private function resetCategoryForm(): void
    {
        $this->categoryId = '';
        $this->categoryName = '';
        $this->categoryKey = '';
        $this->categoryDescription = '';
        $this->resetErrorBag();
    }

    // --- Item Actions ---

    public function openCreateModal(): void
    {
        if (!$this->selectedCategoryKey) {
            $this->error('Lütfen önce bir kategori seçiniz.');
            return;
        }
        $this->resetItemForm();
        $this->showItemModal = true;
    }

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
     * Save reference item (Create/Update)
     * TABLE: reference_items
     * NOTIFY: success/error toast
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
     * Delete reference item
     * TABLE: reference_items
     * NOTIFY: success/error toast
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
     * Reorder item (Up)
     * LOGIC: Swaps 'sort_order' with the preceding item in the same category.
     * TABLE: reference_items
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
     * Reorder item (Down)
     * LOGIC: Swaps 'sort_order' with the following item in the same category.
     * TABLE: reference_items
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
