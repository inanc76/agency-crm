# 🎯 AGENCY V10 REFACTORING AKSIYON PLANI

**Plan Tarihi:** 16 Ocak 2026  
**Hedef Skor:** 72/100 → 90/100  
**Tahmini Süre:** 4-6 Hafta  
**Baseline:** Constitution V11

---

## 📅 HAFTALIK SPRINT PLANI

### 🔴 SPRINT 1 (Hafta 1-2): Kritik Dosya Refactoring

#### Gün 1-2: Projects Create/Edit Refactoring

**Hedef:** 1,375-1,493 satır → 150 satır

**Adım 1: Trait Separation**
```bash
# Yeni trait dosyaları oluştur
touch app/Livewire/Projects/Traits/HasPhaseActions.php
touch app/Livewire/Projects/Traits/HasTaskActions.php
touch app/Livewire/Projects/Traits/HasTeamActions.php
touch app/Livewire/Projects/Traits/HasProjectCalculations.php
```

**Adım 2: Partial Dosyaları Oluştur**
```bash
mkdir -p resources/views/livewire/projects/partials
touch resources/views/livewire/projects/partials/_project-header.blade.php
touch resources/views/livewire/projects/partials/_project-form.blade.php
touch resources/views/livewire/projects/partials/_project-phases.blade.php
touch resources/views/livewire/projects/partials/_project-tasks.blade.php
touch resources/views/livewire/projects/partials/_project-team.blade.php
touch resources/views/livewire/projects/partials/_project-summary.blade.php
```

**Adım 3: Logic Taşıma**
```php
// app/Livewire/Projects/Traits/HasPhaseActions.php
<?php

namespace App\Livewire\Projects\Traits;

/**
 * ╔══════════════════════════════════════════════════════════════════════════╗
 * ║  📋 SORUMLULUK ALANI: HasPhaseActions Trait                              ║
 * ║  🎯 ANA GÖREV: Proje fazları yönetimi (CRUD)                             ║
 * ║                                                                          ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                  ║
 * ║  • addPhase(): Yeni faz ekleme                                           ║
 * ║  • removePhase(): Faz silme                                              ║
 * ║  • updatePhase(): Faz güncelleme                                         ║
 * ║  • reorderPhases(): Faz sıralama                                         ║
 * ╚══════════════════════════════════════════════════════════════════════════╝
 */
trait HasPhaseActions
{
    public array $phases = [];
    
    public function addPhase(): void
    {
        $this->phases[] = [
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => '',
            'description' => '',
            'start_date' => null,
            'end_date' => null,
            'status' => 'PENDING',
        ];
    }
    
    public function removePhase(int $index): void
    {
        unset($this->phases[$index]);
        $this->phases = array_values($this->phases);
    }
}
```

**Adım 4: Ana Dosya Slim Hale Getir**
```php
// resources/views/livewire/projects/create.blade.php (150 satır)
<?php
use Livewire\Volt\Component;
use App\Livewire\Projects\Traits\HasPhaseActions;
use App\Livewire\Projects\Traits\HasTaskActions;
use App\Livewire\Projects\Traits\HasTeamActions;

new class extends Component {
    use HasPhaseActions, HasTaskActions, HasTeamActions;
    
    public string $activeTab = 'info';
}; ?>

<div class="max-w-7xl mx-auto">
    @include('livewire.projects.partials._project-header')
    
    @if($activeTab === 'info')
        @include('livewire.projects.partials._project-form')
    @elseif($activeTab === 'phases')
        @include('livewire.projects.partials._project-phases')
    @elseif($activeTab === 'tasks')
        @include('livewire.projects.partials._project-tasks')
    @endif
</div>
```

