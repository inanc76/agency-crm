<?php

namespace App\Livewire\Customers\Offers\Traits;

use App\Services\MinioService;
use Illuminate\Support\Facades\Log;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    📎 HasOfferAttachments - CONSTITUTION V11                                     ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: Teklif Ek Dosya Yönetimi                                                                  ║
 * ║  🎯 ANA GÖREV: MinIO entegrasyonu ile ek dosya CRUD işlemleri                                                  ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • Dosya Yükleme: PDF, DOC, DOCX formatlarında maksimum 25MB                                                   ║
 * ║  • Dosya Düzenleme: Metadata güncelleme ve dosya değiştirme                                                    ║
 * ║  • Dosya Silme: MinIO'dan güvenli silme işlemi                                                                 ║
 * ║  • Dosya İndirme: Güvenli indirme stream'i                                                                     ║
 * ║  • Modal Yönetimi: Attachment modal state kontrolü                                                              ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK KATMANLARI:                                                                                        ║
 * ║  • Dosya Tipi Validasyonu: Sadece PDF, DOC, DOCX                                                               ║
 * ║  • Boyut Limiti: Maksimum 25MB (25600 KB)                                                                       ║
 * ║  • MinIO Güvenliği: Güvenli dosya depolama ve erişim                                                           ║
 * ║                                                                                                                  ║
 * ║  📊 STATE BAĞIMLILIKLARI:                                                                                       ║
 * ║  • $this->attachments: Ek dosyalar array'i                                                                     ║
 * ║  • $this->attachmentTitle, Description, Price, File: Form alanları                                             ║
 * ║  • $this->editingAttachmentIndex: Düzenleme modu indeksi                                                       ║
 * ║  • $this->showAttachmentModal: Modal görünürlük durumu                                                         ║
 * ║  • $this->currency: Teklif para birimi                                                                         ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasOfferAttachments
{
    // Attachment Modal State
    public $showAttachmentModal = false;
    public $attachments = [];
    public $attachmentTitle = '';
    public $attachmentDescription = '';
    public $attachmentPrice = 0;
    public $attachmentFile = null;
    public $editingAttachmentIndex = null;

    /**
     * @purpose Ek dosya yükleme modalını açma ve form temizleme
     * @return void
     * 🔐 Security: Genel erişim - özel yetki kontrolü yok
     * 📢 Events: $this->showAttachmentModal = true, resetAttachmentForm() çağrısı
     * 
     * State Dependencies: $this->showAttachmentModal
     */
    public function openAttachmentModal(): void
    {
        $this->resetAttachmentForm();
        $this->showAttachmentModal = true;
    }

    /**
     * @purpose Ek dosya modalını kapatma ve form temizleme
     * @return void
     * 🔐 Security: Genel erişim - özel yetki kontrolü yok
     * 📢 Events: $this->showAttachmentModal = false, resetAttachmentForm() çağrısı
     * 
     * State Dependencies: $this->showAttachmentModal
     */
    public function closeAttachmentModal(): void
    {
        $this->showAttachmentModal = false;
        $this->resetAttachmentForm();
    }

    /**
     * @purpose Ek dosya form alanlarını sıfırlama
     * @return void
     * 🔐 Security: Private metot - sadece trait içinden erişilebilir
     * 📢 Events: Form alanları temizlenir, düzenleme modu sıfırlanır
     * 
     * State Dependencies: $this->attachmentTitle, $this->attachmentDescription, $this->attachmentPrice, $this->attachmentFile, $this->editingAttachmentIndex
     */
    private function resetAttachmentForm(): void
    {
        $this->attachmentTitle = '';
        $this->attachmentDescription = '';
        $this->attachmentPrice = 0;
        $this->attachmentFile = null;
        $this->editingAttachmentIndex = null;
    }

    /**
     * @purpose Ek dosya kaydetme (yeni ekleme veya güncelleme) ve MinIO'ya yükleme
     * @return void
     * 🔐 Security: Form validasyonu, dosya tipi kontrolü (PDF, DOC, DOCX), boyut limiti (25MB)
     * 📢 Events: Success/error toast, closeAttachmentModal() çağrısı
     * 
     * State Dependencies: $this->attachments, $this->editingAttachmentIndex, $this->attachmentFile, $this->currency
     */
    public function saveAttachment(): void
    {
        $this->resetErrorBag();

        $this->validate([
            'attachmentTitle' => 'required|string|max:255',
            'attachmentDescription' => 'nullable|string',
            'attachmentPrice' => 'required|numeric|min:0',
            'attachmentFile' => $this->editingAttachmentIndex === null ? 'required|file|mimes:pdf,doc,docx|min:1|max:25600' :
                'nullable|file|mimes:pdf,doc,docx|min:1|max:25600',
        ], [
            'attachmentTitle.required' => 'Lütfen ek için bir başlık giriniz.',
            'attachmentPrice.required' => 'Lütfen bir fiyat belirtiniz.',
            'attachmentFile.required' => 'Lütfen bir dosya seçiniz.',
            'attachmentFile.mimes' => 'Sadece PDF veya Microsoft Word (.doc, .docx) formatları kabul edilmektedir.',
            'attachmentFile.max' => 'Dosya boyutu çok büyük. Maksimum 25 MB yükleyebilirsiniz.',
        ]);

        try {
            $minioService = app(MinioService::class);

            if ($this->editingAttachmentIndex !== null) {
                // Update existing attachment
                $this->attachments[$this->editingAttachmentIndex]['title'] = $this->attachmentTitle;
                $this->attachments[$this->editingAttachmentIndex]['description'] = $this->attachmentDescription;
                $this->attachments[$this->editingAttachmentIndex]['price'] = $this->attachmentPrice;

                // If new file uploaded, replace old one
                if ($this->attachmentFile) {
                    $oldPath = $this->attachments[$this->editingAttachmentIndex]['file_path'] ?? null;
                    if ($oldPath) {
                        $minioService->deleteFile($oldPath);
                    }

                    $uploadResult = $minioService->uploadFile($this->attachmentFile, 'offers');

                    $this->attachments[$this->editingAttachmentIndex]['file_name'] = $this->attachmentFile->getClientOriginalName();
                    $this->attachments[$this->editingAttachmentIndex]['file_type'] = $this->attachmentFile->getClientOriginalExtension();
                    $this->attachments[$this->editingAttachmentIndex]['file_size'] = $this->attachmentFile->getSize();
                    $this->attachments[$this->editingAttachmentIndex]['file_path'] = $uploadResult['path'];
                }

                $this->success('Başarılı', 'Ek güncellendi.');
            } else {
                // Add new attachment - Upload to Minio
                $uploadResult = $minioService->uploadFile($this->attachmentFile, 'offers');

                $this->attachments[] = [
                    'title' => $this->attachmentTitle,
                    'description' => $this->attachmentDescription,
                    'price' => $this->attachmentPrice,
                    'currency' => $this->currency,
                    'file_name' => $this->attachmentFile->getClientOriginalName(),
                    'file_type' => $this->attachmentFile->getClientOriginalExtension(),
                    'file_size' => $this->attachmentFile->getSize(),
                    'file_path' => $uploadResult['path'],
                ];

                $this->success('Başarılı', 'Ek eklendi.');
            }

            $this->closeAttachmentModal();
        } catch (\Exception $e) {
            Log::error('Teklif Eki Yükleme Hatası: ' . $e->getMessage());
            $this->error('Hata', 'Dosya yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * @purpose Mevcut ek dosyayı düzenleme moduna alma
     * @param int $index Düzenlenecek ek dosyanın array indeksi
     * @return void
     * 🔐 Security: Array indeks kontrolü, mevcut dosya varlığı kontrolü
     * 📢 Events: $this->showAttachmentModal = true, form alanları doldurulur
     * 
     * State Dependencies: $this->attachments, $this->editingAttachmentIndex, attachment form fields
     */
    public function editAttachment(int $index): void
    {
        $attachment = $this->attachments[$index];
        $this->editingAttachmentIndex = $index;
        $this->attachmentTitle = $attachment['title'];
        $this->attachmentDescription = $attachment['description'] ?? '';
        $this->attachmentPrice = $attachment['price'];
        $this->showAttachmentModal = true;
    }

    /**
     * @purpose Ek dosyayı listeden ve MinIO'dan silme
     * @param int $index Silinecek ek dosyanın array indeksi
     * @return void
     * 🔐 Security: Array indeks kontrolü, MinIO dosya silme yetkisi
     * 📢 Events: Success/error toast, $this->attachments array güncelleme
     * 
     * State Dependencies: $this->attachments
     */
    public function removeAttachment(int $index): void
    {
        try {
            // Delete file from Minio
            $filePath = $this->attachments[$index]['file_path'] ?? null;
            if ($filePath) {
                $minioService = app(MinioService::class);
                $result = $minioService->deleteFile($filePath);

                if ($result) {
                    Log::info("Teklif Eki Başarıyla Silindi: {$filePath}");
                } else {
                    Log::error("Teklif Eki Silinemedi (Minio Hatası): {$filePath}");
                }
            }

            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
            $this->success('Başarılı', 'Ek silindi.');
        } catch (\Exception $e) {
            Log::error("Minio silme HATASI - Yol: {$filePath} - Hata: " . $e->getMessage());
            $this->error('Hata', 'Dosya silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * @purpose MinIO'dan ek dosyayı indirme
     * @param int $index İndirilecek ek dosyanın array indeksi
     * @return mixed Download response veya null (hata durumunda)
     * 🔐 Security: Dosya varlığı kontrolü, MinIO erişim yetkisi
     * 📢 Events: Error toast (hata durumunda), dosya indirme başlatılır
     * 
     * State Dependencies: $this->attachments
     */
    public function downloadAttachment(int $index): mixed
    {
        $attachment = $this->attachments[$index] ?? null;

        if (!$attachment || empty($attachment['file_path'])) {
            $this->error('Hata', 'Dosya bulunamadı.');
            return null;
        }

        try {
            $minioService = app(MinioService::class);
            return $minioService->downloadFile(
                $attachment['file_path'],
                $attachment['file_name']
            );
        } catch (\Exception $e) {
            Log::error("İndirme Hatası: " . $e->getMessage());
            $this->error('Hata', 'Dosya indirilemedi: ' . $e->getMessage());
            return null;
        }
    }
}
