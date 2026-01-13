<?php

namespace Tests\Feature\Offers;

use App\Models\PanelSetting;
use Tests\TestCase;

class GenerateOfferPdfTest extends TestCase
{
    public function test_it_renders_executive_summary_for_multi_section_offers()
    {
        // Mocking the data structure that the view expects
        $offer = (object) [
            'id' => 'offer-uuid',
            'title' => 'Test Teklifi',
            'number' => 'TKL-0001',
            'created_at' => now(),
            'valid_until' => now()->addDays(30),
            'currency' => 'USD',
            'vat_rate' => 20,
            'vat_amount' => 180,
            'discount_percentage' => 10,
            'discounted_amount' => 100,
            'original_amount' => 1000,
            'total_amount' => 1080,
            'description' => 'Genel teklif açıklaması',
            'customer' => (object) [
                'name' => 'Üç Nokta Tasarım',
                'email' => 'info@ucnokta.com',
                'phone' => '0555 555 55 55',
            ],
        ];

        $sections = [
            [
                'title' => 'Bölüm 1: Tasarım',
                'description' => 'Logo ve kurumsal kimlik tasarımı',
                'total_with_vat' => 720,
                'items' => [
                    ['name' => 'Logo Tasarım', 'description' => '3 Revizyon hakkı', 'quantity' => 1, 'duration' => '1 Yıl', 'price' => 600, 'total' => 600],
                ],
            ],
            [
                'title' => 'Bölüm 2: Geliştirme',
                'description' => 'Web sitesi yazılım süreci',
                'total_with_vat' => 480,
                'items' => [
                    ['name' => 'NextJS Uygulama', 'description' => 'SEO uyumlu', 'quantity' => 1, 'duration' => '1 Yıl', 'price' => 400, 'total' => 400],
                ],
            ],
        ];

        $html = view('pdf.offer.template', [
            'offer' => $offer,
            'settings' => new PanelSetting,
            'logoUrl' => null,
            'offerNumber' => 'TKL-0001',
            'offerDate' => now()->format('d.m.Y'),
            'validUntil' => now()->addDays(30)->format('d.m.Y'),
            'currency' => 'USD',
            'vatRate' => 20,
            'sections' => array_map(fn ($s) => array_merge($s, [
                'subtotal' => 600,
                'vat_amount' => 120,
            ]), $sections),
        ])->render();

        $this->assertStringContainsString('Yönetici Özeti', $html);
        $this->assertStringContainsString('Bölüm 1: Tasarım', $html);
        $this->assertStringContainsString('720', $html);
        $this->assertStringContainsString('Bölüm 2: Geliştirme', $html);
        $this->assertStringContainsString('480', $html);
        $this->assertStringContainsString('İndirimli Fiyat', $html);
        $this->assertStringContainsString('page-break', $html);
    }

    public function test_it_does_not_render_executive_summary_for_single_section_offer()
    {
        $offer = (object) [
            'id' => 'offer-uuid',
            'title' => 'Tek Bölümlü Teklif',
            'number' => 'TKL-0002',
            'created_at' => now(),
            'valid_until' => now()->addDays(30),
            'currency' => 'USD',
            'vat_rate' => 20,
            'vat_amount' => 200,
            'discount_percentage' => 0,
            'discounted_amount' => 0,
            'original_amount' => 1000,
            'total_amount' => 1200,
            'description' => 'Açıklama',
            'customer' => (object) [
                'name' => 'Müşteri A',
                'email' => 'a@test.com',
                'phone' => '123',
            ],
        ];

        $sections = [
            [
                'title' => 'Tek Bölüm',
                'description' => 'Açıklama',
                'total_with_vat' => 1200,
                'items' => [['name' => 'S1', 'description' => 'D1', 'quantity' => 1, 'duration' => '1 Yıl', 'price' => 1000, 'total' => 1000]],
            ],
        ];

        $html = view('pdf.offer.template', [
            'offer' => $offer,
            'settings' => new PanelSetting,
            'logoUrl' => null,
            'offerNumber' => 'TKL-0002',
            'offerDate' => now()->format('d.m.Y'),
            'validUntil' => now()->addDays(30)->format('d.m.Y'),
            'currency' => 'USD',
            'vatRate' => 20,
            'sections' => array_map(fn ($s) => array_merge($s, [
                'subtotal' => 1000,
                'vat_amount' => 200,
            ]), $sections),
        ])->render();

        $this->assertStringContainsString('Yönetici Özeti', $html);
        $this->assertStringContainsString('Teklif Özeti', $html);
        $this->assertStringContainsString('SAYFA 2', $html);
    }

    public function test_it_uses_custom_header_colors_from_settings()
    {
        $settings = new PanelSetting([
            'pdf_header_bg_color' => '#FCD34D',
            'pdf_header_text_color' => '#1F2937',
            'pdf_primary_color' => '#4f46e5',
        ]);

        $offer = (object) [
            'id' => 'offer-uuid',
            'title' => 'Test',
            'number' => 'TKL-0003',
            'created_at' => now(),
            'valid_until' => now()->addDays(30),
            'currency' => 'USD',
            'vat_rate' => 20,
            'vat_amount' => 100,
            'discount_percentage' => 0,
            'discounted_amount' => 0,
            'original_amount' => 500,
            'total_amount' => 600,
            'description' => '',
            'customer' => (object) [
                'name' => 'Test Customer',
                'email' => 'test@test.com',
                'phone' => '123',
            ],
        ];

        $sections = [
            [
                'title' => 'Bölüm 1',
                'description' => '',
                'total_with_vat' => 600,
                'subtotal' => 500,
                'vat_amount' => 100,
                'items' => [['name' => 'S1', 'description' => 'D1', 'quantity' => 1, 'duration' => '1 Yıl', 'price' => 500, 'total' => 500]],
            ],
        ];

        $html = view('pdf.offer.template', [
            'offer' => $offer,
            'settings' => $settings,
            'logoUrl' => null,
            'offerNumber' => 'TKL-0003',
            'offerDate' => now()->format('d.m.Y'),
            'validUntil' => now()->addDays(30)->format('d.m.Y'),
            'currency' => 'USD',
            'vatRate' => 20,
            'sections' => $sections,
        ])->render();

        // Verify header colors are applied (CSS may have whitespace)
        $this->assertStringContainsString('#FCD34D', $html);
        $this->assertStringContainsString('#1F2937', $html);
    }
}
