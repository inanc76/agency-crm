@props([
    'headers' => [],
    'emptyMessage' => 'Veri bulunamadı'
])

<x-mary-card shadow class="shadow-sm overflow-hidden" separator 
    style="background-color: var(--list-card-bg); border: 1px solid var(--list-card-border);">
    <div class="overflow-x-auto">
        <table class="table w-full border-separate border-spacing-0">
            <thead>
                <tr class="bg-[var(--table-header-bg)]">
                    @foreach($headers as $header)
                        <th class="text-[10px] uppercase tracking-wider font-bold text-[var(--table-header-text)] py-4 px-6 border-b border-[var(--table-border)] first:rounded-tl-xl last:rounded-tr-xl">
                            <div class="flex items-center gap-1.5 {{ ($header['align'] ?? '') === 'center' ? 'justify-center' : '' }}">
                                {{ $header['label'] }}
                                @if($header['sortable'] ?? false)
                                    <x-mary-icon name="o-chevron-up-down" class="w-3 h-3 text-[var(--icon-muted)]" />
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--table-border)]">
                {{ $slot }}
                
                @if(!isset($slot) || trim($slot) === '')
                    <tr>
                        <td colspan="{{ count($headers) }}" class="px-6 py-16 text-center text-[var(--color-text-muted)]">
                            <div class="flex flex-col items-center gap-2">
                                <x-mary-icon name="o-inbox" class="w-8 h-8 opacity-20" />
                                <span class="text-sm italic font-light">{{ $emptyMessage }}</span>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</x-mary-card>


