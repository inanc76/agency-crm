{{--
📜 PRICE LIST PARTIAL
---------------------------------------------------------
MİMARIN NOTU: Bu parça, 'price_definitions' tablosundaki verilerin
listelendiği, arandığı ve durumlarının yönetildiği UI bloktur.
Mali veri bütünlüğü için burada sadece "View" yetkisi esastır.

BAĞLANTILAR:
- $prices: Ana sorgudan gelen paginated data.
- $categories, $durations: Referans verileri (Label eşleşmesi için).
- edit($id): Düzenleme modalını tetikler.
- delete($id): Silme işlemini tetikler (Confirm ile).

VALIDASYON UYARISI:
- Silme işleminde 'wire:confirm' zorunludur. Yanlışlıkla silinen fiyatlar
hesaplamaları bozabilir.
---------------------------------------------------------
--}}
<div class="theme-card shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-skin-light bg-skin-hover">
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Durum</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Hizmet Adı</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Kategori</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Süre</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Fiyat</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Oluşturulma</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-skin-light">
                @forelse($prices as $price)
                    <tr class="hover:bg-skin-hover transition-colors group">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-2 h-2 rounded-full {{ $price->is_active ? 'bg-[var(--status-active)] shadow-[0_0_8px_rgba(34,197,94,0.4)]' : 'bg-[var(--status-inactive)]' }}">
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-skin-heading">
                            {{ $price->name }}
                        </td>
                        <td class="px-4 py-4 text-sm opacity-70">
                            {{ $categories->firstWhere('key', $price->category)?->display_label ?? $price->category }}
                        </td>
                        <td class="px-4 py-4 text-sm opacity-70">
                            {{ $durations->firstWhere('key', $price->duration)?->display_label ?? $price->duration }}
                        </td>
                        <td class="px-4 py-4 text-sm text-skin-heading">
                            {{ number_format($price->price, 2) }} {{ $price->currency }}
                        </td>
                        <td class="px-4 py-4 text-sm opacity-60">{{ $price->created_at->format('d.m.Y') }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <x-mary-button icon="o-pencil" class="btn-ghost btn-xs text-skin-muted"
                                    wire:click="edit('{{ $price->id }}')" />
                                <x-mary-button icon="o-trash" class="btn-ghost btn-xs text-skin-muted"
                                    wire:click="delete('{{ $price->id }}')"
                                    wire:confirm="Bu fiyat tanımını silmek istediğinize emin misiniz?" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center opacity-40">
                                <x-mary-icon name="o-banknotes" class="w-12 h-12 mb-2" />
                                <p class="text-sm font-medium">Herhangi bir fiyat tanımı bulunamadı.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>