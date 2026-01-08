{{-- Temel Bilgiler Card --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4" style="color: var(--color-text-heading);">Temel Bilgiler</h2>

    <div class="grid grid-cols-2 gap-8">
        {{-- Firma Tipi --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60" style="color: var(--color-text-base);">Firma
                Tipi</label>
            @if($isViewMode)
                @php
                    $typeLabel = collect($customerTypes)->firstWhere('id', $customer_type)['name'] ?? '-';
                @endphp
                <div class="text-sm font-medium" style="color: var(--color-text-base);">{{ $typeLabel }}</div>
            @else
                <select wire:model="customer_type" class="select w-full">
                    @foreach($customerTypes as $type)
                        <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                    @endforeach
                </select>
                @error('customer_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Firma Adı --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60" style="color: var(--color-text-base);">Firma
                Adı</label>
            @if($isViewMode)
                <div class="text-sm font-medium" style="color: var(--color-text-base);">{{ $name ?: '-' }}</div>
            @else
                <input type="text" wire:model.blur="name" placeholder="Firma adını girin" class="input w-full">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Email --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-medium opacity-60" style="color: var(--color-text-base);">Email</label>
                @if(!$isViewMode && count($emails) < 3)
                    <button type="button" wire:click="addEmail" class="hover:opacity-80 text-sm font-medium"
                        style="color: var(--action-link-color);">
                        + email
                    </button>
                @endif
            </div>

            @if($isViewMode)
                @foreach($emails as $email)
                    @if($email)
                        <div class="text-sm font-medium mb-1" style="color: var(--color-text-base);">{{ $email }}</div>
                    @endif
                @endforeach
                @if(empty(array_filter($emails)))
                <div class="text-sm opacity-40" style="color: var(--color-text-base);">-</div> @endif
            @else
                @foreach($emails as $index => $email)
                    <div class="flex items-center gap-2 mb-2">
                        <input type="email" wire:model="emails.{{ $index }}" placeholder="info@sirket.com" class="input flex-1">
                        @if($index > 0)
                            <button type="button" wire:click="removeEmail({{ $index }})" class="text-red-500 hover:text-red-700">
                                <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                @endforeach
                @error('emails.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Telefon --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-medium opacity-60"
                    style="color: var(--color-text-base);">Telefon</label>
                @if(!$isViewMode)
                    <button type="button" wire:click="addPhone" class="hover:opacity-80 text-sm font-medium"
                        style="color: var(--action-link-color);">
                        + telefon
                    </button>
                @endif
            </div>

            @if($isViewMode)
                @foreach($phones as $phone)
                    @if($phone)
                        <div class="text-sm font-medium mb-1" style="color: var(--color-text-base);">{{ $phone }}</div>
                    @endif
                @endforeach
                @if(empty(array_filter($phones)))
                <div class="text-sm opacity-40" style="color: var(--color-text-base);">-</div> @endif
            @else
                @foreach($phones as $index => $phone)
                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" wire:model.blur="phones.{{ $index }}" placeholder="+90 555 123 45 67"
                            class="input flex-1" x-on:input="$el.value = $el.value.replace(/[^0-9+ ]/g, '')">
                        @if($index > 0)
                            <button type="button" wire:click="removePhone({{ $index }})" class="text-red-500 hover:text-red-700">
                                <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Web Sitesi --}}
        <div class="col-span-2">
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-medium opacity-60" style="color: var(--color-text-base);">Web
                    Sitesi</label>
                @if(!$isViewMode)
                    <button type="button" wire:click="addWebsite" class="hover:opacity-80 text-sm font-medium"
                        style="color: var(--action-link-color);">
                        + websitesi
                    </button>
                @endif
            </div>

            @if($isViewMode)
                @foreach($websites as $website)
                    @if($website)
                        <div class="text-sm font-bold mb-1">
                            <a href="{{ $website }}" target="_blank" class="hover:underline"
                                style="color: var(--action-link-color);">{{ $website }}</a>
                        </div>
                    @endif
                @endforeach
                @if(empty(array_filter($websites)))
                <div class="text-sm opacity-40" style="color: var(--color-text-base);">-</div> @endif
            @else
                @foreach($websites as $index => $website)
                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" wire:model.blur="websites.{{ $index }}" placeholder="https://www.ornek.com"
                            class="input flex-1" x-on:blur="
                                                                        let val = $el.value.trim();
                                                                        if (val && !val.match(/^https?:\/\//)) {
                                                                            $el.value = 'https://' + val;
                                                                            $el.dispatchEvent(new Event('input'));
                                                                        }
                                                                    ">
                        @if($index > 0)
                            <button type="button" wire:click="removeWebsite({{ $index }})" class="text-red-500 hover:text-red-700">
                                <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>