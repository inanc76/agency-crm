{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
PARTIAL : Değişken Rehberi (_available-shortcodes.blade.php)
SORUMLULUK : Mail şablonunda kullanılabilecek dinamik değişkenlerin listesini ve kopyalama işlevini sunar.

BAĞIMLILIKLAR (Variables):
@var array $variables (Kategorize edilmiş kısa kod listesi)
-------------------------------------------------------------------------
--}}

<div class="theme-card p-4 shadow-sm h-full max-h-[calc(100vh-100px)] overflow-y-auto sticky top-6">
    <h2 class="text-base font-bold mb-4 text-[var(--color-text-heading)]">Değişken Rehberi</h2>
    <p class="text-xs text-[var(--color-text-muted)] mb-6">Değişkenleri tıklayarak veya buton ile kopyalayabilirsiniz.
    </p>

    <div class="space-y-3" x-data="{ openCategory: 'MÜŞTERİ BİLGİLERİ' }">
        @foreach($variables as $category => $vars)
            <div class="border border-[var(--card-border)] rounded-lg overflow-hidden bg-[var(--color-background)]">
                {{-- Accordion Header --}}
                <button @click="openCategory = (openCategory === '{{ $category }}' ? null : '{{ $category }}')"
                    class="w-full flex items-center justify-between p-3 text-left hover:bg-[var(--dropdown-hover-bg)] transition-colors bg-[var(--card-bg)]">
                    <span
                        class="text-xs font-bold uppercase text-[var(--color-text-muted)] tracking-wider">{{ $category }}</span>
                    <x-mary-icon name="o-chevron-down" class="w-3 h-3 transition-transform duration-200"
                        ::class="openCategory === '{{ $category }}' ? 'rotate-180' : ''" />
                </button>

                {{-- Accordion Content --}}
                <div x-show="openCategory === '{{ $category }}'" x-collapse
                    class="p-2 space-y-1 bg-[var(--card-bg)] border-t border-[var(--card-border)]">
                    @foreach($vars as $var)
                        <div class="group flex items-center justify-between p-2 rounded border border-transparent bg-[var(--color-background)]"
                            x-data="{ copied: false }">
                            <div class="flex-1 min-w-0 mr-2">
                                <code
                                    class="text-[11px] font-mono text-[var(--primary-color)] block truncate leading-tight">{{ $var['code'] }}</code>
                                <span
                                    class="text-[10px] text-[var(--color-text-base)] opacity-60 truncate block">{{ $var['desc'] }}</span>
                            </div>

                            <button type="button"
                                class="flex-shrink-0 p-1.5 rounded hover:bg-[var(--card-bg)] transition-colors border border-transparent hover:border-[var(--card-border)] cursor-pointer"
                                :class="copied ? 'text-green-600' : 'text-[var(--color-text-muted)] hover:text-[var(--primary-color)]'"
                                @click="
                                            navigator.clipboard.writeText('{{ $var['code'] }}'); 
                                            copied = true; 
                                            setTimeout(() => copied = false, 2000);
                                        " title="Kopyala">
                                <template x-if="!copied">
                                    <x-mary-icon name="o-document-duplicate" class="w-3.5 h-3.5" />
                                </template>
                                <template x-if="copied">
                                    <x-mary-icon name="o-check" class="w-3.5 h-3.5" />
                                </template>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>