<?php

namespace App\Livewire\Customers\Contacts\Traits;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Support\Str;

trait HasContactActions
{
    // Contact Fields
    public string $customer_id = '';
    public string $status = 'WORKING';
    public string $gender = '';
    public string $name = '';
    public string $position = '';

    // Contact Details
    public array $emails = [''];
    public array $phones = [['number' => '', 'extension' => '']];
    public ?string $birth_date = null;
    public array $social_profiles = [['name' => '', 'url' => '']];

    // State Management
    public bool $isViewMode = false;
    public ?string $contactId = null;
    public string $activeTab = 'info';

    // Reference Data
    public $customers = [];
    public $contactStatuses = [];
    public $genders = [];
    public function mount(?string $contact = null): void
    {
        // Load Customers
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // Load Contact Statuses
        $this->contactStatuses = \App\Models\ReferenceItem::where('category_key', 'CONTACT_STATUS')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key', 'metadata'])
            ->map(fn($i) => ['id' => $i->id, 'display_label' => $i->display_label, 'key' => $i->key, 'color_class' => $i->color_class])
            ->toArray();

        // Load Genders from Reference Data if exists, fallback to static
        $this->genders = \App\Models\ReferenceItem::where('category_key', 'GENDER')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key'])
            ->map(fn($i) => ['id' => $i->key, 'name' => $i->display_label])
            ->toArray();

        if (empty($this->genders)) {
            $this->genders = [
                ['id' => 'male', 'name' => 'Erkek'],
                ['id' => 'female', 'name' => 'Kadın'],
                ['id' => 'other', 'name' => 'Diğer'],
            ];
        }

        // If contact ID is provided, load data
        if ($contact) {
            $this->contactId = $contact;
            $this->loadContactData();

            // Set active tab from URL if present
            $this->activeTab = request()->query('tab', 'info');
        } else {
            // Check for customer query parameter
            $customerId = request()->query('customer');
            if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
                $this->customer_id = $customerId;
            }
            if (!empty($this->contactStatuses)) {
                $this->status = $this->contactStatuses[0]['key'];
            }
        }
    }

    private function loadContactData(): void
    {
        $contact = Contact::findOrFail($this->contactId);

        $this->customer_id = $contact->customer_id;
        $this->name = $contact->name;
        $this->status = $contact->status ?? 'WORKING';
        $this->gender = $contact->gender ?? '';
        $this->position = $contact->position ?? '';
        $this->birth_date = $contact->birth_date ? \Carbon\Carbon::parse($contact->birth_date)->format('Y-m-d') : null;

        $this->emails = !empty($contact->emails) ? (array) $contact->emails : [''];

        // Parse phones to extract number and extension
        if (!empty($contact->phones)) {
            $this->phones = array_map(function ($phone) {
                if (preg_match('/^(.*?)\s*\(Dahili:(.*?)\)$/', $phone, $matches)) {
                    return ['number' => trim($matches[1]), 'extension' => trim($matches[2])];
                }
                return ['number' => $phone, 'extension' => ''];
            }, (array) $contact->phones);
        } else {
            $this->phones = [['number' => '', 'extension' => '']];
        }

        $this->social_profiles = !empty($contact->social_profiles) ? (array) $contact->social_profiles : [['name' => '', 'url' => '']];

        $this->isViewMode = true;
    }

    public function save(): void
    {
        // 🔐 Security: Authorization check based on operation type (contacts.create or contacts.edit)
        if ($this->contactId) {
            $this->authorize('contacts.edit');
        } else {
            $this->authorize('contacts.create');
        }

        $statusKeys = collect($this->contactStatuses)->pluck('key')->implode(',');
        $genderKeys = collect($this->genders)->pluck('id')->implode(',');

        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|min:2|max:150',
            'status' => "required|in:{$statusKeys}",
            'gender' => "nullable|string|in:{$genderKeys}",
            'position' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'emails' => 'array',
            'emails.*' => 'nullable|email|max:150',
            'phones' => 'array',
            'phones.*.number' => 'nullable|string|max:20',
            'phones.*.extension' => 'nullable|numeric|digits_between:1,10',
            'social_profiles' => 'array',
            'social_profiles.*.url' => 'nullable|url|max:255',
        ]);

        // Format phones for storage
        $formattedPhones = array_map(function ($phone) {
            $number = $phone['number'];
            $extension = $phone['extension'] ?? '';

            if (empty($number))
                return null;

            if (!empty($extension)) {
                return "{$number} (Dahili:{$extension})";
            }
            return $number;
        }, $this->phones);

        $data = [
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'status' => $this->status,
            'gender' => $this->gender,
            'position' => $this->position,
            'birth_date' => $this->birth_date,
            'emails' => array_values(array_filter($this->emails)),
            'phones' => array_values(array_filter($formattedPhones)),
            'social_profiles' => array_values(array_filter($this->social_profiles, fn($s) => !empty($s['name']) || !empty($s['url']))),
        ];

        if ($this->contactId) {
            $contact = Contact::findOrFail($this->contactId);
            $contact->update($data);
            $message = 'Kişi bilgileri güncellendi.';
        } else {
            $this->contactId = Str::uuid()->toString();
            $data['id'] = $this->contactId;
            Contact::create($data);
            $message = 'Yeni kişi başarıyla oluşturuldu.';
        }

        $this->success('İşlem Başarılı', $message);
        $this->isViewMode = true;
        // Reload data to ensure consistent state
        $this->loadContactData();

        // Dispatch event
        $this->dispatch('contact-saved');
    }

    public function cancel(): void
    {
        if ($this->contactId) {
            $this->loadContactData();
        } else {
            $this->redirect('/dashboard/customers/' . $this->customer_id . '?tab=contacts', navigate: true);
        }
    }

    public function toggleEditMode(): void
    {
        // 🔐 Security: Require edit permission to enter edit mode
        $this->authorize('contacts.edit');

        $this->isViewMode = false;
    }

    public function delete(): void
    {
        // 🔐 Security: Require delete permission
        $this->authorize('contacts.delete');

        if ($this->contactId) {
            $contact = Contact::findOrFail($this->contactId);
            $customer_id = $contact->customer_id;
            $contact->delete();
            $this->success('Kişi Silindi', 'Kişi kaydı başarıyla silindi.');
            $this->redirect('/dashboard/customers/' . $customer_id . '?tab=contacts');
        }
    }

    // Dynamic Fields Helper Methods
    public function addEmail()
    {
        $this->emails[] = '';
    }
    public function removeEmail($index)
    {
        unset($this->emails[$index]);
        $this->emails = array_values($this->emails);
    }

    public function addPhone()
    {
        $this->phones[] = ['number' => '', 'extension' => ''];
    }
    public function removePhone($index)
    {
        unset($this->phones[$index]);
        $this->phones = array_values($this->phones);
    }

    public function addSocialProfile()
    {
        $this->social_profiles[] = ['name' => '', 'url' => ''];
    }
    public function removeSocialProfile($index)
    {
        unset($this->social_profiles[$index]);
        $this->social_profiles = array_values($this->social_profiles);
    }
}
