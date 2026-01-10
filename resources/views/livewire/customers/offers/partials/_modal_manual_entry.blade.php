{{--
@component: _modal_manual_entry.blade.php
@section: Manuel Kalem Ekleme Modalı
@description: Ürün/Hizmet veritabanında olmayan kalemlerin manuel olarak teklife eklenmesini sağlayan modal.
@params: $showManualEntryModal (bool), $manualItems (array), $currency (string)
@events: saveManualItems, addManualItemRow, removeManualItemRow
--}}
{{-- Manual Entry Modal --}}
<x-mary-modal wire:model="showManualEntryModal" title="Manuel Kalem Ekle" class="backdrop-blur" box-class="!max-w-6xl">
    <div class="overflow-x-auto min-h-[300px]">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="text-left py-2 px-2 w-[25%]">Hizmet Adı *</th>
                    <th class="text-left py-2 px-2 w-[25%]">Açıklama</th>
                    <th class="text-center py-2 px-2 w-[10%]">Süre (Yıl)</th>
                    <th class="text-right py-2 px-2 w-[15%]">Fiyat ({{ $currency }}) *</th>
                    <th class="text-center py-2 px-2 w-[10%]">Adet *</th>
                    <th class="text-right py-2 px-2 w-[10%]">Toplam</th>
                    <th class="w-[5%]"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($manualItems as $index => $mItem)
                    <tr wire:key="manual-item-{{ $index }}">
                        <td class="p-2 align-top">
                            <input type="text" wire:model="manualItems.{{ $index }}.service_name"
                                class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400"
                                placeholder="Hizmet Adı">
                            @error("manualItems.{$index}.service_name")
                                <span class="text-skin-danger text-[10px] block mt-1">{{ $message }}</span>
                            @enderror
                        </td>
                        <td class="p-2 align-top">
                            <textarea wire:model="manualItems.{{ $index }}.description"
                                class="textarea textarea-sm w-full bg-white border-slate-200 focus:border-blue-400" rows="1"
                                placeholder="Açıklama"></textarea>
                        </td>
                        <td class="p-2 align-top">
                            <input type="number" wire:model="manualItems.{{ $index }}.duration"
                                class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400 text-center"
                                placeholder="Opsiyonel" min="1">
                        </td>
                        <td class="p-2 align-top">
                            <input type="number" wire:model.live="manualItems.{{ $index }}.price"
                                class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400 text-right"
                                min="0" step="0.01">
                            @error("manualItems.{$index}.price")
                                <span class="text-skin-danger text-[10px] block mt-1">{{ $message }}</span>
                            @enderror
                        </td>
                        <td class="p-2 align-top">
                            <input type="number" wire:model.live="manualItems.{{ $index }}.quantity"
                                class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400 text-center"
                                min="1">
                        </td>
                        <td class="p-2 align-top text-right font-medium pt-3 text-slate-700">
                            {{ number_format(((float) ($mItem['price'] ?? 0)) * ((int) ($mItem['quantity'] ?? 1)), 0, ',', '.') }}
                        </td>
                        <td class="p-2 align-top pt-2 text-center">
                            @if(count($manualItems) > 1)
                                <button type="button" wire:click="removeManualItemRow({{ $index }})"
                                    class="text-skin-danger hover:opacity-80 p-1">
                                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <button type="button" wire:click="addManualItemRow"
                class="flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                <x-mary-icon name="o-plus-circle" class="w-4 h-4" />
                Yeni Satır Ekle
            </button>
        </div>
    </div>

    <x-slot:actions>
        <button wire:click="$set('showManualEntryModal', false)" class="theme-btn-cancel">
            Vazgeç
        </button>
        <button wire:click="saveManualItems" class="theme-btn-save">
            <x-mary-icon name="o-check" class="w-4 h-4" />
            Listeye Ekle
        </button>
    </x-slot:actions>
</x-mary-modal>