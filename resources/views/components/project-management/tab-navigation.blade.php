@props(['activeTab' => 'projects'])

@php
    $tabs = [
        'projects' => 'Projeler',
        'tasks' => 'Görevler',
    ];
@endphp

<div class="border-b border-gray-200">
    <nav class="flex space-x-8" aria-label="Tabs">
        @foreach($tabs as $key => $label)
            <a href="{{ url('/dashboard/projects?tab=' . $key) }}"
                class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium transition-colors cursor-pointer"
                style="{{ $activeTab === $key ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>
</div>