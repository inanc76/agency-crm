# 🧪 Proje Yönetimi Modülü - Test Anayasası
**Modül:** Proje Yönetimi (Projects, Tasks, Reports)  
**URL'ler:** 
- `/dashboard/projects?tab=projects`
- `/dashboard/projects?tab=tasks`
- `/dashboard/projects?tab=reports`
- `/dashboard/projects/create`
- `/dashboard/projects/tasks/create`
- `/dashboard/projects/reports/create`

**Tarih:** 2026-01-16  
**Durum:** Kritik Bölge - E2E Test Senaryoları

---

## 📋 Test Kategorileri

### 🔄 A. Sekme Navigasyonu Tests - 4 Senaryo

#### T01: Projeler Sekmesine Geçiş
- **Amaç:** Kullanıcı Projeler sekmesine başarıyla geçiş yapabilir
- **URL:** `/dashboard/projects?tab=projects`
- **Beklenen:** 
  - URL'de `tab=projects` parametresi görünür
  - "Projeler" başlığı görünür
  - "Yeni Proje" butonu görünür
  - Proje kartları listelenir
- **Kritiklik:** 🟢 Düşük

#### T02: Görevler Sekmesine Geçiş
- **Amaç:** Kullanıcı Görevler sekmesine başarıyla geçiş yapabilir
- **URL:** `/dashboard/projects?tab=tasks`
- **Beklenen:**
  - URL'de `tab=tasks` parametresi görünür
  - "Görevler" başlığı görünür
  - "Yeni Görev" butonu görünür
  - Görev tablosu görünür
- **Kritiklik:** 🟢 Düşük

#### T03: Raporlar Sekmesine Geçiş
- **Amaç:** Kullanıcı Raporlar sekmesine başarıyla geçiş yapabilir
- **URL:** `/dashboard/projects?tab=reports`
- **Beklenen:**
  - URL'de `tab=reports` parametresi görünür
  - "Raporlar" başlığı görünür
  - "Yeni Rapor" butonu görünür
  - Rapor tablosu görünür
- **Kritiklik:** 🟢 Düşük

#### T04: Sekmeler Arası Geçiş
- **Amaç:** Kullanıcı sekmeler arasında sorunsuz geçiş yapabilir
- **Akış:** Projeler → Görevler → Raporlar → Projeler
- **Beklenen:** Her geçişte doğru içerik yüklenir, URL güncellenir
- **Kritiklik:** 🟡 Orta

---

### 📊 B. Projeler Sekmesi - Listeleme ve Filtreleme - 6 Senaryo

#### T05: Proje Listesi Görüntüleme
- **Amaç:** Proje kartları başarıyla listelenir
- **Beklenen:**
  - En az 1 proje kartı görünür
  - Her kartta: Proje kodu, isim, durum, gün sayısı, sahip, tarih bilgisi
- **Kritiklik:** 🔴 Yüksek

#### T06: Proje Arama Fonksiyonu
- **Amaç:** Arama kutusu ile projeler filtrelenir
- **Test Verisi:** "Deneme Firması"
- **Beklenen:** Sadece arama kriterine uyan projeler listelenir
- **Kritiklik:** 🟡 Orta

#### T07: Durum Filtreleri
- **Amaç:** Durum dropdown'ı ile projeler filtrelenir
- **Test Verisi:** "Devam Ediyor"
- **Beklenen:** Sadece seçilen durumdaki projeler görünür
- **Kritiklik:** 🟡 Orta

#### T08: Tip Filtreleri
- **Amaç:** Proje tipi dropdown'ı ile projeler filtrelenir
- **Test Verisi:** "Destek Hizmeti"
- **Beklenen:** Sadece seçilen tipteki projeler görünür
- **Kritiklik:** 🟡 Orta

#### T09: Proje Kartı Detayları
- **Amaç:** Proje kartı tüm gerekli bilgileri içerir
- **Beklenen Alanlar:**
  - Proje kodu (örn: PRJ-2026-004)
  - Proje adı
  - Durum badge'i
  - Kalan gün sayısı
  - Proje sahibi
  - Oluşturma tarihi
  - Hizmet tipi badge'i
- **Kritiklik:** 🟡 Orta

#### T10: Proje Kartı Hover Efektleri
- **Amaç:** Kart üzerine gelindiğinde aksiyon butonları görünür
- **Beklenen:** Düzenle, Sil, Detay butonları
- **Kritiklik:** 🟢 Düşük

---

### ➕ C. Proje Oluşturma - Pozitif Senaryolar - 6 Senaryo

