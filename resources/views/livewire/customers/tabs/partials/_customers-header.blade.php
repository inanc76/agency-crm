{{--
═══════════════════════════════════════════════════════════════════════════
📄 CUSTOMERS LIST HEADER
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: Müşteri listesi üst başlığı. Toplu silme, müşteri sayısı ve yeni ekleme aksiyonunu içerir.
📝 Kullanım Notu: $selected array (bulk selection), $customers paginator gereklidir.
🔗 State Dependencies: $selected, $customers

--}}

<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-bold" class="text-skin-heading">Müşteriler</h2>
        <p class="text-sm opacity-60">Tüm müşterilerinizi görüntüleyin ve yönetin</p>
    </div>
    <div class="flex items-center gap-4">
        @if(count($selected) > 0)
            <button wire:click="deleteSelected"
                wire:confirm="Seçili {{ count($selected) }} müşteriyi silmek istediğinize emin misiniz?"
                class="btn-danger-outline">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Seçilileri Sil ({{ count($selected) }})
            </button>
        @endif

        <span class="text-sm opacity-60">
            <span class="font-medium" style="color: var(--btn-save-bg);">Aktif</span>
            {{ $customers->total() }} müşteri
        </span>
        <x-customer-management.action-button label="Yeni Müşteri" href="/dashboard/customers/create" />
    </div>
</div>