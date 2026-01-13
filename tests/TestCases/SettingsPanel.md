# Test Cases: SettingsPanel

## 1. Overview
Module: **Panel Settings** (Theme & Design)
Target Component: `livewire/settings/panel.blade.php` (including Atomic Sub-components)
Goal: validate Theming, UI Customization, and Style Guide. - ALL TESTS PASSING

## 2. Critical Test Scenarios (Completed items marked)

### A. Color Customization (General)
1. [x] [Validation] Color inputs must match HEX regex in Header component.
2. [x] [UI] Color picker updates the input value.
3. [x] [Logic] Saving colors persists to DB.
4. [x] [Logic] Dashboard stats colors persistence.

### B. Font & Typography
5. [x] [UI] Font Family settings persistence.
6. [x] [Validation] Font Size inputs have min/max limits (8-72px).
7. [x] [Logic] Heading font size persistence.

### C. Button Granular Settings
8. [x] [UI] Create/Save/Cancel Button colors are correctly saved to DB.
9. [x] [Logic] Persistence checks for all button color variables.

### D. Layout Settings
10. [x] [UI] `header_border_width` validation (0-20px).
11. [x] [UI] `activeTab` switches between "Tema Ayarları" and "Tasarım Rehberi" via URL.

### E. Actions
12. [x] [Action] "Kaydet" (Save) functionality across all atomic components.
13. [x] [Action] "Varsayılana Dön" (Reset) restores default values.
14. [x] [Cache] Saving clears `theme_settings` cache.
15. [x] [Event] `theme-updated` event is dispatched on save.

### F. Edge Cases
16. [x] [Logic] Persistence across session (Model based).
17. [x] [Validation] Invalid HEX values blocked.
18. [x] [Validation] Multi-component save sync.