#### T11: Yeni Proje Sayfasına Erişim
- **Amaç:** "Yeni Proje" butonuna tıklandığında form sayfası açılır
- **URL:** `/dashboard/projects/create`
- **Beklenen:** "Yeni Proje Oluştur" başlığı ve form görünür
- **Kritiklik:** 🔴 Yüksek

#### T12: Zorunlu Alanlarla Proje Oluşturma
- **Amaç:** Tüm zorunlu alanlar doldurularak proje oluşturulur
- **Test Verisi:**
  - Proje Adı: "Test Projesi"
  - Müşteri: "Volkan İnanç"
  - Durum: "Tasak"
  - Zaman Dilimi: "Istanbul (UTC+3)"
  - Proje Tipi: "Web Geliştirme"
  - Başlangıç: "01.01.2026"
  - Bitiş: "31.12.2026"
  - Açıklama: "Test amaçlı proje"
- **Beklenen:** 
  - Başarı mesajı görünür
  - Proje listesine yönlendirilir
  - Yeni proje listede görünür
- **Kritiklik:** 🔴 Yüksek

#### T13: Proje Lideri Seçimi
- **Amaç:** Proje lideri dropdown'ından seçim yapılır
- **Test Verisi:** "Volkan İnanç"
- **Beklenen:** Seçilen lider proje ile ilişkilendirilir
- **Kritiklik:** 🟡 Orta

#### T14: Proje Üyeleri Ekleme
- **Amaç:** Birden fazla proje üyesi eklenebilir
- **Test Verisi:** ["Volkan İnanç", "Admin User"]
- **Beklenen:** Tüm üyeler proje ile ilişkilendirilir
- **Kritiklik:** 🟡 Orta

#### T15: Faz Ekleme Butonu
- **Amaç:** "Faz Ekle" butonu çalışır
- **Beklenen:** Faz ekleme modalı/formu açılır
- **Kritiklik:** 🟢 Düşük

#### T16: İptal Butonu
- **Amaç:** "İptal" butonuna tıklandığında proje listesine dönülür
- **Beklenen:** Form verileri kaydedilmez, liste sayfasına yönlendirilir
- **Kritiklik:** 🟢 Düşük

---

### ❌ D. Proje Oluşturma - Negatif Senaryolar - 5 Senaryo

#### T17: Boş Proje Adı Kontrolü
- **Amaç:** Proje adı boş bırakıldığında hata mesajı gösterilir
- **Test:** Proje adı girilmeden "Kaydet" butonuna tıklanır
- **Beklenen:** "Proje adı gereklidir" veya "Bu alan zorunludur" hatası
- **Kritiklik:** 🔴 Yüksek

#### T18: Müşteri Seçimi Kontrolü
- **Amaç:** Müşteri seçilmeden kayıt yapılamaz
- **Test:** Müşteri seçilmeden "Kaydet" butonuna tıklanır
- **Beklenen:** "Müşteri seçimi zorunludur" hatası
- **Kritiklik:** 🔴 Yüksek

#### T19: Geçersiz Tarih Aralığı
- **Amaç:** Bitiş tarihi başlangıç tarihinden önce olamaz
- **Test Verisi:** Başlangıç: "31.12.2026", Bitiş: "01.01.2026"
- **Beklenen:** "Bitiş tarihi başlangıç tarihinden önce olamaz" hatası
- **Kritiklik:** 🔴 Yüksek

#### T20: Çok Uzun Proje Adı
- **Amaç:** 255 karakterden uzun proje adı kabul edilmez
- **Test Verisi:** 256 karakterlik string
- **Beklenen:** "Proje adı çok uzun" hatası
- **Kritiklik:** 🟡 Orta

#### T21: XSS Koruması
- **Amaç:** Özel karakterler ve script tagları temizlenir
- **Test Verisi:** `<script>alert("test")</script>`
- **Beklenen:** Script çalışmaz, güvenli şekilde kaydedilir
- **Kritiklik:** 🔴 Yüksek

---

### 📋 E. Görevler Sekmesi - Listeleme ve Filtreleme - 8 Senaryo

#### T22: Görev Listesi Görüntüleme
- **Amaç:** Görevler tablo formatında listelenir
- **Beklenen:** En az 1 görev satırı görünür
- **Kritiklik:** 🔴 Yüksek

#### T23: Görev Arama Fonksiyonu
- **Amaç:** Arama kutusu ile görevler filtrelenir
- **Test Verisi:** "İletişim sayfasının yapılması"
- **Beklenen:** Sadece arama kriterine uyan görevler listelenir
- **Kritiklik:** 🟡 Orta

