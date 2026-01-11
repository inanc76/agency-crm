# Test Cases: SettingsVariables

## 1. Overview
Module: **Variables Settings** (Değişken Yönetimi / Site Identity)
Target Components: `livewire/settings/panel.blade.php` (Identity Section) & `livewire/variables/index.blade.php`
Goal: Manage Global Config (Site Name, Logo) and System Reference Variables.

## 2. Critical Test Scenarios (25 Items)

### A. Site Identity (Site Name)
1. [Validation] `site_name` is required.
2. [Validation] `site_name` max length check (e.g. 255 chars).
3. [UI] `site_name` input reflects current DB value on load.
4. [Logic] Updating `site_name` updates the global header title immediately (after refresh).
5. [XSS] Entering HTML/Script in `site_name` is sanitized.

### B. Logo Management
6. [Validation] Uploading non-image file fails.
7. [Validation] Uploading image > 2MB fails.
8. [Preview] Uploaded image shows immediate preview.
9. [Logic] `logo` file is stored in public disk/storage.
10. [Logic] `logo_scale` slider adjusts the preview size in real-time.
11. [Logic] Saving stores the new logo path.

### C. Favicon Management
12. [Validation] Uploading `.ico` or `.png` works.
13. [Validation] Favicon size limit (e.g. 512KB).
14. [Success] "Favicon Updated" toast appears.

### D. Reference Variables (Variables Index)
15. [UI] List of system variables (Categories, Statuses) loads.
16. [Action] "Yeni Değişken" opens creation modal.
17. [Validation] Key/Value pair validation.
18. [Logic] `default` flag marks the variable as default selection.
19. [Logic] Only one item can be `default` per group (Check logic).
20. [Search] Search filters variable list.

### E. Theme Independence
21. [Logic] Changing Identity settings does NOT reset Panel Theme colors.
22. [Logic] Resetting Theme to defaults does NOT delete the Logo.
23. [UI] Variables page is accessible via `/dashboard/settings/variables`.
24. [DB] Variables are stored in `panel_settings` (Identity) or `reference_items` (Vars).
25. [Role] Only Admin can access these settings.
