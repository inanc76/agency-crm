@props([
    'headers' => [],
    'emptyMessage' => 'Veri bulunamadı'
])

<x-mary-card shadow class="bg-white border-0 shadow-sm rounded-xl overflow-hidden" separator>
    <div class="overflow-x-auto">
        <table class="table w-full border-separate border-spacing-0">
            <thead>
                <tr class="bg-gray-50/50">
                    @foreach($headers as $header)
                        <th class="text-[10px] uppercase tracking-wider font-bold text-gray-400 py-4 px-6 border-b border-gray-100 first:rounded-tl-xl last:rounded-tr-xl">
                            <div class="flex items-center gap-1.5 {{ ($header['align'] ?? '') === 'center' ? 'justify-center' : '' }}">
                                {{ $header['label'] }}
                                @if($header['sortable'] ?? false)
                                    <x-mary-icon name="o-chevron-up-down" class="w-3 h-3 text-gray-300" />
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                {{ $slot }}
                
                @if(!isset($slot) || trim($slot) === '')
                    <tr>
                        <td colspan="{{ count($headers) }}" class="px-6 py-16 text-center text-gray-400">
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


