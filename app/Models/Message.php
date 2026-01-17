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
 * @property string|null $type Mesaj tipi (ReferenceData: EMAIL, SMS, etc.)
 * @property string|null $status Mesaj durumu (SENT, FAILED, PENDING)
 * @property \Carbon\Carbon|null $sent_at Gönderim zamanı
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Customer $customer BelongsTo: Mesajın gönderildiği müşteri
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

    public function mailTemplate()
    {
        return $this->belongsTo(MailTemplate::class);
    }
}
