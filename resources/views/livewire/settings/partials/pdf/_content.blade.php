{{--
🎨 PARTIAL: PDF İçerik ve Renk Ayarları
---------------------------------------------------------------------
Bu dosya PDF'in gövde (Body) stil özelliklerini yönetir.
Font ailesi ve temel renk paleti buradan ayarlanır.

@architect-note [HEX Code Handling]:
Burada seçilen HEX renk kodları (#RWGGBB), backend tarafında
DOMPDF veya Browsershot motoruna iletilirken hiçbir dönüşüme uğramaz.
CSS variable olarak değil, inline-style olarak render edilmelidir.

BAĞIMLILIKLAR (Variables):
- $pdf_font_family (String)
- $pdf_primary_color (String/Hex)
- $pdf_secondary_color (String/Hex)
- $pdf_discount_color (String/Hex)
- $pdf_total_color (String/Hex)
- $pdf_table_header_bg_color (String/Hex)
- $pdf_table_header_text_color (String/Hex)
---------------------------------------------------------------------
--}}
<x-mary-collapse name="content" icon="o-paint-brush">
    <x-slot:heading>
        <span class="font-medium text-sm">İçerik & Renkler</span>
    </x-slot:heading>
    <x-slot:content>
        <div class="space-y-6 pt-4">
            <div class="max-w-md">
                <x-mary-select label="Yazı Tipi Ailesi" :options="[['id' => 'Segoe UI', 'name' => 'Segoe UI'], ['id' => 'Roboto', 'name' => 'Roboto'], ['id' => 'Open Sans', 'name' => 'Open Sans']]"
                    wire:model="pdf_font_family" icon="o-identification" class="!bg-white" />
            </div>

            {{-- Color Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Primary --}}
                <div class="form-control">
                    <label class="label"><span class="label-text text-xs font-semibold uppercase opacity-70">Ana Renk
                            (Primary)</span></label>
                    <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                        <input type="color" wire:model="pdf_primary_color"
                            class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                        <span class="text-xs font-mono opacity-80 uppercase">{{ $pdf_primary_color }}</span>
                    </div>
                </div>
                {{-- Secondary --}}
                <div class="form-control">
                    <label class="label"><span class="label-text text-xs font-semibold uppercase opacity-70">İkincil
                            Renk</span></label>
                    <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                        <input type="color" wire:model="pdf_secondary_color"
                            class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                        <span class="text-xs font-mono opacity-80 uppercase">{{ $pdf_secondary_color }}</span>
                    </div>
                </div>
                {{-- Discount --}}
                <div class="form-control">
                    <label class="label"><span class="label-text text-xs font-semibold uppercase opacity-70">İndirim
                            Rengi</span></label>
                    <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                        <input type="color" wire:model="pdf_discount_color"
                            class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                        <span class="text-xs font-mono opacity-80 uppercase">{{ $pdf_discount_color }}</span>
                    </div>
                </div>
                {{-- Total --}}
                <div class="form-control">
                    <label class="label"><span class="label-text text-xs font-semibold uppercase opacity-70">Toplam
                            Tutar
                            Rengi</span></label>
                    <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                        <input type="color" wire:model="pdf_total_color"
                            class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                        <span class="text-xs font-mono opacity-80 uppercase">{{ $pdf_total_color }}</span>
                    </div>
                </div>
                {{-- Table Header BG --}}
                <div class="form-control">
                    <label class="label"><span class="label-text text-xs font-semibold uppercase opacity-70">Tablo
                            Başlık Arka
                            Plan</span></label>
                    <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                        <input type="color" wire:model="pdf_table_header_bg_color"
                            class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                        <span class="text-xs font-mono opacity-80 uppercase">{{ $pdf_table_header_bg_color }}</span>
                    </div>
                </div>
                {{-- Table Header Text --}}
                <div class="form-control">
                    <label class="label"><span class="label-text text-xs font-semibold uppercase opacity-70">Tablo
                            Başlık
                            Yazı</span></label>
                    <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                        <input type="color" wire:model="pdf_table_header_text_color"
                            class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                        <span class="text-xs font-mono opacity-80 uppercase">{{ $pdf_table_header_text_color }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>