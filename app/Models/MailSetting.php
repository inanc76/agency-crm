<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📬 MailSetting Model - Mail Sunucu Ayarları
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * MailSetting, SMTP mail sunucu yapılandırmasını saklar:
 * - SMTP host, port, username, password, encryption
 * - is_active: Aktif mail ayarı (tek bir kayıt aktif olmalı)
 * - Ayarlar sayfasında yönetilir
 * - Mail gönderiminde runtime'da config'e inject edilir
 * 
 * ⚠️ Guarded: Tüm alanlar mass-assignable
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class MailSetting extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mail_settings';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'smtp_secure' => 'boolean',
    ];
}
