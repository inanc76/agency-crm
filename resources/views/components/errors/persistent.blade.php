@props(['error' => null])

@if($error)
    <div class="mb-6 rounded-xl p-4 shadow-sm"
        style="background-color: var(--error-panel-bg); border: 1px solid var(--error-panel-border);" x-data="{ 
                copied: false, 
                errorMessage: @js($error) 
            }">
        <div class="flex justify-between items-start gap-4">
            <div class="text-sm font-mono break-all leading-relaxed" style="color: var(--error-panel-text);">
                {{ $error }}
            </div>
            <button type="button" @click="
                        navigator.clipboard.writeText(errorMessage); 
                        copied = true; 
                        setTimeout(() => copied = false, 2000);
                    "
                class="flex items-center gap-1 text-xs font-bold rounded-lg px-3 py-1.5 bg-white transition cursor-pointer min-w-[80px] justify-center"
                style="color: var(--error-btn-text); border: 1px solid var(--error-btn-border);"
                onmouseover="this.style.color='var(--error-btn-hover-text)'; this.style.backgroundColor='var(--error-btn-hover-bg)';"
                onmouseout="this.style.color='var(--error-btn-text)'; this.style.backgroundColor='white';">
                <span x-show="!copied">Kopyala</span>
                <span x-show="copied" class="text-emerald-600 font-bold">Kopyalandı!</span>
            </button>
        </div>
    </div>
@endif