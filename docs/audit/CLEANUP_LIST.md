# PROJE GENELİ TEMİZLİK VE REFACTORING LİSTESİ (CLEANUP LIST)

Aşağıdaki dosyalar "300 Satır Sınırı"nı aşmaktadır ve parçalanması (Decomposition) önerilmektedir.

## Kritik Öncelikli (Logic Heavy)
1. **app/Livewire/Customers/Offers/Traits/HasOfferActions.php (360 LOC):** Teklif aksiyonları çok yoğun. `Save`, `StatusUpdate`, `Delete` gibi alt traitlere bölünebilir.
2. **app/Livewire/Customers/Offers/Traits/HasOfferDataLoader.php (359 LOC):** Veri yükleme işlemleri.
3. **app/Livewire/Projects/Traits/HasProjectHierarchy.php (332 LOC):** Proje hiyerarşisi (Phases/Modules) yönetimi.
4. **app/Console/Commands/RestoreSourceData.php (345 LOC):** Konsol komutu, belki Service'e alınabilir.

## Arayüz ve Şablonlar (Views)
1. **resources/views/pdf/offer/template.blade.php (692 LOC):** DOMPDF şablonu. CSS ve HTML ayrılmalı. Çok büyük.
2. **resources/views/livewire/settings/pdf-template.blade.php (569 LOC):** Ayar ekranı. Tablara veya partial'lara bölünmeli.
3. **resources/views/livewire/projects/create.blade.php (348 LOC):** Proje oluşturma formu.
4. **resources/views/livewire/customers/create.blade.php (316 LOC):** Müşteri oluşturma formu.

**Not:** Bu liste otomatik tarama (`wc -l`) sonucu oluşturulmuştur.
