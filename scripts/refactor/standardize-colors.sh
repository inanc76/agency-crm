#!/bin/bash

# ═══════════════════════════════════════════════════════════════════════════
# 🎨 RENK STANDARDİZASYON SCRIPT'İ
# ═══════════════════════════════════════════════════════════════════════════
# Kullanım: ./scripts/refactor/standardize-colors.sh
# Dikkat: Bu script dosyaları değiştirir. Önce git commit yapın!

echo "🎨 Renk Standardizasyonu Başlıyor..."
echo ""

# Güvenlik kontrolü
if [[ -n $(git status -s) ]]; then
    echo "⚠️  UYARI: Commit edilmemiş değişiklikler var!"
    echo "Devam etmek istiyor musunuz? (y/n)"
    read -r response
    if [[ ! "$response" =~ ^[Yy]$ ]]; then
        echo "❌ İşlem iptal edildi"
        exit 1
    fi
fi

echo "📝 Yedek oluşturuluyor..."
backup_dir="backups/color-refactor-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$backup_dir"
cp -r resources/views "$backup_dir/"
echo "✅ Yedek: $backup_dir"
echo ""

echo "🔄 gray → slate dönüşümü..."
find resources/views -name "*.blade.php" -type f -exec sed -i '' \
  -e 's/border-gray-50/border-slate-50/g' \
  -e 's/border-gray-100/border-slate-100/g' \
  -e 's/border-gray-200/border-slate-200/g' \
  -e 's/border-gray-300/border-slate-300/g' \
  -e 's/bg-gray-50/bg-slate-50/g' \
  -e 's/bg-gray-100/bg-slate-100/g' \
  -e 's/text-gray-400/text-slate-400/g' \
  -e 's/text-gray-500/text-slate-500/g' \
  -e 's/text-gray-600/text-slate-600/g' \
  -e 's/text-gray-700/text-slate-700/g' \
  -e 's/text-gray-900/text-slate-900/g' \
  {} \;

echo "🔄 zinc → slate dönüşümü (sidebar hariç)..."
find resources/views -name "*.blade.php" -type f \
  ! -path "*/layouts/app/sidebar.blade.php" \
  ! -path "*/layouts/app/header.blade.php" \
  -exec sed -i '' \
  -e 's/bg-zinc-50/bg-slate-50/g' \
  -e 's/bg-zinc-100/bg-slate-100/g' \
  -e 's/border-zinc-200/border-slate-200/g' \
  -e 's/border-zinc-300/border-slate-300/g' \
  -e 's/text-zinc-500/text-slate-500/g' \
  -e 's/text-zinc-600/text-slate-600/g' \
  {} \;

echo ""
echo "📊 Değişiklik Özeti:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Değişen dosya sayısı
changed_files=$(git diff --name-only | wc -l)
echo "Değişen dosya: $changed_files"

# Yeni renk dağılımı
gray_count=$(grep -r 'bg-gray-\|text-gray-\|border-gray-' resources/views --include="*.blade.php" | wc -l)
zinc_count=$(grep -r 'bg-zinc-\|text-zinc-\|border-zinc-' resources/views --include="*.blade.php" | wc -l)
slate_count=$(grep -r 'bg-slate-\|text-slate-\|border-slate-' resources/views --include="*.blade.php" | wc -l)

echo "gray-* kullanımı: $gray_count adet"
echo "zinc-* kullanımı: $zinc_count adet"
echo "slate-* kullanımı: $slate_count adet"

echo ""
echo "✅ Renk standardizasyonu tamamlandı!"
echo ""
echo "📝 Sonraki Adımlar:"
echo "1. Değişiklikleri kontrol edin: git diff"
echo "2. Uygulamayı test edin: php artisan serve"
echo "3. Commit yapın: git commit -am 'refactor: Renk paleti slate'e standardize edildi'"
