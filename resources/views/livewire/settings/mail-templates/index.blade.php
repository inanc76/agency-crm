<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\MailTemplate;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')]
    class extends Component {
    use WithPagination;

    public $search = '';
    public int $perPage = 25;

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'templates' => MailTemplate::query()
                ->with('creator')
                ->where('is_system', false)
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('subject', 'like', "%{$this->search}%"))
                ->orderBy('created_at', 'desc')
                ->paginate($this->perPage)
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

        <div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
            <div class="p-4 border-b border-skin-light">
                <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Şablon ara..."
                    icon="o-magnifying-glass" class="max-w-xs" />
            </div>

            <style>
                .mail-template-row:hover * {
                    color: var(--table-hover-text) !important;
                }
            </style>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-skin-light">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-skin-base">Şablon Adı</th>
                            <th class="px-6 py-3 font-semibold text-skin-base">Konu</th>
                            <th class="px-6 py-3 font-semibold text-skin-base">Tarih</th>
                            <th class="px-6 py-4 text-right font-semibold text-skin-base">Oluşturan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($templates as $template)
                            <tr class="mail-template-row hover:bg-[var(--table-hover-bg)] transition-all duration-200 cursor-pointer group"
                                onclick="window.location.href='{{ route('settings.mail-templates.edit', $template->id) }}'">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="font-medium text-[var(--color-text-heading)] transition-colors">{{ $template->name }}</span>
                                        @if($template->system_key)
                                            <span
                                                class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-600 rounded border border-blue-100 uppercase group-hover:bg-white/20 group-hover:text-inherit group-hover:border-transparent transition-colors">
                                                {{ $template->system_key }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-base)] transition-colors">
                                    {{ $template->subject }}
                                </td>
                                <td
                                    class="px-6 py-4 text-[var(--color-text-muted)] transition-colors uppercase text-[10px] font-mono">
                                    {{ $template->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($template->is_system || $template->system_key)
                                        <span
                                            class="px-2 py-1 text-[10px] font-bold bg-gray-100 text-gray-500 rounded border border-gray-200 group-hover:bg-white/20 group-hover:text-inherit group-hover:border-transparent transition-colors uppercase">SYSTEM</span>
                                    @else
                                        <span class="text-sm font-medium text-[var(--color-text-heading)] transition-colors">
                                            {{ $template->creator?->name ?? 'Bilinmiyor' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-skin-muted">
                                    <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                    <p>Henüz mail şablonu bulunmuyor.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-skin-light flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-skin-muted">Göster:</span>
                    <select wire:model.live="perPage"
                        class="select select-xs bg-white border-skin-light text-xs w-18 h-8 min-h-0 focus:outline-none focus:border-slate-400">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                    </select>
                </div>
                <div>{{ $templates->links() }}</div>
                <div class="text-[10px] text-skin-muted font-mono">
                    {{ number_format(microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT')), 3) }}s
                </div>
            </div>
        </div>
    </div>
</div>