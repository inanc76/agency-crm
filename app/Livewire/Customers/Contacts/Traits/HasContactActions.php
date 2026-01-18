<?php

namespace App\Livewire\Customers\Contacts\Traits;

use App\Models\Contact;
use Illuminate\Support\Str;

/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * TRAIT      : HasContactActions
 * SORUMLULUK : Müşteri kontak kişilerinin (Contact) CRUD operasyonlarını,
 *              iletişim bilgilerini ve sosyal profil yönetimini sağlar.
 *
 * BAĞIMLILIKLAR:
 * - Mary\Traits\Toast (Bileşen seviyesinde)
 *
 * METODLAR:
 * - loadContactData(): Mevcut kontak bilgilerini form alanlarına yükler.
 * - save(): Yeni kontak oluşturur veya mevcut olanı günceller.
 * - cancel(): İşlemi durdurur ve geri yönlendirir.
 * - toggleEditMode(): Görüntüleme ve düzenleme modları arasında geçiş yapar.
 * - delete(): Kontağı sistemden siler.
 * -------------------------------------------------------------------------
 */
trait HasContactActions
{
    /**
     * Mevcut bir kontağın tüm verilerini ve ilişkili istatistiklerini form alanlarına yükler.
     * İş Kuralı: Veriler yüklendikten sonra View moduna zorlanır.
     */
    public function loadContactData(): void
    {
        $contact = Contact::findOrFail($this->contactId);

        $this->customer_id = $contact->customer_id;
        $this->name = $contact->name;
        $this->status = $contact->status ?? 'WORKING';
        $this->gender = $contact->gender ?? '';
        $this->position = $contact->position ?? '';
        $this->birth_date = $contact->birth_date ? \Carbon\Carbon::parse($contact->birth_date)->format('Y-m-d') : null;

        // E-posta listesini birleştir ve tekilleştir (Primary email + array formatında diğerleri)
        $emailList = [];
        if ($contact->email) {
            $emailList[] = $contact->email;
        }
        if (!empty($contact->emails)) {
            $emailList = array_merge($emailList, (array) $contact->emails);
        }
        $this->emails = !empty($emailList) ? array_unique($emailList) : [''];

        // Telefon numaralarını (Dahili:XX) formatından kurtararak form alanlarına dağıtır
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

        // Performans Notu: İlişkili veriler select ile kısıtlanmıştır
        $this->relatedMessages = \App\Models\Message::where('customer_id', $this->customer_id)
            ->with(['customer', 'offer', 'contact'])
            ->orderBy('created_at', 'desc')
            ->get();

        $this->messageCount = $this->relatedMessages->count();
        $this->noteCount = $contact->notes()->count();

        $this->isViewMode = true;
    }

    /**
     * Kontak kişisini kaydeder veya günceller.
     * Güvenlik: contacts.create veya contacts.edit yetkisi zorunludur.
     */
    public function save(): void
    {
        // 🔐 Security: Yetki denetimi operasyon tipine göre yapılır
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

        // Telefonları "Numara (Dahili:XXX)" formatında depolama için paketler
        $formattedPhones = array_map(function ($phone) {
            $number = $phone['number'];
            $extension = $phone['extension'] ?? '';

            if (empty($number)) {
                return null;
            }

            if (!empty($extension)) {
                return "{$number} (Dahili:{$extension})";
            }

            return $number;
        }, $this->phones);

        $data = [
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'email' => !empty($this->emails[0]) ? $this->emails[0] : null,
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

        $this->loadContactData();
        $this->dispatch('contact-saved');
    }

    /**
     * İşlemi iptal eder. Kayıt varsa verileri geri yükler, yoksa müşteri sayfasına döner.
     */
    public function cancel(): void
    {
        if ($this->contactId) {
            $this->loadContactData();
        } else {
            $this->redirect('/dashboard/customers/' . $this->customer_id . '?tab=contacts', navigate: true);
        }
    }

    /**
     * Düzenleme modunu açar.
     * Güvenlik: contacts.edit yetkisi gerektirir.
     */
    public function toggleEditMode(): void
    {
        // 🔐 Security: View modundan Edit moduna geçişte yetki check edilir
        $this->authorize('contacts.edit');

        $this->isViewMode = false;
    }

    /**
     * Kaydı siler.
     * Güvenlik: contacts.delete yetkisi gerektirir.
     */
    public function delete(): void
    {
        // 🔐 Security: Silme işlemi için kritik yetki denetimi
        $this->authorize('contacts.delete');

        if ($this->contactId) {
            $contact = Contact::findOrFail($this->contactId);
            $customer_id = $contact->customer_id;
            $contact->delete();
            $this->success('Kişi Silindi', 'Kişi kaydı başarıyla silindi.');
            $this->redirect('/dashboard/customers/' . $customer_id . '?tab=contacts');
        }
    }

    // --- Dinamik Alan Yönetim Metotları ---
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
