{{--
═══════════════════════════════════════════════════════════════════════════
📱 TWO-FACTOR QR CODE MODAL
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: QR kod tarama ve manuel kurulum anahtarı gösterimi.
                     TOTP uygulamasına (Google Authenticator, Authy) entegrasyon için gerekli.
📝 Kullanım Notu: $qrCodeSvg SVG formatında QR kod, $manualSetupKey metin formatında anahtar.
🔗 State Dependencies: $showModal, $showVerificationStep, $qrCodeSvg, $manualSetupKey

🔄 Modal States:
   - Initial: QR kod gösterimi ve manuel anahtar
   - Verification: OTP kodu girişi (showVerificationStep = true)
   - Success: Tamamlandı bildirimi

--}}

<flux:modal name="two-factor-setup-modal" class="max-w-md md:min-w-md" @close="closeModal" wire:model="showModal">
    <div class="space-y-6">
        {{-- Modal Header with Icon --}}
        <div class="flex flex-col items-center space-y-4">
            <div
                class="p-0.5 w-auto rounded-full border border-[var(--card-border)] dark:border-[var(--card-border)] bg-[var(--card-bg)] dark:bg-[var(--card-bg)] shadow-sm">
                <div
                    class="p-2.5 rounded-full border border-[var(--card-border)] dark:border-[var(--card-border)] overflow-hidden bg-[var(--dropdown-hover-bg)] dark:bg-[var(--dropdown-hover-bg)] relative">
                    <div
                        class="flex items-stretch absolute inset-0 w-full h-full divide-x [&>div]:flex-1 divide-[var(--card-border)] dark:divide-[var(--card-border)] justify-around opacity-50">
                        @for ($i = 1; $i <= 5; $i++)
                            <div></div>
                        @endfor
                    </div>

                    <div
                        class="flex flex-col items-stretch absolute w-full h-full divide-y [&>div]:flex-1 inset-0 divide-[var(--card-border)] dark:divide-[var(--card-border)] justify-around opacity-50">
                        @for ($i = 1; $i <= 5; $i++)
                            <div></div>
                        @endfor
                    </div>

                    <flux:icon.qr-code class="relative z-20 dark:text-accent-foreground" />
                </div>
            </div>

            <div class="space-y-2 text-center">
                <flux:heading size="lg">{{ $this->modalConfig['title'] }}</flux:heading>
                <flux:text>{{ $this->modalConfig['description'] }}</flux:text>
            </div>
        </div>

        @if ($showVerificationStep)
            {{-- Verification Step: OTP Input --}}
            @include('livewire.settings.two-factor._two-factor-verification', [
                'code' => $code
            ])
        @else
            {{-- QR Code Display --}}
            @error('setupData')
                <flux:callout variant="danger" icon="x-circle" heading="{{ $message }}" />
            @enderror

            <div class="flex justify-center">
                <div
                    class="relative w-64 overflow-hidden border rounded-lg border-[var(--card-border)] dark:border-[var(--card-border)] aspect-square">
                    @empty($qrCodeSvg)
                        <div
                            class="absolute inset-0 flex items-center justify-center bg-[var(--card-bg)] dark:bg-[var(--card-bg)] animate-pulse">
                            <flux:icon.loading />
                        </div>
                    @else
                        <div class="flex items-center justify-center h-full p-4">
                            <div class="bg-white p-3 rounded">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>
                    @endempty
                </div>
            </div>

            <div>
                <flux:button :disabled="$errors->has('setupData')" variant="primary" class="w-full"
                    wire:click="showVerificationIfNecessary">
                    {{ $this->modalConfig['buttonText'] }}
                </flux:button>
            </div>

            {{-- Manual Setup Key Section --}}
            <div class="space-y-4">
                <div class="relative flex items-center justify-center w-full">
                    <div
                        class="absolute inset-0 w-full h-px top-1/2 bg-[var(--card-border)] dark:bg-[var(--card-border)]">
                    </div>
                    <span
                        class="relative px-2 text-sm bg-[var(--card-bg)] dark:bg-[var(--card-bg)] text-[var(--color-text-base)] dark:text-[var(--color-text-muted)]">
                        {{ __('veya, kodu manuel olarak girin') }}
                    </span>
                </div>

                <div class="flex items-center space-x-2" x-data="{
                            copied: false,
                            async copy() {
                                try {
                                    await navigator.clipboard.writeText('{{ $manualSetupKey }}');
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 1500);
                                } catch (e) {
                                    console.warn('Could not copy to clipboard');
                                }
                            }
                        }">
                    <div class="flex items-stretch w-full border rounded-xl dark:border-[var(--card-border)]">
                        @empty($manualSetupKey)
                            <div
                                class="flex items-center justify-center w-full p-3 bg-[var(--dropdown-hover-bg)] dark:bg-[var(--dropdown-hover-bg)]">
                                <flux:icon.loading variant="mini" />
                            </div>
                        @else
                            <input type="text" readonly value="{{ $manualSetupKey }}"
                                class="w-full p-3 bg-transparent outline-none text-skin-heading dark:text-skin-heading" />

                            <button @click="copy()"
                                class="px-3 transition-colors border-l cursor-pointer border-[var(--card-border)] dark:border-[var(--card-border)]">
                                <flux:icon.document-duplicate x-show="!copied" variant="outline"></flux:icon>
                                    <flux:icon.check x-show="copied" variant="solid" class="text-[var(--color-success)]">
                                        </flux:icon>
                            </button>
                        @endempty
                    </div>
                </div>
            </div>
        @endif
    </div>
</flux:modal>
