<x-layouts.app title="Proje Yönetimi">
    @php
        $activeTab = request()->query('tab', 'projects');
    @endphp
    
    <div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
        <div class="max-w-7xl mx-auto">
            {{-- Page Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-[var(--color-text-heading)]">Proje Yönetimi</h1>
                <p class="text-[var(--color-text-base)] text-sm mt-1">Projeler, fazlar, modüller ve görevleri tek yerden yönetin</p>
            </div>

            {{-- Tab Navigation --}}
            <x-project-management.tab-navigation :activeTab="$activeTab" />

            {{-- Tab Content --}}
            <div class="mt-6">
                @switch($activeTab)
                    @case('projects')
                        <livewire:projects.tabs.projects-tab />
                        @break
                    @case('tasks')
                        <livewire:projects.tabs.tasks-tab />
                        @break
                    @case('reports')
                        <livewire:projects.tabs.reports-tab />
                        @break
                    @default
                        <livewire:projects.tabs.projects-tab />
                @endswitch
            </div>
        </div>
    </div>
</x-layouts.app>
