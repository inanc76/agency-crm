{{--
🚀 CONTACT OTHER DETAILS PARTIAL
---------------------------------------------------------
SORUMLULUK ALANI: Kişinin doğum günü, özel notları ve ek meta verileri.
STATE BAĞLANTISI: $birth_date, $isViewMode.
VALIDASYON ŞERHİ (V10):
- 'birth_date' must be a valid date and before today.
---------------------------------------------------------
--}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Diğer Detaylar</h2>

    {{-- Birth Date --}}
    <div class="mb-4">
        <label class="block text-xs font-medium mb-1 opacity-60">Doğum Tarihi</label>
        @if($isViewMode)
            <div class="text-sm font-medium">{{ $birth_date ?: '-' }}</div>
        @else
            <input type="date" wire:model="birth_date" class="input w-full bg-[var(--card-bg)]">
            @error('birth_date') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
        @endif
    </div>

    {{-- Notes Placeholder --}}
    <div class="text-xs opacity-40 italic">Notlar ve etkinlik geçmişi üst sekmelerden yönetilebilir.</div>
</div>