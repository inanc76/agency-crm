<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📧 Message Model - Müşteri Mesajları/Mail İletişimi
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * @version Constitution V10
 *
 * 🔑 UUID: ⚠️ MANUAL (No HasUuids trait) | PK: string | Incrementing: false
 *
 * @property string $id UUID primary key (manuel)
 * @property string $customer_id Müşteri UUID (FK: customers)
 * @property string|null $offer_id Teklif UUID (FK: offers)
 * @property string|null $mail_template_id Şablon UUID (FK: mail_templates)
 * @property string|null $subject Mesaj konusu
 * @property string|null $body Mesaj içeriği
 * @property string|null $recipient_name Alıcının adı
 * @property string|null $recipient_email Alıcının e-posta adresi
 * @property string|null $contact_id İletişim kişisi UUID (FK: contacts)
 * @property string|null $type Mesaj tipi (ReferenceData: EMAIL, SMS, etc.)
 * @property string|null $status Mesaj durumu (SENT, FAILED, PENDING)
 * @property \Carbon\Carbon|null $sent_at Gönderim zamanı
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Customer $customer BelongsTo: Mesajın gönderildiği müşteri
 * @property-read Contact|null $contact BelongsTo: Mesajın gönderildiği kişi
 * @property-read Offer|null $offer BelongsTo: İlişkili teklif
 * @property-read MailTemplate|null $mailTemplate BelongsTo: Kullanılan şablon
 *
 * Message, müşteriyle yapılan e-posta/SMS iletişimini loglar.
 * Mail queue sistemi ile entegre çalışır.
 *
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Message extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'offer_id',
        'mail_template_id',
        'subject',
        'body',
        'recipient_name',
        'recipient_email',
        'cc',
        'bcc',
        'contact_id',
        'type',
        'status',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function mailTemplate()
    {
        return $this->belongsTo(MailTemplate::class);
    }

    public function status_item()
    {
        return $this->hasOne(ReferenceItem::class, 'key', 'status')
            ->where('category_key', 'MAIL_STATUS');
    }

    public function type_item()
    {
        return $this->hasOne(ReferenceItem::class, 'key', 'type')
            ->where('category_key', 'MAIL_TYPE');
    }
}
