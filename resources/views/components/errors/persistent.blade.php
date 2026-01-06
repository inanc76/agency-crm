@props(['error' => null])

@if($error)
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 shadow-sm" x-data="{ 
            copied: false, 
            errorMessage: @js($error) 
        }">
        <div class="flex justify-between items-start gap-4">
            <div class="text-sm text-red-800 font-mono break-all leading-relaxed">
                {{ $error }}
            </div>
            <button type="button" @click="
                    navigator.clipboard.writeText(errorMessage); 
                    copied = true; 
                    setTimeout(() => copied = false, 2000);
                "
                class="flex items-center gap-1 text-red-600 hover:text-red-800 text-xs font-bold border border-red-300 rounded-lg px-3 py-1.5 bg-white hover:bg-red-50 transition cursor-pointer min-w-[80px] justify-center">
                <span x-show="!copied">Kopyala</span>
                <span x-show="copied" class="text-emerald-600 font-bold">Kopyalandı!</span>
            </button>
        </div>
    </div>
@endif