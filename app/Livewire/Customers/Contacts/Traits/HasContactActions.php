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
    public $genders = [];

    public function mount(?string $contact = null): void
    {
        // Load Customers
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // Load Genders (Static for now or from Reference)
        $this->genders = [
            ['id' => 'male', 'name' => 'Erkek'],
            ['id' => 'female', 'name' => 'Kadın'],
            ['id' => 'other', 'name' => 'Diğer'],
        ];

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
        $this->validate([
            'customer_id' => 'required',
            'name' => 'required|min:2',
            'status' => 'required',
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
        $this->isViewMode = false;
    }

    public function delete(): void
    {
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
