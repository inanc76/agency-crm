<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoş Geldiniz</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 8px 0 0 0;
            font-size: 16px;
        }

        .content {
            padding: 40px 30px;
        }

        .welcome-text {
            font-size: 18px;
            color: #374151;
            margin-bottom: 24px;
            line-height: 1.7;
        }

        .user-info {
            background-color: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }

        .user-info h3 {
            margin: 0 0 8px 0;
            color: #1f2937;
            font-size: 16px;
        }

        .user-info p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .cta-section {
            text-align: center;
            margin: 32px 0;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s ease;
        }

        .cta-button:hover {
            transform: translateY(-2px);
        }

        .instructions {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }

        .instructions h4 {
            margin: 0 0 12px 0;
            color: #92400e;
            font-size: 16px;
        }

        .instructions ul {
            margin: 0;
            padding-left: 20px;
            color: #92400e;
        }

        .instructions li {
            margin-bottom: 8px;
        }

        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .footer a {
            color: #667eea;
            text-decoration: none;
        }

        .security-note {
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
        }

        .security-note p {
            margin: 0;
            color: #991b1b;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }

            .header,
            .content,
            .footer {
                padding: 30px 20px;
            }

            .cta-button {
                padding: 14px 24px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            @if(isset($isReset) && $isReset)
                <h1>🔑 Şifre Sıfırlama</h1>
                <p>Güvenliğiniz için şifre yenileme</p>
            @else
                <h1>🎉 Hoş Geldiniz!</h1>
                <p>Hesabınız başarıyla oluşturuldu</p>
            @endif
        </div>

        {{-- Content --}}
        <div class="content">
            <div class="welcome-text">
                Merhaba <strong>{{ $user->name }}</strong>,
            </div>

            <p style="color: #6b7280; font-size: 16px; line-height: 1.7;">
                @if(isset($isReset) && $isReset)
                    Şifrenizi sıfırlama talebiniz tarafımıza ulaştı. Aşağıdaki adımları takip ederek yeni şifrenizi
                    belirleyebilirsiniz.
                @else
                    Sistemimize hoş geldiniz! Hesabınız başarıyla oluşturuldu ve artık platformumuzu kullanmaya
                    başlayabilirsiniz.
                @endif
            </p>

            {{-- User Info (Hide for Reset to be cleaner, or keep it. Let's keep basics) --}}
            <div class="user-info">
                <h3>Hesap Bilgileri</h3>
                <p><strong>Ad Soyad:</strong> {{ $user->name }}</p>
                <p><strong>E-posta:</strong> {{ $user->email }}</p>
                @if($user->title)
                    <p><strong>Unvan:</strong> {{ $user->title }}</p>
                @endif
            </div>

            {{-- Instructions --}}
            <div class="instructions">
                @if(isset($isReset) && $isReset)
                    <h4>📋 Ne Yapmalısınız?</h4>
                    <ul>
                        <li>Aşağıdaki butona tıklayarak yeni şifrenizi belirleyin</li>
                        <li>Bu işlemi siz talep etmediyseniz, bu e-postayı görmezden gelebilirsiniz</li>
                        <li>Linkin geçerlilik süresi güvenlik nedeniyle kısıtlıdır (1 Saat)</li>
                    </ul>
                @else
                    <h4>📋 Sonraki Adımlar</h4>
                    <ul>
                        <li>Aşağıdaki butona tıklayarak şifrenizi belirleyin</li>
                        <li>Güvenli bir şifre seçin (en az 8 karakter)</li>
                        <li>Şifrenizi belirledikten sonra sisteme giriş yapabilirsiniz</li>
                    </ul>
                @endif
            </div>

            {{-- CTA Button --}}
            <div class="cta-section">
                <a href="{{ $setupUrl }}" class="cta-button">
                    @if(isset($isReset) && $isReset)
                        🔄 Yeni Şifre Oluştur
                    @else
                        🔐 Şifremi Belirle
                    @endif
                </a>
            </div>

            {{-- Security Note --}}
            <div class="security-note">
                <p>
                    <strong>🔒 Güvenlik Notu:</strong> Bu link güvenlik nedeniyle kısıtlı bir süre için geçerlidir. Link
                    çalışmıyorsa yönetici ile iletişime geçin.
                </p>
            </div>

            <p style="color: #6b7280; font-size: 14px; margin-top: 32px;">
                Herhangi bir sorunuz varsa, lütfen bizimle iletişime geçmekten çekinmeyin.
            </p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>
                Bu e-posta {{ config('app.name', 'MEDIACLICK') }} tarafından gönderilmiştir.<br>
                <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
            </p>
        </div>
    </div>
</body>

</html>