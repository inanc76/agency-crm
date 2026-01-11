{{--
🚀 MAIL HEADER PARTIAL
---------------------------------------------------------
DESCRIPTION: Handles page title, back navigation, provider selector, and save button.
STATE: $provider, $is_active
TRAIT METHODS: save(), test()
---------------------------------------------------------
--}}
<div>
    {{-- Back Button --}}
    <a href="{{ route('settings.index') }}"
        class="inline-flex items-center gap-2 text-[var(--color-text-base)] hover:text-[var(--color-text-heading)] mb-6 transition-colors">
        <x-mary-icon name="o-arrow-left" class="w-5 h-5" />
        <span class="text-sm font-medium">Geri</span>
    </a>

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-skin-heading">Mail Ayarları</h1>
        <p class="text-sm opacity-60 mt-1 text-skin-base">SMTP ve Mailgun e-posta servis ayarlarını yapılandırın</p>
    </div>

    {{-- Header Actions Card --}}
    <div class="theme-card shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
            <h2 class="text-sm font-bold text-skin-heading">E-posta Servis Yapılandırması</h2>
            <div class="flex items-center gap-4">
                {{-- Status Toggle --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model.live="is_active" class="toggle toggle-success toggle-lg" />
                    <div>
                        <div class="text-xs font-medium opacity-60 text-skin-base">Gönderim Durumu</div>
                        <div
                            class="text-sm font-bold {{ $is_active ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]' }}">
                            {{ $is_active ? 'Aktif' : 'Kapalı' }}
                        </div>
                    </div>
                </div>

                {{-- Test Button --}}
                <x-mary-button label="Test Gönder" icon="o-paper-airplane" class="btn-sm theme-btn-edit"
                    wire:click="test" />
            </div>
        </div>

        {{-- Provider Selection --}}
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider mb-4 opacity-60 text-skin-base">Servis Seçimi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- SMTP Option --}}
                <label class="block cursor-pointer">
                    <input type="radio" wire:model.live="provider" value="smtp" class="peer sr-only">
                    <div
                        class="p-4 bg-[var(--card-bg)] border-2 border-[var(--card-border)] shadow-sm rounded-xl peer-checked:ring-2 peer-checked:ring-[var(--primary-color)] transition-all relative">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $provider === 'smtp' ? 'border-[var(--primary-color)]' : 'border-[var(--card-border)]' }}">
                                    @if($provider === 'smtp')
                                        <div class="w-2.5 h-2.5 rounded-full bg-[var(--primary-color)]"></div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-skin-heading">SMTP</div>
                                    <div class="text-xs opacity-60 text-skin-base">Kendi sunucunuz</div>
                                </div>
                            </div>
                            @if($provider === 'smtp')
                                <span
                                    class="text-xs font-bold px-2 py-1 rounded text-[var(--color-success)] bg-[var(--color-success)]/10">Aktif</span>
                            @endif
                        </div>
                    </div>
                </label>

                {{-- MAILGUN Option --}}
                <label class="block cursor-pointer">
                    <input type="radio" wire:model.live="provider" value="mailgun" class="peer sr-only">
                    <div
                        class="p-4 bg-[var(--card-bg)] border-2 border-[var(--card-border)] rounded-xl peer-checked:border-[var(--color-active-border)] peer-checked:bg-[var(--color-active-bg)] transition-all relative">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $provider === 'mailgun' ? 'border-[var(--color-active-border)]' : 'border-[var(--card-border)]' }}">
                                    @if($provider === 'mailgun')
                                        <div class="w-2.5 h-2.5 rounded-full bg-[var(--color-active-border)]"></div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-[var(--color-text-heading)] text-sm">MAILGUN</div>
                                    <div class="text-xs text-[var(--color-text-muted)]">API Hizmeti</div>
                                </div>
                            </div>
                            @if($provider === 'mailgun')
                                <span
                                    class="text-xs font-semibold px-2 py-1 rounded text-[var(--color-success)] bg-[var(--color-success)]/10">Aktif</span>
                            @endif
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="pt-6 mt-6 border-t border-[var(--card-border)] flex justify-end">
            <button type="button" wire:click="save" wire:loading.attr="disabled" class="theme-btn-save">
                <x-mary-icon name="o-check" class="w-4 h-4" wire:loading.remove wire:target="save" />
                <span class="loading loading-spinner loading-xs" wire:loading wire:target="save"></span>
                <span>Ayarları Kaydet</span>
            </button>
        </div>
    </div>
</div>