#### T24: Öncelik Filtreleri
- **Amaç:** Öncelik dropdown'ı ile görevler filtrelenir
- **Test Verisi:** "Normal"
- **Beklenen:** Sadece seçilen öncelikteki görevler görünür
- **Kritiklik:** 🟡 Orta

#### T25: Durum Filtreleri
- **Amaç:** Durum dropdown'ı ile görevler filtrelenir
- **Test Verisi:** "Devam Ediyor"
- **Beklenen:** Sadece seçilen durumdaki görevler görünür
- **Kritiklik:** 🟡 Orta

#### T26: Görev Satırı Tıklama
- **Amaç:** Görev satırına tıklandığında detay açılır
- **Beklenen:** Görev detay modalı veya sayfası açılır
- **Kritiklik:** 🟡 Orta

#### T27: Tablo Sütunları
- **Amaç:** Tüm gerekli sütunlar görünür
- **Beklenen Sütunlar:** Konu, Proje, Öncelik, Durum, Atanan
- **Kritiklik:** 🟡 Orta

#### T28: Checkbox Seçimi
- **Amaç:** Görev satırındaki checkbox çalışır
- **Beklenen:** Checkbox işaretlenebilir/kaldırılabilir
- **Kritiklik:** 🟢 Düşük

#### T29: Toplu Seçim
- **Amaç:** Başlıktaki checkbox tüm görevleri seçer
- **Beklenen:** Tüm satırlardaki checkbox'lar işaretlenir
- **Kritiklik:** 🟢 Düşük

---

### ➕ F. Görev Oluşturma - Pozitif Senaryolar - 5 Senaryo

#### T30: Yeni Görev Sayfasına Erişim
- **Amaç:** "Yeni Görev" butonuna tıklandığında form sayfası açılır
- **URL:** `/dashboard/projects/tasks/create`
- **Beklenen:** "Yeni Görev Oluştur" başlığı ve form görünür
- **Kritiklik:** 🔴 Yüksek

#### T31: Zorunlu Alanlarla Görev Oluşturma
- **Amaç:** Tüm zorunlu alanlar doldurularak görev oluşturulur
- **Test Verisi:**
  - Müşteri: "Volkan İnanç"
  - Proje: "Deneme Firması"
  - Atanan: "Volkan İnanç"
  - Öncelik: "Normal"
  - Durum: "Yapılacak"
  - Başlık: "Test Görevi"
  - Açıklama: "Test amaçlı görev"
- **Beklenen:**
  - Başarı mesajı görünür
  - Görev listesine yönlendirilir
  - Yeni görev listede görünür
- **Kritiklik:** 🔴 Yüksek

#### T32: Müşteri-Proje İlişkisi
- **Amaç:** Müşteri seçildiğinde ilgili projeler yüklenir
- **Test:** Müşteri dropdown'ından seçim yapılır
- **Beklenen:** Proje dropdown'ı aktif olur ve ilgili projeler listelenir
- **Kritiklik:** 🔴 Yüksek

#### T33: Dosya Ekleme
- **Amaç:** Görev için dosya yüklenebilir
- **Test Verisi:** test-file.pdf
- **Beklenen:** Dosya başarıyla yüklenir ve listede görünür
- **Kritiklik:** 🟡 Orta

#### T34: Görev Özeti Güncelleme
- **Amaç:** Sağ taraftaki özet bölümü dinamik güncellenir
- **Test:** Müşteri seçilir
- **Beklenen:** Özet bölümünde seçilen müşteri adı görünür
- **Kritiklik:** 🟢 Düşük

---

### ❌ G. Görev Oluşturma - Negatif Senaryolar - 5 Senaryo

#### T35: Müşteri Seçimi Kontrolü
- **Amaç:** Müşteri seçilmeden görev oluşturulamaz
- **Test:** Müşteri seçilmeden "Kaydet" butonuna tıklanır
- **Beklenen:** "Müşteri seçimi zorunludur" hatası
- **Kritiklik:** 🔴 Yüksek

#### T36: Proje Seçimi Kontrolü
- **Amaç:** Proje seçilmeden görev oluşturulamaz
- **Test:** Proje seçilmeden "Kaydet" butonuna tıklanır
- **Beklenen:** "Proje seçimi zorunludur" hatası
- **Kritiklik:** 🔴 Yüksek

