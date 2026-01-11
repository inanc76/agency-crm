<?php
/**
 * 🚀 VARIABLES MANAGEMENT ORCHESTRATOR
 * ---------------------------------------------------------------------------------------
 * ROL: Bu dosya Değişken Yönetimi modülünün "Orkestra Şefi"dir.
 * MİMARİ: Volt + Trait tabanlı atomik yapı.
 * LOGIC: Tüm iş mantığı App\Livewire\Variables\Traits\HasVariableActions trait'ine delege edilmiştir.
 * GRID SİSTEMİ: 12 sütunlu (Lg:w-3/4 mx-auto) yapıda, Sidebar (w-1/2) ve Content (w-1/2) dengesiyle çalışır.
 * ---------------------------------------------------------------------------------------
 */

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\ReferenceCategory;
use App\Models\ReferenceItem;
use App\Repositories\ReferenceDataRepository;
use App\Services\ReferenceDataService;
use Mary\Traits\Toast;
use App\Livewire\Variables\Traits\HasVariableActions;

new
    #[Layout('components.layouts.app', ['title' => 'Değişken Yönetimi'])]
    class extends Component {
    use Toast, HasVariableActions;

    /**
     * Data provider for the view
     */
    public function with(): array
    {
        $selectedCategory = null;
        if ($this->selectedCategoryKey) {
            $category = $this->repository->getCategoryByKey($this->selectedCategoryKey);
            if ($category) {
                $category->load([
                    'items' => function ($query) {
                        $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'asc');
                    }
                ]);
                $selectedCategory = $category;
            }
        }

        return [
            'categories' => ReferenceCategory::query()
                ->when($this->search, fn($q) => $q->where('name', 'ilike', "%{$this->search}%")
                    ->orWhere('key', 'ilike', "%{$this->search}%"))
                ->withCount('items')
                ->orderBy('name')
                ->get(),
            'selectedCategory' => $selectedCategory,
            'availableColors' => $this->service->getColorSchemes(),
        ];
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="w-full lg:w-3/4 mx-auto">
        @include('livewire.variables.parts._index-header')

        {{-- Main Card --}}
        <div class="card theme-card border p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-300px)] min-h-[600px]">
                @include('livewire.variables.parts._index-sidebar')
                @include('livewire.variables.parts._index-items')
            </div>
        </div>
    </div>

    @include('livewire.variables.parts._modal-category')
    @include('livewire.variables.parts._modal-item')
</div>