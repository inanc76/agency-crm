<?php

namespace App\Livewire\Public;

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
class extends Component
{
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
        
        $action = new \App\Actions\Offers\GenerateOfferPdfAction;
        $pdfPath = $action->execute($this->offer);
        $fileName = 'Teklif-'.$this->offer->number.'.pdf';

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

   <div class="min-h-screen flex items-center justify-center p-4 bg-gray-50/50" style="background-color: var(--page-bg, #f9fafb);">
        <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            
            {{-- Header Color Bar --}}
            <div class="h-2 w-full" style="background-color: {{ $settings->pdf_header_bg_color ?? '#4f46e5' }}"></div>

            {{-- Main Content --}}
            <div class="p-8 md:p-12">
                
                {{-- Header Section --}}
                <div class="text-center mb-10">
                    <h1 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight">
                        {{ $offer->title ?? 'TEKLİF' }}
                    </h1>
                    <div class="flex items-center justify-center gap-3 text-sm font-medium text-gray-500">
                        <span>Teklif No: {{ $offer->number }}</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span>Tarih: {{ $offer->created_at->format('d.m.Y') }}</span>
                    </div>

                    @if($logoUrl)
                        <div class="mt-6 flex justify-center">
                             <img src="{{ $logoUrl }}" alt="Logo" class="h-12 object-contain transition-all duration-300">
                        </div>
                    @endif
                </div>

                {{-- Status Card --}}
                <div class="relative overflow-hidden rounded-2xl bg-gray-50 border border-gray-100 p-8 text-center transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/5">
                    
                    @if($isBlocked)
                        {{-- BLOCKED / EXPIRED STATE --}}
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-6 text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            
                            <h2 class="text-xl font-bold text-gray-900 mb-3">Teklif Geçerlilik Süresi Doldu</h2>
                            <p class="text-gray-500 text-sm mb-8 leading-relaxed max-w-md mx-auto">
                                Teklif geçerlilik süresi dolduğu için indirilemez.<br>
                                Yeni bir teklif almak için lütfen aşağıdaki butonu kullanınız.
                            </p>

                            @if($formSent)
                                <div class="bg-emerald-50 text-emerald-700 px-6 py-4 rounded-xl flex items-center gap-3 w-full">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-semibold">Talebiniz başarıyla iletildi. En kısa sürede size dönüş yapacağız.</span>
                                </div>
                            @else
                                <button wire:click="$set('showRequestModal', true)" class="w-full max-w-sm bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-[0.98] cursor-pointer">
                                    YENİ TEKLİF İSTE
                                </button>
                            @endif
                        </div>

                    @else
                        {{-- ACTIVE / DOWNLOADABLE STATE --}}
                        <div class="flex flex-col items-center">
                            
                            {{-- Icon Circle --}}
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 shadow-lg shadow-emerald-100 flex items-center justify-center mb-6 text-white transform hover:scale-105 transition-transform duration-300">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                </svg>
                            </div>

                            <h2 class="text-2xl font-black text-gray-900 mb-2">Teklifiniz Hazır</h2>
                            <p class="text-gray-500 text-sm mb-8">
                                <span class="font-bold text-gray-700">{{ $offer->number }}</span> numaralı teklifinizi PDF formatında indirebilirsiniz.
                            </p>

                            {{-- Main PDF Download Button --}}
                            @if($offer->is_pdf_downloadable)
                                <button wire:click="downloadPdf" wire:loading.attr="disabled" class="group relative w-full sm:w-64 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-4 px-8 rounded-2xl shadow-xl shadow-emerald-200 transition-all duration-300 transform hover:-translate-y-1 active:scale-[0.98] cursor-pointer">
                                    <div class="flex items-center justify-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Teklifi İndir</span>
                                    </div>
                                    <div class="absolute inset-0 rounded-2xl ring-4 ring-white/20 group-hover:ring-white/30 transition-all"></div>
                                </button>
                            @else
                                <div class="text-amber-600 font-medium text-sm bg-amber-50 px-4 py-2 rounded-lg">
                                    PDF indirme özelliği şu an aktif değil.
                                </div>
                            @endif

