{{--
═══════════════════════════════════════════════════════════════════════════
📄 ASSETS LIST HEADER
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: Varlık listesi üst başlığı. Toplu silme, varlık sayısı ve yeni ekleme aksiyonunu içerir.
📝 Kullanım Notu: $selected array (bulk selection), $assets paginator gereklidir.
🔗 State Dependencies: $selected, $assets

--}}

<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-bold" class="text-skin-heading">Varlıklar</h2>
        <p class="text-sm opacity-60">Tüm müşteri varlıklarını görüntüleyin ve yönetin</p>
    </div>
    <div class="flex items-center gap-4">
        @if(count($selected) > 0)
            <button wire:click="deleteSelected"
                wire:confirm="Seçili {{ count($selected) }} varlığı silmek istediğinize emin misiniz?"
                class="btn-danger-outline">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Seçilileri Sil ({{ count($selected) }})
            </button>
        @endif

        <span class="text-sm opacity-60">{{ $assets->total() }} varlık</span>
        <x-customer-management.action-button label="Yeni Varlık" href="{{ route('customers.assets.create') }}" />
    </div>
</div>