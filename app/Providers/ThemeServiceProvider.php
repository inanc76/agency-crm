<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\PanelSetting;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('panel_settings')) {
                // Cache settings forever, invalidate on save
                $themeSettings = Cache::rememberForever('theme_settings', function () {
                    return PanelSetting::where('is_active', true)->first();
                });

                // Share globally with all views
                View::share('themeSettings', $themeSettings);
            }
        } catch (\Exception $e) {
            // Failsafe for migrations or DB issues
        }
    }
}
