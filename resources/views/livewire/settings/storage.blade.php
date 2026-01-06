<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Repositories\StorageSettingRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Added Log facade
use Mary\Traits\Toast;

new
    #[Layout('components.layouts.app', ['title' => 'Depolama Ayarları'])]
    class extends Component {
    use Toast;

    public ?string $lastError = null; // Variable to hold the error message

    public string $endpoint = '';
    public int $port = 443;
    public bool $use_ssl = true;
    public string $access_key = '';
    public string $secret_key = '';
    public string $bucket_name = '';

    public function mount(StorageSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();

        if ($setting) {
            $this->endpoint = $setting->endpoint;
            $this->port = $setting->port;
            $this->use_ssl = $setting->use_ssl;
            $this->access_key = $setting->access_key;
            $this->secret_key = $setting->secret_key;
            $this->bucket_name = $setting->bucket_name;
        }
    }

    public function testConnection(): void
    {
        $this->lastError = null; // Clear previous error

        // CLEANUP INPUT: Remove protocols and slashes
        $this->endpoint = str_replace(['https://', 'http://'], '', $this->endpoint);
        $this->endpoint = rtrim($this->endpoint, '/');

        $this->validate([
            'endpoint' => 'required|string',
            'port' => 'required|integer',
            'access_key' => 'required|string',
            'secret_key' => 'required|string',
            'bucket_name' => 'required|string',
        ]);

        try {
            $this->attemptConnection($this->use_ssl);
            $this->success('Bağlantı Başarılı', 'Minio sunucusuna başarıyla bağlanıldı.');
        } catch (\Exception $e) {
            // SMART RETRY: If SSL was used and failed, try non-SSL
            if ($this->use_ssl) {
                try {
                    $this->attemptConnection(false);

                    // If we get here, non-SSL worked!
                    $this->use_ssl = false; // Auto-correct the checkbox
                    $this->success('Bağlantı Başarılı', 'Güvenli bağlantı başarısız oldu ancak şifresiz (HTTP) bağlantı sağlandı. Ayar güncellendi.');
                    return;
                } catch (\Exception $inner) {
                    // Fall back to original error if retry also fails, BUT append the inner error
                    $e = new \Exception($e->getMessage() . " || OTOMATİK HTTP DENEMESİ DE BAŞARISIZ OLDU: " . $inner->getMessage());
                }
            }

            $errorMsg = $e->getMessage();
            if ($e->getPrevious()) {
                $errorMsg .= ' | Caused by: ' . $e->getPrevious()->getMessage();
            }

            // Friendly Error for SSL Certificate Issues
            if (str_contains($errorMsg, 'SSL certificate') || str_contains($errorMsg, 'unable to get local issuer certificate')) {
                $this->lastError = "SSL SERTİFİKA HATASI!\n\nMinio sunucunuzun SSL sertifikası doğrulanamıyor. Bu genellikle self-signed (kendi imzalı) sertifika kullanıldığında olur.\n\nÇözüm: Sunucu yöneticinizle iletişime geçip geçerli bir SSL sertifikası kurulmasını talep edin, veya 'SSL Kullan' seçeneğini kapatıp HTTP ile bağlanmayı deneyin.";
                $this->error('SSL Sertifika Hatası', 'Sertifika doğrulanamadı.');
                return;
            }

            // Friendly Error for NoSuchBucket
            if (str_contains($errorMsg, 'NoSuchBucket') || str_contains($errorMsg, 'The specified bucket does not exist')) {
                $this->lastError = "BAĞLANTI BAŞARILI ANCAK BUCKET BULUNAMADI!\n\nMinio sunucunuza erişim sağlandı (Port 443/HTTPS) fakat '$this->bucket_name' adında bir bucket (klasör) yok.\n\nLütfen Minio panelinizden bu bucket'ı oluşturun.";
                $this->error('Bucket Bulunamadı', 'Sunucuya erişildi ancak bucket yok.');
                return;
            }

            $this->lastError = $errorMsg; // Set for UI display
            \Illuminate\Support\Facades\Log::error('Minio Connection Failed: ' . $errorMsg);
            $this->error('Bağlantı Hatası', 'Hata detayları aşağıda gösterilmiştir.');
        }
    }

    private function attemptConnection(bool $useSsl): void
    {
        $config = [
            'driver' => 's3',
            'endpoint' => ($useSsl ? 'https://' : 'http://') . $this->endpoint . ':' . $this->port,
            'use_path_style_endpoint' => true,
            'key' => $this->access_key,
            'secret' => $this->secret_key,
            'region' => 'us-east-1',
            'bucket' => $this->bucket_name,
            'throw' => true,
            'http' => [
                'verify' => false,
                'connect_timeout' => 5,
            ],
        ];

        $disk = Storage::build($config);
        $disk->files();
    }

    public function store(): void
    {
        $repository = app(StorageSettingRepository::class);

        $data = $this->validate([
            'endpoint' => 'required|string',
            'port' => 'required|integer',
            'use_ssl' => 'boolean',
            'access_key' => 'required|string',
            'secret_key' => 'required|string',
            'bucket_name' => 'required|string',
        ]);

        // Force provider to MINIO as per current requirement
        $data['provider'] = 'MINIO';

        // Remove https/http from endpoint if present, we handle it with use_ssl and dynamic construction
        $data['endpoint'] = str_replace(['https://', 'http://'], '', $data['endpoint']);
        // Remove trailing slash and port if explicitly added in text (though port is separate field)
        $parts = explode(':', $data['endpoint']);
        if (count($parts) > 1 && is_numeric(end($parts))) {
            // Use the port from string if user typed it, or stick to the int field?
            // Let's stick to the separated fields to match UI perfectly.
            // Just clean the endpoint.
            $data['endpoint'] = $parts[0];
        }

        $repository->saveSettings($data);

        $this->success('Ayarlar Kaydedildi', 'Depolama ayarları başarıyla güncellendi.');
    }
}; ?>

