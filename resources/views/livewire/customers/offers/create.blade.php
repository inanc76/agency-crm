<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Teklif Oluştur'])]
    class extends Component {
    public ?string $offer = null;

    public function mount(?string $offer = null): void
    {
        $this->offer = $offer;
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <livewire:modals.offer-form :offer="$offer" />
</div>