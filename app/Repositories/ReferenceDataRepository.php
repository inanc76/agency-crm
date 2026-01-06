<?php

namespace App\Repositories;

use App\Models\ReferenceCategory;
use App\Models\ReferenceItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class ReferenceDataRepository
{
    /**
     * Get all categories with item counts.
     */
    public function getCategories(): Collection
    {
        return ReferenceCategory::withCount('items')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get a specific category by key, including active items sorted by order/label.
     */
    public function getCategoryByKey(string $key): ?ReferenceCategory
    {
        return ReferenceCategory::where('key', $key)
            ->with([
                'items' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('display_label');
                }
            ])
            ->first();
    }

    /**
     * Create a new category.
     */
    public function createCategory(array $data): ReferenceCategory
    {
        return ReferenceCategory::create([
            'key' => $data['key'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(string $id, array $data): ReferenceCategory
    {
        $category = ReferenceCategory::findOrFail($id);
        $category->update($data);
        return $category;
    }

    /**
     * Delete a category (prevent if has items).
     */
    public function deleteCategory(string $id): void
    {
        $category = ReferenceCategory::findOrFail($id);

        if ($category->items()->count() > 0) {
            throw new Exception("Kategori içinde öğeler bulunmaktadır. Önce öğeleri siliniz.");
        }

        $category->delete();
    }

    /**
     * Get items for a category key.
     */
    public function getItems(string $categoryKey): Collection
    {
        return ReferenceItem::where('category_key', $categoryKey)
            ->orderBy('sort_order')
            ->orderBy('display_label')
            ->get();
    }

    /**
     * Create a new item with auto-sorting and default handling.
     */
    public function createItem(array $data): ReferenceItem
    {
        return DB::transaction(function () use ($data) {
            // Check uniqueness
            $exists = ReferenceItem::where('category_key', $data['category_key'])
                ->where('key', $data['key'])
                ->exists();

            if ($exists) {
                throw new Exception("Bu kategoride '{$data['key']}' anahtarı zaten mevcut.");
            }

            // Handle default logic
            if (!empty($data['is_default'])) {
                ReferenceItem::where('category_key', $data['category_key'])
                    ->update(['is_default' => false]);
            }

            // Auto-calculate sort order if strictly null (0 is a valid order)
            if (!isset($data['sort_order'])) {
                $maxOrder = ReferenceItem::where('category_key', $data['category_key'])
                    ->max('sort_order');
                $data['sort_order'] = ($maxOrder ?? 0) + 1;
            }

            return ReferenceItem::create($data);
        });
    }

    /**
     * Update an item.
     */
    public function updateItem(string $id, array $data): ReferenceItem
    {
        return DB::transaction(function () use ($id, $data) {
            $item = ReferenceItem::findOrFail($id);

            // Handle default logic if changing
            if (!empty($data['is_default']) && !$item->is_default) {
                ReferenceItem::where('category_key', $item->category_key)
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $item->update($data);
            return $item;
        });
    }

    /**
     * Delete (or soft delete) an item.
     * Currently doing hard delete based on existing codebase, but could switch to soft delete if requested.
     */
    public function deleteItem(string $id): void
    {
        $item = ReferenceItem::findOrFail($id);
        $item->delete();
    }
}
