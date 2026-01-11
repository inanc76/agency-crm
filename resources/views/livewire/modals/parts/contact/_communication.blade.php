{{--
🚀 CONTACT COMMUNICATION PARTIAL
---------------------------------------------------------
SORUMLULUK ALANI: Kişinin iletişim kanallarının (Email, Telefon, Dahili) dinamik yönetimi.
STATE BAĞLANTISI: $emails (array), $phones (array), $isViewMode.
VALIDASYON ŞERHİ (V10):
- emails.* must be valid email format.
- phones.*.number max:20.
- 'Dahili' fields strictly numeric (JS protected).
---------------------------------------------------------
--}}
<div class="theme-card p-6 shadow-sm border border-[var(--success-border)] bg-[var(--success-bg)]">
    <h2 class="text-base font-bold mb-4 text-skin-heading">İletişim Bilgileri</h2>

    <div class="grid grid-cols-2 gap-6">
        {{-- Emails --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-medium opacity-60">Email</label>
                @if(!$isViewMode)
                    <button type="button" wire:click="addEmail" class="hover:opacity-80 text-xs font-bold"
                        style="color: var(--action-link-color);">
                        + Email
                    </button>
                @endif
            </div>
            @if($isViewMode)
                @foreach($emails as $email)
                    @if($email)
                        <div class="text-sm font-medium mb-1">{{ $email }}</div>
                    @endif
                @endforeach
                @if(empty(array_filter($emails)))
                    <div class="text-sm opacity-40">-</div>
                @endif
            @else
                @foreach($emails as $index => $email)
                    <div class="flex items-center gap-2 mb-2">
                        <input type="email" wire:model="emails.{{ $index }}" placeholder="Email {{ $index + 1 }}"
                            class="input flex-1 bg-[var(--card-bg)]">
                        @if($index > 0)
                            <button type="button" wire:click="removeEmail({{ $index }})" class="text-[var(--color-danger)]">
                                <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                    @error('emails.' . $index) <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                @endforeach
            @endif
        </div>

        {{-- Phones --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-medium opacity-60">Telefon</label>
                @if(!$isViewMode)
                    <button type="button" wire:click="addPhone" class="hover:opacity-80 text-xs font-bold"
                        style="color: var(--action-link-color);">
                        + Telefon
                    </button>
                @endif
            </div>
            @if($isViewMode)
                @foreach($phones as $phone)
                    @if(!empty($phone['number']))
                        <div class="text-sm font-medium mb-1">
                            {{ $phone['number'] }}
                            @if(!empty($phone['extension']))
                                <span class="opacity-70 text-xs">(Dahili: {{ $phone['extension'] }})</span>
                            @endif
                        </div>
                    @endif
                @endforeach
                @if(empty(array_filter(array_column($phones, 'number'))))
                    <div class="text-sm opacity-40">-</div>
                @endif
            @else
                @foreach($phones as $index => $phone)
                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" wire:model="phones.{{ $index }}.number" placeholder="Telefon {{ $index + 1 }}"
                            class="input flex-1 bg-[var(--card-bg)]">
                        <input type="text" wire:model="phones.{{ $index }}.extension" placeholder="Dahili" maxlength="5"
                            class="input w-24 bg-[var(--card-bg)] text-center"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5)">
                        @if($index > 0)
                            <button type="button" wire:click="removePhone({{ $index }})" class="text-[var(--color-danger)]">
                                <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                    @error('phones.' . $index . '.number') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span>
                    @enderror
                @endforeach
            @endif
        </div>
    </div>
</div>