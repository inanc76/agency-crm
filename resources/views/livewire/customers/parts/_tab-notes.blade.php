{{-- 📝 Notlar Sekmesi (Bağımlılık: $relatedNotes) --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Notlar</h2>
    @if(count($relatedNotes) > 0)
        <div class="space-y-3">
            @foreach($relatedNotes as $note)
                <div class="p-3 border border-[var(--card-border)]/60 rounded-lg bg-[var(--card-bg)]/50">
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="text-xs font-mono opacity-50">{{ \Carbon\Carbon::parse($note['created_at'])->format('d.m.Y H:i') }}</span>
                    </div>
                    <p class="text-sm">{{ $note['content'] ?? $note['note'] ?? '-' }}</p>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-[var(--color-text-muted)]">
            <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p class="text-sm">Henüz not bulunmuyor</p>
        </div>
    @endif
</div>