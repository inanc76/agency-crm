{{--
═══════════════════════════════════════════════════════════════════════════
📄 OFFERS LIST HEADER
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: Teklif listesi üst başlığı. Toplu silme, teklif sayısı ve yeni ekleme aksiyonunu içerir.
📝 Kullanım Notu: $selected array (bulk selection), $offers paginator gereklidir.
🔗 State Dependencies: $selected, $offers

--}}

<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-bold" class="text-skin-heading">Teklifler</h2>
        <p class="text-sm opacity-60">Tüm müşteri tekliflerini görüntüleyin ve yönetin</p>
    </div>
    <div class="flex items-center gap-4">
        @if(count($selected) > 0)
            <button wire:click="deleteSelected"
                wire:confirm="Seçili {{ count($selected) }} teklifi silmek istediğinize emin misiniz?"
                class="btn-danger-outline">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Seçilileri Sil ({{ count($selected) }})
            </button>
        @endif

        <span class="text-sm opacity-60">{{ $offers->total() }} teklif</span>
        <x-customer-management.action-button label="Yeni Teklif" href="/dashboard/customers/offers/create" />
    </div>
</div>