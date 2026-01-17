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
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--card-border)]">
                        <th class="text-left py-2 px-2 font-medium opacity-60">Proje Kodu</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">Proje Adı</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Başlangıç</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Hedef Bitiş</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredProjects as $project)
                        @php
                            $statusLabel = $project->status->display_label ?? 'Taslak';
                            $badgeClass = $project->status->color_class ?? 'bg-slate-50 text-slate-600 border-slate-100';
                        @endphp
                        <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] cursor-pointer transition-colors"
                            onclick="window.location.href='/dashboard/projects/{{ $project->id }}'">
                            <td class="py-3 px-2 font-mono text-xs">{{ $project->project_id_code }}</td>
                            <td class="py-3 px-2 font-medium">{{ $project->name }}</td>
                            <td class="py-3 px-2 text-center opacity-70">
                                {{ $project->start_date ? $project->start_date->format('d.m.Y') : '-' }}
                            </td>
                            <td class="py-3 px-2 text-center opacity-70">
                                {{ $project->target_end_date ? $project->target_end_date->format('d.m.Y') : '-' }}
                            </td>
                            <td class="py-3 px-2 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-medium border {{ $badgeClass }}">
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