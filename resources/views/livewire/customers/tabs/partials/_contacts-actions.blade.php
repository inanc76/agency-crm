{{-- 
    SECTION: Contacts Actions & Header
    Mimarın Notu: Bu bölüm yeni kişi ekleme ve toplu işlem butonlarını içerir.
    İş Mantığı Şerhi: Contact modeli ile konuşur, HasCustomerActions trait'ini kullanır.
    Mühür Koruması: MaryUI button bileşenleri ve action-button CSS sınıfları korunmalıdır.
--}}

{{-- Header with Action Button --}}
<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-bold text-skin-heading">Kişiler</h2>
        <p class="text-sm text-skin-muted">Tüm iletişim kişilerini görüntüleyin ve yönetin</p>
    </div>
    <div class="flex items-center gap-4">
        @if(count($selected) > 0)
            <button wire:click="deleteSelected"
                wire:confirm="Seçili {{ count($selected) }} kişiyi silmek istediğinize emin misiniz?"
                class="btn-danger-outline">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Seçilileri Sil ({{ count($selected) }})
            </button>
        @endif

        <span class="text-sm text-skin-muted">{{ $contacts->total() }} kişi</span>
        <x-customer-management.action-button label="Yeni Kişi" href="{{ route('customers.contacts.create') }}" />
    </div>
</div>

{{-- Filter Panel --}}
<x-mary-card class="theme-card shadow-sm mb-6" shadow separator>
    <div class="flex flex-wrap items-center gap-4">
        <div class="w-48">
            <x-mary-select :options="[['id' => 'all', 'display_label' => 'Tüm Durumlar']] + $statusOptions->map(fn($i) => ['id' => $i->key, 'display_label' => $i->display_label])->toArray()"
                option-label="display_label" option-value="id" wire:model.live="statusFilter"
                class="select-sm !bg-white !border-gray-200" />
        </div>

        <div class="flex-grow max-w-xs">
            <x-mary-input placeholder="Ara..." icon="o-magnifying-glass" class="input-sm !bg-white !border-gray-200"
                wire:model.live.debounce.300ms="search" />
        </div>

        <div class="flex items-center gap-1 ml-auto flex-wrap justify-end">
            <x-mary-button label="0-9" wire:click="$set('letter', '0-9')"
                class="btn-ghost btn-xs font-medium {{ $letter === '0-9' ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover px-2" />
            <x-mary-button label="Tümü" wire:click="$set('letter', '')"
                class="btn-ghost btn-xs font-medium {{ $letter === '' ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover px-2" />
            <div class="divider divider-horizontal mx-0 h-4"></div>
            @foreach(range('A', 'Z') as $char)
                <x-mary-button :label="$char" wire:click="$set('letter', '{{ $char }}')"
                    class="btn-ghost btn-xs font-medium {{ $letter === $char ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover min-w-[24px] !px-1" />
            @endforeach
        </div>
    </div>
</x-mary-card>