<?php

use App\Livewire\Customers\Offers\Traits\HasOfferActions;
use App\Livewire\Customers\Offers\Traits\HasOfferCalculations;
use App\Livewire\Customers\Offers\Traits\HasOfferItems;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component
{
    use HasOfferActions, HasOfferCalculations, HasOfferItems;
    use Toast, WithFileUploads;
}; ?>

<div class="max-w-7xl mx-auto">
    {{-- Back Button --}}
    @include('livewire.customers.offers.partials._header', ['isViewMode' => $isViewMode, 'title' => $title, 'number' => $number, 'offerId' => $offerId])

    {{-- Tab Navigation --}}
    @include('livewire.customers.offers.partials._tabs', ['isViewMode' => $isViewMode, 'activeTab' => $activeTab])

    <div class="grid grid-cols-12 gap-6">
        {{-- Left Column (8/12 for info tab, 12/12 for notes/messages/downloads tabs) --}}
        <div class="{{ ($activeTab === 'notes' || $activeTab === 'messages' || $activeTab === 'downloads') ? 'col-span-12' : 'col-span-8' }}">
            @if($activeTab === 'info')
                <div class="space-y-6">
                    {{-- Validation Errors Summary --}}
                    @if($errors->any())
                        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @include('livewire.customers.offers.partials._customer_info', [
                        'isViewMode' => $isViewMode,
                        'customers' => $customers,
                        'customer_id' => $customer_id,
                        'status' => $status,
                        'valid_days' => $valid_days,
                        'currency' => $currency,
                        'discount_type' => $discount_type,
                        'discount_value' => $discount_value,
                        'vat_rate' => $vat_rate,
                        'vatRates' => $vatRates,
                        'offerStatuses' => $offerStatuses,
                        'currencies' => $currencies,
                        'offerModel' => $offerModel,
                        'title' => $title,
                        'description' => $description
                    ])
                                        @foreach($sections as $index => $section)
                                            @include('livewire.customers.offers.partials._section_row', [
                                                'isViewMode' => $isViewMode,
                                                'section' => $section,
                                                'index' => $index
                                            ])
                                        @endforeach
                            @if(!$isViewMode)
                                            <div class="flex justify-center mt-4">
                                            <button type="button" wire:click="addSection" 
                                        class="flex items-center gap-2 px-4 py-2 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                                    <x-mary-icon name="o-plus-circle" class="w-4 h-4" />
                                Bölüm Ekle
                                    </button>
                                        </div>
                            @endif
                            @include('livewire.customers.offers.partials._attachments', [
                                'isViewMode' => $isViewMode,
                                'attachments' => $attachments
                            ])
                </div>
              @endif
          @if($activeTab === 'messages')
            <div class="theme-card p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-skin-heading">Mesajlar</h2>
                    <x-customer-management.action-button label="Yeni Mesaj"
                        href="/dashboard/customers/messages/create?offer={{ $offerId }}&customer={{ $offerModel?->customer_id }}" />
                </div>

                <div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="agency-table">
                            <thead>
                                <tr>
                                    <th class="w-10">
                                        <input type="checkbox" disabled
                                            class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                                    </th>
                                    <th>Konu</th>
                                    <th>Alıcı</th>
                                    <th>Durum</th>
                                    <th class="text-right">Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($offerModel?->messages ?? [] as $message)
                                    @php
                                        $statusLabel = $message->status_item->display_label ?? $message->status ?? 'Bilinmiyor';
                                        $statusClass = $message->status_item->metadata['color_class'] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                                        $char = mb_substr($message->subject, 0, 1);
                                    @endphp
                                    <tr onclick="Livewire.navigate('/dashboard/customers/messages/{{ $message->id }}')">
                                        <td onclick="event.stopPropagation()">
                                            <input type="checkbox" disabled
                                                class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="avatar-circle">{{ $char }}</div>
                                                <div class="item-name">{{ $message->subject }}</div>
                                            </div>
                                        </td>
                                        <td class="opacity-70">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-skin-heading">{{ $message->recipient_name }}</span>
                                                <span class="text-xs text-skin-muted">{{ $message->recipient_email }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="text-right text-xs font-mono opacity-60">
                                            {{ $message->created_at->format('d.m.Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-skin-muted">
                                            <div class="flex flex-col items-center justify-center">
                                                <x-mary-icon name="o-chat-bubble-left-right" class="w-12 h-12 opacity-20 mb-4" />
                                                <div class="font-medium">Henüz bu teklifle ilişkili mesaj bulunmuyor.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t border-skin-light flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-skin-muted">Göster:</span>
                            <div class="px-2 py-1 border border-skin-light rounded text-xs bg-white">25</div>
                        </div>

                        <div class="text-[10px] text-skin-muted font-mono">
                            {{ $offerModel?->messages?->count() ?? 0 }} kayıt listelendi
                        </div>
                    </div>
                </div>
            </div>
          @endif
        @if($activeTab === 'notes')
            @if($offerId)
                @livewire('shared.notes-tab', [
                    'entityType' => 'OFFER',
                    'entityId' => $offerId
                ], key('notes-tab-' . $offerId))
            @else
                    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                        <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Teklifi kaydedin, ardından not ekleyebilirsiniz</div>
                    </div>
                @endif
        @endif

            @if($activeTab === 'downloads')
                <div class="theme-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-[var(--card-border)] bg-gray-50/50">
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">E-posta</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Tarih / Saat</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Dosya Adı</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">IP Adresi</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Cihaz / Tarayıcı</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--card-border)]">
                                @forelse($offerModel?->downloadLogs ?? [] as $log)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-700 break-all">
                                                {{ $log->downloader_email ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-slate-900">{{ $log->downloaded_at->format('d.m.Y') }}</div>
                                            <div class="text-[10px] text-slate-500 font-medium">{{ $log->downloaded_at->format('H:i:s') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-slate-700 truncate max-w-xs" title="{{ $log->file_name ?? '-' }}">
                                                {{ $log->file_name ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200">
                                                {{ $log->ip_address ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-[10px] text-slate-600 line-clamp-1 max-w-sm" title="{{ $log->user_agent }}">
                                                {{ $log->user_agent ?? '-' }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <x-mary-icon name="o-arrow-down-tray" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                            <p class="text-sm text-slate-500 italic">Henüz indirme kaydı bulunmuyor.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column (4/12) - Summary (hidden on notes/messages/downloads tabs) --}}
        @if($activeTab !== 'notes' && $activeTab !== 'messages' && $activeTab !== 'downloads')
            <div class="col-span-4 space-y-6">
                @include('livewire.customers.offers.partials._summary', [
                    'isViewMode' => $isViewMode,
                    'currency' => $currency,
                    'discount_type' => $discount_type,
                    'discount_value' => $discount_value,
                    'vat_rate' => $vat_rate,
                    'valid_until' => $valid_until,
                    'created_at' => $created_at,
                    'sections' => $sections,
                    'offerModel' => $offerModel,
                    'vatRates' => $vatRates
                ])
                @include('livewire.customers.offers.partials._modal-attachment', [
                    'showAttachmentModal' => $showAttachmentModal,
                    'editingAttachmentIndex' => $editingAttachmentIndex,
                    'attachmentTitle' => $attachmentTitle,
                    'attachmentDescription' => $attachmentDescription,
                    'attachmentPrice' => $attachmentPrice,
                    'currency' => $currency,
                    'attachmentFile' => $attachmentFile,
                    'attachments' => $attachments
                ])
            </div>
        @endif
    </div>
    @include('livewire.customers.offers.partials._modal-item-description', [
        'showItemDescriptionModal' => $showItemDescriptionModal,
        'itemDescriptionTemp' => $itemDescriptionTemp
    ])
</div>
