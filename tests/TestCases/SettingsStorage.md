# Test Cases: SettingsStorage

## 1. Overview
Module: **Storage Settings**
Target Component: `livewire/settings/storage.blade.php`
Goal: Validate S3/Minio connection, bucket operations, and error handling.

## 2. Critical Test Scenarios (25 Items)

### A. Endpoint & Protocol
1. [Validation] `endpoint` is required.
2. [Logic] `endpoint` input strips 'http://' or 'https://' prefixes automatically on save/test.
3. [Logic] Trailing slashes in `endpoint` are removed.
4. [UI] `use_ssl` toggle correctly switches between HTTP/HTTPS logic in backend.
5. [Validation] `port` must be a valid integer (e.g. 9000, 443).

### B. Authentication
6. [Validation] `access_key` is required.
7. [Validation] `secret_key` is required.
8. [Security] `secret_key` input type is 'password' (masked).
9. [Logic] Updating keys updates the `Storage` config facade at runtime for testing.

### C. Bucket Configuration
10. [Validation] `bucket_name` is required.
11. [Validation] `bucket_name` supports alphanumeric and dashes.
12. [Error] Invalid bucket name format returns detailed error.

### D. Connection Test
13. [Action] Clicking "Bağlantıyı Test Et" triggers `testConnection`.
14. [UI] Loading state displays on button during test.
15. [Logic] Test attempts to `listObjects` or `headBucket`.
16. [Success] Valid credentials return "Bağlantı Başarılı" toast.
17. [Failure] Invalid Endpoint returns "Connection Reserved/Refused" or generic network error.
18. [Failure] Invalid Keys return "Access Denied" (403).
19. [Failure] Non-existent Bucket returns "Bucket Not Found" (404).
20. [UI] Error message is displayed in a red alert box, not just a toast.

### E. CRUD & Persistence
21. [DB] Settings are saved to `storage_settings` (or equivalent) table or JSON file.
22. [Logic] "Sıfırla" (Reset) clears the form to defaults.
23. [Logic] Changing settings does NOT affect existing file URLs until cache clear/config reload.
24. [Edge Case] Endpoint with IP address (e.g. 192.168.1.1) works.
25. [Multi-Environment] Localhost vs Production endpoint behavior.
