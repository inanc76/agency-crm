{{-- Accordion 6: Combo Box --}}
<x-mary-collapse name="group_design_6" group="settings_design" separator
    class="bg-[var(--card-bg)] border border-[var(--card-border)] shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-chevron-up-down" class="w-5 h-5 text-[var(--brand-primary)]" />
            <span class="font-semibold text-skin-heading">Combo Box</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="space-y-8 py-4">

            {{-- 1. Filtre Combo Box --}}
            <div>
                <h3 class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                    <x-mary-icon name="o-funnel" class="w-4 h-4 text-blue-500" />
                    Filtre Combo Box
                    <span class="text-xs font-normal text-slate-400 ml-2">(Liste sayfalarındaki
                        filtreler)</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Preview --}}
                    <div class="bg-[var(--dropdown-hover-bg)] rounded-lg p-4 border border-[var(--card-border)]">
                        <label class="block text-xs font-medium text-[var(--color-text-muted)] mb-2">Önizleme</label>
                        <div class="flex items-center gap-3">
                            <select
                                class="select select-sm bg-[var(--card-bg)] border-[var(--card-border)] text-xs w-40">
                                <option>Tüm Kategoriler</option>
                                <option>Kategori 1</option>
                                <option>Kategori 2</option>
                            </select>
                            <select
                                class="select select-sm bg-[var(--card-bg)] border-[var(--card-border)] text-xs w-32">
                                <option>Tüm Durumlar</option>
                                <option>Aktif</option>
                                <option>Pasif</option>
                            </select>
                        </div>
                    </div>
                    {{-- Code Example --}}
                    <div
                        class="bg-[var(--color-surface-dark-def)] rounded-lg p-4 text-xs font-mono text-[var(--color-success)] overflow-x-auto">
                        <pre>&lt;select class="select select-sm bg-[var(--card-bg)] 
        border-[var(--card-border)] text-xs"&gt;
    &lt;option&gt;Seçenek&lt;/option&gt;
&lt;/select&gt;</pre>
                    </div>
                </div>
                <div
                    class="text-xs text-[var(--color-info)] bg-[var(--color-info)]/10 p-3 rounded-lg border border-[var(--color-info)]/20">
                    <strong class="text-[var(--color-info)]">Kullanım:</strong> Liste sayfalarının üst
                    kısmındaki
                    filtreleme panellerinde kullanılır.
                    <code class="bg-[var(--color-info)]/20 px-1 rounded text-[var(--color-info)]">select-sm</code>
                    sınıfı küçük boyut
                    sağlar.
                </div>
            </div>

            {{-- 1b. Tab Inline Filtre Combo Box --}}
            <div>
                <h3
                    class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
                    <x-mary-icon name="o-adjustments-horizontal" class="w-4 h-4 text-[var(--color-success)]" />
                    Tab Inline Filtre
                    <span class="text-xs font-normal text-[var(--color-text-muted)] ml-2">(Tab içi kompakt
                        filtreler)</span>
                    <span
                        class="px-1.5 py-0.5 bg-[var(--color-success)]/10 text-[var(--color-success)] text-[10px] font-bold rounded">XS</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Preview --}}
                    <div class="bg-[var(--dropdown-hover-bg)] rounded-lg p-4 border border-[var(--card-border)]">
                        <label class="block text-xs font-medium text-[var(--color-text-muted)] mb-2">Önizleme</label>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-skin-heading">Hizmetler</span>
                            <select class="select select-xs bg-[var(--card-bg)] border-[var(--card-border)]">
                                <option>Tüm Durumlar</option>
                                <option>Aktif</option>
                                <option>Pasif</option>
                            </select>
                        </div>
                    </div>
                    {{-- Code Example --}}
                    <div
                        class="bg-[var(--color-surface-dark-def)] rounded-lg p-4 text-xs font-mono text-[var(--color-success)] overflow-x-auto">
                        <pre>&lt;select class="select select-xs bg-[var(--card-bg)] 
        border-[var(--card-border)]"&gt;
    &lt;option&gt;Tüm Durumlar&lt;/option&gt;
&lt;/select&gt;</pre>
                    </div>
                </div>
                <div
                    class="text-xs text-[var(--color-success)] bg-[var(--color-success)]/10 p-3 rounded-lg border border-[var(--color-success)]/20">
                    <strong class="text-[var(--color-success)]">Kullanım:</strong> Müşteri detay tabları içinde
                    satır başı
                    filtreler için kullanılır.
                    <code class="bg-[var(--color-success)]/20 px-1 rounded text-[var(--color-success)]">select-xs</code>
                    sınıfı ekstra
                    küçük boyut sağlar (28px yükseklik).
                </div>
            </div>

            {{-- 2. Form Combo Box --}}
            <div>
                <h3
                    class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
                    <x-mary-icon name="o-document-plus" class="w-4 h-4 text-[var(--brand-primary)]" />
                    Form Combo Box
                    <span class="text-xs font-normal text-[var(--color-text-muted)] ml-2">(Yeni ekle / düzenle
                        formları)</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Preview --}}
                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                        <label class="block text-xs font-medium text-[var(--color-text-muted)] mb-2">Önizleme</label>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium mb-1 opacity-60">Müşteri Seçimi
                                    *</label>
                                <select class="select w-full">
                                    <option>Müşteri Seçin</option>
                                    <option>Örnek Müşteri A.Ş.</option>
                                    <option>Demo Ltd. Şti.</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 opacity-60">Kategori *</label>
                                <select class="select w-full">
                                    <option>Kategori Seçin</option>
                                    <option>Web Hosting</option>
                                    <option>Domain</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- Code Example --}}
                    <div class="bg-slate-900 rounded-lg p-4 text-xs font-mono text-green-400 overflow-x-auto">
                        <pre>&lt;label class="block text-xs font-medium 
       mb-1 opacity-60"&gt;Label *&lt;/label&gt;
