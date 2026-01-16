@if($projectId)
    <div class="flex items-center border-b border-[var(--card-border)] mb-8 overflow-x-auto scrollbar-hide">
        @php
            $tabs = [
                'project_info' => 'Proje Bilgileri',
                'tasks' => 'Görevler',
                'reports' => 'Raporlar',
                'notes' => 'Notlar',
            ];
        @endphp

        @foreach($tabs as $key => $label)
            <button wire:click="$set('activeTab', '{{ $key }}')"
                class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === $key ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}"
                onclick="history.pushState(null, '', '{{ route('projects.edit', ['project' => $projectId, 'tab' => $key]) }}')">
                {{ $label }}
            </button>
        @endforeach
    </div>
@endif