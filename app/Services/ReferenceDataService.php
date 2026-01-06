<?php

namespace App\Services;

use App\Repositories\ReferenceDataRepository;
use Illuminate\Support\Collection;

class ReferenceDataService
{
    protected ReferenceDataRepository $repository;

    // Matches the React implementation's color schemes
    const COLOR_SCHEMES = [
        ['id' => 'gray', 'name' => 'Gri', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-200'],
        ['id' => 'blue', 'name' => 'Mavi', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-200'],
        ['id' => 'green', 'name' => 'Yeşil', 'bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-200'],
        ['id' => 'red', 'name' => 'Kırmızı', 'bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-200'],
        ['id' => 'yellow', 'name' => 'Sarı', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-200'],
        ['id' => 'orange', 'name' => 'Turuncu', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'border' => 'border-orange-200'],
        ['id' => 'purple', 'name' => 'Mor', 'bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-200'],
        ['id' => 'pink', 'name' => 'Pembe', 'bg' => 'bg-pink-100', 'text' => 'text-pink-800', 'border' => 'border-pink-200'],
        ['id' => 'indigo', 'name' => 'İndigo', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'border' => 'border-indigo-200'],
        ['id' => 'teal', 'name' => 'Teal', 'bg' => 'bg-teal-100', 'text' => 'text-teal-800', 'border' => 'border-teal-200'],
        ['id' => 'cyan', 'name' => 'Cyan', 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-800', 'border' => 'border-cyan-200'],
        ['id' => 'emerald', 'name' => 'Zümrüt', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'border' => 'border-emerald-200'],
        ['id' => 'rose', 'name' => 'Gül', 'bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'border' => 'border-rose-200'],
        ['id' => 'amber', 'name' => 'Amber', 'bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'border' => 'border-amber-200'],
        ['id' => 'lime', 'name' => 'Lime', 'bg' => 'bg-lime-100', 'text' => 'text-lime-800', 'border' => 'border-lime-200'],
    ];

    public function __construct(ReferenceDataRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get select options for a category formatted for frontend.
     */
    public function getSelectOptions(string $categoryKey): array
    {
        $items = $this->repository->getItems($categoryKey);

        return $items->where('is_active', true)
            ->map(function ($item) {
                return [
                    'value' => $item->key,
                    'label' => $item->display_label,
                    'description' => $item->description,
                    'isDefault' => $item->is_default,
                    'colorScheme' => $item->metadata['color'] ?? null,
                ];
            })
            ->sortBy(function ($item) {
                // Default first, then label
                if ($item['isDefault'])
                    return -1;
                return $item['label'];
            })
            ->values()
            ->all();
    }

    /**
     * Get available color schemes.
     */
    public function getColorSchemes(): array
    {
        return self::COLOR_SCHEMES;
    }

    /**
     * Get Tailwind classes for a specific color scheme ID.
     */
    public function getColorClasses(?string $schemeId): string
    {
        if (!$schemeId)
            return '';

        $scheme = collect(self::COLOR_SCHEMES)->firstWhere('id', $schemeId);
        if (!$scheme)
            return '';

        return "{$scheme['bg']} {$scheme['text']} {$scheme['border']}";
    }
}