**Checklist:**
- [ ] HasPhaseActions.php oluşturuldu (150 satır)
- [ ] HasTaskActions.php oluşturuldu (180 satır)
- [ ] HasTeamActions.php oluşturuldu (120 satır)
- [ ] 6 partial dosyası oluşturuldu
- [ ] create.blade.php 150 satıra düştü
- [ ] edit.blade.php 150 satıra düştü
- [ ] Tüm fonksiyonlar çalışıyor
- [ ] Test suite geçiyor

---

#### Gün 3-4: PDF Template Refactoring

**Hedef:** 757 satır → 200 satır

**Adım 1: Partial Dosyaları**
```bash
mkdir -p resources/views/livewire/settings/pdf-template/partials
touch resources/views/livewire/settings/pdf-template/partials/_header-section.blade.php
touch resources/views/livewire/settings/pdf-template/partials/_body-section.blade.php
touch resources/views/livewire/settings/pdf-template/partials/_footer-section.blade.php
touch resources/views/livewire/settings/pdf-template/partials/_preview.blade.php
```

**Adım 2: Ana Dosya Refactor**
```php
// resources/views/livewire/settings/pdf-template.blade.php (120 satır)
<div class="max-w-7xl mx-auto space-y-6">
    <x-mary-card title="PDF Şablon Ayarları">
        <x-mary-tabs wire:model="activeSection">
            <x-mary-tab name="header" label="Başlık">
                @include('livewire.settings.pdf-template.partials._header-section')
            </x-mary-tab>
            
            <x-mary-tab name="body" label="İçerik">
                @include('livewire.settings.pdf-template.partials._body-section')
            </x-mary-tab>
            
            <x-mary-tab name="footer" label="Alt Bilgi">
                @include('livewire.settings.pdf-template.partials._footer-section')
            </x-mary-tab>
            
            <x-mary-tab name="preview" label="Önizleme">
                @include('livewire.settings.pdf-template.partials._preview')
            </x-mary-tab>
        </x-mary-tabs>
    </x-mary-card>
</div>
```

**Checklist:**
- [ ] 4 partial dosyası oluşturuldu
- [ ] pdf-template.blade.php 200 satıra düştü
- [ ] PDF önizleme çalışıyor
- [ ] Kaydetme fonksiyonu çalışıyor

---

#### Gün 5: Inline Style Temizliği

**Hedef:** 50+ inline style → 0

**Adım 1: CSS Variables Tanımla**
```css
/* public/css/theme-variables.css */
:root {
  /* Dashboard Stats Colors */
  --dashboard-stats-1: #3b82f6;
  --dashboard-stats-2: #10b981;
  --dashboard-stats-3: #f59e0b;
  --dashboard-stats-4: #8b5cf6;
  
  /* Header Colors */
  --header-bg: #3D3373;
  --header-text: #ffffff;
  --header-active-bg: rgba(255, 255, 255, 0.2);
  --header-active-text: #ffffff;
  
  /* Tab Colors */
  --active-tab-color: #6366f1;
  --inactive-tab-color: #94a3b8;
  
  /* Error Panel */
  --error-panel-bg: #fef2f2;
  --error-panel-border: #fecaca;
  --error-panel-text: #991b1b;
}
```

**Adım 2: Inline Style'ları Değiştir**
```bash
# dashboard.blade.php
# ❌ ÖNCE:
<div style="background-color: color-mix(in srgb, var(--dashboard-stats-1), white 90%);">

# ✅ SONRA:
<div class="bg-[var(--dashboard-stats-1)]/10">
```

**Adım 3: Toplu Değiştirme Script**
```bash
# inline-style-cleanup.sh
#!/bin/bash

# Filter panel
sed -i '' 's/style="background-color: white !important;"//g' \
  resources/views/components/customer-management/filter-panel.blade.php

# Dashboard
sed -i '' 's/style="background-color: color-mix.*"//g' \
  resources/views/dashboard.blade.php

echo "✅ Inline style temizliği tamamlandı"
```

