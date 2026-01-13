<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f3f4f6;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-top: 0;
            border-radius: 0 0 8px 8px;
        }

        .field {
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            color: #374151;
            font-size: 0.9em;
            text-transform: uppercase;
        }

        .value {
            color: #1f2937;
            margin-top: 5px;
        }

        .footer {
            margin-top: 20px;
            font-size: 0.8em;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0; color:#111827;">Yeni Teklif Talebi</h2>
            <p style="margin:5px 0 0; color:#6b7280;">{{ $offer->number }} numaralı teklif için süresi dolduğundan yeni
                teklif talep edilmiştir.</p>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Teklif No</div>
                <div class="value" style="font-family: monospace; font-size: 1.1em; font-weight: 600;">{{ $offer->number }}</div>
            </div>
            <div class="field">
                <div class="label">Firma Adı</div>
                <div class="value">{{ $data['company_name'] }}</div>
            </div>
            <div class="field">
                <div class="label">Adı Soyadı</div>
                <div class="value">{{ $data['name'] }}</div>
            </div>
            <div class="field">
                <div class="label">Telefon</div>
                <div class="value">{{ $data['phone'] ?: '-' }}</div>
            </div>
            <div class="field">
                <div class="label">E-Posta</div>
                <div class="value">{{ $data['email'] }}</div>
            </div>
            <div class="field">
                <div class="label">Notunuz</div>
                <div class="value" style="background-color: #f9fafb; padding: 10px; border-radius: 6px;">{{ $data['note'] ?? '-' }}</div>
            </div>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <a href="{{ route('customers.offers.edit', $offer->id) }}"
                    style="display: inline-block; background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;">Teklifi
                    Görüntüle</a>
            </div>
        </div>
        <div class="footer">
            bu bildirim {{ config('app.name') }} tarafından otomatik olarak gönderilmiştir.
        </div>
    </div>
</body>

</html>