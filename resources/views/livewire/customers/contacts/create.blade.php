<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Kişi Ekle'])]
    class extends Component {
    public ?string $contact = null;

    public function mount(?string $contact = null): void
    {
        $this->contact = $contact;
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        <livewire:modals.contact-form :contact="$contact" />
    </div>
</div>