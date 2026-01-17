{{--
📝 PARTIAL: PDF Footer Ayarları
---------------------------------------------------------------------
Bu dosya PDF'in alt kısmı (Footer) ile ilgili metin ayarlarını içerir.
Genellikle banka bilgileri, adres veya feragatname metinleri için kullanılır.

BAĞIMLILIKLAR (Variables):
- $pdf_footer_text (String|Null)
---------------------------------------------------------------------
--}}
<x-mary-collapse name="footer" icon="o-pencil-square">
    <x-slot:heading>
        <span class="font-medium text-sm">Footer Ayarları</span>
    </x-slot:heading>
    <x-slot:content>
        <div class="pt-4">
            <x-mary-textarea label="Varsayılan Footer Notu" wire:model="pdf_footer_text"
                placeholder="Şirket bilgileri, IBAN vb. (Teklif açıklamasının altında görünür)" rows="4"
                hint="Bu metin tüm PDF tekliflerin en altında varsayılan olarak görünecektir." class="bg-white" />
        </div>
    </x-slot:content>
</x-mary-collapse>