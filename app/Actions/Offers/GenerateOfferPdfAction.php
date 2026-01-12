<?php

namespace App\Actions\Offers;

use App\Models\Offer;
use App\Models\StorageSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class GenerateOfferPdfAction
{
    public function execute(Offer $offer): string
    {
        $offer->load(['customer', 'sections.items']);
        $settings = \App\Models\PanelSetting::where('is_active', true)->first() ?? new \App\Models\PanelSetting;

        // Resolve Logo as Base64 Data URI for Browsershot
        $logoDataUri = null;
        if (! empty($settings->pdf_logo_path)) {
            try {
                $logoDataUri = $this->getLogoAsBase64($settings->pdf_logo_path);
            } catch (\Exception $e) {
                // Fail silently, display no logo
            }
        }

        // Teklifi hazırlayan kişi bilgisi (auth user veya creator)
        $preparedBy = auth()->user()?->name ?? 'Belirtilmemiş';

        $html = view('pdf.offer.template', [
            'offer' => $offer,
            'settings' => $settings,
            'logoUrl' => $logoDataUri,
            'offerNumber' => $offer->number ?? 'TKL-0001',
            'offerDate' => $offer->created_at->format('d.m.Y'),
            'validUntil' => $offer->valid_until?->format('d.m.Y') ?? 'Belirtilmemiş',
            'preparedBy' => $preparedBy,
            'currency' => $offer->currency ?? 'USD',
            'vatRate' => $offer->vat_rate ?? 20,
            'sections' => $offer->sections->map(function ($section) use ($offer) {
                $subtotal = $section->items->sum(fn ($item) => ($item->quantity ?? 1) * ($item->price ?? 0));
                $vatRate = $offer->vat_rate ?? 20;
                $vatAmount = $subtotal * ($vatRate / 100);
                $totalWithVat = $subtotal + $vatAmount;

                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'description' => $section->description,
                    'subtotal' => $subtotal,
                    'vat_amount' => $vatAmount,
                    'total_with_vat' => $totalWithVat,
                    'items' => $section->items->map(fn ($item) => [
                        'name' => $item->service_name ?? $item->name ?? 'Hizmet',
                        'description' => $item->description ?? '',
                        'quantity' => $item->quantity ?? 1,
                        'duration' => $item->duration ? $item->duration.' Yıl' : '-',
                        'price' => $item->price ?? 0,
                        'total' => ($item->quantity ?? 1) * ($item->price ?? 0),
                    ])->toArray(),
                ];
            })->toArray(),
        ])->render();

        $fileName = 'teklif-'.Str::uuid().'.pdf';
        $path = storage_path('app/public/offers/pdfs');

        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $fullPath = $path.'/'.$fileName;

        Browsershot::html($html)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground()
            ->emulateMedia('print')
            ->save($fullPath);

        return $fullPath;
    }

    /**
     * Get logo file from Minio and convert to base64 data URI
     */
    private function getLogoAsBase64(string $path): ?string
    {
        $setting = StorageSetting::where('is_active', true)->first();

        if (! $setting) {
            \Log::warning('PDF Logo: StorageSetting not found');

            return null;
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

        try {
            $disk = Storage::build($config);

            if (! $disk->exists($path)) {
                \Log::warning("PDF Logo: File not found in Minio - {$path}");

                return null;
            }

            $content = $disk->get($path);
            $mimeType = $disk->mimeType($path) ?? 'image/png';

            \Log::info("PDF Logo: Successfully loaded from Minio - {$path}, size: ".strlen($content).' bytes');

            return 'data:'.$mimeType.';base64,'.base64_encode($content);
        } catch (\Exception $e) {
            \Log::error('PDF Logo: Error loading from Minio - '.$e->getMessage());

            return null;
        }
    }
}
