<?php
/**
 * ✅ NOTES-TAB COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Class-based)
 * ---------------------------------------------------------
 */

use Livewire\Volt\Component;

new class extends Component {
    public ?string $project_id = null;
    public ?string $task_id = null;
}; ?>

<div class="theme-card p-12 text-center">
    <div class="flex flex-col items-center justify-center">
        <x-mary-icon name="o-pencil-square" class="w-16 h-16 opacity-20 mb-4" />
        <div class="text-xl font-bold text-skin-heading">Notlar Sekmesi</div>
        <p class="text-sm text-skin-base opacity-60 mt-2 max-w-sm mx-auto">
            Geliştirme aşamasındadır. Yakında burada notlarınızı yönetebileceksiniz.
        </p>
    </div>
</div>