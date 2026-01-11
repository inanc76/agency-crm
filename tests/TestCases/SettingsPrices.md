# Test Cases: SettingsPrices

## 1. Overview
Module: **Prices Settings** (Fiyat Tanımları)
Target Component: `livewire/settings/prices.blade.php`
Goal: verify CRUD operations for Service Reference Prices.

## 2. Critical Test Scenarios (25 Items)

### A. List & Filter
1. [UI] Table loads with existing price definitions.
2. [Search] Typing in search box filters by Name.
3. [Search] Search filters by Description.
4. [Filter] Selecting Category filters the list correctly.
5. [Filter] Selecting Duration (Yearly/Monthly) filters the list.
6. [Pagination] List paginates correctly if items > 10.

### B. Creation (New Price)
7. [Action] Clicking "Yeni Ekle" opens modal.
8. [Validation] `name` is required.
9. [Validation] `price` must be numeric and >= 0.
10. [Validation] `currency` defaults to TRY.
11. [Validation] `category` selection is mandatory.
12. [Validation] `duration` (Monthly, Yearly, OTP) is required.
13. [Logic] Creating a duplicate name (if restricted) shows error.
14. [Success] Valid form closes modal and refreshes list.
15. [Toast] "Kayıt Başarılı" toast appears.

### C. Editing
16. [Action] Clicking Edit icon loads data into modal.
17. [Validation] Updating price to negative value fails.
18. [Logic] Changing category updates the DB record correctly.
19. [UI] Cancel button closes modal without saving.
20. [Concurrency] fast double-click on Save doesn't create duplicates.

### D. Deletion & Status
21. [Action] Clicking Delete icon shows confirmation dialog.
22. [Logic] Confirming delete removes record from DB (soft delete or hard delete).
23. [Logic] "Aktif/Pasif" toggle updates `is_active` status immediately.
24. [UI] Passive items appear grayed out or marked in table.

### E. Reference Data Integrity
25. [Integrity] Deleting a category from Reference Data that is used here should be handled (warn or restrict).
