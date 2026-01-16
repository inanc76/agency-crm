#!/bin/bash

# ═══════════════════════════════════════════════════════════════════════════
# 📊 DOSYA BOYUTU ANALİZ SCRIPT'İ
# ═══════════════════════════════════════════════════════════════════════════
# Kullanım: ./scripts/audit/analyze-file-sizes.sh

echo "🔍 Agency V10 Dosya Boyutu Analizi Başlıyor..."
echo ""

# Renkler
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

# Blade dosyaları analizi
echo "📄 BLADE DOSYALARI (400+ satır):"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

blade_violations=0
find resources/views/livewire -name "*.blade.php" -type f | while read file; do
    lines=$(wc -l < "$file")
    if [ "$lines" -gt 400 ]; then
        blade_violations=$((blade_violations + 1))
        percentage=$(( (lines - 400) * 100 / 400 ))
        echo -e "${RED}❌ $file${NC}"
        echo "   Satır: $lines (400 sınırını %$percentage aşıyor)"
        echo ""
    fi
done

echo ""
echo "📦 PHP TRAIT DOSYALARI (400+ satır):"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php_violations=0
find app/Livewire -name "*.php" -type f | while read file; do
    lines=$(wc -l < "$file")
    if [ "$lines" -gt 400 ]; then
        php_violations=$((php_violations + 1))
        echo -e "${RED}❌ $file${NC}"
        echo "   Satır: $lines"
        echo ""
    elif [ "$lines" -gt 350 ]; then
        echo -e "${YELLOW}⚠️  $file${NC}"
        echo "   Satır: $lines (Sınıra yakın)"
        echo ""
    fi
done

echo ""
echo "📊 İSTATİSTİKLER:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Blade istatistikleri
blade_total=$(find resources/views/livewire -name "*.blade.php" -type f | wc -l)
blade_lines=$(find resources/views/livewire -name "*.blade.php" -type f -exec wc -l {} \; | awk '{sum+=$1} END {print sum}')
blade_avg=$(echo "scale=0; $blade_lines / $blade_total" | bc)

echo "Blade Dosyaları:"
echo "  Toplam: $blade_total dosya"
echo "  Toplam Satır: $blade_lines"
echo "  Ortalama: $blade_avg satır/dosya"
echo ""

# PHP istatistikleri
php_total=$(find app/Livewire -name "*.php" -type f | wc -l)
php_lines=$(find app/Livewire -name "*.php" -type f -exec wc -l {} \; | awk '{sum+=$1} END {print sum}')
php_avg=$(echo "scale=0; $php_lines / $php_total" | bc)

echo "PHP Dosyaları:"
echo "  Toplam: $php_total dosya"
echo "  Toplam Satır: $php_lines"
echo "  Ortalama: $php_avg satır/dosya"
echo ""

echo "✅ Analiz tamamlandı!"