#### T37: Görev Başlığı Kontrolü
- **Amaç:** Görev başlığı boş bırakılamaz
- **Test:** Başlık girilmeden "Kaydet" butonuna tıklanır
- **Beklenen:** "Görev başlığı gereklidir" hatası
- **Kritiklik:** 🔴 Yüksek

#### T38: Geçersiz Dosya Formatı
- **Amaç:** Sadece izin verilen dosya formatları yüklenebilir
- **Test Verisi:** malicious.exe
- **Beklenen:** "Geçersiz dosya formatı" hatası
- **Kritiklik:** 🔴 Yüksek

#### T39: Maksimum Dosya Boyutu
- **Amaç:** 10MB'dan büyük dosya yüklenemez
- **Test Verisi:** large-file.pdf (>10MB)
- **Beklenen:** "Dosya boyutu çok büyük" hatası
- **Kritiklik:** 🔴 Yüksek

---

### 📊 H. Raporlar Sekmesi - Listeleme - 7 Senaryo

#### T40: Rapor Listesi Görüntüleme
- **Amaç:** Raporlar tablo formatında listelenir
- **Beklenen:** En az 1 rapor satırı görünür
- **Kritiklik:** 🔴 Yüksek

#### T41: Rapor Arama Fonksiyonu
- **Amaç:** Arama kutusu ile raporlar filtrelenir
- **Test Verisi:** "Destek Hizmeti"
- **Beklenen:** Sadece arama kriterine uyan raporlar listelenir
- **Kritiklik:** 🟡 Orta

#### T42: Tablo Sütunları
- **Amaç:** Tüm gerekli sütunlar görünür
- **Beklenen Sütunlar:** Tarih, Raporu Giren, Müşteri, Hizmet/Proje, Süre, Rapor Özeti
- **Kritiklik:** 🟡 Orta

#### T43: Rapor Satırı Detayları
- **Amaç:** Her satır tüm gerekli bilgileri içerir
- **Beklenen:** Tarih, kişi, müşteri, hizmet, süre bilgileri görünür
- **Kritiklik:** 🟡 Orta

#### T44: Destek Hizmeti Badge
- **Amaç:** Hizmet tipi badge'i görünür
- **Beklenen:** "Destek Hizmeti" badge'i renkli ve okunabilir
- **Kritiklik:** 🟢 Düşük

#### T45: Rapor Özeti
- **Amaç:** Rapor özeti metni görünür
- **Beklenen:** Özet sütununda metin içeriği var
- **Kritiklik:** 🟢 Düşük

#### T46: Süre Formatı
- **Amaç:** Süre bilgisi doğru formatta görünür
- **Beklenen Format:** "1s 00dk" veya "0s 15dk"
- **Kritiklik:** 🟢 Düşük

---

### ➕ I. Rapor Oluşturma - Pozitif Senaryolar - 6 Senaryo

#### T47: Yeni Rapor Sayfasına Erişim
- **Amaç:** "Yeni Rapor" butonuna tıklandığında form sayfası açılır
- **URL:** `/dashboard/projects/reports/create`
- **Beklenen:** "Yeni Rapor Ekle" başlığı ve form görünür
- **Kritiklik:** 🔴 Yüksek

#### T48: Müşteri Seçerek Rapor Oluşturma
- **Amaç:** Müşteri seçilerek rapor oluşturulur
- **Test Verisi:**
  - Müşteri: "Volkan İnanç"
  - İlişki: "Proje"
  - Proje Tipi: "Web Geliştirme"
- **Beklenen:** Başarı mesajı ve rapor listesine yönlendirme
- **Kritiklik:** 🔴 Yüksek

#### T49: Rapor İlişkisi Sekmeleri
- **Amaç:** Proje/Görev/Rapor Yok sekmeleri çalışır
- **Test:** Her sekmeye tıklanır
- **Beklenen:** İlgili form alanları görünür
- **Kritiklik:** 🟡 Orta

#### T50: Rapor Özeti Görüntüleme
- **Amaç:** Sağ taraftaki özet bölümü bilgileri gösterir
- **Beklenen Bilgiler:**
  - Oluşturan: "Volkan İnanç"
  - Tarih: "16.01.2026"
  - Toplam Süre: "0s 00dk"
- **Kritiklik:** 🟢 Düşük

#### T51: Rapor Satırı Ekleme
- **Amaç:** "Rapor Ekle" butonu ile yeni satır eklenir
- **Beklenen:** Rapor satırı formu açılır
- **Kritiklik:** 🟡 Orta

#### T52: Rapor Bilgileri Doldurma
- **Amaç:** Rapor detayları ve süre bilgisi girilir
- **Test Verisi:**
  - Başlık: "Test Rapor"
  - Açıklama: "Test açıklaması"
  - Saat: "2"
  - Dakika: "30"
