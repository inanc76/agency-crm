<?php

/**
 * 🛡️ ZIRHLI MODÜL: Customer Management
 * ---------------------------------------------------------
 * DURUM: %100 Test Pass (Pest)
 * YETKİ: [customers.create, customers.edit, customers.delete]
 * TEST: tests/Feature/Customers/CustomerCreateTest.php
 * MİMARİ: Volt + Trait + Lazy Load
 * ---------------------------------------------------------
 */

namespace App\Livewire\Traits;

use App\Models\Customer;
use Illuminate\Support\Str;

trait HasCustomerActions
{
    /**
     * Save customer (create or update)
     * 🔐 PERMISSIONS: customers.create (new) or customers.edit (existing)
     * 📢 EVENTS: Redirects on create, stays on viewMode on edit.
     */
    public function save(): void
    {
        // 🔐 Security: Authorization check based on operation type
        if ($this->customerId) {
            $this->authorize('customers.edit');
        } else {
            $this->authorize('customers.create');
        }

        $this->validate([
            'customer_type' => 'required|string',
            'name' => 'required|string|max:255',
            'emails.*' => 'nullable|email',
            'phones.*' => 'nullable|string|max:50',
            'websites.*' => 'nullable|url',
            'country_id' => 'required|string',
            'city_id' => 'required|string',
            'address' => 'nullable|string|max:1000',
            'title' => 'nullable|string|max:255',
            'tax_office' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:20',
            'current_code' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:5120',
        ]);

        // Filter and Normalize
        $emails = array_filter($this->emails, fn($e) => !empty($e));
        $phones = array_map(fn($p) => $this->normalizePhone($p), array_filter($this->phones, fn($p) => !empty($p)));
        $websites = array_map(fn($url) => $this->normalizeUrl($url), array_filter($this->websites, fn($w) => !empty($w)));

        $data = [
            'name' => $this->formatTitleCase($this->name),
            'customer_type' => $this->customer_type,
            'email' => $emails[0] ?? null,
            'emails' => array_values($emails),
            'phone' => $phones[0] ?? null,
            'phones' => array_values($phones),
            'website' => $websites[0] ?? null,
            'websites' => array_values($websites),
            'country_id' => $this->country_id ?: null,
            'city_id' => $this->city_id ?: null,
            'address' => $this->address ?: null,
            'title' => $this->formatTitleCase($this->title),
            'tax_office' => $this->formatTitleCase($this->tax_office),
            'tax_number' => $this->tax_number ?: null,
            'current_code' => $this->current_code ?: null,
        ];

        $wasCreating = empty($this->customerId);

        if ($this->customerId) {
            // Update existing
            $customer = Customer::find($this->customerId);
            $customer->update($data);
            $message = 'Müşteri bilgileri güncellendi.';
        } else {
            // Create new
            $this->customerId = Str::uuid()->toString();
            $data['id'] = $this->customerId;
            $customer = Customer::create($data);
            $message = 'Yeni müşteri başarıyla oluşturuldu.';
        }

        // Handle logo upload
        if ($this->logo) {
            $path = $this->logo->store('uploads/customer-logo', 'public');
            $customer->update(['logo_url' => '/' . $path]);
            $this->logo = null; // Reset upload input
        }

        // Attach related customers
        $customer->relatedCustomers()->sync($this->related_customers);

        $this->success('İşlem Başarılı', $message);

        if ($wasCreating) {
            $this->redirect('/dashboard/customers/' . $this->customerId, navigate: true);
        } else {
            $this->isViewMode = true;
        }
    }

    /**
     * Toggle edit mode
     * 🔐 PERMISSIONS: customers.edit (Admin bypass enabled)
     * Neden: View modundan form moduna geçişi kontrol eder.
     */
    public function toggleEditMode(): void
    {
        $this->authorize('customers.edit');

        $this->isViewMode = false;
    }

    /**
     * Cancel edit mode
     */
    public function cancel(): void
    {
        if ($this->customerId) {
            $this->loadCustomerData();
        } else {
            $this->redirect('/dashboard/customers?tab=customers', navigate: true);
        }
    }

    /**
     * Delete customer
     * 🔐 PERMISSIONS: customers.delete (Admin bypass enabled)
     * 📢 ACTIONS: Removes record and redirects to list.
     */
    public function delete(): void
    {
        $this->authorize('customers.delete');

        if ($this->customerId) {
            Customer::findOrFail($this->customerId)->delete();
            $this->success('Müşteri Silindi', 'Müşteri kaydı başarıyla silindi.');
            $this->redirect('/dashboard/customers?tab=customers');
        }
    }

    /**
     * Create new customer (redirect)
     */
    public function createNew(): void
    {
        $this->redirect('/dashboard/customers/create', navigate: true);
    }

    /**
     * Add related customer
     */
    public function addRelatedCustomer(string $customerId): void
    {
        if (!in_array($customerId, $this->related_customers) && count($this->related_customers) < 10) {
            $this->related_customers[] = $customerId;
        }
    }

    /**
     * Remove related customer
     */
    public function removeRelatedCustomer(string $customerId): void
    {
        $this->related_customers = array_values(
            array_filter($this->related_customers, fn($id) => $id !== $customerId)
        );
    }

    /**
     * Normalize URL to https:// format
     */
    private function normalizeUrl(?string $url): string
    {
        if (empty($url)) {
            return '';
        }

        $url = trim($url);
        if (!preg_match('#^https?://#i', $url)) {
            return 'https://' . $url;
        }

        return $url;
    }

    /**
     * Convert to Title Case
     */
    private function formatTitleCase(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        return Str::title(trim($text));
    }

    /**
     * Allow only numbers, + and spaces in phone
     */
    private function normalizePhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        return preg_replace('/[^0-9+ ]/', '', $phone);
    }
}
