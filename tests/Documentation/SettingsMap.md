# Settings Architecture Map & Dependency Analysis

## 1. Overview
Bu döküman, Panel ve Ayarlar modüllerinin mevcut teknik yapısını, değişken haritasını ve gelecekteki modüler mimarisini tanımlar.
Analiz `resources/views/livewire/settings/panel.blade.php` dosyası (1154 satır) ve ilişkili diğer ayar modülleri üzerinde yapılmıştır.

## 2. Mevcut Dosya Analizi: `settings/panel.blade.php`
**Konum:** `resources/views/livewire/settings/panel.blade.php`
**Tip:** Single-File Volt Component
**Satır Sayısı:** 1154

### 2.1. Property Gruplandırması (Mapping)

Aşağıdaki değişkenler incelenmiş ve ilgili alanlara atanmıştır:

#### A. Variables (Kimlik & Genel Ayarlar)
*Bu değişkenler `SettingsVariables` modülüne taşınabilir veya "Panel Kimliği" olarak kalabilir.*
- `public string $site_name` (Default: 'MEDIACLICK')
- `public $favicon`
- `public $logo`
- `public float $logo_scale`
- `public ?string $current_favicon_path`
- `public ?string $current_logo_path`

#### B. Panel (Tema & Tasarım - Core)
*Bu değişkenler `SettingsPanel` (veya `SettingsTheme`) altında yönetilmelidir.*
- `public string $activeTab` ('theme' | 'style-guide')
- `public string $font_family`
- `public string $page_bg_color`
- `public string $base_text_color`
- `public string $heading_color`

**Layout Colors:**
- `public string $header_bg_color`, `$header_border_color`, `$header_border_width`, `$header_icon_color`
- `public string $menu_bg_color`, `$menu_text_color`
- `public string $sidebar_bg_color`, `$sidebar_text_color`, `$sidebar_hover_bg_color`, `$sidebar_hover_text_color`, `$sidebar_active_item_bg_color`
- `public string $header_active_item_bg_color`, `$header_active_item_text_color`

**UI Elements:**
- `public string $card_bg_color`, `$card_border_color`, `$card_border_radius` (12px)
- `public string $table_hover_bg_color`, `$table_hover_text_color`
- `public string $list_card_bg_color`, `$list_card_border_color`, `$list_card_link_color`
- `public string $table_avatar_bg_color`, `$table_avatar_border_color`, `$table_avatar_text_color`
- `public string $notification_badge_color`
- `public string $avatar_gradient_start_color`, `$avatar_gradient_end_color`
- `public string $dropdown_header_bg_start_color`, `$dropdown_header_bg_end_color`

**Dashboard Specific:**
- `public string $dashboard_card_bg_color`, `$dashboard_card_text_color`
- `public string $dashboard_stats_1_color`, `$dashboard_stats_2_color`, `$dashboard_stats_3_color`

#### C. Input & Buttons (Granular Design)
*Bu değişkenler de Tema/Panel altına aittir.*
**Inputs:**
- `$input_focus_ring_color`, `$input_border_color`, `$input_vertical_padding`, `$input_border_radius`
- `$input_error_ring_color`, `$input_error_border_color`, `$input_error_text_color`

**Typography Sizes:**
- `$label_font_size`, `$input_font_size`, `$heading_font_size`, `$error_font_size`, `$helper_font_size`

**Buttons (Create, Edit, Delete, Cancel, Save):**
- Her buton tipi için: `*_bg_color`, `*_text_color`, `*_hover_color`, `*_border_color`

### 2.2. Metodlar
- `mount(PanelSettingRepository $repository)`: Mevcut ayarları DB'den yükler.
- `save()`: Tüm değişkenleri doğrular ve `PanelSettingRepository` üzerinden kaydeder. Cache'i temizler.
- `resetToDefaults()`: Ayarları sıfırlar.

## 3. Diğer Modüller (External Modules)
Proje mimarisinde şu modüller ayrı dosyalar olarak tespit edilmiştir:

1.  **Mail Settings** (`settings/mail.blade.php`)
    -   SMTP ve Mailgun ayarları.
    -   `MailSetting` modelini kullanır.
2.  **Storage Settings** (`settings/storage.blade.php`)
    -   Minio/S3 bağlantı ayarları.
    -   Prop: `endpoint`, `access_key`, `secret_key`, `bucket_name`.
3.  **Prices Settings** (`settings/prices.blade.php`)
    -   Hizmet ve ürün fiyatlandırma.
    -   `PriceDefinition` modelini kullanır.
4.  **Variables Module** (`variables/index.blade.php`)
    -   Sistem değişkenleri (Referans Data).
    -   Rota: `dashboard/settings/variables`.

## 4. Gelecek Mimari & Identity Cards

Her modül için "Identity Card" (Kimlik Kartı) ve Trait yapısı önerilmiştir:

### A. SettingsMail
- **Trait:** `HasMailSettings`
- **Identity:** Mail sunucu yapılandırması ve test metodları.
- **Security:** Şifreli saklama (Encryption).

### B. SettingsStorage
- **Trait:** `HasStorageSettings`
- **Identity:** S3/Minio bağlantı ve bucket yönetimi.
- **Safety:** Bağlantı testi zorunluluğu.

### C. SettingsPrices
- **Trait:** `HasPriceDefinitions`
- **Identity:** Dinamik fiyat ve para birimi yönetimi.

### D. SettingsVariables
- **Trait:** `HasSystemVariables`
- **Identity:** Site kimliği (Logo, İsim) ve genel referanslar.
- **Migration Plan:** `panel.blade.php` içindeki `site_name` ve `logo` buraya taşınabilir.

### E. SettingsPanel (Theme)
- **Trait:** `HasThemeSettings`
- **Identity:** Sadece görsel tasarım, renkler ve fontlar.
- **Structure:** `panel.blade.php` sadeleştirilerek sadece tema ayarlarını ve style-guide'ı içermelidir.
