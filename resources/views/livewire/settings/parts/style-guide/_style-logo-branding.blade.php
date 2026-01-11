{{--
═══════════════════════════════════════════════════════════════════════════
LOGO & BRANDING
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: Logo varyasyonları, ölçekleme ve kurumsal isim fall-back yapılarını içerir.
Genellikle Header ve Login sayfalarında standart olarak kullanılır.
📝 Kullanım Notu: Logo scale ayarı için --logo-scale değişkeni kullanılır. asset() helper ile logoya erişilir.

--}}

{{-- 1. Logo Ayarları Önizleme --}}
<x-mary-collapse name="preview1" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-photo" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Logo Ayarları Önizleme</span>
            </div>
            <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--logo-scale:
                {{ $logo_scale }}</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="p-6 bg-slate-100 rounded-xl flex items-center justify-center border border-dashed border-slate-300">
            <div class="flex items-center gap-4 bg-white p-4 rounded-lg shadow-sm w-full max-w-md">
                @if($current_logo_path)
                    <img src="{{ asset('storage/' . $current_logo_path) }}" alt="Logo" class="object-contain"
                        style="height: {{ 2.5 * $logo_scale }}rem">
                @else
                    <span class="text-xl font-bold tracking-tight text-slate-800">{{ $site_name }}</span>
                @endif
                <div class="h-4 w-px bg-slate-200 mx-2"></div>
                <span class="text-sm font-medium text-slate-500">Uygulama Önizleme</span>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>