- **Beklenen:** Bilgiler kaydedilir, toplam süre güncellenir
- **Kritiklik:** 🟡 Orta

---

### ❌ J. Rapor Oluşturma - Negatif Senaryolar - 4 Senaryo

#### T53: Müşteri Seçimi Kontrolü
- **Amaç:** Müşteri seçilmeden rapor oluşturulamaz
- **Test:** Müşteri seçilmeden "Kaydet" butonuna tıklanır
- **Beklenen:** "Müşteri seçimi zorunludur" hatası
- **Kritiklik:** 🔴 Yüksek

#### T54: Proje Tipi Kontrolü
- **Amaç:** Proje sekmesinde proje tipi seçilmelidir
- **Test:** Proje tipi seçilmeden "Kaydet" butonuna tıklanır
- **Beklenen:** "Lütfen önce proje seçin" hatası
- **Kritiklik:** 🔴 Yüksek

#### T55: Rapor Satırı Kontrolü
- **Amaç:** En az 1 rapor satırı eklenmelidir
- **Test:** Rapor satırı eklenmeden "Kaydet" butonuna tıklanır
- **Beklenen:** "Henüz rapor satırı eklenmemiş" hatası
- **Kritiklik:** 🔴 Yüksek

#### T56: Geçersiz Süre Girişi
- **Amaç:** Negatif veya geçersiz süre kabul edilmez
- **Test Verisi:** Saat: "-1", Dakika: "70"
- **Beklenen:** "Geçersiz süre girişi" hatası
- **Kritiklik:** 🟡 Orta

---

### 🔗 K. Entegrasyon Testleri - 3 Senaryo

#### T57: Proje-Görev-Rapor Akışı
- **Amaç:** Tam iş akışı test edilir
- **Akış:**
  1. Yeni proje oluştur
  2. Proje için görev ekle
  3. Görev için rapor oluştur
- **Beklenen:** Tüm adımlar başarılı, veriler ilişkilendirilmiş
- **Kritiklik:** 🔴 Yüksek

#### T58: Çoklu Proje Oluşturma
- **Amaç:** Aynı müşteri için birden fazla proje oluşturulur
- **Test:** 3 farklı proje oluştur
- **Beklenen:** Tüm projeler listede görünür
- **Kritiklik:** 🟡 Orta

#### T59: Proje Silme Etkisi
- **Amaç:** Proje silindiğinde ilgili görevler etkilenir
- **Test:** Görevli bir proje silinir
- **Beklenen:** Görevlerde uygun mesaj gösterilir
- **Kritiklik:** 🔴 Yüksek

---

### ⚡ L. Performans Testleri - 4 Senaryo

#### T60: Sayfa Yükleme Süresi
- **Amaç:** Proje listesi 3 saniyeden kısa sürede yüklenir
- **Beklenen:** Yükleme süresi < 3000ms
- **Kritiklik:** 🟡 Orta

#### T61: Pagination Performansı
- **Amaç:** Sayfalama çalışır ve performanslıdır
- **Test:** "Sonraki" butonuna tıklanır
- **Beklenen:** Yeni sayfa < 1000ms'de yüklenir
- **Kritiklik:** 🟢 Düşük

#### T62: Arama Performansı
- **Amaç:** Arama sonuçları hızlı döner
- **Test:** Arama kutusuna yazılır
- **Beklenen:** Sonuçlar < 1000ms'de görünür
- **Kritiklik:** 🟡 Orta

#### T63: Lazy Loading
- **Amaç:** Sayfa kaydırıldığında yeni içerik yüklenir
- **Test:** Sayfa sonuna kaydırılır
- **Beklenen:** Yeni proje kartları yüklenir
- **Kritiklik:** 🟢 Düşük

---

### ♿ M. Erişilebilirlik Testleri - 4 Senaryo

#### T64: Klavye Navigasyonu
- **Amaç:** Tab tuşu ile form elemanları arasında gezinilebilir
- **Test:** Tab ve Enter tuşları kullanılır
- **Beklenen:** Tüm interaktif elemanlar erişilebilir
- **Kritiklik:** 🟡 Orta

#### T65: ARIA Etiketleri
- **Amaç:** Butonlar ve form alanları ARIA etiketlerine sahip
- **Beklenen:** aria-label, aria-describedby attribute'ları mevcut
- **Kritiklik:** 🟡 Orta

