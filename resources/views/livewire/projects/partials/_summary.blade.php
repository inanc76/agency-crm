<div class="col-span-4 flex flex-col gap-6" wire:key="customer-preview-{{ $customer_id }}">
    {{-- Customer Logo Card --}}
    <div class="theme-card p-6 shadow-sm sticky top-6">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">Proje Özeti</h3>

        @if($selectedCustomer)
            <div class="flex items-center gap-4 mb-4">
                @if($selectedCustomer["logo_url"])
                    <img src="{{ str_contains($selectedCustomer["logo_url"], "/storage/") ? $selectedCustomer["logo_url"] : asset("storage" . $selectedCustomer["logo_url"]) }}" alt="{{ $selectedCustomer["name"] }}" class="w-12 h-12 rounded-lg object-cover shadow-sm bg-white" />
                @else
                    <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <x-mary-icon name="o-building-office" class="w-6 h-6 text-slate-400" />
                    </div>
                @endif
                <div class="font-bold text-[var(--color-text-heading)]">{{ $selectedCustomer["name"] }}
                </div>
            </div>

            {{-- Deadline Logic --}}
            {{-- Phases Deadline Logic --}}
            @if(!empty($phases))
                @foreach($phases as $phase)
                    @if(!empty($phase['end_date']))
                        @php
                            $pDeadline = \Carbon\Carbon::parse($phase['end_date'])->startOfDay();
                            $today = \Carbon\Carbon::now()->startOfDay();
                            $pDiff = $today->diffInDays($pDeadline, false);
                            
                            $pBusinessDays = $today->diffInDaysFiltered(function(\Carbon\Carbon $date) {
                                return !$date->isWeekend();
                            }, $pDeadline);

                            $pColorClass = 'text-green-600';
                            $pText = abs($pDiff) . ' Gün var (' . $pBusinessDays . ' İş Günü)';
                            
                            if ($pDiff < 0) {
                                $pColorClass = 'text-red-500';
                                $pText = abs($pDiff) . ' Gün geçti (' . $pBusinessDays . ' İş Günü)';
                            } elseif ($pDiff <= 7) {
                                $pColorClass = 'text-orange-500';
                                $pText = abs($pDiff) . ' Gün var (' . $pBusinessDays . ' İş Günü)';
                            }
                        @endphp
                        <div class="flex items-center justify-between text-xs py-1.5 border-t border-slate-50">
                            <div class="flex items-center gap-2 overflow-hidden mr-2">
                                @php
                                    $words = explode(' ', $phase['name']);
                                    $initials = mb_substr($words[0] ?? '', 0, 1);
                                    if (count($words) > 1) {
                                        $initials .= mb_substr($words[1] ?? '', 0, 1);
                                    }
                                    $initials = mb_strtoupper($initials);
                                @endphp
                                <div class="w-5 h-5 rounded flex items-center justify-center font-bold text-white text-[9px] flex-shrink-0"
                                     style="background-color: {{ $phase['color'] ?? 'var(--primary-color)' }};">
                                    {{ $initials }}
                                </div>
                                <span class="text-slate-500 font-medium truncate">{{ $phase['name'] }}:</span>
                            </div>
                            <span class="{{ $pColorClass }} whitespace-nowrap">{{ $pText }}</span>
                        </div>
                    @endif
                @endforeach
            @endif

            {{-- Project Deadline Logic --}}
            @if($target_end_date)
                @php
                    $deadline = \Carbon\Carbon::parse($target_end_date)->startOfDay();
                    $isFrozen = !empty($completed_at);
                    $referenceDate = $isFrozen ? \Carbon\Carbon::parse($completed_at)->startOfDay() : \Carbon\Carbon::now()->startOfDay();
                    
                    $diff = $referenceDate->diffInDays($deadline, false);
                    
                    $businessDays = $referenceDate->diffInDaysFiltered(function(\Carbon\Carbon $date) {
                        return !$date->isWeekend();
                    }, $deadline);

                    // Default: Active & Future
                    $colorClass = 'text-green-600';
                    $text = abs($diff) . ' Gün var (' . $businessDays . ' İş Günü)';
                    
                    if ($isFrozen) {
                        if ($diff >= 0) {
                            $text = abs($diff) . ' Gün Erken Bitti (' . $businessDays . ' İş Günü)';
                            $colorClass = 'text-green-600';
                        } else {
                            $text = abs($diff) . ' Gün Gecikmeli Bitti (' . $businessDays . ' İş Günü)';
                            $colorClass = 'text-red-500';
                        }
                    } else {
                        if ($diff < 0) {
                            $colorClass = 'text-red-500';
                            $text = abs($diff) . ' Gün geçti (' . $businessDays . ' İş Günü)';
                        } elseif ($diff <= 7) {
                            $colorClass = 'text-orange-500';
                            $text = abs($diff) . ' Gün var (' . $businessDays . ' İş Günü)';
                        }
                    }
                @endphp
                <div class="flex items-center justify-between text-base font-bold py-2 border-t-2 border-slate-100 mt-2">
                    <span class="text-slate-700">Proje Bitiş:</span>
                    <span class="{{ $colorClass }}">{{ $text }}</span>
                </div>
                
                @php
                    $totalAssignedHours = 0;
                    foreach($phases as $p) {
                        foreach($p['modules'] ?? [] as $m) {
                            $totalAssignedHours += (int)($m['estimated_hours'] ?? 0);
                        }
                    }
                    
                    $spentTime = $this->spentTime; // Accessed via Computed Property from Parent
                    $spentMinutes = $spentTime['total_minutes'];
                    $assignedMinutes = $totalAssignedHours * 60;
                    $remainingMinutes = $assignedMinutes - $spentMinutes;
                    
                    $remainingHours = floor(abs($remainingMinutes) / 60);
                    $remainingMins = abs($remainingMinutes) % 60;
                    $isNegative = $remainingMinutes < 0;
                @endphp
                
                <div class="flex items-center justify-between text-base font-bold py-2 border-t-2 border-slate-100 mt-2">
                    <span class="text-slate-700">Atanan Saatler:</span>
                    <span class="text-slate-900">{{ $totalAssignedHours > 0 ? $totalAssignedHours . ' Saat' : '-' }}</span>
                </div>

                <div class="flex items-center justify-between text-base font-bold py-2 border-t border-slate-100">
                    <span class="text-slate-700">Harcanan Saatler:</span>
                    <span class="text-red-500">{{ $spentTime['hours'] }}:{{ sprintf('%02d', $spentTime['minutes']) }} Saat</span>
                </div>

                <div class="flex items-center justify-between text-base font-bold py-2 border-t border-slate-100">
                    <span class="text-slate-700">Kalan Saatler:</span>
                    <span class="{{ $isNegative ? 'text-red-500' : 'text-green-600' }}">
                        {{ $isNegative ? '-' : '' }}{{ $remainingHours }}:{{ sprintf('%02d', $remainingMins) }} Saat
                    </span>
                </div>
            @else
                    <div class="flex items-center justify-between text-base font-bold py-2 border-t-2 border-slate-100 mt-2">
                    <span class="text-slate-700">Proje Bitiş:</span>
                    <span class="text-slate-400">-</span>
                </div>
            @endif                    @else
            <div class="text-center py-8">
                <x-mary-icon name="o-building-office-2" class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                <p class="text-sm text-slate-500">Müşteri seçilmedi</p>
            </div>
        @endif
    </div>

</div>
