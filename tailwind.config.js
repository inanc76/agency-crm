/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./vendor/robsontenorio/mary/src/View/Components/**/*.php",
        "./vendor/livewire/flux-pro/stubs/**/*.blade.php",
        "./vendor/livewire/flux/stubs/**/*.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                // ===== SKIN PALETTE =====
                // These map directly to CSS variables for full theme control
                skin: {
                    heading: 'var(--color-text-heading)',
                    base: 'var(--color-text-base)',
                    muted: 'var(--color-text-muted)',
                    primary: 'var(--brand-primary)',
                    success: 'var(--brand-success)',
                    danger: 'var(--color-danger-base)',
                    'danger-muted': 'var(--color-danger-muted-base)',
                    info: 'var(--color-info)',
                },
                // Card system
                card: {
                    DEFAULT: 'var(--card-bg)',
                    border: 'var(--card-border)',
                },
                // Input system
                input: {
                    border: 'var(--input-border)',
                    focus: 'var(--input-focus-ring)',
                    error: 'var(--input-error-border)',
                },
                // Dropdown system
                dropdown: {
                    DEFAULT: 'var(--dropdown-bg)',
                    border: 'var(--dropdown-border)',
                    hover: 'var(--dropdown-item-hover)',
                    'hover-text': 'var(--dropdown-item-hover-text)',
                    danger: 'var(--dropdown-danger-text)',
                    'danger-hover': 'var(--dropdown-danger-hover)',
                },
                // Avatar system
                avatar: {
                    DEFAULT: 'var(--avatar-bg)',
                    table: 'var(--table-avatar-bg)',
                    'table-border': 'var(--table-avatar-border)',
                    'table-text': 'var(--table-avatar-text)',
                },
                // Error panel system
                'error-panel': {
                    DEFAULT: 'var(--error-panel-bg)',
                    border: 'var(--error-panel-border)',
                    text: 'var(--error-panel-text)',
                },
            },
            textColor: {
                skin: {
                    heading: 'var(--color-text-heading)',
                    base: 'var(--color-text-base)',
                    muted: 'var(--color-text-muted)',
                    primary: 'var(--brand-primary)',
                    success: 'var(--brand-success)',
                    danger: 'var(--color-danger-base)',
                    link: 'var(--action-link-color)',
                },
            },
            backgroundColor: {
                skin: {
                    page: 'var(--page-bg)',
                    card: 'var(--card-bg)',
                    'table-hover': 'var(--table-hover-bg)',
                    'danger-muted': 'var(--color-danger-muted-base)',
                },
            },
            borderColor: {
                skin: {
                    light: 'var(--card-border)',
                    input: 'var(--input-border)',
                    danger: 'var(--color-danger-base)',
                },
            },
            fontFamily: {
                sans: ['var(--font-main)', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            borderRadius: {
                card: 'var(--card-radius)',
                input: 'var(--input-radius)',
            },
        },
    },
    plugins: [],
}
