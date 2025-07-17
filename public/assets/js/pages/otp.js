// OTP Input Management
document.addEventListener('DOMContentLoaded', function() {
    const otpInputs = document.querySelectorAll('.otp-input');
    const hiddenOtpInput = document.getElementById('hidden-otp');
    const submitButton = document.getElementById('otp-submit');

    // Focus first input on page load
    if (otpInputs.length) otpInputs[0].focus();

    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            this.classList.toggle('filled', !!this.value);
            if (this.value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            updateHiddenInput();
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                otpInputs[index - 1].value = '';
                otpInputs[index - 1].classList.remove('filled');
                otpInputs[index - 1].focus();
                updateHiddenInput();
            }
            if (e.key === 'ArrowLeft' && index > 0) {
                otpInputs[index - 1].focus();
            }
            if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
            for (let i = 0; i < Math.min(pastedData.length, otpInputs.length); i++) {
                otpInputs[i].value = pastedData[i];
                otpInputs[i].classList.add('filled');
            }
            const nextEmpty = Array.from(otpInputs).findIndex(input => !input.value);
            (nextEmpty !== -1 ? otpInputs[nextEmpty] : otpInputs[otpInputs.length - 1]).focus();
            updateHiddenInput();
        });
    });

    function updateHiddenInput() {
        const otpValue = Array.from(otpInputs).map(input => input.value).join('');
        hiddenOtpInput.value = otpValue;
        const isComplete = otpValue.length === otpInputs.length;
        submitButton.disabled = !isComplete;
        submitButton.classList.toggle('btn-primary', isComplete);
        submitButton.classList.toggle('btn-secondary', !isComplete);
        if (isComplete) {
            setTimeout(() => document.getElementById('otp-form').submit(), 300);
        }
    }
});
