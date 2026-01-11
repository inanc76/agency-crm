<?php

use App\Services\ReferenceDataService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Contact;
use App\Models\ReferenceItem;
use Illuminate\Database\Eloquent\Builder;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination;
    use Toast;

    public string $customerId = '';
    public string $search = '';
    public string $letter = '';
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

        Contact::whereIn('id', $this->selected)->delete();

        $this->dispatch('contacts-updated');

        $this->success('İşlem Başarılı', count($this->selected) . ' kişi silindi.');
        $this->selected = [];
        $this->selectAll = false;
    }

    private function getQuery(): Builder
    {
        return Contact::query()
            ->with('customer')
            ->when($this->customerId, fn($q) => $q->where('customer_id', $this->customerId))
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->letter, function (Builder $query) {
                if ($this->letter === '0-9') {
                    $query->whereRaw("name ~ '^[0-9]'")
                        ->orWhereHas('customer', function ($q) {
                            $q->whereRaw("name ~ '^[0-9]'");
                        });
                } else {
                    $query->where('name', 'like', $this->letter . '%')
                        ->orWhereHas('customer', function ($q) {
                            $q->where('name', 'like', $this->letter . '%');
                        });
                }
            })
            ->when($this->statusFilter && $this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('name');
    }

    public function with(ReferenceDataService $service): array
    {
        $statusOptions = ReferenceItem::where('category_key', 'CONTACT_STATUS')->where('is_active', true)->orderBy('sort_order')->get();
        // Prepare map with both label and color
        $statusMap = [];
        foreach ($statusOptions as $opt) {
            $colorId = $opt->metadata['color'] ?? 'gray';
            $statusMap[$opt->key] = [
                'label' => $opt->display_label,
                'class' => $service->getColorClasses($colorId)
            ];
        }

        return [
            'contacts' => $this->getQuery()->paginate($this->perPage),
            'statusOptions' => $statusOptions,
            'statusMap' => $statusMap,
        ];
    }
}; ?>

{{-- 
    SECTION: Contacts Tab Main Container
    Mimarın Notu: Bu ana sekme Contact modeli ile konuşur ve HasCustomerActions trait'ini kullanır.
    İş Mantığı Şerhi: ReferenceDataService ile durum verilerini alır, WithPagination trait'i ile sayfalama yapar.
    Mühür Koruması: Tüm değişkenler explicit olarak partials'a aktarılır.
--}}
<div>
    {{-- SECTION: Actions & Filters - Yeni kişi ekleme ve toplu işlem butonları --}}
    @include('livewire.customers.tabs.partials._contacts-actions', [
        'selected' => $selected,
        'contacts' => $contacts,
        'statusOptions' => $statusOptions,
        'search' => $search,
        'letter' => $letter,
        'statusFilter' => $statusFilter
    ])

    {{-- SECTION: Data Grid - Kişi kartları veya listesi --}}
    @include('livewire.customers.tabs.partials._contacts-grid', [
        'contacts' => $contacts,
        'selected' => $selected,
        'selectAll' => $selectAll,
        'statusMap' => $statusMap,
        'perPage' => $perPage
    ])
</div>