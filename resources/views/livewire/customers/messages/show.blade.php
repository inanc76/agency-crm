<?php
/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🛡️ MİSYON LIGHTHOUSE - MESAJ DETAYI                                          ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: Mesaj Detay Görünümü ve Yönetimi                                                           ║
 * ║  🎯 ANA GÖREV: Mesaj içeriğinin render edilmesi, şablon değişkenlerinin işlenmesi ve e-posta gönderimi           ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • Render: Mail şablonu veya düz metin üzerinden dinamik değişkenlerin (placeholder) değiştirilmesi              ║
 * ║  • Gönderim: DynamicCustomerMail üzerinden SMTP gönderimi ve durum (SENT/FAILED) güncelleme                     ║
 * ║  • Takip: Teklif indirme loglarının (DownloadLogs) mesaj detayı içinde gösterilmesi                             ║
 * ║                                                                                                                  ║
 * ║  📦 BAĞIMLILIKLAR:                                                                                              ║
 * ║  • App\Services\MailTemplateService: Şablon render motoru                                                       ║
 * ║  • App\Mail\DynamicCustomerMail: Mailable sınıfı                                                                 ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */

use App\Mail\DynamicCustomerMail;
use App\Models\Contact;
use App\Models\Message;
use App\Services\MailTemplateService;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new #[Layout('components.layouts.app', ['title' => 'Mesaj Detayı'])]
    class extends Component {

    use Toast;

    public Message $message;

    public string $renderedBody = '';

    public string $activeTab = 'info';

    /**
     * Başlatma İşlemi: Veri yükleme ve mesaj içeriğinin dinamik render edilmesi.
     * 
     * @param Message $message
     * @param MailTemplateService $mailService
     * @return void
     */
    public function mount(Message $message, MailTemplateService $mailService): void
    {
        $this->activeTab = request()->query('tab', 'info');
        $this->message = $message->load(['customer', 'offer.downloadLogs', 'status_item', 'type_item']);

        // İş Kuralı: Mesaj değişkenleri alıcı kişi (Contact) ve teklif detaylarından beslenir.
        $contact = Contact::where('customer_id', $this->message->customer_id)->first();

        // Prizma Değişken Haritası: Veritabanındaki {{...}} etiketlerini gerçek verilerle eşleştirir.
        $variables = [
            '{{name}}' => $contact?->name ?? 'Kullanıcı',
            '{{contact.name}}' => $contact?->name ?? 'Kullanıcı',
            '{{customer.name}}' => $this->message->customer?->name ?? '',
            '{{offer.download_link}}' => $this->message->offer?->tracking_token
                ? url('/offer/' . $this->message->offer->tracking_token)
                : '#',
            '{{offer.number}}' => $this->message->offer?->number ?? '',
            '{{offer.title}}' => $this->message->offer?->title ?? '',
        ];

        // Render Stratejisi: MailTemplateService varsa gelişmiş render (HTML + Style), yoksa basit replace kullanılır.
        if ($this->message->mail_template_id) {
            $rendered = $mailService->renderById($this->message->mail_template_id, $variables);
            $this->renderedBody = $rendered['content'] ?? $this->message->body;
        } else {
            $this->renderedBody = str_replace(
                array_keys($variables),
                array_values($variables),
                $this->message->body
            );
        }
    }

    /**
     * Mesaj Kaydını Silme
     * İş Kuralı: Kayıt silindiğinde müşteri listesindeki mesajlar sekmesine yönlendirilir.
     */
    public function delete(): void
    {
        $this->message->delete();
        $this->success('Başarılı', 'Mesaj silindi.');
        $this->redirect('/dashboard/customers?tab=messages', navigate: true);
    }

    /**
     * E-Posta Gönderim Protokolü
     * İş Kuralı: SENT durumundaki mesajlar tekrar gönderilemez.
     */
    public function sendMessage(): void
    {
        if ($this->message->status === 'SENT') {
            $this->error('Hata', 'Bu mesaj zaten gönderilmiş.');

            return;
        }

        if (!$this->message->recipient_email) {
            $this->error('Hata', 'Alıcı e-posta adresi bulunamadı.');

            return;
        }

        // CC/BCC Ayrıştırma: Virgülle ayrılmış string değerleri diziye dönüştürür.
        $cc = $this->message->cc ? array_map('trim', explode(',', $this->message->cc)) : [];
        $bcc = $this->message->bcc ? array_map('trim', explode(',', $this->message->bcc)) : [];

        try {
            // SMTP Gönderim ve Durum Güncelleme (Atomic Operation)
            Mail::to($this->message->recipient_email)->send(new DynamicCustomerMail(
                $this->message->subject,
                $this->renderedBody,
                $cc,
                $bcc
            ));

            $this->message->update([
                'status' => 'SENT',
                'sent_at' => now(),
            ]);

            // Bağlantılı Kaynak Güncelleme: Teklif durumunu 'SENT' (Gönderildi) olarak mühürler.
            if ($this->message->offer_id && $this->message->offer?->status === 'DRAFT') {
                $this->message->offer->update(['status' => 'SENT']);
            }

            $this->success('Başarılı', 'Mesaj ' . $this->message->recipient_email . ' adresine gönderildi.');
            $this->redirect('/dashboard/customers?tab=messages', navigate: true);
        } catch (\Exception $e) {
            $this->message->update([
                'status' => 'FAILED',
            ]);

            $this->error('Hata', 'Mesaj gönderilemedi: ' . $e->getMessage());
        }
    }
}; ?>

