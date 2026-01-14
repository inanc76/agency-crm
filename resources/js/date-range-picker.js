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

            this.$nextTick(() => {
                this.picker = flatpickr(this.$refs.calendar, {
                    mode: 'range',
                    inline: false,
                    dateFormat: 'Y-m-d',
                    defaultDate: this.startDate && this.endDate
                        ? [this.startDate, this.endDate]
                        : null,
                    locale: {
                        firstDayOfWeek: 1,
                        weekdays: {
                            shorthand: ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'],
                            longhand: ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi']
                        },
                        months: {
                            shorthand: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'],
                            longhand: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık']
                        }
                    },
                    onChange: (selectedDates) => {
                        if (selectedDates.length === 2) {
                            this.startDate = this.formatDate(selectedDates[0]);
                            this.endDate = this.formatDate(selectedDates[1]);
                            this.updateDisplayText();

                            // Dispatch to Livewire
                            this.$dispatch('date-range-updated', {
                                start: this.startDate,
                                end: this.endDate
                            });
                        }
                    }
                });
            });
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
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
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
        }
    }));
});
