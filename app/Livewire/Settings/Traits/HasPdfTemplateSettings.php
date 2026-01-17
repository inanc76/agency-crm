<?php

namespace App\Livewire\Settings\Traits;

use App\Repositories\PanelSettingRepository;
use App\Services\MinioService;
use Illuminate\Support\Facades\Cache;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * TRAIT      : HasPdfTemplateSettings
 * SORUMLULUK : PDF Şablonu ve İndirme Sayfası ayarlarını yönetmek.
 * KULLANIM   : Bu trait, PDF Template Settings bileşeni tarafından consume edilir.
 * 
 * BAĞIMLILIKLAR:
 * - PanelSettingRepository (Veri erişimi)
 * - MinioService (Dosya yönetimi)
 * 
 * ÖNEMLİ:
 * - Bu trait hem "PDF Şablonu" hem de "İndirme Sayfası" mantığını kapsar.
 * - Livewire bileşeni sadece view render işleminden sorumlu olmalıdır.
 * -------------------------------------------------------------------------
 */
trait HasPdfTemplateSettings
{
    use Toast, WithFileUploads;

    // Header Settings
    public $group = 'header';
    public $pdf_logo;
    public ?string $current_pdf_logo_path = null;
    public int $pdf_logo_height = 50;
    public string $pdf_header_bg_color = '#4f46e5';
    public string $pdf_header_text_color = '#ffffff';

    // Content Settings
    public string $pdf_font_family = 'Segoe UI';
    public string $pdf_primary_color = '#4f46e5';
    public string $pdf_secondary_color = '#6b7280';
    public string $pdf_table_header_bg_color = '#f9fafb';
    public string $pdf_table_header_text_color = '#6b7280';

    // Specific Colors
    public string $pdf_discount_color = '#16a34a';
    public string $pdf_total_color = '#4f46e5';

    // Footer Settings
    public ?string $pdf_footer_text = null;

    // Download Page Settings
    public string $dl_header_bg_color = '#4f46e5';
    public string $dl_header_text_color = '#ffffff';
    public int $dl_logo_height = 50;
    public $dl_logo;
    public ?string $current_dl_logo_path = null;

