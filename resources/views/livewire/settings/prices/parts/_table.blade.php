<div class="theme-card shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/50">
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Durum</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Hizmet Adı</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Kategori</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Süre</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Fiyat</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Oluşturulma</th>
                    <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 text-right">
                        İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($prices as $price)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-2 h-2 rounded-full {{ $price->is_active ? 'bg-emerald-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]' : 'bg-slate-300' }}">
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm font-medium text-slate-900">
                            {{ $price->name }}
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600">
                            {{ $categories->firstWhere('key', $price->category)?->display_label ?? $price->category }}
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600">
                            {{ $durations->firstWhere('key', $price->duration)?->display_label ?? $price->duration }}
                        </td>
                        <td class="px-4 py-4 text-sm font-bold text-slate-900 text-right">
                            {{ number_format($price->price, 2) }} {{ $price->currency }}
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-500">{{ $price->created_at->format('d.m.Y') }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <x-mary-button icon="o-pencil" class="btn-ghost btn-xs text-slate-400 hover:text-indigo-600"
                                    wire:click="edit('{{ $price->id }}')" />
                                <x-mary-button icon="o-trash" class="btn-ghost btn-xs text-slate-400 hover:text-rose-600"
                                    wire:click="delete('{{ $price->id }}')"
                                    wire:confirm="Bu fiyat tanımını silmek istediğinize emin misiniz?" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <x-mary-icon name="o-banknotes" class="w-12 h-12 mb-2 opacity-20" />
                                <p class="text-sm font-medium">Herhangi bir fiyat tanımı bulunamadı.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>