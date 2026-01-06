<?php

namespace App\Repositories;

use App\Models\StorageSetting;
use Illuminate\Support\Facades\DB;

class StorageSettingRepository
{
    /**
     * Get the active storage setting or the first one available.
     */
    public function getActiveSetting(): ?StorageSetting
    {
        return StorageSetting::where('is_active', true)
            ->first();
    }

    /**
     * Update or create the storage settings.
     * Since we likely only want one active config for now, we can update the existing one or create it.
     */
    public function saveSettings(array $data): StorageSetting
    {
        return DB::transaction(function () use ($data) {
            // Check if we have an ID to update, or just take the first one
            $setting = StorageSetting::first();

            if ($setting) {
                $setting->update($data);
                return $setting;
            }

            return StorageSetting::create($data);
        });
    }
}
