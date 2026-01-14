<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Hizmet Ekle'])]
    class extends Component {
    public ?string $service = null;

    public function mount(?string $service = null): void
    {
        if ($service) {
            $this->authorize('services.view');
        } else {
            $this->authorize('services.create');
        }
        $this->service = $service;
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        <livewire:modals.service-form :service="$service" />
    </div>
</div>