<div class="p-6 bg-slate-50 min-h-screen">
    <div class="w-full lg:w-3/4 mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/settings"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm font-medium">Geri</span>
        </a>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Depolama Ayarları</h1>
            <p class="text-sm text-slate-500 mt-1">Minio (S3 Uyumlu) depolama entegrasyonu ayarları.</p>
        </div>

        {{-- Main Card --}}
        <div class="bg-[#eff4ff] border border-[#bfdbfe] rounded-xl shadow-sm p-6">
            {{-- Card Header --}}
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
                <h2 class="text-sm font-medium text-slate-700">Minio Bağlantı Ayarları</h2>
                <x-mary-button label="Bağlantıyı Test Et" icon="o-wifi" class="btn-sm btn-outline"
                    wire:click="testConnection" spinner="testConnection" />
            </div>

            {{-- Error Display Area --}}
            <x-errors.persistent :error="$lastError" />

            {{-- Form --}}
            <div class="grid grid-cols-1 gap-6">
                {{-- Bucket Name, Port & SSL --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                    <div class="md:col-span-6">
                        <x-mary-input label="Bucket Adı" wire:model="bucket_name"
                            hint="Dosyaların saklanacağı bucket adı. Yoksa otomatik oluşturulur." />
                    </div>
                    <div class="md:col-span-2">
                        <x-mary-input label="Port" type="number" wire:model="port" />
                    </div>
                    <div class="md:col-span-4 self-center pt-4">
                        <x-mary-checkbox label="SSL Kullan (HTTPS)" wire:model="use_ssl"
                            hint="Güvenli bağlantı için önerilir" />
                    </div>
                </div>

                {{-- Endpoint --}}
                <div>
                    <x-mary-input label="Endpoint" wire:model="endpoint"
                        hint="Minio sunucunuzun adresi (http/https olmadan)" placeholder="minio.example.com" />
                </div>

                {{-- Access Key --}}
                <div>
                    <x-mary-input label="Access Key" wire:model="access_key" />
                </div>

                {{-- Secret Key --}}
                <div>
                    <x-mary-password label="Secret Key" wire:model="secret_key" />
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="pt-6 mt-6 border-t border-slate-200 flex justify-end gap-3">
                <x-mary-button label="Ayarları Kaydet"
                    class="btn-primary bg-emerald-500 hover:bg-emerald-600 border-none text-white" wire:click="store"
                    spinner="store" />
            </div>
        </div>
    </div>
</div>