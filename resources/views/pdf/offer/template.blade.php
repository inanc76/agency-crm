<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teklif - {{ $offerNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: '{{ $settings->pdf_font_family ?? 'Segoe UI' }}', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background-color: #f8fafc;
            line-height: 1.4;
        }

        .page {
            padding: 32px;
            background: #ffffff;
            position: relative;
            min-height: 297mm;
        }

        .page-content {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.12);
            border: 1px solid #f1f5f9;
            padding: 32px;
            position: relative;
            overflow: hidden;
        }

        .page-badge {
            position: absolute;
            top: 0;
            right: 0;
            padding: 5px 16px;
            background-color: #f3f4f6;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #9ca3af;
            border-bottom-left-radius: 10px;
        }

        /* Header - Sarı Premium Look */
        .pdf-header {
            display: table;
            width: 100%;
            margin-bottom: 24px;
            padding: 16px 20px;
            background-color: {{ $settings->pdf_header_bg_color ?? '#EBD300' }};
            border-radius: 10px;
        }

        .header-logo-cell {
            display: table-cell;
            vertical-align: middle;
        }

        .header-meta-cell {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }

        .logo-img {
            height: {{ ($settings->pdf_logo_height ?? 40) * 0.8 }}px;
            object-fit: contain;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: {{ $settings->pdf_header_text_color ?? '#000000' }};
        }

        .meta-box {
            display: inline-block;
            text-align: left;
            vertical-align: middle;
        }

        .meta-divider {
            display: inline-block;
            padding-left: 16px;
            margin-left: 16px;
            border-left: 1px solid {{ $settings->pdf_header_text_color ?? '#000000' }}40;
            text-align: left;
            vertical-align: middle;
        }

        .label-tiny {
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
            color: {{ $settings->pdf_header_text_color ?? '#000000' }};
            opacity: 0.8;
            letter-spacing: 0.12em;
            margin-bottom: 2px;
            display: block;
        }

        .value-bold {
            font-size: 11px;
            font-weight: 900;
            color: {{ $settings->pdf_header_text_color ?? '#000000' }};
        }

        /* Customer Info Card - Mor border accent */
        .customer-card {
            background-color: rgba(249, 250, 251, 0.5);
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            padding: 20px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .customer-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background-color: {{ $settings->pdf_primary_color ?? '#4F46E5' }};
        }

        .customer-title {
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #9ca3af;
            margin-bottom: 16px;
        }

        .info-table {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-col {
            display: table-cell;
            width: 50%;
            padding: 8px 0;
        }

        .info-label {
            font-size: 7px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 11px;
            font-weight: 700;
            color: #1f2937;
        }

        /* Section Styling */
        .section-box {
            margin-bottom: 20px;
        }

        .section-header {
            margin-bottom: 16px;
            padding-bottom: 6px;
            border-bottom: 3px solid {{ $settings->pdf_primary_color ?? '#4F46E5' }};
            display: inline-block;
        }

        .section-title {
            font-size: 15px;
            font-weight: 900;
            color: {{ $settings->pdf_primary_color ?? '#111827' }};
            font-style: italic;
        }

        .section-desc {
            font-size: 11px;
            color: #6b7280;
            font-style: italic;
            margin-bottom: 20px;
            padding: 12px;
            background: #f9fafb;
            border-radius: 10px;
            border-left: 3px solid #e5e7eb;
        }

        /* Table - Executive Summary Sarı Header */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .summary-table th {
            background-color: {{ $settings->pdf_header_bg_color ?? '#EBD300' }};
            color: {{ $settings->pdf_header_text_color ?? '#000000' }};
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 8px 12px;
            text-align: left;
        }

        .summary-table th:last-child {
            text-align: right;
        }

        .summary-table td {
            padding: 10px 12px;
            font-size: 11px;
            border-bottom: 1px solid #f3f4f6;
            background: #ffffff;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
        }

        .summary-table td:first-child {
            font-weight: 500;
            color: #374151;
        }

        .summary-table td:last-child {
            text-align: right;
            font-weight: 700;
            color: #111827;
        }

        /* Table - Detay Tabloları için Açık Gri Header */
        .premium-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 24px;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .premium-table th {
            background-color: {{ $settings->pdf_table_header_bg_color ?? '#f8fafc' }};
            color: {{ $settings->pdf_table_header_text_color ?? '#374151' }};
            font-size: 7px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .premium-table td {
            padding: 12px 16px;
            font-size: 11px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }

        .premium-table tr:last-child td {
            border-bottom: none;
        }

        .premium-table tr:hover {
            background-color: rgba(249, 250, 251, 0.5);
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Summary Wrapper */
        .summary-wrapper {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .summary-left {
            display: table-cell;
            width: 58%;
            vertical-align: top;
            padding-right: 24px;
        }

        .summary-right {
            display: table-cell;
            width: 42%;
            vertical-align: top;
        }

        /* Description Box */
        .description-box {
            padding: 16px;
            background: #ffffff;
            border-radius: 10px;
            border-left: 3px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .description-title {
            font-size: 7px;
            font-weight: 900;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 8px;
        }

        .description-text {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.5;
            font-style: italic;
            white-space: pre-line;
        }

        /* Premium Summary Box */
        .premium-summary {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            padding: 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .summary-box-title {
            font-size: 8px;
            font-weight: 900;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f3f4f6;
        }

        .summary-item {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .item-label {
            display: table-cell;
            font-size: 9px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .item-value {
            display: table-cell;
            text-align: right;
            font-weight: 700;
            font-size: 11px;
            color: #1f2937;
        }

        .discount-row {
            color: #dc2626;
        }

        .discount-row .item-label,
        .discount-row .item-value {
            color: #dc2626;
            font-weight: 700;
        }

        .total-row {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
            display: table;
            width: 100%;
        }

        .total-label {
            display: table-cell;
            font-size: 8px;
            font-weight: 900;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .total-value {
            display: table-cell;
            text-align: right;
            font-size: 18px;
            font-weight: 900;
            font-style: italic;
            color: {{ $settings->pdf_total_color ?? '#4F46E5' }};
        }

        .vat-note {
            font-size: 7px;
            color: #9ca3af;
            text-align: right;
            font-style: italic;
            margin-top: 10px;
        }

        /* Footer */
        .page-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
            text-align: center;
        }

        .footer-text {
            font-size: 8px;
            color: #d1d5db;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
        }

        .page-break {
            height: 0;
            page-break-after: always;
            break-after: page;
        }
    </style>
</head>

<body>
    {{-- YÖNETİCİ ÖZETİ SAYFASI --}}
    @if(count($sections) > 1)
        <div class="page">
            <div class="page-content">
                <div class="page-badge">YÖNETİCİ ÖZETİ</div>

                {{-- Header --}}
                <div class="pdf-header">
                    <div class="header-logo-cell">
                        @if(!empty($logoUrl))
                            <img src="{{ $logoUrl }}" class="logo-img" alt="Logo">
                        @else
                            <span class="logo-text">{{ config('app.name') }}</span>
                        @endif
                    </div>
                    <div class="header-meta-cell">
                        <div class="meta-box">
                            <span class="label-tiny">Teklif No</span>
                            <span class="value-bold">{{ $offerNumber }}</span>
                        </div>
                        <div class="meta-divider">
                            <span class="label-tiny">Tarih</span>
                            <span class="value-bold">{{ $offerDate }}</span>
                        </div>
                    </div>
                </div>

                {{-- Teklif Bilgileri --}}
                <div class="customer-card">
                    <div class="customer-title">Teklif Bilgileri</div>
                    <div class="info-table">
                        <div class="info-row">
                            <div class="info-col">
                                <div class="info-label">Müşteri Adı</div>
                                <div class="info-value">{{ $offer->customer?->name ?? 'Belirtilmemiş' }}</div>
                            </div>
                            <div class="info-col">
                                <div class="info-label">Teklifi Hazırlayan</div>
                                <div class="info-value">{{ $preparedBy ?? 'Belirtilmemiş' }}</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-col">
                                <div class="info-label">Hazırlanma Tarihi</div>
                                <div class="info-value">{{ $offerDate }}</div>
                            </div>
                            <div class="info-col">
                                <div class="info-label">Geçerlilik Tarihi</div>
                                <div class="info-value">{{ $validUntil }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Yönetici Özeti Bölümü --}}
                <div class="section-box">
                    <div class="section-header">
                        <h2 class="section-title">Yönetici Özeti</h2>
                    </div>

                    {{-- Özet Tablosu --}}
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th>Bölüm Başlığı</th>
                                <th>Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sections as $section)
                                <tr>
                                    <td>{{ $section['title'] }}</td>
                                    <td>{{ number_format($section['total_with_vat'], 0, ',', '.') }} {{ $currency }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Açıklama ve Özet --}}
                <div class="summary-wrapper">
                    <div class="summary-left">
                        @if($offer->description)
                            <div class="description-box">
                                <div class="description-title">Teklif Açıklaması</div>
                                <p class="description-text">{{ $offer->description }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="summary-right">
                        <div class="premium-summary">
                            <div class="summary-box-title">Teklif Özeti</div>

                            @if(($offer->discounted_amount ?? 0) > 0)
                                <div class="summary-item discount-row">
                                    <span class="item-label">İndirim (@if($offer->discount_percentage > 0) %{{ (int) $offer->discount_percentage }} @else Tutar @endif):</span>
                                    @php $discountWithVat = $offer->discounted_amount * (1 + ($vatRate / 100)); @endphp
                                    <span class="item-value">-{{ number_format($discountWithVat, 0, ',', '.') }} {{ $currency }}</span>
                                </div>
                                <div class="total-row">
                                    <span class="total-label">İndirimli Fiyat</span>
                                    <span class="total-value">{{ number_format($offer->total_amount, 0, ',', '.') }} {{ $currency }}</span>
                                </div>
                            @else
                                <div class="total-row" style="border-top: none; padding-top: 0; margin-top: 0;">
                                    <span class="total-label">Genel Toplam</span>
                                    <span class="total-value">{{ number_format($offer->total_amount, 0, ',', '.') }} {{ $currency }}</span>
                                </div>
                            @endif
                            <p class="vat-note">Fiyatlara KDV dahildir.</p>
                        </div>
                    </div>
                </div>

                @if(!empty($settings->pdf_footer_text))
                    <div class="page-footer">
                        <p class="footer-text">{{ $settings->pdf_footer_text }}</p>
                    </div>
                @endif
            </div>
        </div>
        <div class="page-break"></div>
    @endif

    {{-- DETAY SAYFALARI (BÖLÜMLER) --}}
    @foreach($sections as $index => $section)
        <div class="page">
            <div class="page-content">
                <div class="page-badge">SAYFA {{ count($sections) > 1 ? ($index + 2) : 1 }}</div>

                {{-- Header --}}
                <div class="pdf-header">
                    <div class="header-logo-cell">
                        @if(!empty($logoUrl))
                            <img src="{{ $logoUrl }}" class="logo-img" alt="Logo">
                        @else
                            <span class="logo-text">{{ config('app.name') }}</span>
                        @endif
                    </div>
                    <div class="header-meta-cell">
                        <div class="meta-box">
                            <span class="label-tiny">Teklif No</span>
                            <span class="value-bold">{{ $offerNumber }}</span>
                        </div>
                        <div class="meta-divider">
                            <span class="label-tiny">Tarih</span>
                            <span class="value-bold">{{ $offerDate }}</span>
                        </div>
                    </div>
                </div>

                {{-- Bölüm Başlığı --}}
                <div class="section-box">
                    <div class="section-header">
                        <h2 class="section-title">{{ $section['title'] }}</h2>
                    </div>

                    @if(!empty($section['description']))
                        <p class="section-desc">{{ $section['description'] }}</p>
                    @endif

                    {{-- Detay Tablosu --}}
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Hizmet</th>
                                <th style="width: 30%;">Açıklama</th>
                                <th class="text-center" style="width: 10%;">Adet</th>
                                <th class="text-center" style="width: 10%;">Süre</th>
                                <th class="text-right" style="width: 15%;">Birim Fiyat</th>
                                <th class="text-right" style="width: 15%;">Toplam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($section['items'] as $item)
                                <tr>
                                    <td style="font-weight: 700; color: #111827;">{{ $item['name'] }}</td>
                                    <td style="color: #6b7280; font-size: 12px; font-style: italic; line-height: 1.5;">{{ $item['description'] }}</td>
                                    <td class="text-center" style="font-weight: 700; color: #374151;">{{ $item['quantity'] }}</td>
                                    <td class="text-center" style="font-weight: 700; color: #374151; font-size: 10px; text-transform: uppercase;">{{ $item['duration'] }}</td>
                                    <td class="text-right" style="font-weight: 600; color: #6b7280; white-space: nowrap;">{{ number_format($item['price'], 0, ',', '.') }} {{ $currency }}</td>
                                    <td class="text-right" style="font-weight: 900; color: #111827; white-space: nowrap; background: rgba(249, 250, 251, 0.5);">{{ number_format($item['total'], 0, ',', '.') }} {{ $currency }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Açıklama ve Özet --}}
                <div class="summary-wrapper">
                    <div class="summary-left">
                        @if(count($sections) == 1 && $offer->description)
                            <div class="description-box">
                                <div class="description-title">Teklif Açıklaması</div>
                                <p class="description-text">{{ $offer->description }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="summary-right">
                        <div class="premium-summary">
                            <div class="summary-box-title">Teklif Özeti</div>
                            <div class="summary-item">
                                <span class="item-label">Ara Toplam:</span>
                                <span class="item-value">{{ number_format($section['subtotal'], 0, ',', '.') }} {{ $currency }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="item-label">KDV (%{{ (int) $vatRate }}):</span>
                                <span class="item-value">{{ number_format($section['vat_amount'], 0, ',', '.') }} {{ $currency }}</span>
                            </div>
                            <div class="total-row">
                                <span class="total-label">Genel Toplam</span>
                                <span class="total-value">{{ number_format($section['total_with_vat'], 0, ',', '.') }} {{ $currency }}</span>
                            </div>
                            <p class="vat-note">Fiyatlara KDV dahildir.</p>
                        </div>
                    </div>
                </div>

                @if(!empty($settings->pdf_footer_text))
                    <div class="page-footer">
                        <p class="footer-text">{{ $settings->pdf_footer_text }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if($index < count($sections) - 1)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>