&lt;select wire:model="field" class="select w-full"&gt;
    &lt;option value=""&gt;Seçin&lt;/option&gt;
    @@foreach($items as $item)
        &lt;option value="@{{ $item['id'] }}"&gt;
            @{{ $item['name'] }}
        &lt;/option&gt;
    @@endforeach
&lt;/select&gt;
@@error('field') 
    &lt;span class="text-red-500 text-xs"&gt;
        @{!! $message !!}
    &lt;/span&gt; 
@@enderror</pre>
                    </div>
                </div>
                <div
                    class="text-xs text-[var(--brand-primary)] bg-[var(--brand-primary)]/10 p-3 rounded-lg border border-[var(--brand-primary)]/20">
                    <strong class="text-[var(--brand-primary)]">Kullanım:</strong> Yeni kayıt oluşturma ve
                    düzenleme
                    formlarında kullanılır.
                    <code class="bg-[var(--brand-primary)]/20 px-1 rounded text-[var(--brand-primary)]">w-full</code>
                    sınıfı tam genişlik
                    sağlar.
                    <code
                        class="bg-[var(--brand-primary)]/20 px-1 rounded text-[var(--brand-primary)]">wire:model</code>
                    ile Livewire
                    binding yapılır.
                </div>
            </div>

            {{-- Style Classes Reference --}}
            <div>
                <h3
                    class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
                    <x-mary-icon name="o-code-bracket" class="w-4 h-4 text-[var(--color-text-muted)]" />
                    CSS Sınıfları Referansı
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[var(--card-border)] bg-[var(--dropdown-hover-bg)]">
                                <th class="text-left py-2 px-3 font-medium text-skin-heading">Sınıf</th>
                                <th class="text-left py-2 px-3 font-medium text-skin-heading">Açıklama</th>
                                <th class="text-left py-2 px-3 font-medium text-skin-heading">Kullanım Alanı
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            <tr class="border-b border-[var(--card-border)]">
                                <td class="py-2 px-3"><code
                                        class="bg-[var(--dropdown-hover-bg)] px-1.5 py-0.5 rounded">select</code>
                                </td>
                                <td class="py-2 px-3 text-skin-base">Temel select stili</td>
                                <td class="py-2 px-3 text-[var(--color-text-muted)]">Tüm comboboxlar</td>
                            </tr>
                            <tr class="border-b border-[var(--card-border)]">
                                <td class="py-2 px-3"><code
                                        class="bg-[var(--dropdown-hover-bg)] px-1.5 py-0.5 rounded">select-sm</code>
                                </td>
                                <td class="py-2 px-3 text-skin-base">Küçük boyut</td>
                                <td class="py-2 px-3 text-[var(--color-text-muted)]">Filtre panelleri</td>
                            </tr>
                            <tr class="border-b border-[var(--card-border)] bg-[var(--color-success)]/10">
                                <td class="py-2 px-3"><code
                                        class="bg-[var(--color-success)]/20 text-[var(--color-success)] px-1.5 py-0.5 rounded">select-xs</code>
                                </td>
                                <td class="py-2 px-3 text-skin-base font-bold">Ekstra Küçük</td>
                                <td class="py-2 px-3 text-[var(--color-text-muted)]">Tab içi kompakt filtreler
                                </td>
                            </tr>
                            <tr class="border-b border-[var(--card-border)]">
                                <td class="py-2 px-3"><code
                                        class="bg-[var(--dropdown-hover-bg)] px-1.5 py-0.5 rounded">w-full</code>
                                </td>
                                <td class="py-2 px-3 text-skin-base">Tam genişlik</td>
                                <td class="py-2 px-3 text-[var(--color-text-muted)]">Form alanları</td>
                            </tr>
                            <tr class="border-b border-[var(--card-border)]">
                                <td class="py-2 px-3"><code
                                        class="bg-[var(--dropdown-hover-bg)] px-1.5 py-0.5 rounded">bg-white</code>
                                </td>
                                <td class="py-2 px-3 text-skin-base font-medium">Büyük Form</td>
                                <td class="py-2 px-3 text-[var(--color-text-muted)]">Ana kayıt formları</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </x-slot:content>
</x-mary-collapse>