{{-- Adres Bilgileri Card --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Adres Bilgileri</h2>

    <div class="grid grid-cols-2 gap-8">
        {{-- Ülke --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Ülke</label>
            @if($isViewMode)
                @php
                    $countryName = collect($countries)->firstWhere('id', $country_id)['name'] ?? '-';
                @endphp
                <div class="text-sm font-medium text-skin-base">{{ $countryName }}</div>
            @else
                <select wire:model="country_id" wire:change="loadCities" class="select w-full">
                    <option value="">Ülke seçin...</option>
                    @foreach($countries as $country)
                        <option value="{{ $country['id'] }}">{{ $country['name'] }}</option>
                    @endforeach
                </select>
                @error('country_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Şehir --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Şehir</label>
            @if($isViewMode)
                @php
                    $cityName = collect($cities)->firstWhere('id', $city_id)['name'] ?? '-';
                @endphp
                <div class="text-sm font-medium text-skin-base">{{ $cityName }}</div>
            @else
                <select wire:model="city_id" class="select w-full">
                    <option value="">Şehir seçin...</option>
                    @foreach($cities as $city)
                        <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                    @endforeach
                </select>
                @error('city_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Adres --}}
        <div class="col-span-2">
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Adres</label>
            @if($isViewMode)
                <div class="text-sm font-medium text-skin-base">{{ $address ?: '-' }}</div>
            @else
                <textarea wire:model.blur="address" rows="3" placeholder="Detaylı adres bilgisi"
                    class="textarea w-full resize-none"></textarea>
                @error('address') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>
    </div>
</div>