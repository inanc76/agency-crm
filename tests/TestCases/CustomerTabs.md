# 🧪 Customer Detail Hub - Test Anayasası (Tab Refactoring)
**Modül:** Customer Detail Tabs  
**Amaç:** Bağımsız, izole ve performanslı sekme yapısı  
**Kapsam:** 7 Tab (Access, Contacts, Assets, Services, Offers, Sales, Messages)

---

## 🏗️ Mimari Değişiklik Tests (Critical)

Bu testler, Tabs yapısının "Monolitik" yapıdan "Mikro-Modüller"e geçişini doğrular.

### T01-T10: Component Isolation & Data Loading
1.  **T01: Isolation Check:** Customer Detail sayfası yüklendiğinde, aktif olmayan tabların (örn: Sales) veritabanı sorguları çalışmamalıdır. (Lazy Loading)
2.  **T02: Parameter Passing:** Her tab `<livewire:... />` ile çağrılmalı ve `customerId` parametresini doğru almalıdır.
3.  **T03: Independent Pagination:** Contacts tab'ındaki sayfalama değiştiğinde, Services tab'ındaki veya ana sayfadaki scroll/state bozulmamalıdır.
4.  **T04: State Retention:** Bir tabdan diğerine geçip geri dönüldüğünde, filtreleme veya arama state'i korunmalıdır (URL query string veya session ile).
5.  **T05: Parent-Child Communication:** Tab içindeki bir işlem (örn: Kişi silme), ana sayfadaki "Kişi Sayısı" (Badge count) bilgisini güncellemeli/tetiklemelidir (`dispatch`).

---

## 👤 Contacts Tab Tests (15 Scenarios)

### Authorization
1.  **Auth-01:** `contacts.view` yetkisi olmayan kullanıcı tab içeriğini göremez.
2.  **Auth-02:** `contacts.create` yetkisi olmayan kullanıcı "Yeni Kişi" butonunu görmez.
3.  **Auth-03:** `contacts.delete` yetkisi olmayan kullanıcı silme işlemini yapamaz.

### Data & Logic
4.  **Data-01:** Sadece ilgili müşteriye ait kişiler listelenir.
5.  **Data-02:** N+1 Check: Kişiler listelenirken her satır için ayrı sorgu atılmaz (User/Role relations).
6.  **Search-01:** İsim, E-posta veya Telefon ile arama yapılabilir.
7.  **Filter-01:** Departman veya Ünvan'a göre filtreleme çalışır.

### UI/UX
8.  **UI-01:** Kişi kartında avatar (Varsa resim, yoksa baş harfler) görüntülenir.
9.  **UI-02:** Uzun isimler veya mailler taşmadan "..." ile gösterilir (Truncate).
10. **UI-03:** "Düzenle" modalı tab içinde açılır, sayfayı yenilemez.
11. **Interact-01:** Kişi silindiğinde liste anında güncellenir (Re-render).

---

## 🛠️ Services Tab Tests (15 Scenarios)

### Authorization
1.  **Auth-01:** `services.view` yetkisi yoksa tab gizli veya 403 döner.
2.  **Auth-02:** `services.manage` yetkisi yoksa "Hizmet Ekle" butonu görünmez.

### Data & Logic
3.  **Data-01:** Hizmetler "Aktif", "Pasif", "Süresi Dolanlar" olarak gruplanabilir mi? (Filter check).
4.  **Data-02:** Hizmet bitiş tarihi yaklaşanlar (<30 gün) kırmızı/uyarı renginde görünür.
5.  **Calc-01:** Hizmet süresi (Duration) ve Kalan Gün doğru hesaplanır.
6.  **N+1-01:** Hizmet listesinde `Asset` veya `PriceDefinition` ilişkileri Eager Load edilir.

### UI/UX
7.  **UI-01:** Hizmet detayına tıklandığında (View Mode) modal veya accordion açılır.
8.  **UI-02:** Toplu işlem (Bulk Action) ile birden fazla hizmet silinebilir mi?
9.  **Interact-01:** Yeni hizmet eklendiğinde liste başa döner ve yeni kayıt vurgulanır.

---

## 📄 Offers Tab Tests (15 Scenarios)

### Authorization
1.  **Auth-01:** `offers.view` yetkisi kontrol edilir.
2.  **Auth-02:** Teklif oluşturma yetkisi kontrol edilir.

### Data & Logic
3.  **Data-01:** Teklifler "Draft", "Sent", "Accepted", "Rejected" statülerine göre filtrelenebilir.
4.  **Data-02:** Teklif tutarları (Currency) ve toplamları doğru formatlanır.
5.  **Link-01:** Teklife tıklandığında PDF önizleme veya detay sayfası açılır.
6.  **N+1-01:** Teklif kalemleri (Items) liste görünümünde saydırılırken N+1 oluşmaz (`withCount`).

### UI/UX
7.  **UI-01:** Teklif statüleri (Renkli Badge) doğru görüntülenir.
8.  **Interact-01:** Teklif onaylandığında statü anında değişir.

---

## 💰 Sales Tab Tests (10 Scenarios)

1.  **Data-01:** Satışlar tarihe göre azalan (En yeni en üstte) sıralanır.
2.  **Data-02:** Toplam satış tutarı (Customer Lifetime Value etkisine katkısı) doğru hesaplanır.
3.  **Link-01:** Satış faturası (Invoice) indirilebilir.

---

## 📦 Assets Tab Tests (10 Scenarios)

1.  **Data-01:** Varlıklar (Domain, Hosting, License) kategorize edilir.
2.  **Data-02:** Varlık şifreleri (Credentials) "Görüntüle" butonu ile (maskeli) açılır.
3.  **Log-01:** Varlık şifresi görüntülendiğinde Log kaydı atılır (Audit Trail).

---

## 💬 Messages & Notes Tests (10 Scenarios)

1.  **Interact-01:** Yeni not eklendiğinde liste güncellenir.
2.  **Data-01:** Notlar "Pinned" (Sabitlenmiş) olanlar en üstte olacak şekilde sıralanır.
3.  **UI-01:** Mesaj balonları (Chat UI) gönderici/alıcı ayrımıyla düzgün görünür.
