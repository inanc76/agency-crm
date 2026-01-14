{{-- 
    📅 Linear-Style Date Range Picker Component
    -------------------------------------------
    Usage: <x-date-range-picker 
               wire:model.live="start_date" 
               wire:model-end.live="target_end_date" 
               :disabled="$isViewMode"
           />
--}}

@props([
    'startDate' => null,
    'endDate' => null,
    'disabled' => false,
])

<div 
    x-data="dateRangePicker({ 
        startDate: @js($startDate), 
        endDate: @js($endDate) 
    })"
    x-on:date-range-updated.window="
        $wire.set('start_date', $event.detail.start);
        $wire.set('target_end_date', $event.detail.end);
    "
    class="relative"
>
    {{-- Display Header (Linear Style) --}}
    <button 
        type="button"
        @click="open()"
        class="input flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 w-full text-left"
        @if($disabled) disabled @endif
        :class="{ 'opacity-60 cursor-not-allowed': {{ $disabled ? 'true' : 'false' }} }"
    >
        {{-- Calendar Icon --}}
        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--brand-primary, #3b82f6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        
        {{-- Date Display --}}
        <span 
            x-text="displayText" 
            class="text-sm font-medium flex-1 text-skin-base"
        ></span>
        
        {{-- Arrow Icon --}}
        <svg class="w-4 h-4 flex-shrink-0 text-skin-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Hidden Input for Flatpickr --}}
    <input 
        type="text" 
        x-ref="calendar" 
        class="sr-only"
        @if($disabled) disabled @endif
    />
    
    {{-- Hidden inputs for form submission --}}
    <input type="hidden" name="start_date" :value="startDate" />
    <input type="hidden" name="target_end_date" :value="endDate" />
</div>

{{-- Flatpickr Custom Styles --}}
@once
@push('styles')
<style>
    /* Linear-style Calendar Theme */
    .flatpickr-calendar {
        background: var(--card-bg, #1f2937) !important;
        border: 1px solid var(--card-border-color, #374151) !important;
        border-radius: var(--border-radius-lg, 12px) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    }
    
    .flatpickr-months {
        background: transparent !important;
    }
    
    .flatpickr-month {
        color: var(--color-text-heading, #f9fafb) !important;
        fill: var(--color-text-heading, #f9fafb) !important;
    }
    
    .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-current-month input.cur-year {
        color: var(--color-text-heading, #f9fafb) !important;
        font-weight: 600 !important;
    }
    
    .flatpickr-weekdays {
        background: transparent !important;
    }
    
    .flatpickr-weekday {
        color: var(--color-text-muted, #9ca3af) !important;
        font-weight: 500 !important;
        font-size: 11px !important;
    }
    
    .flatpickr-day {
        color: var(--color-text-base, #e5e7eb) !important;
        border-radius: 50% !important;
    }
    
    .flatpickr-day:hover {
        background: var(--primary-color, #3b82f6) !important;
        border-color: var(--primary-color, #3b82f6) !important;
        color: white !important;
    }
    
    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background: var(--primary-color, #3b82f6) !important;
        border-color: var(--primary-color, #3b82f6) !important;
        color: white !important;
    }
    
    .flatpickr-day.inRange {
        background: rgba(59, 130, 246, 0.3) !important;
        border-color: transparent !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }
    
    .flatpickr-day.startRange {
        border-radius: 50% 0 0 50% !important;
    }
    
    .flatpickr-day.endRange {
        border-radius: 0 50% 50% 0 !important;
    }
    
    .flatpickr-day.startRange.endRange {
        border-radius: 50% !important;
    }
    
    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: var(--color-text-muted, #6b7280) !important;
        opacity: 0.5;
    }
    
    .flatpickr-prev-month,
    .flatpickr-next-month {
        fill: var(--color-text-heading, #f9fafb) !important;
    }
    
    .flatpickr-prev-month:hover svg,
    .flatpickr-next-month:hover svg {
        fill: var(--primary-color, #3b82f6) !important;
    }
</style>
@endpush
@endonce