    // Introduction Files
    public array $introduction_files = [];
    public $new_intro_file;
    public string $new_intro_name = '';
    public bool $is_uploading_intro = false;

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();
        if ($setting) {
            $this->current_pdf_logo_path = $setting->pdf_logo_path;
            $this->pdf_logo_height = $setting->pdf_logo_height ?? 50;
            $this->pdf_header_bg_color = $setting->pdf_header_bg_color ?? '#4f46e5';
            $this->pdf_header_text_color = $setting->pdf_header_text_color ?? '#ffffff';

            $this->pdf_font_family = $setting->pdf_font_family ?? 'Segoe UI';
            $this->pdf_primary_color = $setting->pdf_primary_color ?? '#4f46e5';
            $this->pdf_secondary_color = $setting->pdf_secondary_color ?? '#6b7280';
            $this->pdf_table_header_bg_color = $setting->pdf_table_header_bg_color ?? '#f9fafb';
            $this->pdf_table_header_text_color = $setting->pdf_table_header_text_color ?? '#6b7280';

            $this->pdf_discount_color = $setting->pdf_discount_color ?? '#16a34a';
            $this->pdf_total_color = $setting->pdf_total_color ?? '#4f46e5';

            $this->pdf_footer_text = $setting->pdf_footer_text;

            // Download Page Init
            $this->dl_header_bg_color = $setting->dl_header_bg_color ?? '#4f46e5';
            $this->dl_header_text_color = $setting->dl_header_text_color ?? '#ffffff';
            $this->dl_logo_height = $setting->dl_logo_height ?? 50;
            $this->current_dl_logo_path = $setting->dl_logo_path;
            $this->introduction_files = $setting->introduction_files ?? [];
        }
    }

    public function save(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'pdf_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg|max:2048',
            'pdf_logo_height' => 'required|integer|min:10|max:500',
            'pdf_header_bg_color' => 'required|string',
            'pdf_header_text_color' => 'required|string',
            'pdf_font_family' => 'required|string',
            'pdf_primary_color' => 'required|string',
            'pdf_secondary_color' => 'required|string',
            'pdf_table_header_bg_color' => 'required|string',
            'pdf_table_header_text_color' => 'required|string',
            'pdf_discount_color' => 'required|string',
            'pdf_total_color' => 'required|string',
            'pdf_footer_text' => 'nullable|string',
        ]);

        // Handle logo upload with Minio
        if ($this->pdf_logo) {
            $minioService = app(MinioService::class);

            // Delete old logo if exists
            if ($this->current_pdf_logo_path) {
                try {
                    $minioService->deleteFile($this->current_pdf_logo_path);
                } catch (\Exception $e) {
                    // Ignore delete errors if file missing
                }
            }

            // Upload to 'template' folder
            try {
                $uploadResult = $minioService->uploadFile($this->pdf_logo, 'template');
                $data['pdf_logo_path'] = $uploadResult['path'];
            } catch (\Exception $e) {
                $this->error('Logo yüklenemedi: ' . $e->getMessage());
                return;
            }
        }

        unset($data['pdf_logo']); // Remove file object from data array

        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        // Update current path for immediate feedback without reload
        if (isset($data['pdf_logo_path'])) {
            $this->current_pdf_logo_path = $data['pdf_logo_path'];
        }

        $this->success('PDF Şablon Ayarları Kaydedildi');
    }

    public function removeLogo(): void
    {
        $repository = app(PanelSettingRepository::class);
        $minioService = app(MinioService::class);

        if ($this->current_pdf_logo_path) {
            try {
                $minioService->deleteFile($this->current_pdf_logo_path);
            } catch (\Exception $e) {
                $this->error('Logo silinirken hata oluştu: ' . $e->getMessage());
                return;
            }
        }

        $repository->saveSettings(['pdf_logo_path' => null]);
        $this->current_pdf_logo_path = null;
        $this->success('Logo kaldırıldı');
    }

    public function getLogoUrlProperty()
    {
        if (!$this->current_pdf_logo_path)
            return null;

        try {
            return app(MinioService::class)->getFileUrl($this->current_pdf_logo_path);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Download Page Methods
    public function saveDownloadSettings(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'dl_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg|max:2048',
            'dl_logo_height' => 'required|integer|min:10|max:500',
            'dl_header_bg_color' => 'required|string',
            'dl_header_text_color' => 'required|string',
        ]);

        if ($this->dl_logo) {
            $minioService = app(MinioService::class);

            if ($this->current_dl_logo_path) {
                try {
                    $minioService->deleteFile($this->current_dl_logo_path);
                } catch (\Exception $e) {
                }
            }
            try {
                $uploadResult = $minioService->uploadFile($this->dl_logo, 'template');
                $data['dl_logo_path'] = $uploadResult['path'];
            } catch (\Exception $e) {
                $this->error('Logo yüklenemedi: ' . $e->getMessage());
                return;
            }
        }

        unset($data['dl_logo']);
        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        if (isset($data['dl_logo_path'])) {
            $this->current_dl_logo_path = $data['dl_logo_path'];
            $this->reset('dl_logo');
        }

        $this->success('İndirme Sayfası Ayarları Kaydedildi');
    }

    public function removeDownloadLogo(): void
    {
        $repository = app(PanelSettingRepository::class);
        $minioService = app(MinioService::class);

        if ($this->current_dl_logo_path) {
            try {
                $minioService->deleteFile($this->current_dl_logo_path);
            } catch (\Exception $e) {
                $this->error('Logo silinirken hata oluştu: ' . $e->getMessage());
                return;
            }
        }

        $repository->saveSettings(['dl_logo_path' => null]);
        $this->current_dl_logo_path = null;
        $this->success('Logo kaldırıldı');
    }

    public function uploadIntroductionFile(): void
    {
        $this->validate([
            'new_intro_file' => 'required|file|mimes:pdf|max:25600', // 25MB
            'new_intro_name' => 'required|string|max:255',
        ]);

        $this->is_uploading_intro = true;
        try {
            $minioService = app(MinioService::class);
            $uploadResult = $minioService->uploadFile($this->new_intro_file, 'teklif-tanitim');

            $fileData = [
                'name' => $this->new_intro_name,
                'path' => $uploadResult['path'],
                'size' => $this->new_intro_file->getSize(),
                'uploaded_at' => now()->toIso8601String(),
            ];

            $this->introduction_files[] = $fileData;

            app(PanelSettingRepository::class)->saveSettings(['introduction_files' => $this->introduction_files]);
            Cache::forget('theme_settings');

            $this->reset(['new_intro_file', 'new_intro_name']);
            $this->success('Dosya yüklendi');
        } catch (\Exception $e) {
            $this->error('Dosya yüklenirken hata: ' . $e->getMessage());
        } finally {
            $this->is_uploading_intro = false;
        }
    }

    public function deleteIntroductionFile(int $index): void
    {
        if (!isset($this->introduction_files[$index]))
            return;

        $file = $this->introduction_files[$index];
        $minioService = app(MinioService::class);

        try {
            $minioService->deleteFile($file['path']);
        } catch (\Exception $e) {
            // Continue even if delete fails (maybe file missing)
        }

        unset($this->introduction_files[$index]);
        $this->introduction_files = array_values($this->introduction_files); // Reindex

        app(PanelSettingRepository::class)->saveSettings(['introduction_files' => $this->introduction_files]);
        Cache::forget('theme_settings');

        $this->success('Dosya silindi');
    }

    public function getDownloadLogoUrlProperty()
    {
        if (!$this->current_dl_logo_path)
            return null;
        try {
            return app(MinioService::class)->getFileUrl($this->current_dl_logo_path);
        } catch (\Exception $e) {
            return null;
        }
    }
}
