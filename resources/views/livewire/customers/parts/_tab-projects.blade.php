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

    <div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" disabled
                                class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                        </th>
                        <th>Proje Kodu</th>
                        <th>Proje Adı</th>
                        <th class="text-center">Başlangıç</th>
                        <th class="text-center">Hedef Bitiş</th>
                        <th class="text-center">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredProjects as $project)
                        @php
                            $statusLabel = $project->status->display_label ?? 'Taslak';
                            $statusClass = $project->status->color_class ?? 'bg-slate-100 text-slate-500';
                        @endphp
                        <tr onclick="window.location.href='/dashboard/projects/{{ $project->id }}'">
                            <td onclick="event.stopPropagation()">
                                <input type="checkbox" disabled
                                    class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                            </td>
                            <td class="font-mono text-[10px] opacity-50">{{ $project->project_id_code }}</td>
                            <td class="item-name">{{ $project->name }}</td>
                            <td class="text-center opacity-70 text-xs font-mono">
                                {{ $project->start_date ? $project->start_date->format('d.m.Y') : '-' }}
                            </td>
                            <td class="text-center opacity-70 text-xs font-mono">
                                {{ $project->target_end_date ? $project->target_end_date->format('d.m.Y') : '-' }}
                            </td>
                            <td class="text-center">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-folder" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">
                                        {{ $projectsStatusFilter ? 'Seçilen duruma uygun proje bulunamadı.' : 'Bu müşteriye ait proje bulunmuyor.' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-skin-light flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs text-skin-muted">Göster:</span>
                <div class="px-2 py-1 border border-skin-light rounded text-xs bg-white">25</div>
            </div>

            <div class="text-[10px] text-skin-muted font-mono">
                {{ $filteredProjects->count() }} kayıt listelendi
            </div>
        </div>
    </div>
</div>