<?php

namespace App\Livewire\Settings\Traits;

use App\Repositories\StorageSettingRepository;
use Illuminate\Support\Facades\Storage;

use Mary\Traits\Toast;

trait HasStorageSettings
{
    use Toast;
    public ?string $lastError = null;

    public string $endpoint = '';
    public $port = 443;
    public bool $use_ssl = true;
    public string $access_key = '';
    public string $secret_key = '';
    public string $bucket_name = '';

    public function mountHasStorageSettings(): void
    {
        $repository = app(StorageSettingRepository::class);
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

    public function testStorageConnection(): void
    {
        $this->lastError = null;

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
            $this->attemptStorageConnection($this->use_ssl);
            $this->success('Bağlantı Başarılı', 'Minio sunucusuna başarıyla bağlanıldı.');
        } catch (\Exception $e) {
            if ($this->use_ssl) {
                try {
                    $this->attemptStorageConnection(false);
                    $this->use_ssl = false;
                    $this->success('Bağlantı Başarılı', 'Güvenli bağlantı başarısız oldu ancak şifresiz (HTTP) bağlantı sağlandı. Ayar güncellendi.');
                    return;
                } catch (\Exception $inner) {
                    $e = new \Exception($e->getMessage() . " || OTOMATİK HTTP DENEMESİ DE BAŞARISIZ OLDU: " . $inner->getMessage());
                }
            }

            $errorMsg = $e->getMessage();
            if ($e->getPrevious()) {
                $errorMsg .= ' | Caused by: ' . $e->getPrevious()->getMessage();
            }

            if (str_contains($errorMsg, 'SSL certificate') || str_contains($errorMsg, 'unable to get local issuer certificate')) {
                $this->lastError = "SSL SERTİFİKA HATASI!\n\nMinio sunucunuzun SSL sertifikası doğrulanamıyor. Bu genellikle self-signed (kendi imzalı) sertifika kullanıldığında olur.\n\nÇözüm: Sunucu yöneticinizle iletişime geçip geçerli bir SSL sertifikası kurulmasını talep edin, veya 'SSL Kullan' seçeneğini kapatıp HTTP ile bağlanmayı deneyin.";
                $this->error('SSL Sertifika Hatası', 'Sertifika doğrulanamadı.');
                return;
            }

            if (str_contains($errorMsg, 'NoSuchBucket') || str_contains($errorMsg, 'The specified bucket does not exist')) {
                $this->lastError = "BAĞLANTI BAŞARILI ANCAK BUCKET BULUNAMADI!\n\nMinio sunucunuza erişim sağlandı (Port 443/HTTPS) fakat '$this->bucket_name' adında bir bucket (klasör) yok.\n\nLütfen Minio panelinizden bu bucket'ı oluşturun.";
                $this->error('Bucket Bulunamadı', 'Sunucuya erişildi ancak bucket yok.');
                return;
            }

            $this->lastError = $errorMsg;
            \Illuminate\Support\Facades\Log::error('Minio Connection Failed: ' . $errorMsg);
            $this->error('Bağlantı Hatası', 'Hata detayları aşağıda gösterilmiştir.');
        }
    }

    private function attemptStorageConnection(bool $useSsl): void
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

    public function saveStorageSettings(): void
    {
        $repository = app(StorageSettingRepository::class);

        $data = $this->validate($this->storageRules());

        $data['provider'] = 'MINIO';
        $data['endpoint'] = str_replace(['https://', 'http://'], '', $data['endpoint']);
        $parts = explode(':', $data['endpoint']);
        if (count($parts) > 1 && is_numeric(end($parts))) {
            $data['endpoint'] = $parts[0];
        }

        $this->endpoint = $data['endpoint'];

        $repository->saveSettings($data);
        $this->success('Ayarlar Kaydedildi', 'Depolama ayarları başarıyla güncellendi.');
    }

    protected function storageRules(): array
    {
        return [
            'endpoint' => 'required|string',
            'port' => 'required|integer',
            'use_ssl' => 'boolean',
            'access_key' => 'required|string',
            'secret_key' => 'required|string',
            'bucket_name' => 'required|string',
        ];
    }
}
