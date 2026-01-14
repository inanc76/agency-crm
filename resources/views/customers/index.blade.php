<x-layouts.app title="Müşteri Yönetimi">
    @php
        $activeTab = request()->query('tab', 'customers');
    @endphp
    
    <div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
        <div class="max-w-7xl mx-auto">
            {{-- Page Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-[var(--color-text-heading)]">Müşteri Yönetimi</h1>
                <p class="text-[var(--color-text-base)] text-sm mt-1">Müşteriler ve ilgili tüm verileri tek yerden yönetin</p>
            </div>

            {{-- Tab Navigation --}}
            <x-customer-management.tab-navigation :activeTab="$activeTab" />

            {{-- Tab Content --}}
            <div class="mt-6">
                @switch($activeTab)
                    @case('customers')
                        <livewire:customers.tabs.customers-tab />
                        @break
                    @case('contacts')
                        <livewire:customers.tabs.contacts-tab />
                        @break
                    @case('assets')
                        <livewire:customers.tabs.assets-tab />
                        @break
                    @case('services')
                        <livewire:customers.tabs.services-tab />
                        @break
                    @case('offers')
                        <livewire:customers.tabs.offers-tab />
                        @break
                    @case('sales')
                        <livewire:customers.tabs.sales-tab />
                        @break
                    @case('messages')
                        <livewire:customers.tabs.messages-tab />
                        @break
                    @default
                        <livewire:customers.tabs.customers-tab />
                @endswitch
            </div>
        </div>
    </div>
</x-layouts.app>
