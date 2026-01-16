<?php
/**
 * 🏗️ PROJECT CREATE COMPONENT (REFACTORED)
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Split Architecture)
 * MODÜL: Projeler (Yeni Oluşturma)
 * ---------------------------------------------------------
 */

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use App\Livewire\Projects\Traits\HasProjectData;
use App\Livewire\Projects\Traits\HasProjectHierarchy;
use App\Livewire\Projects\Traits\HasProjectPersistence;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Proje Oluştur'])]
    class extends Component {
    use Toast, HasProjectData, HasProjectHierarchy, HasProjectPersistence;

    public function mount(): void
    {
        $this->loadReferenceData();
        $this->loadHierarchyReferenceData();

        // Default Status
        $defaultStatus = \App\Models\ReferenceItem::where('category_key', 'PROJECT_STATUS')
            ->where('is_default', true)
            ->first();

        if ($defaultStatus) {
            $this->status_id = $defaultStatus->id;
        }
    }
}; ?>

<div x-data="unsavedChangesWatcher" x-on:input="markDirty()" x-on:change="markDirty()" class="p-6 min-h-screen"
    style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">

        {{-- Back Button (Specific to Create Page) --}}
        <a href="/dashboard/projects"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Proje Listesi</span>
        </a>

        {{-- Header --}}
        @include('livewire.projects.partials._header')

        {{-- Tabs (Auto-hidden if no ID) --}}
        @include('livewire.projects.partials._tabs')

        {{-- CONTENT --}}
        <div>
            {{-- Tab 1: Proje Bilgileri --}}
            <div x-show="$wire.activeTab === 'project_info'">
                <div class="grid grid-cols-12 gap-6">
                    {{-- Left Column (8/12) --}}
                    <div class="col-span-8 flex flex-col gap-6">
                        @include('livewire.projects.partials._basic_info')
                        @include('livewire.projects.partials._participants')
                        @include('livewire.projects.partials._hierarchy')
                    </div>

                    {{-- Right Column (4/12) --}}
                    @include('livewire.projects.partials._summary')
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    @include('livewire.projects.partials._modals')
</div>

@script
<script>
    Alpine.data('unsavedChangesWatcher', () => ({
        isDirty: false,
        init() {
            window.addEventListener('beforeunload', (e) => {
                if (this.isDirty) {
                    e.preventDefault();
                    e.returnValue = 'Kaydedilmemiş değişiklikleriniz var.';
                }
            });
            document.addEventListener('livewire:navigate', (event) => {
                if (this.isDirty && !confirm('Kaydedilmemiş değişiklikleriniz var. Çıkmak istediğinize emin misiniz?')) {
                    event.preventDefault();
                }
            });
        },
        markDirty() { this.isDirty = true; },
        markClean() { this.isDirty = false; }
    }));
</script>
@endscript