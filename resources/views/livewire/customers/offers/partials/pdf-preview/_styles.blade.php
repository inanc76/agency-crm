{{--
🎨 PARTIAL: PDF Style Definitions
---------------------------------------------------------------------
PDF render motoru (DomPDF/Browsershot) için hayati CSS kuralları.

@architect-note [DomPDF Limitation & Media Print]:
- `display: flex` ve `grid` özellikleri PDF motorlarında bazen hatalı çalışır.
- `@media print` bloğu içindeki kurallar, sayfa yazdırılırken veya PDF'e
dönüştürülürken devreye girer.
- `.w-3/12` (Sağ sidebar) gizlenerek ana içeriğin `.w-9/12 -> 100%` genişliğe
ulaşması sağlanır. Bu, kağıt boyutunu tam kullanmak için kritiktir.
---------------------------------------------------------------------
--}}
<style>
    @media print {
        .w-3/12 {
            display: none !important;
        }

        .w-9/12 {
            width: 100% !important;
        }
    }
</style>