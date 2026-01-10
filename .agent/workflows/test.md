---
description: Mimarın Test Dashboard'u - Envanter ve Tarihçe
---

# Test Dashboard Protokolü

Bu iş akışı `/test` komutuyla tetiklenir.

## 1. Veri Toplama
Önce test envanterini ve geçmişini çek:
// turbo
`php tests/Manager/test_manager.php inventory`

## 2. Dashboard Sunumu
Çıktıyı (JSON) analiz et ve kullanıcıya şu formatta sun:

```text
📊 GÜNCEL TEST DURUMU [Toplam Defined Senaryo (MD): X]
1. Tümünü Çalıştır ......... [X Defined / Y Coded / Z System Tests]
2. Teklif Modülü ........... [A Defined / B Coded] (CreateOfferTest.php)
3. [Diğer Modüller...]

📅 SON TEST GEÇMİŞİ
[Tarih]: [Modül] ([Sonuç])
...
```

## 3. Seçim ve Çalıştırma
Kullanıcı '2' (veya OffersCreate) seçerse:
1. `php artisan test --filter CreateOfferTest` komutunu çalıştır.
2. Sonuca göre log kaydı oluştur:
   // turbo
   `php tests/Manager/test_manager.php log "Offers/Create" "SUCCESS/FAIL" "Detay metni"`
3. Sonuçları rapola.

Bu dosya bir yol haritasıdır. Agent, bu adımları takip ederek dashboard'u oluşturur ve sunar.
