<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use Illuminate\Database\Seeder;

class SystemMailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $admin = \App\Models\User::whereHas('role', fn ($q) => $q->where('name', 'Super Admin'))->first();
        $adminId = $admin?->id;

        $templates = [
            [
                'system_key' => 'welcome_email',
                'name' => 'Hoş Geldiniz Maili',
                'subject' => '🎉 Hoş Geldiniz - Şifrenizi Belirleyin',
                'content' => $this->getWelcomeEmailHtml(),
                'variables' => [
                    ['code' => '{{user.name}}', 'desc' => 'Kullanıcı Adı'],
                    ['code' => '{{user.email}}', 'desc' => 'Kullanıcı E-postası'],
                    ['code' => '{{setup_url}}', 'desc' => 'Şifre Belirleme Linki'],
                ],
                'is_system' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'system_key' => 'password_reset',
                'name' => 'Şifre Sıfırlama Maili',
                'subject' => '🔑 Şifre Sıfırlama İsteği',
                'content' => $this->getPasswordResetEmailHtml(),
                'variables' => [
                    ['code' => '{{user.name}}', 'desc' => 'Kullanıcı Adı'],
                    ['code' => '{{user.email}}', 'desc' => 'Kullanıcı E-postası'],
                    ['code' => '{{setup_url}}', 'desc' => 'Şifre Sıfırlama Linki'],
                ],
                'is_system' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'system_key' => 'new_offer_request',
                'name' => 'Yeni Teklif Talebi Bildirimi',
                'subject' => 'Yeni Teklif Talebi [{{offer.number}}] - {{company_name}}',
                'content' => $this->getNewOfferRequestHtml(),
                'variables' => [
                    ['code' => '{{offer.number}}', 'desc' => 'Teklif Numarası'],
                    ['code' => '{{company_name}}', 'desc' => 'Müşteri Firma Adı'],
                    ['code' => '{{name}}', 'desc' => 'İletişim Kişisi Adı'],
                    ['code' => '{{phone}}', 'desc' => 'İletişim Telefonu'],
                    ['code' => '{{email}}', 'desc' => 'İletişim E-postası'],
                    ['code' => '{{note}}', 'desc' => 'Talep Notu'],
                    ['code' => '{{offer.view_url}}', 'desc' => 'Teklif Görüntüleme Linki'],
                ],
                'is_system' => true,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        ];

        foreach ($templates as $template) {
            MailTemplate::updateOrCreate(
                ['system_key' => $template['system_key']],
                $template
            );
        }
    }

    private function getWelcomeEmailHtml(): string
    {
        return '
<div style="font-family: sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
        <h1 style="color: #ffffff; margin: 0; font-size: 28px;">🎉 Hoş Geldiniz!</h1>
        <p style="color: rgba(255, 255, 255, 0.9); margin: 8px 0 0 0;">Hesabınız başarıyla oluşturuldu</p>
    </div>
    <div style="padding: 40px 30px;">
        <div style="font-size: 18px; color: #374151; margin-bottom: 24px;">Merhaba <strong>{{user.name}}</strong>,</div>
        <p style="color: #6b7280; font-size: 16px;">Sistemimize hoş geldiniz! Hesabınız başarıyla oluşturuldu ve artık platformumuzu kullanmaya başlayabilirsiniz.</p>
        <div style="background-color: #f9fafb; border-left: 4px solid #667eea; padding: 20px; margin: 24px 0; border-radius: 0 8px 8px 0;">
            <p style="margin: 0; color: #1f2937;"><strong>Ad Soyad:</strong> {{user.name}}</p>
            <p style="margin: 0; color: #1f2937;"><strong>E-posta:</strong> {{user.email}}</p>
        </div>
        <div style="text-align: center; margin: 32px 0;">
            <a href="{{setup_url}}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600;">🔐 Şifremi Belirle</a>
        </div>
    </div>
</div>';
    }

    private function getPasswordResetEmailHtml(): string
    {
        return '
<div style="font-family: sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <div style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); padding: 40px 30px; text-align: center;">
        <h1 style="color: #ffffff; margin: 0; font-size: 28px;">🔑 Şifre Sıfırlama</h1>
        <p style="color: rgba(255, 255, 255, 0.9); margin: 8px 0 0 0;">Güvenliğiniz için şifre yenileme</p>
    </div>
    <div style="padding: 40px 30px;">
        <div style="font-size: 18px; color: #374151; margin-bottom: 24px;">Merhaba <strong>{{user.name}}</strong>,</div>
        <p style="color: #6b7280; font-size: 16px;">Şifrenizi sıfırlama talebiniz tarafımıza ulaştı. Aşağıdaki butona tıklayarak yeni şifrenizi belirleyebilirsiniz.</p>
        <div style="text-align: center; margin: 32px 0;">
            <a href="{{setup_url}}" style="display: inline-block; background: #ef4444; color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600;">🔄 Yeni Şifre Oluştur</a>
        </div>
        <p style="color: #991b1b; font-size: 14px; background: #fef2f2; padding: 10px; border-radius: 6px;"><strong>Not:</strong> Bu link güvenlik nedeniyle kısıtlı bir süre için geçerlidir. İşlemi siz yapmadıysanız bu e-postayı dikkate almayın.</p>
    </div>
</div>';
    }

    private function getNewOfferRequestHtml(): string
    {
        return '
<div style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f3f4f6; padding: 20px; border-radius: 8px 8px 0 0;">
        <h2 style="margin:0; color:#111827;">Yeni Teklif Talebi</h2>
        <p style="margin:5px 0 0; color:#6b7280;">{{offer.number}} numaralı teklif için süresi dolduğundan yeni teklif talep edilmiştir.</p>
    </div>
    <div style="padding: 20px; border: 1px solid #e5e7eb; border-top: 0; border-radius: 0 0 8px 8px;">
        <div style="margin-bottom: 15px;"><strong>Teklif No:</strong> {{offer.number}}</div>
        <div style="margin-bottom: 15px;"><strong>Firma Adı:</strong> {{company_name}}</div>
        <div style="margin-bottom: 15px;"><strong>Adı Soyadı:</strong> {{name}}</div>
        <div style="margin-bottom: 15px;"><strong>Telefon:</strong> {{phone}}</div>
        <div style="margin-bottom: 15px;"><strong>E-Posta:</strong> {{email}}</div>
        <div style="margin-bottom: 15px;"><strong>Talep Notu:</strong><br><div style="background-color: #f9fafb; padding: 10px; border-radius: 6px;">{{note}}</div></div>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <a href="{{offer.view_url}}" style="display: inline-block; background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;">Teklifi Görüntüle</a>
        </div>
    </div>
</div>';
    }
}
