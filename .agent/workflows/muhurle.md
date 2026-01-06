---
description: Veritabanı snapshot alır ve git push yapar
---

## 🚀 Operasyonel İş Akışı
Kullanıcı `/muhurle "not"` dediğinde şu sırayla terminal komutlarını tetikle:

1. **DB Snapshot:** `php artisan snapshot:create "backup-[Tarih]"` (Veritabanının o anki fotoğrafını çek).
2. **Git Add:** `git add .` (SQL yedeği dahil her şeyi sahneye al).
3. **Git Commit:** `git commit -m "[Mühür]: {not}"` (Kod ve veriyi aynı ana mühürle).
4. **Git Push:** `git push origin [current-branch]` (Zırhlı yedeği buluta gönder).

## ⚠️ Güvenlik Denetimi (AI Diagnostic)
İşlem bitince şu raporu ver:
- [X] Database Audit: Snapshot `database/snapshots` içine kaydedildi.
- [X] Git Audit: Commit hash üretildi ve uzak sunucuya iletildi.
- [X] Restore Point: "Geri dönmek için: git checkout [hash] && php artisan snapshot:load [name]" bilgisini yaz.

"BU AŞAMA MÜHÜRLENMİŞTİR." mesajı ile işlemi sonlandır.