#### T66: Alternatif Metinler
- **Amaç:** Tüm görseller alt text'e sahip
- **Beklenen:** Her img tag'inde alt attribute'u var
- **Kritiklik:** 🟡 Orta

#### T67: Form Hataları Erişilebilirliği
- **Amaç:** Hata mesajları ekran okuyucu için erişilebilir
- **Beklenen:** role="alert" attribute'u mevcut
- **Kritiklik:** 🟡 Orta

---

### 📱 N. Responsive Tasarım Testleri - 4 Senaryo

#### T68: Mobil Hamburger Menü
- **Amaç:** Mobil görünümde menü çalışır
- **Viewport:** 375x667 (iPhone)
- **Beklenen:** Hamburger menü görünür ve çalışır
- **Kritiklik:** 🟡 Orta

#### T69: Tablet Layout
- **Amaç:** Tablet görünümde layout düzgün
- **Viewport:** 768x1024 (iPad)
- **Beklenen:** Proje kartları grid düzeninde
- **Kritiklik:** 🟢 Düşük

#### T70: Desktop Görünüm
- **Amaç:** Desktop'ta tüm öğeler görünür
- **Viewport:** 1920x1080
- **Beklenen:** Tüm sekmeler ve butonlar görünür
- **Kritiklik:** 🟢 Düşük

#### T71: Mobil Form Kullanımı
- **Amaç:** Mobilde form alanları kullanılabilir
- **Viewport:** 375x667
- **Beklenen:** Input'lar tıklanabilir ve yazılabilir
- **Kritiklik:** 🟡 Orta

---

### 🔒 O. Güvenlik Testleri - 4 Senaryo

#### T72: XSS Koruması
- **Amaç:** Script injection engellenir
- **Test Verisi:** `<script>alert("XSS")</script>`
- **Beklenen:** Script çalışmaz, güvenli şekilde kaydedilir
- **Kritiklik:** 🔴 Yüksek

#### T73: SQL Injection Koruması
- **Amaç:** SQL injection engellenir
- **Test Verisi:** `'; DROP TABLE projects; --`
- **Beklenen:** Sorgu güvenli şekilde işlenir
- **Kritiklik:** 🔴 Yüksek

#### T74: CSRF Token Kontrolü
- **Amaç:** Form gönderimlerinde CSRF token var
- **Beklenen:** input[name="_token"] mevcut ve dolu
- **Kritiklik:** 🔴 Yüksek

#### T75: Yetkisiz Erişim Engeli
- **Amaç:** Oturum açmamış kullanıcı erişemez
- **Test:** Cookie'ler temizlenir, sayfaya erişilir
- **Beklenen:** Login sayfasına yönlendirilir
- **Kritiklik:** 🔴 Yüksek

---

### 🚨 P. Hata Yönetimi Testleri - 5 Senaryo

#### T76: Network Hatası
- **Amaç:** API hatası durumunda uygun mesaj gösterilir
- **Test:** Network request abort edilir
- **Beklenen:** "Bağlantı hatası" mesajı görünür
- **Kritiklik:** 🔴 Yüksek

#### T77: 404 Hatası
- **Amaç:** Olmayan sayfa için 404 gösterilir
- **Test:** `/dashboard/projects/nonexistent` sayfasına gidilir
- **Beklenen:** "Sayfa bulunamadı" mesajı
- **Kritiklik:** 🟡 Orta

#### T78: 500 Hatası
- **Amaç:** Sunucu hatası durumunda kullanıcı bilgilendirilir
- **Test:** API 500 döndürür
- **Beklenen:** "Sunucu hatası" mesajı
- **Kritiklik:** 🔴 Yüksek

#### T79: Timeout ve Retry
- **Amaç:** Timeout durumunda retry mekanizması çalışır
- **Test:** İlk 2 request abort edilir
- **Beklenen:** 3. denemede başarılı olur
- **Kritiklik:** 🟡 Orta

#### T80: Validation Hataları
- **Amaç:** Validation hataları kullanıcı dostu gösterilir
- **Test:** Zorunlu alanlar boş bırakılır
- **Beklenen:** Her alan için hata mesajı görünür
- **Kritiklik:** 🟡 Orta

---

### 🎨 Q. Kullanıcı Deneyimi Testleri - 7 Senaryo

#### T81: Loading Spinner
- **Amaç:** Yükleme sırasında spinner gösterilir
- **Test:** Yavaş network simüle edilir
- **Beklenen:** Spinner/loading animasyonu görünür
- **Kritiklik:** 🟢 Düşük

