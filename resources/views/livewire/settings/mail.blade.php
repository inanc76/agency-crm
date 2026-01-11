<?php
/**
 * 🚀 MAIL SETTINGS MAIN COMPONENT
 * ---------------------------------------------------------
 * ARCHITECTURE: Volt Component (Multi-Provider Management)
 * RESPONSIBILITY: Managing the overall mail configuration UI and state transition.
 * UI STRUCTURE: Calls atomic partials from parts/ folder via @include.
 * LOGIC LAYER: Business logic and mutations delegated to HasMailSettings trait.
 * ---------------------------------------------------------
 */

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;
use App\Livewire\Settings\Traits\HasMailSettings;

new
    #[Layout('components.layouts.app', ['title' => 'Mail Ayarları'])]
    class extends Component {
    use Toast, HasMailSettings;

    public function mount(): void
    {
        $this->mountHasMailSettings();
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="w-full lg:w-3/4 mx-auto">
        
        {{-- 1. Header & Provider Selector & Save Button --}}
        @include('livewire.settings.parts._mail-header', [
            'provider' => $provider,
            'is_active' => $is_active
        ])

        {{-- 2. Service Specific Form --}}
        <div class="space-y-6">
            @if($provider === 'smtp')
                @include('livewire.settings.parts._form-smtp', [
                    'smtp_host' => $smtp_host,
                    'smtp_port' => $smtp_port,
                    'smtp_username' => $smtp_username,
                    'smtp_password' => $smtp_password,
                    'smtp_from_email' => $smtp_from_email,
                    'smtp_from_name' => $smtp_from_name,
                    'smtp_secure' => $smtp_secure
                ])
            @else
                @include('livewire.settings.parts._form-mailgun', [
                    'mailgun_api_key' => $mailgun_api_key,
                    'mailgun_domain' => $mailgun_domain,
                    'mailgun_from_email' => $mailgun_from_email,
                    'mailgun_from_name' => $mailgun_from_name,
                    'mailgun_region' => $mailgun_region
                ])
            @endif
        </div>

        {{-- 3. Modals --}}
        @include('livewire.settings.parts._modal-test-mail', [
            'showTestModal' => $showTestModal,
            'test_email' => $test_email,
            'test_subject' => $test_subject,
            'test_body' => $test_body
        ])

    </div>
</div>