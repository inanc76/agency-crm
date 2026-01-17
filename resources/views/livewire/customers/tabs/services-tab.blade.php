<?php

use App\Services\ReferenceDataService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Service;
use App\Models\ReferenceItem;
use Illuminate\Database\Eloquent\Builder;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination;
    use Toast;

    public string $search = '';
    public string $letter = '';
    public string $categoryFilter = 'all';
    public string $statusFilter = 'all';

    // Pagination & Selection
    public int $perPage = 25;
    public array $selected = [];
    public bool $selectAll = false;

    // Reset pagination when filtering
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedLetter()
    {
        $this->resetPage();
    }
    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        Service::whereIn('id', $this->selected)->delete();

        $this->success('İşlem Başarılı', count($this->selected) . ' hizmet silindi.');
        $this->selected = [];
        $this->selectAll = false;
    }

    private function getQuery(): Builder
    {
        return Service::query()
            ->with(['customer', 'asset', 'status_item', 'category_item'])
            ->when($this->search, function (Builder $query) {
                $query->where('service_name', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'ilike', '%' . $this->search . '%');
                    });
            })
            ->when($this->letter, function (Builder $query) {
                if ($this->letter === '0-9') {
                    $query->whereRaw("service_name ~ '^[0-9]'")
                        ->orWhereHas('customer', function ($q) {
                            $q->whereRaw("name ~ '^[0-9]'");
                        });
                } else {
                    $query->where('service_name', 'ilike', $this->letter . '%')
                        ->orWhereHas('customer', function ($q) {
                            $q->where('name', 'ilike', $this->letter . '%');
                        });
                }
            })
            ->when($this->categoryFilter && $this->categoryFilter !== 'all', function (Builder $query) {
                $query->where('service_category', $this->categoryFilter);
            })
            ->when($this->statusFilter && $this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('service_name');
    }

    public function with(ReferenceDataService $service): array
    {
        $categoryOptions = ReferenceItem::where('category_key', 'SERVICE_CATEGORY')->where('is_active', true)->orderBy('sort_order')->get();
        $statusOptions = ReferenceItem::where('category_key', 'SERVICE_STATUS')->where('is_active', true)->orderBy('sort_order')->get();

        return [
            'services' => $this->getQuery()->paginate($this->perPage),
            'categoryOptions' => $categoryOptions,
            'statusOptions' => $statusOptions,
        ];
    }
}; ?>

{{--
SECTION: Services Tab Main Container
Mimarın Notu: Bu ana sekme Service modeli ile konuşur ve HasCustomerActions trait'ini kullanır.
İş Mantığı Şerhi: ReferenceDataService ile kategori ve durum verilerini alır, WithPagination trait'i ile sayfalama
yapar.
Mühür Koruması: Tüm değişkenler explicit olarak partials'a aktarılır.
--}}
<div>
    {{-- SECTION: Summary & Filters - Özet kartları ve filtreleme alanı --}}
    @include('livewire.customers.tabs.partials._services-summary', [
        'selected' => $selected,
        'services' => $services,
        'categoryOptions' => $categoryOptions,
        'statusOptions' => $statusOptions,
        'search' => $search,
        'letter' => $letter,
        'categoryFilter' => $categoryFilter,
        'statusFilter' => $statusFilter
    ])

    {{-- SECTION: Data Table - Hizmetlerin listelendiği tablo --}}
    @include('livewire.customers.tabs.partials._services-list', [
        'services' => $services,
        'selected' => $selected,
        'selectAll' => $selectAll,
        'perPage' => $perPage
    ])
</div>