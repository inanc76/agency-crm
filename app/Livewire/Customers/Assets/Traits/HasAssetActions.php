<?php

namespace App\Livewire\Customers\Assets\Traits;

use App\Models\Asset;
use Illuminate\Support\Str;

/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * TRAIT      : HasAssetActions
 * SORUMLULUK : Müşteri varlıklarının (Asset) CRUD operasyonlarını ve
 *              URL bazlı varlık yönetimini sağlar.
 *
 * BAĞIMLILIKLAR:
 * - Mary\Traits\Toast (Bileşen seviyesinde)
 *
 * METODLAR:
 * - loadAssetData(): Mevcut varlık bilgilerini form alanlarına yükler.
 * - save(): Yeni varlık oluşturur veya mevcut olanı günceller.
 * - cancel(): İşlemi durdurur ve geri yönlendirir.
 * - toggleEditMode(): Görüntüleme ve düzenleme modları arasında geçiş yapar.
 * - delete(): Varlığı sistemden siler.
 * - updatedUrl(): URL formatını otomatik düzeltir.
 * -------------------------------------------------------------------------
 */
trait HasAssetActions
{
    /**
     * Mevcut bir varlığın verilerini form alanlarına yükler.
     */
    public function loadAssetData(): void
    {
        $asset = Asset::findOrFail($this->assetId);

        $this->customer_id = $asset->customer_id;
        $this->name = $asset->name;
        $this->type = $asset->type;
        $this->url = $asset->url ?? '';

        $this->isViewMode = true;
    }

    /**
     * Varlığı kaydeder veya günceller.
     * Güvenlik: Asset yönetimi form seviyesinde yetki denetimine tabidir.
     */
    public function save(): void
    {
        $typeKeys = collect($this->assetTypes)->pluck('id')->implode(',');

        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => [
                'required',
                'string',
                'min:2',
                'max:150',
                \Illuminate\Validation\Rule::unique('assets', 'name')
                    ->where('customer_id', $this->customer_id)
                    ->ignore($this->assetId)
            ],
            'type' => "required|in:{$typeKeys}",
            'url' => 'nullable|url|max:255',
        ]);

        $data = [
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'type' => $this->type,
            'url' => $this->url,
        ];

        if ($this->assetId) {
            $asset = Asset::findOrFail($this->assetId);
            $asset->update($data);
            $message = 'Varlık bilgileri güncellendi.';
        } else {
            $this->assetId = Str::uuid()->toString();
            $data['id'] = $this->assetId;
            Asset::create($data);
            $message = 'Yeni varlık başarıyla oluşturuldu.';
        }

        $this->success('İşlem Başarılı', $message);
        $this->isViewMode = true;

        $this->dispatch('asset-saved');
    }

    /**
     * İşlemi iptal eder ve müşteri detaylarındaki varlıklar sekmesine döner.
     */
    public function cancel(): void
    {
        if ($this->assetId) {
            $this->loadAssetData();
        } else {
            $this->redirect('/dashboard/customers/' . $this->customer_id . '?tab=assets', navigate: true);
        }
    }

    /**
     * Düzenleme modunu açar.
     */
    public function toggleEditMode(): void
    {
        $this->isViewMode = false;
    }

    /**
     * Kaydı siler.
     */
    public function delete(): void
    {
        if ($this->assetId) {
            $asset = Asset::findOrFail($this->assetId);
            $customer_id = $asset->customer_id;
            $asset->delete();
            $this->success('Varlık Silindi', 'Varlık kaydı başarıyla silindi.');
            $this->redirect('/dashboard/customers/' . $customer_id . '?tab=assets');
        }
    }

    /**
     * URL güncellendiğinde protokol yoksa otomatik https:// ekler.
     * İş Kuralı: Kullanıcının protokol yazma zahmetini azaltır.
     */
    public function updatedUrl()
    {
        $val = trim($this->url);
        if ($val && !preg_match('/^https?:\/\//', $val) && str_contains($val, '.')) {
            $this->url = 'https://' . $val;
        }
    }
}