**Checklist:**
- [ ] theme-variables.css oluşturuldu
- [ ] dashboard.blade.php temizlendi
- [ ] filter-panel.blade.php temizlendi
- [ ] header.blade.php temizlendi
- [ ] offer-download.blade.php temizlendi
- [ ] Tüm sayfalar görsel olarak aynı

---

### 🟡 SPRINT 2 (Hafta 3-4): Service Layer & Architecture

#### Gün 6-7: OfferService Oluşturma

**Adım 1: Service Dosyası**
```php
// app/Services/OfferService.php
<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\OfferItem;
use Illuminate\Support\Facades\DB;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 💰 OfferService - Teklif İş Mantığı Yönetimi
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @purpose Teklif CRUD operasyonları ve iş mantığı
 * @layer Service Layer (Business Logic)
 */
class OfferService
{
    /**
     * Yeni teklif oluştur (Atomic Transaction)
     */
    public function createOffer(array $data): Offer
    {
        return DB::transaction(function () use ($data) {
            // 1. Offer kaydı oluştur
            $offer = Offer::create([
                'customer_id' => $data['customer_id'],
                'number' => $this->generateOfferNumber(),
                'title' => $data['title'],
                'status' => 'DRAFT',
                'currency' => $data['currency'] ?? 'TRY',
                'vat_rate' => $data['vat_rate'] ?? 20,
                'discount_percentage' => $data['discount_percentage'] ?? 0,
            ]);
            
            // 2. Items ekle
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $offer->items()->create($item);
                }
            }
            
            // 3. Totals hesapla
            $this->calculateOfferTotals($offer);
            
            return $offer->fresh(['items', 'customer']);
        });
    }
    
    /**
     * Teklif güncelle
     */
    public function updateOffer(Offer $offer, array $data): Offer
    {
        return DB::transaction(function () use ($offer, $data) {
            $offer->update($data);
            
            // Items güncelle
            if (isset($data['items'])) {
                $offer->items()->delete();
                foreach ($data['items'] as $item) {
                    $offer->items()->create($item);
                }
            }
            
            $this->calculateOfferTotals($offer);
            
            return $offer->fresh(['items', 'customer']);
        });
    }
    
    /**
     * Teklif toplamlarını hesapla
     */
    private function calculateOfferTotals(Offer $offer): void
    {
        $originalAmount = $offer->items->sum(fn($item) => $item->price * $item->quantity);
        $discountAmount = $originalAmount * ($offer->discount_percentage / 100);
        $subtotal = $originalAmount - $discountAmount;
        $vatAmount = $subtotal * ($offer->vat_rate / 100);
        $totalAmount = $subtotal + $vatAmount;
        
        $offer->update([
            'original_amount' => $originalAmount,
            'discounted_amount' => $discountAmount,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
        ]);
    }
    
    /**
     * Teklif numarası oluştur
     */
    private function generateOfferNumber(): string
    {
        $year = now()->year;
        $lastOffer = Offer::whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $sequence = $lastOffer ? ((int) substr($lastOffer->number, -3)) + 1 : 1;
        
        return sprintf('TKL-%d-%03d', $year, $sequence);
    }
}
```

**Adım 2: Trait'te Kullanım**
```php
// app/Livewire/Customers/Offers/Traits/HasOfferActions.php
trait HasOfferActions
{
    public function save()
    {
        $this->validate();
        
        $offerService = app(OfferService::class);
        
        if ($this->offerId) {
            $offer = Offer::findOrFail($this->offerId);
            $offer = $offerService->updateOffer($offer, $this->all());
            $message = 'Teklif güncellendi';
        } else {
            $offer = $offerService->createOffer($this->all());
            $message = 'Teklif oluşturuldu';
        }
        
        $this->success($message);
        $this->redirect(route('customers.show', $offer->customer_id));
    }
}
```

