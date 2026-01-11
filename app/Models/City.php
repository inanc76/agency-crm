<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🏙️ City Model - Şehir Verileri (Static Lookup)
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ❌ NONE | PK: string (manual) | Incrementing: false | Timestamps: false
 * 
 * City modeli, statik şehir verilerini tutar (seed ile doldurulur).
 * Customer ve diğer modellerde city_id ile ilişkilendirilir.
 * 
 * ⚠️ Minimal model: Guarded tüm alanlar, timestamps kapalı
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class City extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
}
