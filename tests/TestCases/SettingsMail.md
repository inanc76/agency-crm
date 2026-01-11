# Test Cases: SettingsMail

## 1. Overview
Module: **Mail Settings**
Target Component: `livewire/settings/mail.blade.php`
Goal: ensure robust SMTP/Mailgun configuration and testing.

## 2. Critical Test Scenarios (25 Items)

### A. Provider Selection
1. [UI] Provider dropdown should default to 'smtp'.
2. [UI] Switching to 'mailgun' should hide SMTP fields and show Mailgun fields.
3. [UI] Switching back to 'smtp' should restore SMTP fields.
4. [Logic] Saving with 'smtp' selected should ignore Mailgun fields in backend validation validation.
5. [Logic] Saving with 'mailgun' selected should ignore SMTP fields validation.

### B. SMTP Configuration
6. [Validation] `smtp_host` is required when provider is SMTP.
7. [Validation] `smtp_port` must be integer (e.g. 587, 465).
8. [Validation] `smtp_port` should not accept strings or floats.
9. [Security] `smtp_password` should be masked in UI.
10. [Validation] `smtp_from_email` must be a valid email format.
11. [Logic] Toggle `smtp_secure` (TLS/SSL) updates the state.
12. [Edge Case] Entering IP address instead of domain in `smtp_host` works.

### C. Mailgun Configuration
13. [Validation] `mailgun_api_key` is required when provider is Mailgun.
14. [Validation] `mailgun_domain` is required.
15. [Validation] `mailgun_region` defaults to 'US' or 'EU'.

### D. Connection Testing
16. [UI] "Test Connection" button should be disabled if form is invalid.
17. [Logic] Clicking "Test Connection" triggers `sendTestEmail`.
18. [UI] Test Modal shows "Sending..." spinner during request.
19. [Logic] Successful test shows Green Toast message.
20. [Logic] Failed test shows Red Toast with error details (not generic error).
21. [Data] `test_email` field pre-fills with current user email.
22. [Validation] `test_email` must be valid before sending.

### E. Persistence & Security
23. [DB] Saved settings persist in `mail_settings` table.
24. [Security] Password/API Key are encrypted in DB (if applicable) or assumed safe.
25. [Audit] Changing settings logs an activity event "Mail Settings Updated".
