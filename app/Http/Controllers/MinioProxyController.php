<?php

namespace App\Http\Controllers;

use App\Services\MinioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Proxy controller for serving Minio files through Laravel
 * This bypasses CORS and SSL issues when accessing Minio directly from browser
 */
class MinioProxyController extends Controller
{
    public function __construct(private MinioService $minioService) {}

    /**
     * Serve a file from Minio storage
     * Route: GET /storage/minio/{path} where path can contain slashes
     */
    public function serve(Request $request, string $path)
    {
        try {
            // Get disk and check if file exists
            $disk = $this->getDisk();

            if (! $disk->exists($path)) {
                Log::warning("Minio proxy: File not found - {$path}");
                abort(404, 'Dosya bulunamadı');
            }

            // Get file content and mime type
            $content = $disk->get($path);
            $mimeType = $disk->mimeType($path) ?? 'application/octet-stream';

            return response($content, 200)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour

        } catch (\Exception $e) {
            Log::error('Minio proxy error: '.$e->getMessage());
            abort(500, 'Dosya yüklenirken hata oluştu');
        }
    }

    /**
     * Get configured Minio disk
     */
    private function getDisk()
    {
        $setting = \App\Models\StorageSetting::where('is_active', true)->first();

        if (! $setting) {
            throw new \Exception('Minio ayarları bulunamadı');
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

        return \Illuminate\Support\Facades\Storage::build($config);
    }
}
