<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasBlameable Trait (Audit Trail for Deletes)                                              ║
 * ║  🎯 ANA GÖREV: Soft delete yapıldığında silen kullanıcıyı otomatik kaydetme                                     ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • bootHasBlameable(): Model boot aşamasında deleting observer kaydı                                            ║
 * ║  • Deleting event: deleted_by kolonunu auth()->id() ile doldurma                                                ║
 * ║  • Column check: Tablo yapısında deleted_by yoksa işlem yapılmaz                                                ║
 * ║                                                                                                                  ║
 * ║  📊 KULLANIM:                                                                                                   ║
 * ║  • Model'e `use HasBlameable;` ekle                                                                             ║
 * ║  • Model'de `use SoftDeletes;` aktif olmalı                                                                     ║
 * ║  • Tabloda `deleted_by` kolonu olmalı (UUID, nullable)                                                          ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK:                                                                                                   ║
 * ║  • Auth::id() null ise (CLI, Scheduler) deleted_by boş kalır                                                    ║
 * ║  • Foreign key constraint ile veri bütünlüğü sağlanır                                                           ║
 * ║                                                                                                                  ║
 * ║  📢 EVENTS:                                                                                                     ║
 * ║  • deleting: SoftDelete öncesi tetiklenir, deleted_by atanır                                                    ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasBlameable
{
    /**
     * Boot the HasBlameable trait
     * 
     * @purpose Model boot aşamasında deleting observer kaydeder
     * @return void
     */
    public static function bootHasBlameable(): void
    {
        static::deleting(function ($model) {
            // Check if the model has deleted_by column
            if (Schema::hasColumn($model->getTable(), 'deleted_by')) {
                // Only set if we have an authenticated user
                if (Auth::check()) {
                    $model->deleted_by = Auth::id();
                    $model->saveQuietly(); // Save without triggering events
                }
            }
        });
    }

    /**
     * Get the user who deleted this record
     * 
     * @purpose Silme işlemini yapan kullanıcıyı döndürür
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }

    /**
     * Check if the record was soft deleted
     * 
     * @purpose Kaydın silinip silinmediğini kontrol eder
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }

    /**
     * Get formatted deletion info
     * 
     * @purpose Silme bilgisini formatlanmış olarak döndürür
     * @return string|null
     */
    public function getDeletionInfo(): ?string
    {
        if (!$this->isDeleted()) {
            return null;
        }

        $deletedBy = $this->deletedBy;
        $deletedAt = $this->deleted_at->format('d.m.Y H:i');

        if ($deletedBy) {
            return "{$deletedBy->name} tarafından {$deletedAt} tarihinde arşivlendi";
        }

        return "{$deletedAt} tarihinde arşivlendi";
    }
}
