{{-- 🏗️ Projeler Sekmesi (Bağımlılık: $relatedProjects, $projectsStatusFilter) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Projeler</h2>
        <select wire:model.live="projectsStatusFilter" class="select select-xs bg-white border-slate-200 min-w-[150px]">
            <option value="">Tüm Durumlar</option>
            @foreach($projectStatuses as $status)
                <option value="{{ $status['id'] }}">{{ $status['display_label'] }}</option>
            @endforeach
        </select>
    </div>

    @php
        $filteredProjects = collect($relatedProjects)->when($projectsStatusFilter, function ($collection) {
            return $collection->where('status_id', $this->projectsStatusFilter);
        });
    @endphp

    @if($filteredProjects->count() > 0)
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th>Proje Kodu</th>
                        <th>Proje Adı</th>
                        <th class="text-center">Başlangıç</th>
                        <th class="text-center">Hedef Bitiş</th>
                        <th class="text-center">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredProjects as $project)
                        @php
                            $statusLabel = $project->status->display_label ?? 'Taslak';
                            $statusClass = $project->status->color_class ?? 'bg-slate-100 text-slate-500';
                        @endphp
                        <tr onclick="window.location.href='/dashboard/projects/{{ $project->id }}'">
                            <td class="font-mono text-[10px] opacity-50">{{ $project->project_id_code }}</td>
                            <td class="item-name">{{ $project->name }}</td>
                            <td class="text-center opacity-70">
                                {{ $project->start_date ? $project->start_date->format('d.m.Y') : '-' }}
                            </td>
                            <td class="text-center opacity-70">
                                {{ $project->target_end_date ? $project->target_end_date->format('d.m.Y') : '-' }}
                            </td>
                            <td class="text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-[var(--color-text-muted)]">
            <x-mary-icon name="o-folder" class="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p class="text-sm">
                {{ $projectsStatusFilter ? 'Seçilen duruma uygun proje bulunamadı.' : 'Bu müşteriye ait proje bulunmuyor.' }}
            </p>
        </div>
    @endif
</div>