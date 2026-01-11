<?php
/**
 * IDENTITY CARD: SettingsVariables
 * Trait: HasVariableSettings
 * Description: Site kimliği (Logo, İsim) ve genel referanslar.
 * Documentation: /tests/Documentation/SettingsMap.md
 */

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\ReferenceCategory;
use App\Models\ReferenceItem;
use App\Repositories\ReferenceDataRepository;
use App\Services\ReferenceDataService;
use App\Livewire\Settings\Traits\HasVariableSettings;

new #[Layout('components.layouts.app', ['title' => 'Değişken Yönetimi'])]
    class extends Component {
    use HasVariableSettings;

    protected ReferenceDataRepository $repository;
    protected ReferenceDataService $service;

    public function boot(ReferenceDataRepository $repository, ReferenceDataService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function with(): array
    {
        $selectedCategory = null;
        if ($this->selectedCategoryKey) {
            $category = $this->repository->getCategoryByKey($this->selectedCategoryKey);
            if ($category) {
                $category->load(['items' => fn($q) => $q->orderBy('sort_order', 'asc')->orderBy('created_at', 'asc')]);
                $selectedCategory = $category;
            }
        }

        return [
            'categories' => ReferenceCategory::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('key', 'like', "%{$this->search}%"))
                ->withCount('items')
                ->orderBy('name')
                ->get(),
            'selectedCategory' => $selectedCategory,
            'availableColors' => $this->service->getColorSchemes(),
        ];
    }

    public function getTailwindColor($colorId)
    {
        return $this->service->getColorClasses($colorId);
    }

    public function selectCategory(string $key): void
    {
        $this->selectVariableCategory($key);
    }
    public function openCreateCategoryModal(): void
    {
        $this->openCreateVariableCategoryModal();
    }
    public function editCategory(string $id): void
    {
        $this->editVariableCategory($id);
    }
    public function saveCategory(): void
    {
        $this->saveVariableCategory($this->repository);
    }
    public function deleteCategory(string $id): void
    {
        $this->deleteVariableCategory($id, $this->repository);
    }

    public function openCreateModal(): void
    {
        $this->openCreateVariableItemModal();
    }
    public function editItem(string $id): void
    {
        $this->editVariableItem($id);
    }
    public function saveItem(): void
    {
        $this->saveVariableItem($this->repository);
    }
    public function deleteItem(string $id): void
    {
        $this->deleteVariableItem($id, $this->repository);
    }

    public function moveItemUp(string $id): void
    {
        $this->moveVariableItem($id, 'up');
    }
    public function moveItemDown(string $id): void
    {
        $this->moveVariableItem($id, 'down');
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="w-full lg:w-4/5 mx-auto pb-20">
        {{-- Back Button --}}
        <a href="/dashboard/settings"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-6 transition-colors font-medium">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span>Geri</span>
        </a>

        {{-- Header Section --}}
        @include('livewire.settings.variables.parts._header')

        {{-- Main Layout --}}
        <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-300px)] min-h-[600px]">
            {{-- Left Sidebar: Categories --}}
            @include('livewire.settings.variables.parts._categories')

            {{-- Right Content: Items --}}
            @include('livewire.settings.variables.parts._items')
        </div>

        {{-- Modals --}}
        @include('livewire.settings.variables.parts._modals')
    </div>
</div>