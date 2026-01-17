<?php
/**
 * ✅ REPORTS-TAB COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Class-based)
 *
 * Proje raporlarının merkezi listesi.
 * ---------------------------------------------------------
 */

use App\Models\ProjectReport;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public string $search = '';

    // Selection
    public array $selected = [];

    public bool $selectAll = false;

    public ?string $project_id = null;

    public ?string $task_id = null;

    public function updatedSelectAll($value)
    {
        $this->selected = $value
            ? $this->reports->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        ProjectReport::whereIn('id', $this->selected)->delete();
        $this->success('İşlem Başarılı', count($this->selected) . ' rapor silindi.');
        $this->selected = [];
        $this->selectAll = false;
    }

    public function getReportsProperty()
    {
        return ProjectReport::query()
            ->with(['customer', 'project', 'service', 'creator', 'task'])
            ->when($this->project_id, fn($q) => $q->where('project_id', $this->project_id))
            ->when($this->task_id, fn($q) => $q->where('task_id', $this->task_id))
            ->when($this->search, fn($q) => $q->where('content', 'LIKE', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }

    public function resetFilters()
    {
        $this->search = '';
    }
}; ?>

<div>
    {{-- Filter Panel --}}
    <div class="theme-card p-4 mb-6 shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Bulk Delete Button --}}
            @if(count($selected) > 0)
                <button wire:click="deleteSelected"
                    wire:confirm="Seçili {{ count($selected) }} raporu silmek istediğinize emin misiniz?"
                    class="theme-btn-delete px-3 py-2 flex items-center gap-2 text-sm">
                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                    <span>{{ count($selected) }} Sil</span>
                </button>
            @endif

            {{-- Search --}}
            <div class="flex-grow max-w-[10rem] !bg-white rounded-lg">
                <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Rapor ara..."
                    icon="o-magnifying-glass" class="input-sm !bg-white !border-gray-200"
                    style="background-color: white !important;" />
            </div>

            {{-- New Report Button --}}
            <a href="{{ route('projects.reports.create', ['project' => $project_id]) }}"
                class="theme-btn-save px-4 py-2 flex items-center gap-2 ml-auto">
                <x-mary-icon name="o-plus" class="w-5 h-5" />
                <span>Yeni Rapor</span>
            </a>
        </div>
    </div>

    {{-- Reports Table --}}
    <div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-skin-light">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="checkbox checkbox-xs rounded border-slate-300">
                        </th>
                        <th class="px-6 py-3 font-semibold text-skin-base">Tarih</th>
                        <th class="px-6 py-3 font-semibold text-skin-base">Raporu Giren</th>
                        <th class="px-6 py-3 font-semibold text-skin-base">Müşteri</th>
                        <th class="px-6 py-3 font-semibold text-skin-base">Hizmet/Proje</th>
                        <th class="px-6 py-3 font-semibold text-skin-base">Süre</th>
                        <th class="px-6 py-3 font-semibold text-skin-base">Rapor Özeti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($this->reports as $report)
                        <tr wire:key="report-{{ $report->id }}" class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3" onclick="event.stopPropagation()">
                                <input type="checkbox" wire:model.live="selected" value="{{ $report->id }}"
                                    class="checkbox checkbox-xs rounded border-slate-300">
                            </td>
                            <td class="px-6 py-3 cursor-pointer"
                                onclick="window.location.href='{{ route('projects.reports.edit', $report->id) }}'">
                                <div class="text-skin-base font-medium">
                                    {{ $report->created_at->format('d.m.Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-3 cursor-pointer"
                                onclick="window.location.href='{{ route('projects.reports.edit', $report->id) }}'">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 border border-slate-200">
                                        {{ substr($report->creator?->name ?? 'S', 0, 1) }}
                                    </div>
                                    <span
                                        class="text-sm font-medium text-slate-700">{{ $report->creator?->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 cursor-pointer"
                                onclick="window.location.href='{{ route('projects.reports.edit', $report->id) }}'">
                                <div class="text-skin-base">
                                    {{ $report->customer?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-3 cursor-pointer"
                                onclick="window.location.href='{{ route('projects.reports.edit', $report->id) }}'">
                                <div
                                    class="text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 inline-block mb-1">
                                    {{ $report->report_type === 'PROJECT' ? 'Yapım Projesi' : 'Destek Hizmeti' }}
                                </div>
                                <div class="text-sm font-medium">
                                    {{ $report->report_type === 'PROJECT' ? ($report->project?->name ?? '-') : ($report->service?->service_name ?? '-') }}
                                </div>
                            </td>
                            <td class="px-6 py-3 cursor-pointer"
                                onclick="window.location.href='{{ route('projects.reports.edit', $report->id) }}'">
                                <div class="text-skin-base font-bold">
                                    {{ $report->hours }}s {{ sprintf('%02d', $report->minutes) }}dk
                                </div>
                            </td>
                            <td class="px-6 py-3 cursor-pointer"
                                onclick="window.location.href='{{ route('projects.reports.edit', $report->id) }}'">
                                <div class="text-xs text-skin-muted truncate max-w-xs">
                                    {{ Str::limit($report->content, 80) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-clipboard-document-check" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">Henüz Rapor Yok</div>
                                    <div class="text-xs opacity-60 mt-1">Raporlar oluşturup listelemeye başlayın.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>