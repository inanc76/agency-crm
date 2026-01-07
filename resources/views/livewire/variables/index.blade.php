<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\ReferenceCategory;
use App\Models\ReferenceItem;
use App\Repositories\ReferenceDataRepository;
use App\Services\ReferenceDataService;
use Mary\Traits\Toast;

new
    #[Layout('components.layouts.app', ['title' => 'Değişken Yönetimi'])]
    class extends Component {
    use Toast;

    // Services
    protected ReferenceDataRepository $repository;
    protected ReferenceDataService $service;

    public function boot(ReferenceDataRepository $repository, ReferenceDataService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }
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
    public string $selectedColor = 'gray'; // Default color

    // Category Form State
    public bool $showCategoryModal = false;
    public string $categoryId = '';
    public string $categoryName = '';
    public string $categoryKey = '';
    public string $categoryDescription = '';

    // Valid colors for metadata
    public function getColorsProperty()
    {
        return $this->service->getColorSchemes();
    }

    public function getTailwindColor($colorId)
    {
        return $this->service->getColorClasses($colorId);
    }

    public function with(): array
    {
        return [
            'categories' => ReferenceCategory::query()
                ->when($this->search, fn($q) => $q->where('name', 'ilike', "%{$this->search}%")
                    ->orWhere('key', 'ilike', "%{$this->search}%"))
                ->withCount('items')
                ->orderBy('name')
                ->get(),
            'selectedCategory' => $this->selectedCategoryKey
                ? $this->repository->getCategoryByKey($this->selectedCategoryKey)
                : null,
            'availableColors' => $this->service->getColorSchemes(),
        ];
    }

    public function selectCategory(string $key): void
    {
        $this->selectedCategoryKey = $key;
        $this->resetItemForm(); // Clear form when switching categories
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

    public function saveCategory(): void
    {
        $this->validate([
            'categoryName' => 'required|string|max:255',
            'categoryKey' => 'required|string|max:255', // Unique check simpler here
            'categoryDescription' => 'nullable|string',
        ]);

        try {
            // Uniqueness check is still good for UX, but repo will enforce it too
            // Keeping simple UX check
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

                // If we edited the key of the currently selected category, update selection
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

    public function saveItem(): void
    {
        $this->validate([
            'key' => 'required|string|max:255', // Unique validation logic below due to composite key
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
                'is_active' => true, // Assuming active by default
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
            $this->addError('key', $e->getMessage()); // Most likely uniqueness error
        }
    }

    public function deleteItem(string $id): void
    {
        try {
            $this->repository->deleteItem($id);
            $this->success('Öğe silindi.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    // Helper: Reset form
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
}; ?>

<div class="p-6 bg-slate-50 min-h-screen">
    <div class="w-full lg:w-3/4 mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/settings"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm font-medium">Geri</span>
        </a>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Değişken Yönetimi</h1>
            <p class="text-sm text-slate-500 mt-1">Sistem değişkenlerini ve referans verilerini yönetin.</p>
        </div>

        {{-- Main Card --}}
        <div class="card border p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-300px)] min-h-[600px]">
                {{-- Left Sidebar: Categories --}}
                <div class="w-full lg:w-1/2 bg-white border border-slate-200 rounded-lg flex flex-col h-full shadow-sm">
                    <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                        <div class="font-bold text-lg text-slate-800">Kategoriler</div>
                        <x-mary-button label="Yeni Kategori" icon="o-plus"
                            class="btn-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 shadow-sm"
                            wire:click="openCreateCategoryModal" />
                    </div>
                    <div class="px-4 py-3 border-b border-slate-100 bg-white">
                        <div class="relative">
                            <x-mary-icon name="o-magnifying-glass"
                                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                            <input wire:model.live="search" type="search" placeholder="Kategori ara..."
                                class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 space-y-2">
                        {{-- Categories List --}}
                        @foreach($categories as $category)
                            <div class="group relative flex items-center justify-between p-4 rounded-lg border transition-all duration-200 cursor-pointer hover:shadow-md {{ $selectedCategoryKey === $category->key ? 'border-orange-400 bg-orange-50/30' : 'border-slate-100 bg-white hover:border-slate-300' }}"
                                wire:click="selectCategory('{{ $category->key }}')">

                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-slate-800 truncate">{{ $category->name }}</div>
                                    <div class="text-xs text-slate-400 font-mono mt-0.5 uppercase">{{ $category->key }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 pl-3">
                                    <span
                                        class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded">{{ $category->items->count() }}
                                        öğe</span>

                                    <div class="flex items-center gap-1">
                                        <button wire:click.stop="editCategory('{{ $category->id }}')"
                                            class="p-1.5 text-slate-400 hover:bg-slate-50 rounded transition-colors"
                                            style="color: var(--action-link-color);">
                                            <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button wire:click.stop="deleteCategory('{{ $category->id }}')"
                                            wire:confirm="Bu kategoriyi silmek istediğinize emin misiniz?"
                                            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                            <x-mary-icon name="o-trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($categories->isEmpty())
                            <div class="p-8 text-center text-slate-400">
                                <x-mary-icon name="o-folder" class="w-8 h-8 mx-auto mb-2 opacity-50" />
                                <p class="text-sm">Kategori bulunamadı.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Content: Items --}}
                <div
                    class="w-full lg:w-1/2 bg-white border border-slate-200 rounded-lg shadow-sm flex flex-col h-full overflow-hidden">
                    @if($selectedCategory)
                        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/70">
                            <div>
                                <h2 class="text-lg font-bold text-slate-800">{{ $selectedCategory->name }}</h2>
                            </div>
                            <x-mary-button label="Yeni Öğe" icon="o-plus"
                                class="btn-sm bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 shadow-sm"
                                wire:click="openCreateModal" />
                        </div>

                        <div class="p-4 flex-1 overflow-y-auto bg-slate-50/30">
                            <div class="space-y-2">
                                @forelse($selectedCategory->items as $item)
                                    <div
                                        class="flex items-center justify-between p-3 bg-white rounded-lg border border-slate-100 shadow-sm hover:shadow-md transition-shadow group relative">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                @if(isset($item->metadata['color']))
                                                    <span
                                                        class="px-3 py-1 rounded-full text-xs font-medium {{ $this->getTailwindColor($item->metadata['color']) }} border border-transparent">
                                                        {{ $item->display_label }}
                                                    </span>
                                                @else
                                                    <span class="font-medium text-slate-700">{{ $item->display_label }}</span>
                                                @endif
                                                @if($item->is_default)
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100"
                                                        style="color: var(--btn-primary-bg);">Varsayılan</span>
                                                @endif
                                                <div class="text-xs text-slate-400 font-mono uppercase">{{ $item->key }}</div>
                                            </div>
                                            @if($item->description)
                                                <div class="text-xs text-slate-500 mt-1 truncate">{{ $item->description }}</div>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2 pl-2">
                                            {{-- Move buttons placeholder
                                            <div class="hidden group-hover:flex flex-col gap-0.5 mr-1">
                                                <button class="text-slate-300 hover:text-slate-600"><x-mary-icon
                                                        name="o-arrow-up" class="w-3 h-3" /></button>
                                                <button class="text-slate-300 hover:text-slate-600"><x-mary-icon
                                                        name="o-arrow-down" class="w-3 h-3" /></button>
                                            </div>
                                            --}}

                                            <button wire:click="editItem('{{ $item->id }}')"
                                                class="p-1.5 text-slate-400 hover:bg-slate-50 rounded transition-colors"
                                                style="color: var(--action-link-color);">
                                                <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                                            </button>
                                            <button wire:click="deleteItem('{{ $item->id }}')"
                                                wire:confirm="Bu öğeyi silmek istediğinize emin misiniz?"
                                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                                <x-mary-icon name="o-trash" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="flex flex-col items-center justify-center py-20 text-center">
                                        <div
                                            class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 border border-dashed border-slate-200">
                                            <x-mary-icon name="o-inbox" class="w-8 h-8 text-slate-300" />
                                        </div>
                                        <p class="text-slate-500 text-sm">Bu kategori boş.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div class="h-full flex flex-col items-center justify-center bg-slate-50/30">
                            <div class="p-8 text-center">
                                <h3 class="text-lg font-medium text-slate-700 mb-2">Kategori Seçimi</h3>
                                <p class="text-slate-500 text-sm">İşlem yapmak için soldan bir kategori seçin.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Category Create/Edit Modal --}}
    <x-mary-modal wire:model="showCategoryModal" title="{{ $categoryId ? 'Kategoriyi Düzenle' : 'Yeni Kategori' }}"
        class="backdrop-blur" box-class="!max-w-lg">
        <div class="grid gap-4">
            <x-mary-input label="Anahtar" wire:model="categoryKey" placeholder="CATEGORY_KEY"
                hint="Sistem tarafında kullanılacak benzersiz kod" />
            <x-mary-input label="İsim" wire:model="categoryName" placeholder="Kategori İsmi" />
            <x-mary-textarea label="Açıklama" wire:model="categoryDescription" placeholder="Kategori açıklaması"
                rows="3" />
        </div>
        <x-slot:actions>
            <x-mary-button label="İptal" class="btn-ghost" wire:click="$set('showCategoryModal', false)" />
            <x-mary-button label="{{ $categoryId ? 'Güncelle' : 'Oluştur' }}" class="btn-primary"
                wire:click="saveCategory" spinner="saveCategory" />
        </x-slot:actions>
    </x-mary-modal>

    {{-- Create/Edit Item Modal --}}
    <x-mary-modal wire:model="showItemModal" title="{{ $itemId ? 'Öğeyi Düzenle' : 'Yeni Öğe' }}" class="backdrop-blur"
        box-class="!max-w-2xl">
        <div class="grid gap-5">
            <x-mary-input label="Anahtar (Key)" wire:model="key"
                hint="Sistem tarafında kullanılacak benzersiz kod (örn: MALE)" />

            <x-mary-input label="Görünen İsim" wire:model="display_label"
                hint="Arayüzde kullanıcıların göreceği isim (örn: Erkek)" />

            {{-- Color Picker --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-3">Renk Şeması</label>
                <div class="p-4 border border-slate-200 rounded-lg bg-slate-50">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Ön İzleme:</span>
                        <span
                            class="px-3 py-1 rounded-md text-sm font-medium border border-transparent {{ $this->getTailwindColor($selectedColor) }} ring-1 ring-black/5 shadow-sm">
                            {{ $display_label ?: 'Örnek Etiket' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-5 gap-y-4 gap-x-2">
                        @foreach($availableColors as $colorScheme)
                            <button type="button" wire:click="$set('selectedColor', '{{ $colorScheme['id'] }}')"
                                class="flex flex-col items-center justify-center p-2 rounded-lg border transition-all duration-200 group {{ $selectedColor === $colorScheme['id'] ? 'border-orange-500 bg-white ring-2 ring-orange-100 shadow-sm' : 'border-transparent hover:bg-slate-200/50' }}">
                                <span
                                    class="px-2 py-0.5 rounded textxs font-medium {{ $this->getTailwindColor($colorScheme['id']) }} mb-1 shadow-sm ring-1 ring-black/5 min-w-[32px] text-center">Abc</span>
                                <span
                                    class="text-[10px] {{ $selectedColor === $colorScheme['id'] ? 'text-orange-700 font-bold' : 'text-slate-500' }}">{{ $colorScheme['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <x-mary-toggle label="Varsayılan Öğe" wire:model="is_default"
                hint="Bu kategorinin varsayılan seçeneği olsun" class="toggle-info" />
        </div>

        <x-slot:actions>
            <x-mary-button label="İptal" class="btn-ghost" wire:click="$set('showItemModal', false)" />
            <x-mary-button label="{{ $itemId ? 'Güncelle' : 'Oluştur' }}" class="btn-primary" wire:click="saveItem"
                spinner="saveItem" />
        </x-slot:actions>
    </x-mary-modal>
</div>