<?php

namespace App\Services;

use App\Models\StorageSetting;
use Aws\S3\S3Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MinioService
{
    private function getDisk()
    {
        $setting = StorageSetting::where('is_active', true)->first();

        if (! $setting) {
            throw new \Exception('Minio ayarları bulunamadı. Lütfen önce depolama ayarlarını yapılandırın.');
        }

        $protocol = $setting->use_ssl ? 'https://' : 'http://';
        $endpoint = $protocol.$setting->endpoint.($setting->port == 443 || $setting->port == 80 ? '' : ':'.$setting->port);

        $config = [
            'driver' => 's3',
            'key' => $setting->access_key,
            'secret' => $setting->secret_key,
            'region' => 'us-east-1',
            'bucket' => $setting->bucket_name,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true,
            'throw' => true,
            'http' => [
                'verify' => false,
            ],
        ];

        return Storage::build($config);
    }

    private function getS3Client()
    {
        $setting = StorageSetting::where('is_active', true)->first();
        if (! $setting) {
            throw new \Exception('Minio ayarları bulunamadı.');
        }

        $protocol = $setting->use_ssl ? 'https://' : 'http://';
        $endpoint = $protocol.$setting->endpoint.($setting->port == 443 || $setting->port == 80 ? '' : ':'.$setting->port);

        return new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $setting->access_key,
                'secret' => $setting->secret_key,
            ],
            'http' => [
                'verify' => false,
            ],
        ]);
    }

    public function uploadFile(UploadedFile $file, string $directory): array
    {
        try {
            $disk = $this->getDisk();

            // Generate unique filename
            $filename = $file->hashName();
            $path = $filename; // Store directly in bucket root or prefix if needed: $directory . '/' . $filename;

            // User requested 'offers' directory, so let's honor $directory
            if (! empty($directory)) {
                $path = rtrim($directory, '/').'/'.$filename;
            }

            // Attempt Deductive Upload Strategy
            // 1. Try local stream (Direct Upload)
            $realPath = $file->getRealPath();

            // Check if it's a valid local file path
            if ($realPath && file_exists($realPath) && is_readable($realPath)) {
                $stream = fopen($realPath, 'r');
                if ($stream) {
                    $disk->put($path, $stream);
                    if (is_resource($stream)) {
                        fclose($stream);
                    }

                    // Helper method: cleanup temporary file if desired (but user asked to keep it simple for now)
                    // $this->cleanupTemporaryFile($realPath);

                    return [
                        'path' => $path,
                        'url' => $disk->url($path),
                    ];
                }
            }

            // 2. Fallback: If local file not accessible (e.g. Livewire tmp on Minio), read content and put
            // This reads entire file into memory, so mostly for fallback or smaller files
            $disk->put($path, $file->get());

            return [
                'path' => $path,
                'url' => $disk->url($path),
            ];

        } catch (\Exception $e) {
            $fullPath = isset($file) ? $file->getRealPath() : 'unknown';
            Log::error("Minio upload HATASI (Path: {$fullPath}): ".$e->getMessage());
            throw $e;
        }
    }

    public function deleteFile(?string $path): bool
    {
        if (empty($path)) {
            Log::warning('Minio silme denemesi: Yol boş.');

            return false;
        }

        try {
            $client = $this->getS3Client();
            $setting = StorageSetting::where('is_active', true)->first();
            $bucket = $setting->bucket_name;

            // 1. Tüm sürümleri ve silme işaretçilerini al
            $versions = $client->listObjectVersions([
                'Bucket' => $bucket,
                'Prefix' => $path,
            ]);

            $toDelete = [];

            if (isset($versions['Versions'])) {
                foreach ($versions['Versions'] as $v) {
                    if ($v['Key'] === $path) {
                        $toDelete[] = ['Key' => $path, 'VersionId' => $v['VersionId']];
                    }
                }
            }

            if (isset($versions['DeleteMarkers'])) {
                foreach ($versions['DeleteMarkers'] as $m) {
                    if ($m['Key'] === $path) {
                        $toDelete[] = ['Key' => $path, 'VersionId' => $m['VersionId']];
                    }
                }
            }

            if (! empty($toDelete)) {
                Log::info('Minio: '.count($toDelete).' adet sürüm temizleniyor.');
                $client->deleteObjects([
                    'Bucket' => $bucket,
                    'Delete' => [
                        'Objects' => $toDelete,
                        'Quiet' => true,
                    ],
                ]);
            } else {
                Log::info('Minio: Sürüm bulunamadı, standart silme deneniyor.');
                $client->deleteObject([
                    'Bucket' => $bucket,
                    'Key' => $path,
                ]);
            }

            Log::info("Minio silme işlemi tamamlandı: {$path}");

            return true;
        } catch (\Exception $e) {
            Log::error("Minio silme HATASI - Yol: {$path} - Hata: ".$e->getMessage());

            return false;
        }
    }

    public function fileExists(string $path): bool
    {
        try {
            return $this->getDisk()->exists($path);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFileUrl(string $path): ?string
    {
        try {
            $disk = $this->getDisk();
            if ($disk->exists($path)) {
                // Return Laravel proxy URL instead of direct Minio URL
                // This bypasses CORS and SSL issues
                return route('minio.proxy', ['path' => $path]);
            }
        } catch (\Exception $e) {
            // URL generation error
        }

        return null;
    }

    public function downloadFile(string $path, ?string $fileName = null)
    {
        $disk = $this->getDisk();

        if (! $disk->exists($path)) {
            throw new \Exception("Dosya bulunamadı: {$path}");
        }

        return $disk->download($path, $fileName);
    }
}
