@props(['text'])

<div x-data="{ copied: false }" class="inline-flex items-center gap-1.5 group">
    <span {{ $attributes->merge(['class' => 'font-mono']) }}>
        {{ $slot->isEmpty() ? $text : $slot }}
    </span>
    <button type="button"
        @click.stop="navigator.clipboard.writeText('{{ $text }}'); copied = true; setTimeout(() => copied = false, 2000)"
        class="cursor-pointer text-slate-300 hover:text-indigo-500 transition-all duration-200 transform hover:scale-110 active:scale-95"
        title="Kopyala">
        <x-mary-icon x-show="!copied" name="o-document-duplicate" class="w-3.5 h-3.5" />
        <x-mary-icon x-show="copied" name="o-check" class="w-3.5 h-3.5 text-green-500" />
    </button>
</div>