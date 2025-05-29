(function(window){
    function closeModal(id){
        const m = document.getElementById(id);
        if (!m) return;
        m.style.display = 'none';
        // reset tất cả các form trong modal
        m.querySelectorAll('form').forEach(f => f.reset());
        // xoá mọi lỗi
        m.querySelectorAll('.error-message').forEach(err => err.textContent = '');
        // gỡ class visible của company-fields
        m.querySelectorAll('.company-fields.visible').forEach(cf => cf.classList.remove('visible'));
    }

    /**
     * Opens a modal dialog by ID.
     */
    function openModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.style.display = 'block';
    }

    /**
     * Formats a date string 'YYYY-MM-DD HH:MM:SS' to 'DD/MM/YYYY'.
     */
    function formatDate(datetime) {
        if (!datetime) return '-';
        const [date] = datetime.split(' ');
        const parts = date.split('-');
        if (parts.length !== 3) return date;
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }

    /**
     * Formats a date string or Date object to 'DD/MM/YYYY HH:MM'.
     */
    function formatDateTime(datetimeInput) {
        if (!datetimeInput) return '-';

        let dateObj;
        if (typeof datetimeInput === 'string') {
            // Handles 'YYYY-MM-DD HH:MM:SS' or 'YYYY-MM-DD'.
            // Replace hyphens in date part with slashes for broader compatibility if it's a string,
            // especially for Date constructor, but ensure it's a valid format.
            // A more robust solution might involve a library if formats are very diverse.
            let normalizedDateTimeString = datetimeInput;
            if (datetimeInput.length === 10 && datetimeInput.includes('-')) { // YYYY-MM-DD
                normalizedDateTimeString = datetimeInput + 'T00:00:00'; // Treat as local time
            }
            dateObj = new Date(normalizedDateTimeString);
        } else if (datetimeInput instanceof Date) {
            dateObj = datetimeInput;
        } else {
            return String(datetimeInput); // Not a string or Date, return as is or handle error
        }

        if (isNaN(dateObj.getTime())) { // Check if date is valid
             // If parsing failed, return original string or a placeholder
            return (typeof datetimeInput === 'string') ? datetimeInput : '-';
        }

        const day = String(dateObj.getDate()).padStart(2, '0');
        const month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed
        const year = dateObj.getFullYear();
        const hours = String(dateObj.getHours()).padStart(2, '0');
        const minutes = String(dateObj.getMinutes()).padStart(2, '0');

        return `${day}/${month}/${year} ${hours}:${minutes}`;
    }

    /**
     * Formats a number to currency string (VND).
     * @param {number|string} amount The amount to format.
     * @param {string} symbol The currency symbol to append. Defaults to 'đ'. Pass '' for no symbol (e.g., for input fields).
     * @returns {string} Formatted currency string.
     */
    function formatCurrency(amount, symbol = 'đ') {
        const num = parseFloat(String(amount).replace(/[^\d.-]/g, ''));

        if (isNaN(num)) {
            return symbol === '' ? '0' : '0' + symbol;
        }

        const formattedNumber = num.toLocaleString('vi-VN', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });

        return symbol === '' ? formattedNumber : formattedNumber + symbol;
    }

    /**
     * Parses a formatted currency string (VND) back to a number.
     */
    function parseCurrency(formattedAmount) {
        if (typeof formattedAmount !== 'string') {
            return NaN;
        }
        // Remove dots and currency symbol (if any, though not added by formatCurrency for inputs)
        const numericString = formattedAmount.replace(/\./g, '').replace('đ', '').trim();
        return parseFloat(numericString);
    }

    // close on outside click
    window.addEventListener('click', e => {
        ['view','edit','create'].forEach(t => {
            const mid = t + 'UserModal';
            if(e.target === document.getElementById(mid)){
                closeModal(mid);
            }
        });
    });

    // expose to helpers namespace
    window.helpers = { closeModal, openModal, formatDate, formatDateTime, formatCurrency, parseCurrency };

})(window);
