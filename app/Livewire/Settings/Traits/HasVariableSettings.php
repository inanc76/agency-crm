<?php

namespace App\Livewire\Settings\Traits;

use App\Models\ReferenceCategory;
use App\Models\ReferenceItem;
use App\Repositories\ReferenceDataRepository;
use App\Services\ReferenceDataService;

use Mary\Traits\Toast;

trait HasVariableSettings
{
    use Toast;
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

    public function selectVariableCategory(string $key): void
    {
        $this->selectedCategoryKey = $key;
        $this->resetVariableItemForm();
    }

    public function openCreateVariableCategoryModal(): void
    {
        $this->resetVariableCategoryForm();
        $this->showCategoryModal = true;
    }

    public function editVariableCategory(string $id): void
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

    public function saveVariableCategory(ReferenceDataRepository $repository): void
    {
        $this->validate([
            'categoryName' => 'required|string|max:255',
            'categoryKey' => 'required|string|max:255',
            'categoryDescription' => 'nullable|string',
        ]);

        try {
            $data = [
                'name' => $this->categoryName,
                'key' => strtoupper($this->categoryKey),
                'description' => $this->categoryDescription,
            ];

            if ($this->categoryId) {
                $repository->updateCategory($this->categoryId, $data);
                if ($this->selectedCategoryKey && $this->categoryId === ReferenceCategory::where('key', $this->selectedCategoryKey)->first()?->id) {
                    $this->selectedCategoryKey = strtoupper($this->categoryKey);
                }
                $this->success('Kategori güncellendi.');
            } else {
                $repository->createCategory($data);
                $this->success('Yeni kategori oluşturuldu.');
            }

            $this->showCategoryModal = false;
            $this->resetVariableCategoryForm();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function deleteVariableCategory(string $id, ReferenceDataRepository $repository): void
    {
        try {
            $category = ReferenceCategory::find($id);
            if (!$category)
                return;
            $key = $category->key;

            $repository->deleteCategory($id);

            if ($this->selectedCategoryKey === $key) {
                $this->selectedCategoryKey = null;
            }
            $this->success('Kategori silindi.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function openCreateVariableItemModal(): void
    {
        if (!$this->selectedCategoryKey) {
            $this->error('Lütfen önce bir kategori seçiniz.');
            return;
        }
        $this->resetVariableItemForm();
        $this->showItemModal = true;
    }

    public function editVariableItem(string $id): void
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

    public function saveVariableItem(ReferenceDataRepository $repository): void
    {
        $this->validate($this->variableItemRules());

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
                $repository->updateItem($this->itemId, $data);
                $this->success('Öğe güncellendi.');
            } else {
                $repository->createItem($data);
                $this->success('Yeni öğe oluşturuldu.');
            }

            $this->showItemModal = false;
            $this->resetVariableItemForm();
        } catch (\Exception $e) {
            $this->addError('key', $e->getMessage());
        }
    }

    public function deleteVariableItem(string $id, ReferenceDataRepository $repository): void
    {
        try {
            $repository->deleteItem($id);
            $this->success('Öğe silindi.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function moveVariableItem(string $id, string $direction): void
    {
        try {
            $item = ReferenceItem::findOrFail($id);
            $query = ReferenceItem::where('category_key', $item->category_key);

            if ($direction === 'up') {
                $targetItem = $query->where('sort_order', '<', $item->sort_order)
                    ->orderBy('sort_order', 'desc')
                    ->first();
            } else {
                $targetItem = $query->where('sort_order', '>', $item->sort_order)
                    ->orderBy('sort_order', 'asc')
                    ->first();
            }

            if ($targetItem) {
                $tempOrder = $item->sort_order;
                $item->sort_order = $targetItem->sort_order;
                $targetItem->sort_order = $tempOrder;

                $item->save();
                $targetItem->save();

                $this->success('Sıralama güncellendi.');
            }
        } catch (\Exception $e) {
            $this->error('Sıralama güncellenemedi.');
        }
    }

    protected function resetVariableCategoryForm(): void
    {
        $this->categoryId = '';
        $this->categoryName = '';
        $this->categoryKey = '';
        $this->categoryDescription = '';
        $this->resetErrorBag();
    }

    protected function resetVariableItemForm(): void
    {
        $this->itemId = '';
        $this->key = '';
        $this->display_label = '';
        $this->description = '';
        $this->is_default = false;
        $this->selectedColor = 'gray';
        $this->resetErrorBag();
    }

    protected function variableItemRules(): array
    {
        return [
            'key' => 'required|string|max:255',
            'display_label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'selectedColor' => 'required|string',
        ];
    }
}
