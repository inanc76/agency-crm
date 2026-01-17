<?php

namespace App\Livewire\Public;

/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * MODÜL      : Public / Offer Download
 * SORUMLULUK : Müşterilerin kendilerine özel token ile tekliflerini görüntüleyip
 *              indirebildikleri "Public" yüz.
 * MİMARİ     : Monolitik -> Partial Tabanlı (Mission Eta).
 *
 * GÜVENLİK PROTOKOLLERİ:
 * 1. Token Validation : URL'deki token `mount()` içinde doğrulanır. Hatalıysa 404/Fail.
 * 2. Expiry Checks    : Teklif süresi dolmuşsa indirme engellenir (Ayara bağlı).
 * 3. Block Logic      : Sistem tarafından engellenen teklifler için erişim kısıtlanır.
 *
 * YAPI HİYERARŞİSİ:
 * 1. w-offer-download/_offer-details : Başlık, logo ve tarih bilgileri.
 * 2. w-offer-download/_access-denied : Süre dolumu veya erişim engeli durumu.
 * 3. w-offer-download/_action-buttons: İndirme butonları ve ek dosyalar.
 * -------------------------------------------------------------------------
 */

use App\Models\Offer;
use App\Models\PanelSetting;
use App\Models\User;
use App\Services\MinioService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOfferRequestMail;

new
    #[Layout('components.layouts.public', ['title' => 'Teklif İndir'])]
    class extends Component {
    public string $token = '';
    public ?Offer $offer = null;
    public ?PanelSetting $settings = null;
    public ?string $logoUrl = null;

    // Contact Form Fields
    public string $company_name = '';
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $note = '';

    // State
    public bool $isExpired = false;
    public bool $isBlocked = false;
    public bool $formSent = false;
    public bool $showRequestModal = false;
    public int $remainingDays = 0;

    public ?string $favicon = null;

    public function mount(string $token): void
    {
        $this->token = $token;

        $this->offer = Offer::where('tracking_token', $token)
            ->with(['customer', 'items', 'attachments'])
            ->firstOrFail();

        $this->settings = PanelSetting::where('is_active', true)->first() ?? new PanelSetting;

        // Settings and Logo
        if ($this->settings->pdf_logo_path) {
            $this->logoUrl = app(MinioService::class)->getFileAsBase64($this->settings->pdf_logo_path);
        }

        // Expiry Logic
        $validUntil = $this->offer->valid_until;
        if ($validUntil) {
            $this->isExpired = $validUntil->isPast();
            $this->remainingDays = max(0, (int) now()->diffInDays($validUntil, false));
        }

        // Block Logic
        // is_downloadable_after_expiry = true -> Allow download even if expired
        // is_downloadable_after_expiry = false -> Block if expired
        if ($this->isExpired && !$this->offer->is_downloadable_after_expiry) {
            $this->isBlocked = true;
        }

        // Do NOT pre-fill form info as per user request, user will enter manually.

        // Share Favicon
        if ($this->settings && $this->settings->favicon_path) {
            // First try verify if it exists on public disk (common for theme settings)
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->settings->favicon_path)) {
                $this->favicon = \Illuminate\Support\Facades\Storage::disk('public')->url($this->settings->favicon_path);
            } else {
                // Fallback to Minio/S3 Base64
                $this->favicon = app(MinioService::class)->getFileAsBase64($this->settings->favicon_path);
            }
        }
    }

    public function downloadPdf()
    {
        if ($this->isBlocked) {
            return;
        }

        if (!$this->offer->is_pdf_downloadable) {
            return;
        }

        // Generate or get PDF URL
        // Currently assuming pdf_url is stored or we generate on fly. 
        // For public download, best to generate on fly to ensure fresh data or check existence.
        // Re-using the action used in preview.

        $action = app(\App\Actions\Offers\GenerateOfferPdfAction::class);
        $pdfPath = $action->execute($this->offer);
        $fileName = 'Teklif-' . $this->offer->number . '.pdf';

        return response()->download($pdfPath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function requestNewOffer()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:1000',
        ]);

        // Notify the offer creator or admin
        // Find who created the offer or default to admin
        // Database does not track created_by currently.
        // FALLBACK: Send to first Admin.
        $recipient = User::whereHas('role', fn($q) => $q->where('name', 'Admin')->orWhere('name', 'Super Admin'))->first() ?? User::first();

        if ($recipient) {
            Mail::to($recipient->email)->send(new NewOfferRequestMail(
                $this->offer,
                [
                    'company_name' => $this->company_name,
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'note' => $this->note
                ]
            ));
        }

        $this->formSent = true;
        $this->showRequestModal = false;
    }
    public function downloadAttachment($attachmentId)
    {
        if ($this->isBlocked || !$this->offer->is_attachments_downloadable) {
            return;
        }

        $attachment = $this->offer->attachments()->findOrFail($attachmentId);

        try {
            return app(MinioService::class)->downloadFile($attachment->file_path, $attachment->file_name);
        } catch (\Exception $e) {
            // Handle file not found or other errors
            session()->flash('error', 'Dosya indirilemedi: ' . $e->getMessage());
        }
    }

    public function downloadIntroFile($index)
    {
        if ($this->isBlocked) {
            return;
        }

        $file = $this->settings->introduction_files[$index] ?? null;

        if (!$file) {
            return;
        }

        try {
            return app(MinioService::class)->downloadFile($file['path'], $file['name']);
        } catch (\Exception $e) {
            session()->flash('error', 'Dosya indirilemedi: ' . $e->getMessage());
        }
    }
};
?>

<div>
    @if($settings && $settings->favicon_path)
        @push('head')
            <link rel="icon" href="{{ asset('storage/' . $settings->favicon_path) }}">
        @endpush
    @endif

    <div class="min-h-screen flex items-center justify-center p-4 bg-gray-50/50"
        style="background-color: var(--page-bg, #f9fafb);">
        <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">

            {{-- Header Color Bar --}}
            <div class="h-2 w-full" style="background-color: {{ $settings->pdf_header_bg_color ?? '#4f46e5' }}"></div>

            {{-- Main Content --}}
            <div class="p-8 md:p-12">

                {{-- Header Section --}}
                @include('livewire.public.partials.offer-download._offer-details')

                {{-- Status Card --}}
                <div
                    class="relative overflow-hidden rounded-2xl bg-gray-50 border border-gray-100 p-8 text-center transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/5">

                    @if($isBlocked)
                        {{-- BLOCKED / EXPIRED STATE --}}
                        @include('livewire.public.partials.offer-download._access-denied')

                    @else
                        {{-- ACTIVE / DOWNLOADABLE STATE --}}
                        @include('livewire.public.partials.offer-download._action-buttons')
                    @endif
                </div>

                {{-- Footer Info --}}
                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-400 flex items-center justify-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Bu link sadece sizinle paylaşılmıştır ve güvenlidir.
                    </p>
                </div>

            </div>
        </div>
    </div>


</div>