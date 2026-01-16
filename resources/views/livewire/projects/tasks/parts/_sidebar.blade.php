<div class="col-span-4 flex flex-col gap-6">
    <div class="theme-card p-6 shadow-sm sticky top-6">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">Görev Özeti</h3>

        @if($customer_id)
            @php
                $selectedCustomer = collect($customers)->firstWhere('id', $customer_id);
                $selectedProject = collect($projects)->firstWhere('id', $project_id);
                $assignedNames = collect($users)
                    ->whereIn('id', is_array($assigned_to) ? $assigned_to : [$assigned_to])
                    ->pluck('name')
                    ->join(', ');
                $selectedPriority = collect($priorities)->firstWhere('id', $priority_id);
                $selectedStatus = collect($statuses)->firstWhere('id', $status_id);
            @endphp

            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Tarih</span>
                    <span
                        class="font-medium text-slate-700">{{ $task?->created_at ? $task->created_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Atanan</span>
                    <span class="font-medium text-slate-700 text-right">{{ $assignedNames ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Öncelik</span>
                    @if($selectedPriority)
                        @php
                            $pColorId = $selectedPriority['metadata']['color'] ?? 'gray';
                            $pClasses = $this->getColorClasses($pColorId);
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $pClasses }}">
                            {{ $selectedPriority['display_label'] }}
                        </span>
                    @else
                        <span class="text-slate-700">-</span>
                    @endif
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-slate-500">Durum</span>
                    @if($selectedStatus)
                        @php
                            $sColorId = $selectedStatus['metadata']['color'] ?? 'gray';
                            $sClasses = $this->getColorClasses($sColorId);
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $sClasses }}">
                            {{ $selectedStatus['display_label'] }}
                        </span>
                    @else
                        <span class="text-slate-700">-</span>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <x-mary-icon name="o-clipboard-document-list" class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                <p class="text-sm text-slate-500">Müşteri seçilmedi</p>
            </div>
        @endif
    </div>
</div>