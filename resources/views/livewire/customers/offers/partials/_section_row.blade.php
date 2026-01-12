{{--
@component: _section_row.blade.php
@section: Teklif Bölümü Başlık, Açıklama ve Kalemler
@description: Teklifin her bir bölümünü temsil eden kart bileşeni.
@params: $isViewMode (bool), $section (array), $index (int)
--}}
<div class="theme-card p-6 shadow-sm mb-6" wire:key="section-container-{{ $index }}">
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
        <h2 class="text-base font-bold text-skin-heading">Teklif Bölümü - {{ $index + 1 }}</h2>
        @if(!$isViewMode && count($sections) > 1)
            <button type="button" wire:click="removeSection({{ $index }})"
                class="text-xs text-skin-danger hover:underline cursor-pointer flex items-center gap-1">
                <x-mary-icon name="o-trash" class="w-3 h-3" />
                Bölümü Kaldır
            </button>
        @endif
    </div>

    {{-- Başlık ve Açıklama Alanı --}}
    <div class="grid grid-cols-1 gap-4 mb-6">
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Bölüm Başlığı *</label>
            @if($isViewMode)
                <div class="text-sm font-medium">{{ $section['title'] }}</div>
            @else
                <input type="text" wire:model.live="sections.{{ $index }}.title"
                    placeholder="Örn: Yazılım Geliştirme Hizmetleri" class="input w-full bg-white">
                @error("sections.{$index}.title") <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Bölüm Açıklaması</label>
            @if($isViewMode)
                <div class="text-sm font-medium whitespace-pre-wrap">
                    {{ $section['description'] ?: '-' }}
                </div>
            @else
                <textarea wire:model.live="sections.{{ $index }}.description" class="textarea w-full bg-white" rows="2"
                    placeholder="Bölüm hakkında kısa açıklama..."></textarea>
            @endif
        </div>
    </div>

    {{-- Separator --}}
    <div class="relative py-4">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-slate-100"></div>
        </div>
        <div class="relative flex justify-start">
            <span class="pr-3 bg-white text-[10px] font-bold uppercase tracking-wider text-slate-400">Kalemler</span>
        </div>
    </div>

    {{-- Kalemler Tablosu Alanı --}}
    <div class="mt-2">
        <div class="flex items-center justify-between mb-4">
            <div class="text-xs font-medium opacity-60">Eklenen Hizmetler</div>
            @if(!$isViewMode)
                <div class="flex gap-2">
                    <button type="button" wire:click="openManualEntryModal({{ $index }})"
                        class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                        <x-mary-icon name="o-pencil" class="w-4 h-4" />
                        Manuel Ekle
                    </button>
                    <button type="button" wire:click="openServiceModal({{ $index }})"
                        class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                        <x-mary-icon name="o-plus" class="w-4 h-4" />
                        Hizmet Ekle
                    </button>
                </div>
            @endif
        </div>

        @if(count($section['items']) > 0)
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
                        @foreach($section['items'] as $itemIndex => $item)
                            <tr class="border-b border-slate-100" wire:key="section-{{ $index }}-item-{{ $itemIndex }}">
                                <td class="py-3 px-2 font-normal text-xs">
                                    {{ $item['service_name'] }}
                                </td>
                                <td class="py-3 px-2 text-xs opacity-70">
                                    <div class="flex items-center gap-1 text-slate-500">
                                        <span>{{ Str::limit($item['description'], 30) }}</span>
                                        @if(!$isViewMode)
                                            <button type="button"
                                                wire:click="openItemDescriptionModal({{ $index }}, {{ $itemIndex }})"
                                                class="p-1 hover:bg-slate-200 rounded text-slate-400 hover:text-blue-600 transition-colors cursor-pointer"
                                                title="Açıklamayı Düzenle">
                                                <x-mary-icon name="o-pencil-square" class="w-3.5 h-3.5" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-2 text-center text-xs">
                                    {{ $item['duration'] ? $item['duration'] . ' Yıl' : '-' }}</td>
                                <td class="py-3 px-2 text-right text-xs">
                                    {{ number_format($item['price'], 0, ',', '.') }}
                                    {{ $currency }}
                                </td>
                                <td class="py-3 px-2 text-center">
                                    @if(!$isViewMode)
                                        <input type="number" wire:model.live="sections.{{ $index }}.items.{{ $itemIndex }}.quantity"
                                            class="input w-16 text-center bg-white" min="1">
                                    @else
                                        {{ $item['quantity'] }}
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-right text-xs font-normal text-skin-heading">
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                    {{ $currency }}
                                </td>
                                @if(!$isViewMode)
                                    <td class="py-3 px-2">
                                        <button type="button" wire:click="removeItem({{ $index }}, {{ $itemIndex }})"
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
            <div class="text-center py-6 text-slate-400">
                <x-mary-icon name="o-inbox" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                <p class="text-xs">Bu bölümde henüz kalem eklenmemiş</p>
            </div>
        @endif
        @error("sections.{$index}.items") <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
    </div>
</div>