                            {{-- Attachments & Info --}}
                            <div class="mt-8 pt-8 border-t border-gray-100 w-full">
                                
                                {{-- Expiry Info --}}
                                @if($offer->valid_until)
                                    <div class="flex flex-col items-center gap-2 mb-6">
                                        <div class="flex items-center gap-2 text-gray-500 text-xs uppercase font-bold tracking-widest">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Geçerlilik Tarihi: {{ $offer->valid_until->format('d F Y') }}
                                        </div>
                                        
                                        @if($remainingDays > 0)
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                                {{ $remainingDays }} gün kaldı
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">
                                                Süresi bugün doldu
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Attachments --}}
                                @if($offer->is_attachments_downloadable && $offer->attachments->count() > 0)
                                    <div class="text-left w-full max-w-sm mx-auto bg-white rounded-xl border border-gray-100 p-4">
                                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Ek Dosyalar</h3>
                                        <div class="space-y-2">
                                            @foreach($offer->attachments as $attachment)
                                                 <a href="#" wire:click.prevent="downloadAttachment('{{ $attachment->id }}')" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 group transition-colors cursor-pointer">
                                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-700 truncate group-hover:text-indigo-600 transition-colors">{{ $attachment->file_name }}</p>
                                                        <p class="text-[10px] text-gray-400">{{ strtoupper(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) }} Dosyası</p>
                                                    </div>
                                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                 </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Introduction Files --}}
                                @if(!empty($offer->selected_introduction_files))
                                    <div class="text-left w-full max-w-sm mx-auto bg-white rounded-xl border border-gray-100 p-4 mt-4">
                                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Tanıtım Dosyaları</h3>
                                        <div class="space-y-2">
                                            @foreach($offer->selected_introduction_files as $fileIndex)
                                                {{-- Fetch file details from settings --}}
                                                @php $file = $settings->introduction_files[$fileIndex] ?? null; @endphp
                                                @if($file)
                                                    <a href="#" wire:click.prevent="downloadIntroFile({{ $fileIndex }})" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 group transition-colors cursor-pointer">
                                                        <div class="w-8 h-8 rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-700 truncate group-hover:text-pink-600 transition-colors">{{ $file['name'] }}</p>
                                                            <p class="text-[10px] text-gray-400">Tanıtım Dosyası</p>
                                                        </div>
                                                        <svg class="w-4 h-4 text-gray-300 group-hover:text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer Info --}}
                <div class="mt-8 text-center">
                     <p class="text-xs text-gray-400 flex items-center justify-center gap-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                         Bu link sadece sizinle paylaşılmıştır ve güvenlidir.
                     </p>
                </div>

            </div>
        </div>
    </div>

   {{-- Custom Modal --}}
   @if($showRequestModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity"
             x-data x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 relative transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                 
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                    <h3 class="text-lg font-bold text-gray-900">Yeni Teklif İste</h3>
                    <button wire:click="$set('showRequestModal', false)" class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="requestNewOffer" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Firma Adı</label>
                        <input type="text" wire:model="company_name" class="w-full px-4 py-2.5 rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow shadow-sm">
                        @error('company_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Adı Soyadı</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2.5 rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow shadow-sm">
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Telefon</label>
                            <input type="text" wire:model="phone" class="w-full px-4 py-2.5 rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow shadow-sm">
                            @error('phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">E-Posta</label>
                            <input type="email" wire:model="email" class="w-full px-4 py-2.5 rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow shadow-sm">
                            @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Notunuz</label>
                        <textarea wire:model="note" rows="3" class="w-full px-4 py-2.5 rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-shadow shadow-sm resize-none"></textarea>
                        @error('note') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit" wire:loading.attr="disabled" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-[0.98] cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
                            GÖNDER
                        </button>
                    </div>
                </form>
            </div>
        </div>
   @endif
</div>
