<?php
/**
 * 🏗️ PROJECT CREATE/EDIT COMPONENT (REFACTORED)
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Split Architecture)
 * MODÜL: Projeler
 * ---------------------------------------------------------
 */

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use App\Livewire\Projects\Traits\HasProjectData;
use App\Livewire\Projects\Traits\HasProjectHierarchy;
use App\Livewire\Projects\Traits\HasProjectPersistence;

new
    #[Layout('components.layouts.app', ['title' => 'Projeler'])]
    class extends Component {
    use Toast, HasProjectData, HasProjectHierarchy, HasProjectPersistence;

    public function mount(?string $project = null): void
    {
        $this->loadReferenceData();
        $this->loadHierarchyReferenceData();

        $tab = request()->query('tab');
        if ($tab && in_array($tab, ['project_info', 'tasks', 'reports', 'notes'])) {
            $this->activeTab = $tab;
        }

        if ($project) {
            $this->projectId = $project;
            $this->loadProjectData();
            // loadProjectData calls helpers provided by Traits
        } else {
            // Default Status for New Project
            $defaultStatus = \App\Models\ReferenceItem::where('category_key', 'PROJECT_STATUS')
                ->where('is_default', true)
                ->first();
            if ($defaultStatus) {
                $this->status_id = $defaultStatus->id;
            }
        }
    }
}; ?>

<div x-data="unsavedChangesWatcher" x-on:input="markDirty()" x-on:change="markDirty()" class="p-6 min-h-screen"
    style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        @include('livewire.projects.partials._header')

        {{-- Tabs --}}
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

            {{-- Tab 2: Görevler --}}
            <div x-show="$wire.activeTab === 'tasks'" style="display: none;">
                @if($projectId)
                    <livewire:projects.tabs.tasks-tab :project_id="$projectId" wire:key="project-tasks-{{ $projectId }}" />
                @else
                    <div class="p-8 text-center text-gray-400">
                        <p>Lütfen önce projeyi kaydedin.</p>
                    </div>
                @endif
            </div>

            {{-- Tab 3: Raporlar --}}
            <div x-show="$wire.activeTab === 'reports'" style="display: none;">
                @if($projectId)
                    <livewire:projects.tabs.reports-tab :project_id="$projectId"
                        wire:key="project-reports-{{ $projectId }}" />
                @else
                    <div class="p-8 text-center text-gray-400">
                        <p>Lütfen önce projeyi kaydedin.</p>
                    </div>
                @endif
            </div>

            {{-- Tab 4: Notlar --}}
            <div x-show="$wire.activeTab === 'notes'" style="display: none;">
                @livewire('projects.tabs.notes-tab', [
                    'project_id' => $projectId
                ], key('notes-tab-project-' . $projectId))
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