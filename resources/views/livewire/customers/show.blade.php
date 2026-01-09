<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\ReferenceItem;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\DB;

new
    #[Layout('components.layouts.app', ['title' => 'Müşteri Detayı'])]
    class extends Component {
    use Toast;

    public Customer $customer;
    public array $customerTypes = [];
    public array $countries = [];
    public array $cities = [];
    public array $relatedCustomers = [];

    public function mount(string $customer): void
    {
        $this->customer = Customer::with('relatedCustomers')->findOrFail($customer);

        // Load reference data for display
        $this->customerTypes = ReferenceItem::where('category_key', 'CUSTOMER_TYPE')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($item) => ['id' => $item->key, 'name' => $item->display_label])
            ->toArray();

        // Load countries for display
        $this->countries = DB::table('countries')
            ->where('is_active', true)
            ->get(['id', 'name'])
            ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
            ->toArray();

        // Load cities for display
        $this->cities = DB::table('cities')
            ->where('is_active', true)
            ->get(['id', 'name'])
            ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
            ->toArray();

        $this->relatedCustomers = $this->customer->relatedCustomers->toArray();
    }

    public function delete(): void
    {
        $this->customer->delete();
        $this->success('Müşteri Silindi', 'Müşteri başarıyla silindi.');
        $this->redirect('/dashboard/customers?tab=customers');
    }

    private function getDisplayValue(string $key, array $items): string
    {
        $item = collect($items)->firstWhere('id', $key);
        return $item['name'] ?? $key;
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=customers"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Müşteri Listesi</span>
        </a>

        {{-- Header with Action Buttons --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-skin-heading">{{ $customer->name }}</h1>
                <p class="text-sm text-[var(--color-text-muted)] mt-1">Müşteri detayları</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/dashboard/customers/{{ $customer->id }}/edit" class="btn-primary">
                    Düzenle
                </a>
                <button type="button" wire:click="delete"
                    wire:confirm="Bu müşteriyi silmek istediğinizden emin misiniz?"
                    class="bg-[var(--color-danger)] text-white px-6 py-2.5 rounded-lg font-medium text-sm hover:bg-red-700 transition-colors shadow-sm">
                    Sil
                </button>>
            </div>
        </div>

        {{-- Main Layout: 80% Left, 20% Right --}}
        <div class="flex gap-6">
            {{-- Left Column (80%) --}}
            <div class="w-4/5 space-y-6">
                {{-- Temel Bilgiler Card --}}
                <x-mary-card title="Temel Bilgiler" class="!rounded-xl shadow-sm">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-skin-base mb-2">Müşteri Tipi</label>
                            <div class="text-sm text-skin-heading">
                                {{ $this->getDisplayValue($customer->customer_type, $customerTypes) }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-skin-base mb-2">Müşteri Adı</label>
                            <div class="text-sm text-skin-heading">{{ $customer->name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-skin-base mb-2">E-posta Adresleri</label>
                            <div class="space-y-1">
                                @foreach($customer->emails ?? [] as $email)
                                    <div class="text-sm text-skin-heading">{{ $email }}</div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-skin-base mb-2">Telefon Numaraları</label>
                            <div class="space-y-1">
                                @foreach($customer->phones ?? [] as $phone)
                                    <div class="text-sm text-skin-heading">{{ $phone }}</div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-skin-base mb-2">Web Siteleri</label>
                            <div class="space-y-1">
                                @foreach($customer->websites ?? [] as $website)
                                    <a href="{{ $website }}" target="_blank"
                                        class="text-sm text-skin-primary hover:underline block">{{ $website }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </x-mary-card>

                {{-- Adres Bilgileri Card --}}
                <x-mary-card title="Adres Bilgileri" class="!rounded-xl shadow-sm">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-skin-base mb-2">Ülke</label>
                            <div class="text-sm text-skin-heading">
                                {{ $this->getDisplayValue($customer->country_id, $countries) }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-skin-base mb-2">Şehir</label>
                            <div class="text-sm text-skin-heading">
                                {{ $this->getDisplayValue($customer->city_id, $cities) }}
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-skin-base mb-2">Adres</label>
                            <div class="text-sm text-skin-heading">{{ $customer->address ?: '-' }}</div>
                        </div>
                    </div>
                </x-mary-card>

                {{-- Cari Bilgiler Card --}}
                <x-mary-card title="Cari Bilgiler" class="!rounded-xl shadow-sm">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-skin-base mb-2">Ünvan</label>
                            <div class="text-sm text-skin-heading">{{ $customer->title ?: '-' }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-skin-base mb-2">Vergi Dairesi</label>
                            <div class="text-sm text-skin-heading">{{ $customer->tax_office ?: '-' }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-skin-base mb-2">Vergi Numarası</label>
                            <div class="text-sm text-skin-heading">{{ $customer->tax_number ?: '-' }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-skin-base mb-2">Cari Kodu</label>
                            <div class="text-sm text-skin-heading">{{ $customer->current_code ?: '-' }}</div>
                        </div>
                    </div>
                </x-mary-card>

                {{-- İlişkili Firmalar Card --}}
                @if(count($relatedCustomers) > 0)
                    <x-mary-card title="İlişkili Firmalar" class="!rounded-xl shadow-sm">
                        <div class="space-y-2">
                            @foreach($relatedCustomers as $related)
                                <div class="flex items-center justify-between p-3 bg-[var(--dropdown-hover-bg)] rounded-lg">
                                    <span class="text-sm text-skin-heading">{{ $related['name'] }}</span>
                                    <a href="/dashboard/customers/{{ $related['id'] }}"
                                        class="text-sm text-skin-primary hover:underline">
                                        Görüntüle
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </x-mary-card>
                @endif
            </div>

            {{-- Right Column (20%) --}}
            <div class="w-1/5">
                <x-mary-card title="Logo" class="!rounded-xl shadow-sm">
                    @if($customer->logo_url)
                        <img src="{{ asset('storage' . $customer->logo_url) }}" alt="{{ $customer->name }}"
                            class="w-full h-auto rounded-lg">
                    @else
                        <div class="flex items-center justify-center h-32 bg-[var(--dropdown-hover-bg)] rounded-lg">
                            <x-mary-icon name="o-photo" class="w-12 h-12 text-[var(--color-text-muted)]" />
                        </div>
                    @endif
                </x-mary-card>
            </div>
        </div>
    </div>
</div>