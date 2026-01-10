{{-- Teklif Kalemleri Card --}}
{{--
@component: _items_table.blade.php
@section: Teklif Kalemleri Tablosu
@description: Teklifin içindeki hizmet kalemlerinin listelendiği, düzenlendiği ve silindiği ana tablodur.
@params: $isViewMode (bool), $items (array)
@events: openServiceModal, openManualEntryModal, removeItem, openItemDescriptionModal
--}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Teklif Kalemleri *
        </h2>
        @if(!$isViewMode)
            <div class="flex gap-2">
                <button type="button" wire:click="openManualEntryModal"
                    class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                    <x-mary-icon name="o-pencil" class="w-4 h-4" />
                    Manuel Ekle
                </button>
                <button type="button" wire:click="openServiceModal"
                    class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                    <x-mary-icon name="o-plus" class="w-4 h-4" />
                    Hizmet Ekle
                </button>
            </div>
        @endif
    </div>

    @if(count($items) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-2 font-medium opacity-60">Hizmet Adı</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">Açıklama</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Süre</th>
                        <th class="text-right py-2 px-2 font-medium opacity-60">Fiyat</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Adet</th>
                        <th class="text-right py-2 px-2 font-medium opacity-60">Toplam</th>
                        @if(!$isViewMode)
                            <th class="w-10"></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                        <tr class="border-b border-slate-100" wire:key="item-{{ $index }}">
                            <td class="py-3 px-2 font-normal text-xs">
                                {{ $item['service_name'] }}
                            </td>
                            <td class="py-3 px-2 text-xs opacity-70">
                                <div class="flex items-center gap-1 text-slate-500">
                                    <span>{{ Str::limit($item['description'], 40) }}</span>
                                    @if(!$isViewMode)
                                        <button type="button" wire:click="openItemDescriptionModal({{ $index }})"
                                            class="p-1 hover:bg-slate-200 rounded text-slate-400 hover:text-blue-600 transition-colors cursor-pointer"
                                            title="Açıklamayı Düzenle">
                                            <x-mary-icon name="o-pencil-square" class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-2 text-center text-xs">{{ $item['duration'] }} Yıl</td>
                            <td class="py-3 px-2 text-right text-xs">
                                {{ number_format($item['price'], 0, ',', '.') }}
                                {{ $item['currency'] }}
                            </td>
                            <td class="py-3 px-2 text-center">
                                @if(!$isViewMode)
                                    <input type="number" wire:model.live="items.{{ $index }}.quantity"
                                        class="input w-16 text-center bg-white" min="1">
                                @else
                                    {{ $item['quantity'] }}
                                @endif
                            </td>
                            <td class="py-3 px-2 text-right text-xs font-normal text-skin-heading">
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                {{ $item['currency'] }}
                            </td>
                            @if(!$isViewMode)
                                <td class="py-3 px-2">
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                        class="text-skin-danger hover:opacity-80 cursor-pointer">
                                        <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-slate-400">
            <x-mary-icon name="o-inbox" class="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p class="text-sm">Henüz kalem eklenmemiş</p>
            <p class="text-xs mt-1">Yukarıdaki "+ Hizmet Ekle" butonuna tıklayarak başlayın</p>
        </div>
    @endif
    @error('items') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
</div>