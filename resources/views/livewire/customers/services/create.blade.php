<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Hizmet Ekle'])]
    class extends Component {
    public ?string $service = null;

    public function mount(?string $service = null): void
    {
        $this->service = $service;
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <livewire:modals.service-form :service="$service" />
</div>