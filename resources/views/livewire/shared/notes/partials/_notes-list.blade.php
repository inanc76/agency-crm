{{--
    @component: _notes-list.blade.php
    @section: Notes Tab - Notes List
    @description: Notların listelendiği kartlar
    @params: $notes (Collection)
    @events: openNoteModal(noteId), deleteNote(noteId)
--}}

<div class="space-y-4">
    @forelse($notes as $note)
        <div class="bg-white rounded-xl border border-skin-light shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            {{-- Header: Yazar ve Tarih --}}
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    {{-- Avatar --}}
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                        {{ $note->author->initials() }}
                    </div>
                    
                    {{-- Yazar Bilgisi --}}
                    <div>
                        <p class="font-semibold text-skin-base">{{ $note->author->name }}</p>
                        <p class="text-xs text-skin-muted">
                            {{ $note->created_at->diffForHumans() }}
                            @if($note->created_at != $note->updated_at)
                                <span class="ml-1">(düzenlendi)</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Actions: Düzenle ve Sil (Sadece yazar görebilir) --}}
                @if($note->author_id === Auth::id())
                    <div class="flex items-center gap-3">
                        <button wire:click="openNoteModal('{{ $note->id }}')" 
                            class="p-2 text-skin-muted hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer"
                            title="Düzenle">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button wire:click="deleteNote('{{ $note->id }}')" 
                            wire:confirm="Bu notu silmek istediğinizden emin misiniz?"
                            class="p-2 text-skin-muted hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
                            title="Sil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        
                        {{-- Görünürlük Bilgisi --}}
                        <div class="flex items-center gap-2 pl-3 border-l border-skin-light">
                            <svg class="w-4 h-4 text-skin-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="text-xs text-skin-muted">
                                {{ $note->visibleTo->count() }} kişi görebilir
                            </span>
                            
                            {{-- Görünürlük Listesi (Hover ile göster) --}}
                            <div class="relative group">
                                <button class="text-xs text-blue-600 hover:underline cursor-pointer">
                                    (detay)
                                </button>
                                <div class="absolute left-0 bottom-full mb-2 hidden group-hover:block z-10 bg-white border border-skin-light rounded-lg shadow-lg p-3 min-w-[200px]">
                                    <p class="text-xs font-semibold text-skin-base mb-2">Bu notu görebilecekler:</p>
                                    <ul class="space-y-1">
                                        @foreach($note->visibleTo as $user)
                                            <li class="text-xs text-skin-muted flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-semibold">
                                                    {{ $user->initials() }}
                                                </div>
                                                {{ $user->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Content: Not İçeriği --}}
            <div class="prose prose-sm max-w-none text-skin-base mb-4">
                <p class="whitespace-pre-wrap">{{ $note->content }}</p>
            </div>


        </div>
    @empty
        {{-- Empty State --}}
        <div class="bg-white rounded-xl border border-skin-light shadow-sm p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-skin-muted opacity-50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-semibold text-skin-base mb-2">Henüz not eklenmemiş</h3>
            <p class="text-sm text-skin-muted mb-6">
                İlk notu eklemek için yukarıdaki "Not Ekle" butonuna tıklayın.
            </p>
            <button wire:click="openNoteModal" class="theme-btn-save">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Yeni Not
            </button>
        </div>
    @endforelse
</div>
