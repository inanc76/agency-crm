<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new
    #[Layout('components.layouts.app', ['title' => 'Tasarım Rehberi'])]
    class extends Component {
}; ?>

<div class="p-6 bg-slate-50 min-h-screen">
    <div class="w-full lg:w-4/5 mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/settings"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm font-medium">Ayarlara Dön</span>
        </a>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <span
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                </span>
                Tasarım Rehberi (Style Guide)
            </h1>
            <p class="text-sm text-slate-500 mt-2 ml-13">
                Sistemdeki tüm UI bileşenlerini canlı olarak önizleyin. Her bileşen, Panel Ayarları'ndan
                yapılandırılan tema değişkenlerini kullanır.
            </p>
        </div>

        {{-- Components Grid --}}
        <div class="space-y-8">

            {{-- ============================================== --}}
            {{-- BUTTONS SECTION --}}
            {{-- ============================================== --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                        Buton Ailesi
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Sistemdeki tüm buton türleri ve durumları</p>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    {{-- Save Button --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Kaydet / Save</span>
                            <span
                                class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">.theme-btn-save</span>
                        </div>
                        <div
                            class="flex items-center justify-center p-4 bg-white rounded-lg border border-slate-100 mb-3">
                            <button class="theme-btn-save">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Kaydet</span>
                            </button>
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Background:</span>
                                <code class="font-mono text-indigo-600">--btn-save-bg</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Text:</span>
                                <code class="font-mono text-indigo-600">--btn-save-text</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Panel Alanı:</span>
                                <span class="text-slate-700 font-medium">Kaydet Butonu</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Aksiyon /
                                Action</span>
                            <span
                                class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">.theme-btn-action</span>
                        </div>
                        <div
                            class="flex items-center justify-center p-4 bg-white rounded-lg border border-slate-100 mb-3">
                            <button class="theme-btn-action">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Yeni Ekle</span>
                            </button>
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Background:</span>
                                <code class="font-mono text-indigo-600">--btn-create-bg</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Text:</span>
                                <code class="font-mono text-indigo-600">--btn-create-text</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Panel Alanı:</span>
                                <span class="text-slate-700 font-medium">Oluştur Butonu</span>
                            </div>
                        </div>
                    </div>

                    {{-- Cancel Button --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">İptal / Cancel</span>
                            <span
                                class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">.theme-btn-cancel</span>
                        </div>
                        <div
                            class="flex items-center justify-center p-4 bg-white rounded-lg border border-slate-100 mb-3">
                            <button class="theme-btn-cancel">
                                <span>İptal</span>
                            </button>
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Background:</span>
                                <code class="font-mono text-indigo-600">--btn-cancel-bg</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Text:</span>
                                <code class="font-mono text-indigo-600">--btn-cancel-text</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Panel Alanı:</span>
                                <span class="text-slate-700 font-medium">İptal Butonu</span>
                            </div>
                        </div>
                    </div>

                    {{-- Delete Button --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Sil / Delete</span>
                            <span
                                class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">.theme-btn-delete</span>
                        </div>
                        <div
                            class="flex items-center justify-center p-4 bg-white rounded-lg border border-slate-100 mb-3">
                            <button class="theme-btn-delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Sil</span>
                            </button>
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Background:</span>
                                <code class="font-mono text-indigo-600">--btn-delete-bg</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Text:</span>
                                <code class="font-mono text-indigo-600">--btn-delete-text</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Panel Alanı:</span>
                                <span class="text-slate-700 font-medium">Sil Butonu</span>
                            </div>
                        </div>
                    </div>

                    {{-- Edit Button --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Düzenle / Edit</span>
                            <span
                                class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">.theme-btn-edit</span>
                        </div>
                        <div
                            class="flex items-center justify-center p-4 bg-white rounded-lg border border-slate-100 mb-3">
                            <button class="theme-btn-edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Düzenle</span>
                            </button>
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Background:</span>
                                <code class="font-mono text-indigo-600">--btn-edit-bg</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Text:</span>
                                <code class="font-mono text-indigo-600">--btn-edit-text</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Panel Alanı:</span>
                                <span class="text-slate-700 font-medium">Düzenle Butonu</span>
                            </div>
                        </div>
                    </div>

                    {{-- Button States --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Buton
                                Durumları</span>
                            <span
                                class="text-[10px] font-mono bg-amber-100 text-amber-700 px-2 py-0.5 rounded">States</span>
                        </div>
                        <div class="flex flex-col gap-2 p-4 bg-white rounded-lg border border-slate-100 mb-3">
                            <button class="theme-btn-save w-full">Normal</button>
                            <button class="theme-btn-save w-full" disabled>Disabled</button>
                        </div>
                        <div class="text-xs text-slate-500">
                            <p><code class="font-mono text-amber-600">:hover</code> → Otomatik karartma</p>
                            <p><code class="font-mono text-amber-600">:disabled</code> → Opaklık %50</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ============================================== --}}
            {{-- INPUTS SECTION --}}
            {{-- ============================================== --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Input Ailesi
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Form elemanları ve doğrulama durumları</p>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    {{-- Text Input --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Text Input</span>
                            <span
                                class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">x-mary-input</span>
                        </div>
                        <div class="p-4 bg-white rounded-lg border border-slate-100 mb-3">
                            <x-mary-input placeholder="Örnek metin..." label="Etiket" />
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Focus Ring:</span>
                                <code class="font-mono text-indigo-600">--input-focus-ring</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Border Radius:</span>
                                <code class="font-mono text-indigo-600">--input-radius</code>
                            </div>
                        </div>
                    </div>

                    {{-- Select --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Select</span>
                            <span
                                class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">x-mary-select</span>
                        </div>
                        <div class="p-4 bg-white rounded-lg border border-slate-100 mb-3">
                            <select class="select select-bordered w-full" style="border-radius: var(--input-radius);">
                                <option disabled selected>Seçim yapın</option>
                                <option>Seçenek 1</option>
                                <option>Seçenek 2</option>
                            </select>
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Border Radius:</span>
                                <code class="font-mono text-indigo-600">--input-radius</code>
                            </div>
                        </div>
                    </div>

                    {{-- Checkbox --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Checkbox &
                                Toggle</span>
                            <span
                                class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">x-mary-checkbox</span>
                        </div>
                        <div class="p-4 bg-white rounded-lg border border-slate-100 mb-3 flex flex-col gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" class="checkbox checkbox-primary" checked />
                                <span class="text-sm">Onay Kutusu</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                <span class="text-sm">Aç/Kapat</span>
                            </label>
                        </div>
                        <div class="text-xs text-slate-500">
                            <p>DaisyUI varsayılan teması kullanılır</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ============================================== --}}
            {{-- TYPOGRAPHY SECTION --}}
            {{-- ============================================== --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h8m-8 6h16" />
                        </svg>
                        Tipografi
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Başlıklar, paragraflar ve metin stilleri</p>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Headings --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Başlıklar</span>
                            <span
                                class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">h1-h6</span>
                        </div>
                        <div class="p-4 bg-white rounded-lg border border-slate-100 mb-3 space-y-2">
                            <h1 class="text-2xl font-bold" style="color: var(--color-text-heading);">Başlık H1</h1>
                            <h2 class="text-xl font-bold" style="color: var(--color-text-heading);">Başlık H2</h2>
                            <h3 class="text-lg font-semibold" style="color: var(--color-text-heading);">Başlık H3</h3>
                            <h4 class="text-base font-semibold" style="color: var(--color-text-heading);">Başlık H4</h4>
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Heading Color:</span>
                                <code class="font-mono text-indigo-600">--color-text-heading</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Font Family:</span>
                                <code class="font-mono text-indigo-600">--font-main</code>
                            </div>
                        </div>
                    </div>

                    {{-- Body Text --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Metin</span>
                            <span class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">p,
                                span</span>
                        </div>
                        <div class="p-4 bg-white rounded-lg border border-slate-100 mb-3 space-y-2">
                            <p style="color: var(--color-text-base);">
                                Bu bir örnek paragraf metnidir. Tema ayarlarından belirlenen temel metin rengi
                                kullanılır.
                            </p>
                            <p class="text-sm text-slate-500">Bu daha küçük bir yardımcı metindir.</p>
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Base Text:</span>
                                <code class="font-mono text-indigo-600">--color-text-base</code>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ============================================== --}}
            {{-- CARDS SECTION --}}
            {{-- ============================================== --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Kart & Konteyner
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Kart yapıları ve gölge ayarları</p>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Card Example --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Standart Kart</span>
                            <span
                                class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded">.card</span>
                        </div>
                        <div class="p-4 bg-white rounded-lg border border-slate-100 mb-3">
                            <div class="card border p-4 shadow-sm">
                                <h3 class="font-semibold text-slate-800 mb-2">Kart Başlığı</h3>
                                <p class="text-sm text-slate-500">Bu bir örnek kart içeriğidir.</p>
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div class="flex justify-between">
                                <span>Background:</span>
                                <code class="font-mono text-indigo-600">--card-bg</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Border Radius:</span>
                                <code class="font-mono text-indigo-600">--card-radius</code>
                            </div>
                        </div>
                    </div>

                    {{-- Color Swatches --}}
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Aktif Renk
                                Paleti</span>
                            <span
                                class="text-[10px] font-mono bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded">Live</span>
                        </div>
                        <div class="p-4 bg-white rounded-lg border border-slate-100 mb-3">
                            <div class="grid grid-cols-5 gap-2">
                                <div class="text-center">
                                    <div class="w-full h-10 rounded-lg shadow-inner mb-1"
                                        style="background-color: var(--btn-save-bg);"></div>
                                    <span class="text-[10px] text-slate-500">Save</span>
                                </div>
                                <div class="text-center">
                                    <div class="w-full h-10 rounded-lg shadow-inner mb-1"
                                        style="background-color: var(--btn-create-bg);"></div>
                                    <span class="text-[10px] text-slate-500">Create</span>
                                </div>
                                <div class="text-center">
                                    <div class="w-full h-10 rounded-lg shadow-inner mb-1"
                                        style="background-color: var(--btn-edit-bg);"></div>
                                    <span class="text-[10px] text-slate-500">Edit</span>
                                </div>
                                <div class="text-center">
                                    <div class="w-full h-10 rounded-lg shadow-inner mb-1"
                                        style="background-color: var(--btn-delete-bg);"></div>
                                    <span class="text-[10px] text-slate-500">Delete</span>
                                </div>
                                <div class="text-center">
                                    <div class="w-full h-10 rounded-lg shadow-inner mb-1"
                                        style="background-color: var(--btn-cancel-bg);"></div>
                                    <span class="text-[10px] text-slate-500">Cancel</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-slate-500">
                            <p>Panel Ayarları'ndan canlı güncellenir</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Quick Link to Panel --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold mb-1">Renkleri Değiştirmek İster Misiniz?</h3>
                        <p class="text-indigo-100 text-sm">Panel Ayarları sayfasından tüm tema renklerini
                            özelleştirebilirsiniz.</p>
                    </div>
                    <a href="/dashboard/settings/panel"
                        class="bg-white text-indigo-600 px-5 py-2.5 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Panel Ayarları
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>