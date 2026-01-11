<div class="p-4 border-t border-[var(--card-border)]">
    <button wire:click="toggleCollapsed"
        class="w-full flex items-center justify-center p-2 rounded-lg hover:bg-[var(--dropdown-hover-bg)] transition-colors">
        <span class="text-lg">
            {{ $collapsed ? '▶' : '◀' }}
        </span>
    </button>
</div>