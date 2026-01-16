@script
<script>
    Alpine.data('unsavedChangesWatcher', () => ({
        isDirty: false,

        init() {
            // Warn on browser close / refresh
            window.addEventListener('beforeunload', (e) => {
                if (this.isDirty) {
                    e.preventDefault();
                    e.returnValue = 'Kaydedilmemiş değişiklikleriniz var. Çıkmak istediğinize emin misiniz?';
                }
            });

            // Warn on internal Livewire navigation (if using wire:navigate)
            document.addEventListener('livewire:navigate', (event) => {
                if (this.isDirty && !confirm('Kaydedilmemiş değişiklikleriniz var. Çıkmak istediğinize emin misiniz?')) {
                    event.preventDefault();
                }
            });
        },

        markDirty() {
            this.isDirty = true;
        },

        markClean() {
            this.isDirty = false;
        }
    }));
</script>
@endscript