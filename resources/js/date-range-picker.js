// Flatpickr Date Range Picker for Linear-style UI
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

// Register Alpine.js component
document.addEventListener('alpine:init', () => {
    Alpine.data('dateRangePicker', (config = {}) => ({
        startDate: config.startDate || null,
        endDate: config.endDate || null,
        picker: null,
        displayText: '',

        init() {
            this.updateDisplayText();

            // Validation and Formatting helpers checks

            this.$nextTick(() => {
                this.picker = flatpickr(this.$refs.calendar, {
                    mode: 'range',
                    inline: false,
                    dateFormat: 'Y-m-d',
                    defaultDate: [this.startDate, this.endDate],
                    locale: {
                        firstDayOfWeek: 1,
                        weekdays: {
                            shorthand: ['Pz', 'Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct'],
                            longhand: ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi']
                        },
                        months: {
                            shorthand: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'],
                            longhand: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık']
                        }
                    },
                    onChange: (selectedDates) => {
                        if (selectedDates.length === 2) {
                            // Update properties (triggers Entangle -> Livewire)
                            this.startDate = this.formatDate(selectedDates[0]);
                            this.endDate = this.formatDate(selectedDates[1]);
                            this.updateDisplayText();
                        }
                    }
                });

                // Watch for external changes (from Livewire)
                this.$watch('startDate', (value) => this.syncPicker());
                this.$watch('endDate', (value) => this.syncPicker());
            });
        },

        syncPicker() {
            if (!this.picker) return;

            // Avoid infinite loops by checking if dates match currently selected
            const currentStart = this.picker.selectedDates[0] ? this.formatDate(this.picker.selectedDates[0]) : null;
            const currentEnd = this.picker.selectedDates[1] ? this.formatDate(this.picker.selectedDates[1]) : null;

            if (this.startDate !== currentStart || this.endDate !== currentEnd) {
                if (this.startDate && this.endDate) {
                    this.picker.setDate([this.startDate, this.endDate], false); // false = no onChange trigger
                } else {
                    this.picker.clear();
                }
                this.updateDisplayText();
            }
        },

        formatDate(date) {
            if (!date) return null;
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },

        formatDisplayDate(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            const months = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
            return `${date.getDate()} ${months[date.getMonth()]}, ${date.getFullYear()}`;
        },

        updateDisplayText() {
            if (this.startDate && this.endDate) {
                this.displayText = `${this.formatDisplayDate(this.startDate)} → ${this.formatDisplayDate(this.endDate)}`;
            } else if (this.startDate) {
                this.displayText = this.formatDisplayDate(this.startDate);
            } else {
                this.displayText = 'Tarih Aralığı Seçin';
            }
        },

        open() {
            if (this.picker) {
                this.picker.open();
            }
        },

        setDates(start, end) {
            this.startDate = start;
            this.endDate = end;
            this.updateDisplayText();

            if (this.picker) {
                if (start && end) {
                    this.picker.setDate([start, end], true); // true to trigger onChange
                } else {
                    this.picker.clear();
                }
            }
        }
    }));
});