#### T82: Toast Mesajları
- **Amaç:** Başarı mesajları otomatik kapanır
- **Test:** Proje kaydedilir
- **Beklenen:** Toast 5 saniye sonra kaybolur
- **Kritiklik:** 🟢 Düşük

#### T83: Onay Dialogları
- **Amaç:** Silme işleminde onay istenir
- **Test:** "Sil" butonuna tıklanır
- **Beklenen:** "Emin misiniz?" dialogu açılır
- **Kritiklik:** 🟡 Orta

#### T84: Tooltip'ler
- **Amaç:** Bilgi ikonlarında tooltip görünür
- **Test:** İkon üzerine gelinir
- **Beklenen:** Tooltip metni görünür
- **Kritiklik:** 🟢 Düşük

#### T85: Breadcrumb Navigasyonu
- **Amaç:** Breadcrumb ile geri dönülebilir
- **Test:** Breadcrumb'a tıklanır
- **Beklenen:** İlgili sayfaya yönlendirilir
- **Kritiklik:** 🟢 Düşük

#### T86: Boş Durum Mesajları
- **Amaç:** Sonuç yoksa uygun mesaj gösterilir
- **Test:** Olmayan bir şey aranır
- **Beklenen:** "Sonuç bulunamadı" mesajı
- **Kritiklik:** 🟢 Düşük

#### T87: Drag and Drop
- **Amaç:** Görev sıralaması değiştirilebilir (varsa)
- **Test:** Görev satırı sürüklenir
- **Beklenen:** Sıralama değişir
- **Kritiklik:** 🟢 Düşük

---

### 📊 R. Veri Tutarlılığı Testleri - 4 Senaryo

#### T88: Oluşturulan Proje Görünürlüğü
- **Amaç:** Yeni proje hemen listede görünür
- **Test:** Unique isimli proje oluşturulur
- **Beklenen:** Proje listede bulunur
- **Kritiklik:** 🔴 Yüksek

#### T89: Güncelleme Kaydı
- **Amaç:** Proje güncellemeleri kaydedilir
- **Test:** Proje adı değiştirilir
- **Beklenen:** Yeni ad listede görünür
- **Kritiklik:** 🔴 Yüksek

#### T90: Silme İşlemi
- **Amaç:** Silinen proje listeden kaldırılır
- **Test:** Proje silinir
- **Beklenen:** Proje listede görünmez
- **Kritiklik:** 🔴 Yüksek

#### T91: Müşteri-Proje İlişkisi
- **Amaç:** Müşteri değiştirildiğinde projeler güncellenir
- **Test:** Farklı müşteri seçilir
- **Beklenen:** Proje listesi değişir
- **Kritiklik:** 🔴 Yüksek

---

### 🔧 S. Özel Durumlar ve Edge Cases - 7 Senaryo

#### T92: Uzun Proje Adı Kesme
- **Amaç:** Çok uzun proje adı ellipsis ile kesilir
- **Test:** 100 karakterlik proje adı
- **Beklenen:** text-overflow: ellipsis uygulanır
- **Kritiklik:** 🟢 Düşük

#### T93: Duplicate İsim
- **Amaç:** Aynı isimde proje oluşturulabilir (izin veriliyorsa)
- **Test:** Aynı isimle 2 proje oluşturulur
- **Beklenen:** Her ikisi de kaydedilir veya hata verilir
- **Kritiklik:** 🟡 Orta

#### T94: Geçmiş Tarih Kontrolü
- **Amaç:** Geçmiş tarih seçimi engellenir (gerekiyorsa)
- **Test:** 2020 tarihi girilir
- **Beklenen:** Uyarı mesajı veya kabul edilir
- **Kritiklik:** 🟢 Düşük

#### T95: Özel Karakterler
- **Amaç:** Özel karakterler güvenli şekilde işlenir
- **Test:** `Test & Co. "Proje" #1`
- **Beklenen:** Tüm karakterler korunur
- **Kritiklik:** 🟡 Orta

#### T96: Boşluk Trim
- **Amaç:** Başta/sonda boşluklar temizlenir
- **Test:** `   Boşluklu Proje   `
- **Beklenen:** `Boşluklu Proje` olarak kaydedilir
- **Kritiklik:** 🟢 Düşük

#### T97: Emoji Desteği
- **Amaç:** Emoji karakterleri desteklenir
- **Test:** `🚀 Roket Projesi 🎯`
- **Beklenen:** Emoji'ler korunur ve görünür
- **Kritiklik:** 🟢 Düşük

