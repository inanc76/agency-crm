<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('storage_settings')) {
                $setting = \App\Models\StorageSetting::where('is_active', true)->first();

                if ($setting) {
                    $endpoint = ($setting->use_ssl ? 'https://' : 'http://') . $setting->endpoint . ':' . $setting->port;

                    config([
                        'filesystems.disks.s3.driver' => 's3',
                        'filesystems.disks.s3.endpoint' => $endpoint,
                        'filesystems.disks.s3.use_path_style_endpoint' => true,
                        'filesystems.disks.s3.key' => $setting->access_key,
                        'filesystems.disks.s3.secret' => $setting->secret_key,
                        'filesystems.disks.s3.region' => 'us-east-1', // Generic region
                        'filesystems.disks.s3.bucket' => $setting->bucket_name,
                        'filesystems.disks.s3.throw' => true,
                    ]);

                    // Set S3 as default if active
                    config(['filesystems.default' => 's3']);
                }
            }
        } catch (\Exception $e) {
            // Quietly fail if DB connection issues or migration not run
        }
    }
}
