{{-- Teklif Ekleri Card --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Teklif Ekleri</h2>
        @if(!$isViewMode)
            <button type="button" wire:click="openAttachmentModal"
                class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                <x-mary-icon name="o-plus" class="w-4 h-4" />
                Teklif Ekle
            </button>
        @endif
    </div>

    @if(count($attachments) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-2 font-medium opacity-60">Başlık</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">Açıklama</th>
                        <th class="text-right py-2 px-2 font-medium opacity-60">Fiyat</th>
                        @if(!$isViewMode)
                            <th class="text-center py-2 px-2 font-medium opacity-60 w-24">İşlemler</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($attachments as $index => $attachment)
                        <tr class="border-b border-slate-100 hover:bg-white/50">
                            <td class="py-3 px-2">
                                <div class="cursor-pointer" wire:click="downloadAttachment({{ $index }})">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $ext = strtolower(pathinfo($attachment['file_name'], PATHINFO_EXTENSION));
                                            $iconName = match (true) {
                                                $ext === 'pdf' => 'o-document-text',
                                                in_array($ext, ['doc', 'docx']) => 'o-clipboard-document',
                                                in_array($ext, ['ppt', 'pptx']) => 'o-presentation-chart-line',
                                                default => 'o-document',
                                            };
                                        @endphp
                                        <x-mary-icon :name="$iconName" class="w-4 h-4 text-slate-400" />
                                        <span
                                            class="font-medium hover:text-blue-600 transition-colors">{{ $attachment['title'] }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 hover:text-blue-500 transition-colors">
                                        {{ $attachment['file_name'] }}
                                        ({{ number_format($attachment['file_size'] / 1024, 1) }} KB)
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-2 text-slate-600 text-xs">
                                {{ Str::limit($attachment['description'] ?? '-', 50) }}
                            </td>
                            <td class="py-3 px-2 text-right font-medium">
                                {{ number_format($attachment['price'], 2) }} {{ $attachment['currency'] }}
                            </td>
                            @if(!$isViewMode)
                                <td class="py-3 px-2 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" wire:click="editAttachment({{ $index }})"
                                            class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                                            <x-mary-icon name="o-pencil" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="removeAttachment({{ $index }})"
                                            wire:confirm="Bu eki silmek istediğinize emin misiniz?"
                                            class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                                            <x-mary-icon name="o-trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-slate-400">
            <x-mary-icon name="o-paper-clip" class="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p class="text-sm">Henüz ek dosya eklenmemiş</p>
            @if(!$isViewMode)
                <p class="text-xs mt-1">Yukarıdaki "+ Teklif Ekle" butonuna tıklayarak başlayın</p>
            @endif
        </div>
    @endif
</div>