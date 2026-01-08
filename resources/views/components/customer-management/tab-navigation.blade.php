@props(['activeTab' => 'customers'])

@php
    $tabs = [
        'customers' => 'Müşteriler',
        'contacts' => 'Kişiler',
        'assets' => 'Varlıklar',
        'services' => 'Hizmetler',
        'offers' => 'Teklifler',
        'sales' => 'Satışlar',
        'messages' => 'Mesajlar',
    ];
@endphp

<div class="border-b border-gray-200">
    <nav class="flex space-x-8" aria-label="Tabs">
        @foreach($tabs as $key => $label)
            <a href="{{ url('/dashboard/customers?tab=' . $key) }}"
                class="whitespace-nowrap py-3 px-1 border-b-2 text-sm font-medium transition-colors"
                style="{{ $activeTab === $key ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>
</div>