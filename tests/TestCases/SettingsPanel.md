# Test Cases: SettingsPanel

## 1. Overview
Module: **Panel Settings** (Theme & Design)
Target Component: `livewire/settings/panel.blade.php`
Goal: validate Theming, UI Customization, and Style Guide.

## 2. Critical Test Scenarios (25 Items)

### A. Color Customization (General)
1. [Validation] Color inputs must match HEX regex (`/^#[0-9A-Fa-f]{6}$/`).
2. [UI] Color picker updates the input value.
3. [Preview] Changing `header_bg_color` updates the "Header Preview" box instantly.
4. [Preview] Changing `sidebar_bg_color` updates the "Sidebar Preview" box instantly.
5. [Logic] Saving colors persists to DB.

### B. Font & Typography
6. [UI] Font Family dropdown lists available fonts (Inter, Roboto, etc.).
7. [Preview] Changing font updates "Typography Preview" section.
8. [Validation] Font Size inputs (Label, Input, Heading) have min/max limits.
9. [Logic] `input_border_radius` accepts 'px' or 'rem' values (or is a select).

### C. Button Granular Settings
10. [UI] Create Button colors (Bg, Text) are editable.
11. [Preview] "Buton Önizleme" section reflects changes to Create/Edit/Delete buttons.
12. [Logic] `btn_save_bg_color` changes apply to the actual Save button in the form (live update check).

### D. Layout Settings
13. [UI] `header_border_width` slider/input works (0-20px).
14. [Logic] `header_border_color` handles 'transparent' correctly.
15. [UI] `activeTab` switches between "Tema Ayarları" and "Tasarım Rehberi".
16. [UI] Style Guide tabs (Previews 1-12) expand/collapse correctly.

### E. Actions
17. [Action] "Kaydet" (Save) shows Success Toast.
18. [Action] "Varsayılana Dön" (Reset) opens confirmation or executes immediately.
19. [Logic] Reset command restores hardcoded default colors defined in Class.
20. [Cache] Saving clears `theme_settings` cache.

### F. Responsiveness & Errors
21. [UI] Review settings page on Mobile view (Sidebar hidden?).
22. [UI] Validation errors (e.g. invalid hex) show under the specific input.
23. [Edge Case] Empty color field defaults to black or previous value? (Should validate required).
24. [Perf] Style Guide renders efficiently (no lag with 12 previews).
25. [Visual] Dark mode overrides (if any) or interference checks.
