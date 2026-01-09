---
description: Muhurlenmiş olan verileri ve kodu geri getirir.
---

Görevin: Kullanıcı `/yukle` komutunu verdiğinde, sistemi daha önce `/muhurle` ile kaydedilmiş bir "güvenli noktaya" geri döndürmektir.

## 🔍 Arama ve Listeleme Protokolü
Kullanıcı `/yukle` dediğinde şu adımları izle:
1. `database/snapshots` klasöründeki mevcut `.sql` dosyalarını tarih sırasına göre listele. Geçmişe doğru 20 tane snapshotı listelemelisin. Kullanıcı bunlardan birini seçerek yükleme yapabilir.
2. Her snapshot ile eşleşen Git commit mesajlarını (mühür notlarını) kullanıcıya bir tablo olarak sun.
3. Kullanıcıdan bir numara veya dosya adı seçmesini bekle.

## ⏪ Geri Yükleme Operasyonu (Seçim Yapıldıktan Sonra)
Seçim yapıldığında şu komutları sırasıyla ve hatasız çalıştır:

1. **Kod Geri Sarma:** Git üzerinden ilgili commit'e dön: `git checkout [commit_id] -- .` (Mevcut dizindeki dosyaları o ana çek).
2. **DB Temizliği:** Mevcut tabloların çakışmaması için: `php artisan migrate:fresh` (Opsiyonel: Kullanıcıya sor).
3. **Snapshot Yükleme:** Seçilen yedek dosyasını veritabanına bas: `php artisan snapshot:load [secilen_snapshot_adi]`.
4. **Cache Reset:** `php artisan optimize:clear` (Eski versiyondan kalan cache'leri temizle).

## ⚠️ Kritik Uyarı (Diagnostic)
İşlem başlamadan önce kullanıcıyı uyar:
"DİKKAT: Şu anki kaydedilmemiş tüm değişiklikler silinecektir. Devam etmek istiyor musunuz?"

İşlem başarıyla bittiğinde: "🕒 ZAMAN MAKİNESİ ÇALIŞTI: Sistem [Tarih/Saat] noktasına başarıyla döndürüldü." mesajını ver.