**Checklist:**
- [ ] OfferService.php oluşturuldu (250 satır)
- [ ] createOffer() metodu çalışıyor
- [ ] updateOffer() metodu çalışıyor
- [ ] calculateOfferTotals() doğru hesaplıyor
- [ ] HasOfferActions trait'i slim hale geldi (360 → 200 satır)
- [ ] Test suite geçiyor

---

#### Gün 8-9: ProjectService Oluşturma

**Adım 1: Service Dosyası**
```php
// app/Services/ProjectService.php
<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectTask;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function createProject(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            $project = Project::create([
                'customer_id' => $data['customer_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => 'PLANNING',
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ]);
            
            // Phases ekle
            if (!empty($data['phases'])) {
                foreach ($data['phases'] as $phaseData) {
                    $this->createPhase($project, $phaseData);
                }
            }
            
            return $project->fresh(['phases', 'tasks', 'customer']);
        });
    }
    
    public function createPhase(Project $project, array $data): ProjectPhase
    {
        $phase = $project->phases()->create($data);
        
        // Tasks ekle
        if (!empty($data['tasks'])) {
            foreach ($data['tasks'] as $taskData) {
                $phase->tasks()->create($taskData);
            }
        }
        
        return $phase;
    }
}
```

**Checklist:**
- [ ] ProjectService.php oluşturuldu
- [ ] createProject() çalışıyor
- [ ] createPhase() çalışıyor
- [ ] Trait'ler slim hale geldi

---

#### Gün 10: Repository Pattern

**Adım 1: Base Repository**
```php
// app/Repositories/BaseRepository.php
<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository
{
    protected Model $model;
    
    public function find(string $id): ?Model
    {
        return $this->model->find($id);
    }
    
    public function all(): Collection
    {
        return $this->model->all();
    }
    
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }
    
    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->fresh();
    }
    
    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
```

**Adım 2: OfferRepository**
```php
// app/Repositories/OfferRepository.php
<?php

namespace App\Repositories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Collection;

class OfferRepository extends BaseRepository
{
    public function __construct(Offer $model)
    {
        $this->model = $model;
    }
    
    public function findWithRelations(string $id): ?Offer
    {
        return $this->model
            ->with(['customer', 'items', 'attachments', 'sections'])
            ->find($id);
    }
    
    public function getCustomerOffers(string $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->with('items')
            ->latest()
            ->get();
    }
    
    public function getOffersByStatus(string $status): Collection
    {
        return $this->model
            ->where('status', $status)
            ->with('customer')
            ->latest()
            ->get();
    }
}
```

**Checklist:**
- [ ] BaseRepository.php oluşturuldu
- [ ] OfferRepository.php oluşturuldu
- [ ] ProjectRepository.php oluşturuldu
- [ ] CustomerRepository.php oluşturuldu
- [ ] Service'lerde kullanılıyor

---

### 🟢 SPRINT 3 (Hafta 5-6): Renk & Volt API

#### Gün 11-12: Renk Standardizasyonu

**Adım 1: Toplu Değiştirme Script**
```bash
#!/bin/bash
# color-standardization.sh

echo "🎨 Renk standardizasyonu başlıyor..."

# gray → slate
find resources/views -name "*.blade.php" -type f -exec sed -i '' \
  -e 's/border-gray-200/border-slate-200/g' \
  -e 's/border-gray-300/border-slate-300/g' \
  -e 's/bg-gray-50/bg-slate-50/g' \
  -e 's/bg-gray-100/bg-slate-100/g' \
  -e 's/text-gray-500/text-slate-500/g' \
  -e 's/text-gray-600/text-slate-600/g' \
  -e 's/text-gray-700/text-slate-700/g' \
  -e 's/text-gray-900/text-slate-900/g' \
  {} \;

# zinc → slate (sadece non-sidebar dosyalar)
find resources/views -name "*.blade.php" -type f \
  ! -path "*/layouts/app/sidebar.blade.php" \
  ! -path "*/layouts/app/header.blade.php" \
  -exec sed -i '' \
  -e 's/bg-zinc-50/bg-slate-50/g' \
  -e 's/border-zinc-200/border-slate-200/g' \
  {} \;

echo "✅ Renk standardizasyonu tamamlandı"
```

