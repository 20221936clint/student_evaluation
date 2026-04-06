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

    // Handle registration form submission
    var registerForm = document.getElementById('registerForm');
    var registerBtn = document.getElementById('registerBtn');
    var formMessage = document.getElementById('formMessage');

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Show loading state
            var btnText = registerBtn.querySelector('.btn-text');
            var btnLoader = registerBtn.querySelector('.btn-loader');
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-block';
            registerBtn.disabled = true;
            formMessage.style.display = 'none';

            // Get form data
            var formData = new FormData(registerForm);

            // Send AJAX request
            fetch(registerForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                // Reset button state
                btnText.style.display = 'inline-block';
                btnLoader.style.display = 'none';
                registerBtn.disabled = false;

                if (data.success) {
                    // Reset form after success
                    registerForm.reset();

                    // Create centered overlay success message
                    var overlay = document.createElement('div');
                    overlay.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        z-index: 10000;
                        animation: fadeIn 0.3s ease;
                    `;

                    var messageBox = document.createElement('div');
                    messageBox.style.cssText = `
                        background: white;
                        padding: 30px 40px;
                        border-radius: 12px;
                        text-align: center;
                        max-width: 400px;
                        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                        animation: scaleIn 0.3s ease;
                    `;

                    messageBox.innerHTML = `
                        <div style="font-size: 48px; color: #10b981; margin-bottom: 15px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 style="margin: 0 0 10px 0; color: #059669; font-size: 1.25rem;">Success!</h3>
                        <p style="margin: 0; color: #6b7280; line-height: 1.5;">${data.message}</p>
                    `;

                    overlay.appendChild(messageBox);
                    document.body.appendChild(overlay);

                    // Auto remove overlay and switch to login after 2 seconds
                    setTimeout(function () {
                        overlay.style.animation = 'fadeOut 0.3s ease forwards';
                        setTimeout(function () {
                            if (document.body.contains(overlay)) {
                                document.body.removeChild(overlay);
                            }
                            // Switch back to login panel
                            var loginView = document.getElementById('loginView');
                            var registerView = document.getElementById('registerView');
                            if (loginView && registerView) {
                                registerView.style.display = 'none';
                                loginView.style.display = 'block';
                            }
                        }, 300);
                    }, 2000);

                } else {
                    // Create centered overlay error message
                    var overlay = document.createElement('div');
                    overlay.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        z-index: 10000;
                        animation: fadeIn 0.3s ease;
                    `;

                    var messageBox = document.createElement('div');
                    messageBox.style.cssText = `
                        background: white;
                        padding: 30px 40px;
                        border-radius: 12px;
                        text-align: center;
                        max-width: 400px;
                        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                        animation: scaleIn 0.3s ease;
                    `;

                    messageBox.innerHTML = `
                        <div style="font-size: 48px; color: #ef4444; margin-bottom: 15px;">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <h3 style="margin: 0 0 10px 0; color: #dc2626; font-size: 1.25rem;">Error</h3>
                        <p style="margin: 0; color: #6b7280; line-height: 1.5;">${data.message}</p>
                    `;

                    overlay.appendChild(messageBox);
                    document.body.appendChild(overlay);

                    // Auto remove overlay after 2 seconds
                    setTimeout(function () {
                        overlay.style.animation = 'fadeOut 0.3s ease forwards';
                        setTimeout(function () {
                            if (document.body.contains(overlay)) {
                                document.body.removeChild(overlay);
                            }
                        }, 300);
                    }, 2000);
                }
            })
            .catch(function (error) {
                // Reset button state
                btnText.style.display = 'inline-block';
                btnLoader.style.display = 'none';
                registerBtn.disabled = false;

                // Show error message
                formMessage.textContent = 'An error occurred. Please try again.';
                formMessage.style.background = 'rgba(239, 68, 68, 0.15)';
                formMessage.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                formMessage.style.color = '#ef4444';
                formMessage.style.display = 'block';
                console.error('Registration error:', error);
            });
        });
    }
});