#### T98: Çoklu Dil Desteği
- **Amaç:** Dil değiştirici çalışır (varsa)
- **Test:** Dil İngilizce'ye çevrilir
- **Beklenen:** Tüm metinler İngilizce görünür
- **Kritiklik:** 🟢 Düşük

---

## 📊 Test Özeti

| Kategori | Senaryo Sayısı | Kritiklik |
|----------|----------------|-----------|
| Sekme Navigasyonu | 4 | 🟢 Düşük |
| Projeler Listeleme | 6 | 🟡 Orta |
| Proje Oluşturma (+) | 6 | 🔴 Yüksek |
| Proje Oluşturma (-) | 5 | 🔴 Yüksek |
| Görevler Listeleme | 8 | 🟡 Orta |
| Görev Oluşturma (+) | 5 | 🔴 Yüksek |
| Görev Oluşturma (-) | 5 | 🔴 Yüksek |
| Raporlar Listeleme | 7 | 🟡 Orta |
| Rapor Oluşturma (+) | 6 | 🔴 Yüksek |
| Rapor Oluşturma (-) | 4 | 🔴 Yüksek |
| Entegrasyon | 3 | 🔴 Yüksek |
| Performans | 4 | 🟡 Orta |
| Erişilebilirlik | 4 | 🟡 Orta |
| Responsive | 4 | 🟡 Orta |
| Güvenlik | 4 | 🔴 Yüksek |
| Hata Yönetimi | 5 | 🔴 Yüksek |
| Kullanıcı Deneyimi | 7 | 🟢 Düşük |
| Veri Tutarlılığı | 4 | 🔴 Yüksek |
| Özel Durumlar | 7 | 🟢 Düşük |
| **TOPLAM** | **98** | - |

---

## 🎯 Öncelik Sırası

### 🔴 Kritik (Öncelikli)
1. **Güvenlik Testleri (T72-T75)** - XSS, SQL Injection, CSRF, Yetkilendirme
2. **Temel CRUD İşlemleri (T11-T12, T30-T31, T47-T48)** - Oluşturma işlemleri
3. **Veri Tutarlılığı (T88-T91)** - Veri bütünlüğü
4. **Entegrasyon (T57, T59)** - İş akışı
5. **Hata Yönetimi (T76, T78)** - Network ve sunucu hataları

### 🟡 Orta Öncelik
1. **Filtreleme ve Arama (T06-T08, T23-T25, T41)** - Kullanıcı verimliliği
2. **Form Validasyonları (T17-T21, T35-T39, T53-T56)** - Veri kalitesi
3. **Performans (T60, T62)** - Kullanıcı deneyimi
4. **Erişilebilirlik (T64-T67)** - Kapsayıcılık
5. **Responsive (T68, T71)** - Mobil uyumluluk

### 🟢 Düşük Öncelik
1. **UI/UX Detayları (T81-T87)** - Görsel iyileştirmeler
2. **Edge Cases (T92-T98)** - Özel durumlar
3. **Performans İyileştirmeleri (T61, T63)** - Optimizasyon

---

## 🛠️ Playwright Test Dosyası

Tüm bu senaryolar için hazır Playwright test kodu:
- **Dosya:** `tests/e2e/project-management.spec.ts`
- **Satır Sayısı:** 1392 satır
- **Test Sayısı:** 100+ otomatik test

### Kurulum ve Çalıştırma

```bash
# Kurulum
npm install
npx playwright install

# Testleri çalıştır
npm run test:e2e

# UI modunda çalıştır (önerilen)
npm run test:e2e:ui

# Sadece Chromium'da çalıştır
npm run test:e2e:chromium

# Rapor görüntüle
npm run test:report
```

---

## 📝 Notlar

1. **Test Fixture Dosyaları:** `tests/fixtures/` klasörüne aşağıdaki dosyaları ekleyin:
   - `test-file.pdf` - Normal dosya yükleme testleri için
   - `large-file.pdf` - 10MB'dan büyük dosya (maksimum boyut testi)
   - `malicious.exe` - Güvenlik testi için geçersiz format

2. **Test Veritabanı:** Production verilerini korumak için test veritabanı kullanın

3. **CI/CD:** GitHub Actions workflow dosyası hazır (`.github/workflows/playwright.yml`)

4. **Raporlama:** HTML, JSON ve JUnit formatlarında raporlar oluşturulur

---

**Test Mimarı Notu:** Bu test anayasası, Proje Yönetimi modülünün tüm kritik senaryolarını kapsar. Testler hem manuel hem de otomatik olarak çalıştırılabilir. Playwright kodu production-ready durumda ve hemen kullanıma hazırdır.
