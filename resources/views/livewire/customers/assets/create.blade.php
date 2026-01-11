<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Varlık Ekle'])]
    class extends Component {
    public ?string $asset = null;

    public function mount(?string $asset = null): void
    {
        $this->asset = $asset;
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <livewire:modals.asset-form :asset="$asset" />
</div>