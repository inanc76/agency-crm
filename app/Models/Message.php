<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📧 Message Model - Müşteri Mesajları/Mail İletişimi
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Message extends Model
{
    use HasFactory;
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
