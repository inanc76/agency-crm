<?php

namespace App\Livewire\Settings\MailTemplates\Traits;

use App\Models\MailTemplate;
use Illuminate\Support\Facades\Mail;

/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * TRAIT      : HasMailTemplateActions
 * SORUMLULUK : Mail şablonu CRUD işlemleri, test maili gönderimi ve validasyon.
 *
 * BAĞIMLILIKLAR:
 * - Mary\Traits\Toast (Bileşen seviyesinde)
 *
 * METODLAR:
 * - save(): Şablonu kaydeder veya günceller.
 * - delete(): Şablonu siler.
 * - sendTestEmail(): Örnek verilerle test maili gönderir.
 * - openHtmlModal() / saveHtmlModal(): HTML kaynak kodu yönetimi.
 * -------------------------------------------------------------------------
 */
trait HasMailTemplateActions
{
    public function showSystemDeleteWarning()
    {
        $this->error('Hata', 'Sistem şablonları silinemez.');
    }

    public function delete()
    {
        if (! $this->template) {
            return;
        }

        if ($this->template->is_system || $this->template->system_key) {
            $this->error('Hata', 'Sistem şablonları silinemez.');

            return;
        }

        $this->template->delete();
        $this->success('Başarılı', 'Şablon silindi.');

        return $this->redirect(route('settings.mail-templates.index'), navigate: true);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $data = [
            'name' => $this->name,
            'subject' => $this->subject,
            'content' => $this->content,
            'updated_by' => auth()->id(),
        ];

        if ($this->template) {
            $this->template->update($data);
            $this->success('Başarılı', 'Şablon güncellendi.');
        } else {
            $data['created_by'] = auth()->id();
            $data['is_system'] = false;
            $newTemplate = MailTemplate::create($data);
            $this->success('Başarılı', 'Şablon oluşturuldu.');

            return $this->redirect(route('settings.mail-templates.index'), navigate: true);
        }
    }

    public function openHtmlModal()
    {
        $this->tempHtml = $this->content;
        $this->htmlModal = true;
    }

    public function saveHtmlModal()
    {
        $this->content = $this->tempHtml;
        $this->htmlModal = false;
        $this->dispatch('content-updated', content: $this->content);
    }

    public function sendTestEmail()
    {
        if (! $this->template) {
            $this->error('Hata', 'Lütfen önce şablonu kaydedin.');

            return;
        }

        $this->validate([
            'testEmails' => 'required|string',
        ]);

        $emails = array_map('trim', explode(',', $this->testEmails));
        $validEmails = array_filter($emails, fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL));

        if (empty($validEmails)) {
            $this->error('Hata', 'Lütfen geçerli en az bir e-posta adresi girin.');

            return;
        }

        try {
            $dummyData = [
                '{{customer.name}}' => 'Ahmet Yılmaz',
                '{{customer.email}}' => 'ahmet@example.com',
                '{{customer.phone}}' => '0555 123 45 67',
                '{{contact.name}}' => 'Mehmet Demir',
                '{{offer.number}}' => 'TK-2024-001',
                '{{offer.download_link}}' => url('/offer/e87844eb-b44f-4835-9aaa-729addc6e2b1'),
                '{{offer.total_amount}}' => '1.500,00 ₺',
                '{{company.name}}' => config('app.name'),
                '{{system.current_date}}' => now()->format('d.m.Y'),
                '{{system.url}}' => config('app.url'),
            ];

            $finalSubject = strtr($this->subject, $dummyData);
            $finalContent = strtr($this->content, $dummyData);

            foreach ($validEmails as $recipient) {
                Mail::html($finalContent, function ($message) use ($recipient, $finalSubject) {
                    $message->to($recipient)
                        ->subject('[TEST] '.$finalSubject);
                });
            }

            $this->testModal = false;
            $this->success('Başarılı', 'Test e-postası gönderildi.');
        } catch (\Exception $e) {
            $this->error('Hata', 'E-posta gönderilemedi: '.$e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirect(route('settings.mail-templates.index'), navigate: true);
    }
}