**Checklist:**
- [ ] Script çalıştırıldı
- [ ] Tüm sayfalar kontrol edildi
- [ ] Görsel tutarlılık sağlandı

---

#### Gün 13-14: Volt Functional API Migration

**Adım 1: Örnek Migration**
```php
// ❌ ÖNCE (Class-based)
new class extends Component {
    public string $search = '';
    public string $status = 'all';
    
    public function updatedSearch() {
        $this->resetPage();
    }
    
    public function deleteSelected() {
        Offer::whereIn('id', $this->selected)->delete();
        $this->success('Silindi');
    }
}

// ✅ SONRA (Functional API)
use function Livewire\Volt\{state, computed, action, on};

state(['search' => '', 'status' => 'all', 'selected' => []]);

$offers = computed(function () {
    return Offer::query()
        ->when($this->search, fn($q) => $q->where('title', 'ilike', "%{$this->search}%"))
        ->when($this->status !== 'all', fn($q) => $q->where('status', $this->status))
        ->paginate();
});

$deleteSelected = action(function () {
    Offer::whereIn('id', $this->selected)->delete();
    $this->success('Silindi');
    $this->selected = [];
});

on(['search' => fn() => $this->resetPage()]);
```

**Checklist:**
- [ ] 5 dosya Functional API'ye geçirildi
- [ ] Tüm fonksiyonlar çalışıyor
- [ ] Performans aynı veya daha iyi

---

## 📊 İLERLEME TAKİP TABLOSU

| Sprint | Görev | Durum | Başlangıç | Bitiş | Sorumlu |
|--------|-------|-------|-----------|-------|---------|
| 1 | Projects Refactoring | ⏳ | - | - | - |
| 1 | PDF Template Refactoring | ⏳ | - | - | - |
| 1 | Inline Style Cleanup | ⏳ | - | - | - |
| 2 | OfferService | ⏳ | - | - | - |
| 2 | ProjectService | ⏳ | - | - | - |
| 2 | Repository Pattern | ⏳ | - | - | - |
| 3 | Color Standardization | ⏳ | - | - | - |
| 3 | Volt API Migration | ⏳ | - | - | - |

---

## 🎯 BAŞARI KRİTERLERİ

### Sprint 1 Tamamlanma Kriterleri
- [ ] 400+ satır dosya sayısı: 5 → 0
- [ ] Inline style kullanımı: 50+ → 0
- [ ] Test coverage: %85 → %85 (korundu)
- [ ] Tüm testler geçiyor

### Sprint 2 Tamamlanma Kriterleri
- [ ] Service dosyası sayısı: 2 → 5
- [ ] Repository dosyası sayısı: 3 → 6
- [ ] Trait ortalama satır: 250 → 180
- [ ] İş mantığı separation: %60 → %90

### Sprint 3 Tamamlanma Kriterleri
- [ ] Renk tutarlılığı: %70 → %100
- [ ] Volt Functional API: %0 → %30
- [ ] Genel mimari skor: 72/100 → 90/100

---

## 🚀 HIZLI BAŞLANGIÇ

```bash
# 1. Audit raporunu oku
cat docs/audit/agency-v10-strategic-architecture-audit.md

# 2. Sprint 1'i başlat
git checkout -b refactor/sprint-1-critical-files

# 3. Projects refactoring
./scripts/refactor-projects.sh

# 4. Testleri çalıştır
php artisan test

# 5. Commit
git add .
git commit -m "refactor: Projects modülü Constitution V11 uyumlu hale getirildi"

# 6. Sonraki sprint
git checkout -b refactor/sprint-2-service-layer
```

---

**Plan Sonu**  
*Güncellenme: 16 Ocak 2026*