<div class="p-6 max-w-7xl mx-auto space-y-6">
    {{-- SECTION: Navigation & Actions --}}
    {{-- Sayfa başlığı ve global aksiyon butonlarının (Gönder, Sil) bulunduğu alan. --}}
    <div class="flex items-start justify-between">
        <div>
            <a href="/dashboard/customers?tab=messages" wire:navigate
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 mb-4 transition-colors">
                <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
                <span class="text-sm font-medium">Mesaj Listesine Dön</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">{{ $message->subject }}</h1>
            <p class="text-sm text-slate-500 mt-1">İletişim Kaydı Detayı</p>
        </div>

        <div class="flex gap-3">
            @if($message->status !== 'SENT')
                {{-- İş Kuralı: Gönderilmemiş mesajlar için gönderim tetikleyicisi. --}}
                <button wire:click="sendMessage" wire:loading.attr="disabled"
                    wire:confirm="Bu mesajı göndermek istediğinize emin misiniz?"
                    class="theme-btn-save flex items-center gap-2">
                    <span wire:loading class="loading loading-spinner loading-xs"></span>
                    <x-mary-icon name="o-paper-airplane" class="w-4 h-4" />
                    Mesaj Gönder
                </button>
            @endif
            <button wire:click="delete" wire:confirm="Bu mesaj kaydını silmek istediğinize emin misiniz?"
                class="theme-btn-delete flex items-center gap-2">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Kaydı Sil
            </button>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex items-center border-b border-slate-200 mb-6 overflow-x-auto scrollbar-hide">
        <button wire:click="$set('activeTab', 'info')"
            class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
            style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
            Mesaj Bilgileri
        </button>
        @if($message->status === 'SENT' && $message->offer_id)
            <button wire:click="$set('activeTab', 'downloads')"
                class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                style="{{ $activeTab === 'downloads' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                İndirmeler ({{ $message->offer?->downloadLogs->count() ?? 0 }})
            </button>
        @endif
    </div>

    @if($activeTab === 'info')
        {{-- SECTION: Message Info Tab --}}
        {{-- Mesajın asıl içeriği ve sağ sidebar verilerinin listelendiği ana yapı. --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- BLOCK: Message Content (2/3) --}}
            {{-- Render edilmiş (HTML/Plain Text) mesaj gövdesi. --}}
            <div class="md:col-span-2 space-y-6">
                <div class="theme-card p-8 shadow-sm min-h-[500px]">
                    <div class="mail-content prose prose-sm max-w-none">
                        {!! $renderedBody !!}
                    </div>
                </div>
            </div>

            {{-- BLOCK: Sidebar Info (1/3) --}}
            {{-- Müşteri bilgisi ve mesaj metadatası (Durum, Tarih, Tür). --}}
            <div class="space-y-6">
                {{-- Customer Card --}}
                <div class="theme-card p-6 shadow-sm">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Müşteri Bilgisi</h2>
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-600 font-bold text-xl">
                            {{ mb_substr($message->customer->name ?? 'M', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 leading-tight">{{ $message->customer->name ?? '-' }}
                            </p>
                            <a href="/dashboard/customers/{{ $message->customer_id }}" wire:navigate
                                class="text-xs text-blue-600 hover:underline">Profiline Git</a>
                        </div>
                    </div>
                </div>

                {{-- Metadata Card --}}
                <div class="theme-card p-6 shadow-sm space-y-4">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Gönderim Detayları</h2>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Kime</label>
                        <p class="text-sm font-bold text-slate-700 mt-0.5 break-all">
                            {{ $message->recipient_email ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Durum</label>
                        <div class="mt-1">
                            @php
                                $statusLabel = $message->status_item->display_label ?? $message->status ?? 'Bilinmiyor';
                                $statusClass = $message->status_item->metadata['color_class'] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                            @endphp
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Tarih</label>
                        <p class="text-sm font-medium text-slate-700 mt-0.5">
                            {{ $message->sent_at?->format('d.m.Y H:i') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Tür</label>
                        <div class="mt-1">
                            @php
                                $typeLabel = $message->type_item->display_label ?? $message->type ?? 'Bilinmiyor';
                                $typeClass = $message->type_item->metadata['color_class'] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                            @endphp
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $typeClass }}">
                                {{ $typeLabel }}
                            </span>
                        </div>
                    </div>

                    @if($message->cc)
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">CC</label>
                            <p class="text-xs text-slate-600 mt-0.5 break-all">
                                {{ $message->cc }}
                            </p>
                        </div>
                    @endif

                    @if($message->bcc)
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">BCC</label>
                            <p class="text-xs text-slate-600 mt-0.5 break-all">
                                {{ $message->bcc }}
                            </p>
                        </div>
                    @endif

                    @if($message->offer_id)
                        <div class="pt-4 border-t border-slate-50">
                            {{-- İş Kuralı: Mesaj bir teklife bağlıysa, teklif detayına hızlı erişim sağlar. --}}
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">İlgili Teklif</label>
                            <a href="/dashboard/customers/offers/{{ $message->offer_id }}" wire:navigate
                                class="flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700 mt-1 transition-colors">
                                <x-mary-icon name="o-document-text" class="w-4 h-4" />
                                {{ $message->offer->number ?? 'Teklife Git' }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif($activeTab === 'downloads')
        {{-- SECTION: Download Logs Tab --}}
        {{-- Mesaj üzerinden gönderilen teklifin müşteri tarafından indirilme kayıtlarını listeler. --}}
        <div class="theme-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[var(--card-border)] bg-gray-50/50">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">E-posta</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Tarih / Saat
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Dosya Adı</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">IP Adresi</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Cihaz /
                                Tarayıcı</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--card-border)]">
                        @forelse($message->offer?->downloadLogs ?? [] as $log)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-700 break-all">
                                        {{ $log->downloader_email ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900">{{ $log->downloaded_at->format('d.m.Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-medium">
                                        {{ $log->downloaded_at->format('H:i:s') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-slate-700 truncate max-w-xs" title="{{ $log->file_name ?? '-' }}">
                                        {{ $log->file_name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200">
                                        {{ $log->ip_address ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    {{-- İş Kuralı: User Agent verisi güvenlik takibi için ham haliyle saklanır. --}}
                                    <div class="text-[10px] text-slate-600 line-clamp-1 max-w-sm"
                                        title="{{ $log->user_agent }}">
                                        {{ $log->user_agent ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <x-mary-icon name="o-arrow-down-tray" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                    <p class="text-sm text-slate-500 italic">Henüz indirme kaydı bulunmuyor.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>