{{--
🚀 CONTACT SOCIAL PROFILES PARTIAL
---------------------------------------------------------
SORUMLULUK ALANI: Kişinin dijital varlıkları ve sosyal medya bağlantılarının yönetimi.
STATE BAĞLANTISI: $social_profiles (array), $isViewMode.
VALIDASYON ŞERHİ (V10):
- social_profiles.*.url must be a valid URL format.
- max:255 characters per URL to prevent DB overflow.
---------------------------------------------------------
--}}
<div class="theme-card p-6 shadow-sm border border-[var(--brand-primary)]/20 bg-[var(--brand-primary)]/5">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Sosyal Medya Profilleri</h2>

    <div>
        <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-medium opacity-60">Profiller</label>
            @if(!$isViewMode)
                <button type="button" wire:click="addSocialProfile" class="hover:opacity-80 text-xs font-bold"
                    style="color: var(--action-link-color);">
                    + Profil
                </button>
            @endif
        </div>

        @if($isViewMode)
            @foreach($social_profiles as $profile)
                @if(!empty($profile['url']))
                    <div class="text-sm font-medium mb-1">
                        <a href="{{ $profile['url'] }}" target="_blank" class="text-[var(--action-link-color)] hover:underline">
                            {{ $profile['name'] ?: $profile['url'] }}
                        </a>
                    </div>
                @endif
            @endforeach
            @if(empty(array_filter(array_column($social_profiles, 'url'))))
                <div class="text-sm opacity-40">-</div>
            @endif
        @else
            @foreach($social_profiles as $index => $profile)
                <div class="flex items-center gap-2 mb-2">
                    <input type="text" wire:model="social_profiles.{{ $index }}.name" placeholder="Başlık (örn: LinkedIn)"
                        class="input w-1/3 bg-[var(--card-bg)]">
                    <input type="text" wire:model="social_profiles.{{ $index }}.url" placeholder="Link"
                        class="input flex-1 bg-[var(--card-bg)]">
                    @if($index > 0)
                        <button type="button" wire:click="removeSocialProfile({{ $index }})" class="text-[var(--color-danger)]">
                            <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                        </button>
                    @endif
                </div>
                @error('social_profiles.' . $index . '.url') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span>
                @enderror
            @endforeach
        @endif
    </div>
</div>