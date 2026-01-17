<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\MailTemplate;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')]
    class extends Component {
    use WithPagination;

    public $search = '';

    public function with(): array
    {
        return [
            'templates' => MailTemplate::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('subject', 'like', "%{$this->search}%"))
                ->orderBy('is_system', 'desc') // System templates first? Or just created_at
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ];
    }

    public function delete(string $id): void
    {
        $template = MailTemplate::findOrFail($id);
        if ($template->is_system) {
            // Prevent deletion of system templates?
            // For now allow it or restrict? User requirement 3 says system emails listed. usually shouldn't delete system templates.
            // I'll add a check.
            $this->dispatch('toast', type: 'error', title: 'Hata', description: 'Sistem şablonları silinemez.');
            return;
        }
        $template->delete();
        $this->dispatch('toast', type: 'success', title: 'Başarılı', description: 'Şablon silindi.');
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/settings"
            class="inline-flex items-center gap-2 text-[var(--color-text-base)] hover:text-[var(--color-text-heading)] mb-6 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Ayarlara Dön</span>
        </a>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-[var(--color-text-heading)]">Mail Şablonları</h1>
                <p class="text-sm opacity-60 mt-1">Sistem ve özel mail şablonlarını yönetin</p>
            </div>
            <a href="{{ route('settings.mail-templates.create') }}"
                class="theme-btn-save px-4 py-2 flex items-center gap-2">
                <x-mary-icon name="o-plus" class="w-5 h-5" />
                <span>Yeni Şablon</span>
            </a>
        </div>

        <div class="theme-card shadow-sm overflow-hidden">
            <div class="p-4 border-b border-[var(--card-border)] bg-[var(--card-bg)]">
                <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Şablon ara..."
                    icon="o-magnifying-glass" class="max-w-xs" />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-[var(--table-header-bg)] text-[var(--table-header-text)]">
                        <tr>
                            <th class="px-6 py-3">Şablon Adı</th>
                            <th class="px-6 py-3">Konu</th>
                            <th class="px-6 py-3">Oluşturulma</th>
                            <th class="px-6 py-3 text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--table-border)]">
                        @forelse($templates as $template)
                            <tr class="bg-[var(--card-bg)] hover:bg-[var(--table-row-hover-bg)] transition-colors cursor-pointer"
                                onclick="window.location.href='{{ route('settings.mail-templates.edit', $template->id) }}'">
                                <td class="px-6 py-4 font-medium text-[var(--color-text-heading)]">
                                    <div class="flex items-center gap-2">
                                        {{ $template->name }}
                                        @if($template->is_system)
                                            <span
                                                class="px-2 py-0.5 rounded text-[10px] bg-blue-100 text-blue-700 font-bold border border-blue-200">SİSTEM</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-base)]">{{ $template->subject }}</td>
                                <td class="px-6 py-4 text-[var(--color-text-muted)]">
                                    {{ $template->created_at->format('d.m.Y H:i') }}
                                    @if($template->creator)
                                        <div class="text-xs opacity-70">{{ $template->creator->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if(!$template->is_system)
                                            <button wire:click="delete('{{ $template->id }}')" onclick="event.stopPropagation()"
                                                wire:confirm="Bu şablonu silmek istediğinize emin misiniz?"
                                                class="p-2 text-[var(--action-delete-text)] hover:bg-[var(--action-delete-bg)] rounded-lg transition-colors"
                                                title="Sil">
                                                <x-mary-icon name="o-trash" class="w-4 h-4" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-[var(--color-text-muted)]">
                                    <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                    <p>Henüz mail şablonu bulunmuyor.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($templates->hasPages())
                <div class="p-4 border-t border-[var(--card-border)]">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>