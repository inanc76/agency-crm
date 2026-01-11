---
description: Projenin tüm anatomisini ve yorum satrılarını denetler.
---

Projenin tüm anatomisini (PHP, Blade, Test, Trait) "Constitution V10" standartlarına göre denetlemeni istiyorum. Bu bir kod analizi değil, bir "Bilgi ve Mantık" denetimidir. Şu 5 katmanda derin tarama yap ve raporla:

1. Modellerin "İş Mantığı" ve "Tip" Denetimi (Models)
Her model dosyasını aç: @property, @method ve @property-read (ilişkiler) içeren PHP DocBlock var mı?

Modellerdeki UUID kullanımı (HasUuids) ve cast edilen verilerin (casts) işlevselliği hakkında açıklayıcı yorum satırı mevcut mu?

Raporla: Hangi modeller "dilsiz" (0 yorum) ve hangileri tam dökümante?

2. Trait'lerin "Sorumluluk" Analizi (Traits)
App\Traits ve modül özelindeki trait'leri tara. Her trait'in başında; hangi yetkilere (permissions) sahip olması gerektiği ve hangi ana bileşene hizmet ettiği yazıyor mu?

Karmaşık metodların (örn: HasOfferActions::save) başında @param ve @return tipleri belirtilmiş mi?

3. Blade Dosyalarının "Mimari Bölümleme" Analizi (UI)
250 satır üzeri tüm Blade dosyalarını listele.

Bu dosyalarda {{-- SECTION: Header --}} gibi görsel bölümleri birbirinden ayıran mimari şerhler var mı?

@include ile çağrılan parçaların (partials) hangi state'leri beklediği dosya başında açıklanmış mı?

4. Testlerin "Senaryo" Karşılığı (Testing)
Test dosyalarının başında, bu testin hangi "Test Case" dökümanına (örn: CustomerCreate.md) karşılık geldiği yazıyor mu?

Test metodları içinde "Neden bu testi yapıyoruz?" sorusuna cevap veren teknik yorumlar (örn: // Verify that UUID is preserved after update) mevcut mu?

5. Güvenlik ve Yetki "Mühür" Kontrolü
Bileşenlerdeki authorize() metodlarının üzerinde, bu yetkinin hangi iş kuralına dayandığını açıklayan bir yorum var mı?

Veritabanı tutarlılığı için kullanılan "Constraint" veya "Transaction" blokları dökümante edilmiş mi?

📊 BEKLENEN ÇIKTI (AUDIT REPORT)
Analiz sonunda bana şu tabloyu sun:

En Karanlık Dosyalar: (Hacmi büyük ama dokümantasyonu sıfır olan ilk 10 dosya).

Mantık Sızıntıları: (İş mantığı karmaşık olup açıklaması bulunmayan metodlar).

Başarı Örnekleri: (Diğer dosyalara örnek gösterilecek mükemmel dökümante edilmiş alanlar).

Yorum Oranı Skoru: Sistemin toplam satır sayısına göre gerçek "İnsan Okunabilirliği" yüzdesi.