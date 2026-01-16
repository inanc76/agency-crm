<?php

use App\Models\ProjectTask;
use App\Models\ProjectTaskItem;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public ProjectTask $task;

    public string $newItemContent = '';

    // Computed properties for efficient rendering
    public function with(): array
    {
        return [
            'activeItems' => $this->task->items()->where('is_completed', false)->get(),
            'completedItems' => $this->task->items()->where('is_completed', true)->get(),
        ];
    }

    public function add(): void
    {
        $this->validate([
            'newItemContent' => 'required|string|min:2|max:255',
        ]);

        $this->task->items()->create([
            'content' => $this->newItemContent,
            'is_completed' => false,
        ]);

        $this->newItemContent = '';
        $this->dispatch('item-added');
    }

    public function toggle(string $itemId): void
    {
        $item = ProjectTaskItem::find($itemId);

        if ($item && $item->project_task_id === $this->task->id) {
            $item->update(['is_completed' => !$item->is_completed]);
        }
    }

    public function delete(string $itemId): void
    {
        $item = ProjectTaskItem::find($itemId);

        if ($item && $item->project_task_id === $this->task->id) {
            $item->delete();
            $this->warning('Öğe silindi.');
        }
    }
}; ?>

<div class="h-full flex flex-col">
    {{-- Header --}}
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-[var(--color-text-heading)] flex items-center gap-2">
            <x-mary-icon name="o-list-bullet" class="w-5 h-5" />
            Öğeler
        </h3>
    </div>

    {{-- Add New Item --}}
    <div class="mb-6">
        <form wire:submit="add" class="relative">
            <x-mary-icon name="o-plus" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input type="text" wire:model="newItemContent" placeholder="Liste öğesi..."
                class="w-full bg-transparent border-0 border-b border-slate-200 focus:border-slate-200 focus:ring-0 pl-10 text-sm placeholder-slate-400 text-slate-700 p-2"
                autofocus>
        </form>
    </div>

    {{-- Active Items --}}
    <div class="flex-grow space-y-1 overflow-y-auto min-h-0 mb-4">
        @foreach($activeItems as $item)
            <div wire:key="item-{{ $item->id }}"
                class="group flex items-center gap-3 py-2 px-2 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer">
                {{-- Custom Checkbox --}}
                <div wire:click="toggle('{{ $item->id }}')"
                    class="w-5 h-5 rounded border-2 border-slate-300 flex items-center justify-center text-white cursor-pointer hover:border-slate-400 transition-colors">
                </div>

                <span class="text-sm text-slate-700 flex-grow">{{ $item->content }}</span>

                <button wire:click="delete('{{ $item->id }}')"
                    class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-500 transition-all p-1">
                    <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                </button>
            </div>
        @endforeach
    </div>

    {{-- Completed Items Section --}}
    @if($completedItems->count() > 0)
        <div class="mt-auto pt-4 border-t border-slate-100">
            <div x-data="{ expanded: true }">
                <button @click="expanded = !expanded"
                    class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2 hover:text-slate-700">
                    <x-mary-icon name="o-chevron-down" class="w-3 h-3 transition-transform duration-200"
                        x-bind:class="expanded ? '' : '-rotate-90'" />
                    Tamamlanmış {{ $completedItems->count() }} öğe
                </button>

                <div x-show="expanded" x-collapse class="space-y-1">
                    @foreach($completedItems as $item)
                        <div wire:key="completed-{{ $item->id }}"
                            class="group flex items-center gap-3 py-1 px-2 opacity-60 hover:opacity-100 transition-opacity">
                            {{-- Checked Box --}}
                            <div wire:click="toggle('{{ $item->id }}')"
                                class="w-5 h-5 rounded bg-primary border-primary flex items-center justify-center text-white cursor-pointer">
                                <x-mary-icon name="o-check" class="w-3.5 h-3.5" />
                            </div>

                            <span
                                class="text-sm text-slate-500 line-through decoration-slate-400 flex-grow">{{ $item->content }}</span>

                            <button wire:click="delete('{{ $item->id }}')"
                                class="opacity-0 group-hover:opacity-100 text-slate-300 hover:text-red-500 transition-all p-1">
                                <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>