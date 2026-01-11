@if($lastError)
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-100 animate-in fade-in slide-in-from-top-2 duration-300">
        <div class="flex gap-3">
            <x-mary-icon name="o-exclamation-triangle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5" />
            <div class="space-y-1">
                <h4 class="text-sm font-bold text-rose-900 uppercase tracking-tight">Bağlantı Hatası Detayları</h4>
                <div class="text-xs text-rose-700 font-mono leading-relaxed whitespace-pre-wrap selection:bg-rose-200">
                    {{ $lastError }}
                </div>
                <div class="pt-3 flex gap-4">
                    <button type="button"
                        class="text-[10px] font-bold text-rose-600 hover:text-rose-800 underline uppercase tracking-widest"
                        on-click="navigator.clipboard.writeText('{{ addslashes($lastError) }}')">
                        Logu Kopyala
                    </button>
                    <a href="https://docs.min.io/docs/minio-client-complete-guide.html" target="_blank"
                        class="text-[10px] font-bold text-rose-600 hover:text-rose-800 underline uppercase tracking-widest">
                        Dökümantasyon
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif