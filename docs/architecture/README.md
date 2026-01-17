# PROJE MİMARİSİ VE ZIRHLI REFACTORING PROTOKOLÜ

## Giriş
Bu doküman, projenin mimari standartlarını, refactoring süreçlerini ve stil yönetimini belirler. "Mission Omega" kapsamında, projenin "Constitution V12.2" anayasasına göre yönetildiği tescil edilmiştir.

---

## 1. Zırhlı Refactoring Protokolü (3-Etaplı)
Herhangi bir modül refactor edilirken, sistem stabilitesini korumak için aşağıdaki 3 aşamalı protokol **zorunludur**:

### ETAP 1: Fiziksel Parçalama (Decomposition)
- Büyük dosyalar (>300 LOC) mantıksal parçalara (`partials/`) ayrılır.
- Kodun çalışırlığı bozulmaz, sadece fiziksel yer değiştirme yapılır.
- PHP Logic (Variables, Loops) olduğu gibi korunur.

### ETAP 2: Mimari Mühürleme (Documentation)
- Her dosyanın başına "Zırhlı Belgeleme Kartı" (Architectural Shield) eklenir.
- Partial'lar arasındaki veri akışı, dependency ve scope ilişkileri açıklanır.
- Kritik notlar `@architect-note` veya `@security-note` etiketiyle düşülür.

### ETAP 3: Fonksiyonel Doğrulama (Test Verification)
- Yapılan değişikliklerin sağlamlığı "Zırhlı Testler" (Feature/E2E) ile kanıtlanır.
- Testler sadece "Happy Path" değil, "Corner Case" (Hata, Null Veri, Saldırı) senaryolarını da kapsamalıdır.
- `/test` komutu ile tüm suit %100 geçmelidir.

---

## 2. Stil Yönetimi: Single Source of Truth
Projenin görsel bütünlüğü için tüm stil değerleri **CSS Değişkenleri** (`:root`) üzerinden yönetilir.

- **Dosya:** `resources/css/app.css` (veya ilgili theme dosyası).
- **Hard-Coded Renk Yasağı:** `bg-indigo-600` veya `#4F46E5` gibi statik değerler yerine `var(--color-primary)` kullanılmalıdır.
- **Dinamik Tema:** Renkler veritabanından (`PanelSetting`) gelse bile, View katmanında bunlar CSS değişkenlerine atanır ve override edilir.

---

## 3. Test Stratejisi
Proje, "Güvenlik Odaklı" bir test stratejisi izler.

- **Unit:** Model ve Servis mantığı.
- **Feature:** Livewire bileşenleri ve Controller akışları.
- **E2E:** Kritik kullanıcı yolculukları (Create -> Preview -> Download).
- **TestCases Folder:** Tüm modüllerin test envanteri ve senaryo dökümü burada tutulur.

---

**Son Güncelleme:** Mission Omega (V12.2)
