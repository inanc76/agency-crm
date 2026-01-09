{{-- Kayıt Bilgileri Card --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center gap-3 mb-4">
        <x-mary-icon name="o-information-circle" class="w-5 h-5 text-[var(--color-text-muted)]" />
        <h2 class="text-base font-bold text-skin-heading">Kayıt Bilgileri</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Müşteri ID --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Müşteri
                ID</label>
            <div class="flex items-center gap-2">
                <code
                    class="text-[11px] font-mono bg-[var(--dropdown-hover-bg)] px-2 py-1 rounded text-skin-base">{{ $customerId }}</code>
                <button type="button" onclick="navigator.clipboard.writeText('{{ $customerId }}')"
                    class="text-[var(--color-text-muted)] hover:text-skin-heading transition-colors" title="Kopyala">
                    <x-mary-icon name="o-clipboard" class="w-3 h-3" />
                </button>
            </div>
        </div>

        {{-- Kayıt Tarihi --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Kayıt
                Tarihi</label>
            <div class="text-sm font-medium flex items-center gap-2 text-skin-base">
                <x-mary-icon name="o-calendar" class="w-4 h-4 opacity-40" />
                {{ $registration_date }}
            </div>
        </div>

        {{-- Kayıt Eden --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Kayıt
                Eden</label>
            <div class="text-sm font-medium flex items-center gap-2 text-skin-base">
                <div
                    class="w-6 h-6 rounded-full bg-[var(--color-info-bg)] flex items-center justify-center text-[10px] text-skin-primary font-bold border border-[var(--color-info-border)]">
                    AD
                </div>
                Admin
            </div>
        </div>
    </div>
</div>