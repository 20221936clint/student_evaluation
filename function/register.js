// Phone input: allow only numbers, auto-format as 0935-856-0253
document.addEventListener('DOMContentLoaded', function () {
    var phoneInput = document.getElementById('regPhone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function (e) {
            let value = phoneInput.value.replace(/\D/g, ''); // Remove non-digits
            if (value.length > 11) value = value.slice(0, 11);
            let formatted = value;
            if (value.length > 3 && value.length <= 7) {
                formatted = value.slice(0, 4) + '-' + value.slice(4);
            } else if (value.length > 7) {
                formatted = value.slice(0, 4) + '-' + value.slice(4, 7) + '-' + value.slice(7);
            }
            phoneInput.value = formatted;
        });
        phoneInput.addEventListener('keypress', function (e) {
            // Only allow numbers
            if (!/\d/.test(e.key)) {
                e.preventDefault();
            }
        });
    }
});