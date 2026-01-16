#!/bin/bash

# ═══════════════════════════════════════════════════════════════════════════
# 🎨 INLINE STYLE KONTROL SCRIPT'İ
# ═══════════════════════════════════════════════════════════════════════════
# Kullanım: ./scripts/audit/check-inline-styles.sh

echo "🎨 Inline Style Kullanımı Analizi..."
echo ""

RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m'

total_violations=0

echo "📄 INLINE STYLE KULLANIMI:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# style= araması
grep -r 'style=' resources/views --include="*.blade.php" | while IFS=: read -r file line; do
    total_violations=$((total_violations + 1))
    echo -e "${RED}❌ $file${NC}"
    echo "   $line"
    echo ""
done

echo ""
echo "🎨 RENK PALETİ TUTARSIZLIĞI:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# gray kullanımı
gray_count=$(grep -r 'bg-gray-\|text-gray-\|border-gray-' resources/views --include="*.blade.php" | wc -l)
echo "gray-* kullanımı: $gray_count adet"

# zinc kullanımı
zinc_count=$(grep -r 'bg-zinc-\|text-zinc-\|border-zinc-' resources/views --include="*.blade.php" | wc -l)
echo "zinc-* kullanımı: $zinc_count adet"

# slate kullanımı (hedef)
slate_count=$(grep -r 'bg-slate-\|text-slate-\|border-slate-' resources/views --include="*.blade.php" | wc -l)
echo "slate-* kullanımı: $slate_count adet"

echo ""
echo "📊 ÖNERİ:"
if [ "$gray_count" -gt 0 ] || [ "$zinc_count" -gt 0 ]; then
    echo -e "${YELLOW}⚠️  Renk standardizasyonu gerekli${NC}"
    echo "   Hedef: Tüm gray/zinc → slate"
    echo "   Script: ./scripts/refactor/standardize-colors.sh"
else
    echo -e "${GREEN}✅ Renk paleti tutarlı${NC}"
fi

echo ""
echo "✅ Analiz tamamlandı!"
