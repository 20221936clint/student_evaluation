document.addEventListener('DOMContentLoaded', function() {
    const dropdownTrigger = document.getElementById('dropdownTrigger');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const dropdownArrow = document.getElementById('dropdownArrow');
    const selectedRole = document.getElementById('selectedRole');
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    const roleInput = document.getElementById('roleInput');

    // Check for saved credentials on page load
    const savedCredentials = localStorage.getItem('rememberMeCredentials');
    if (savedCredentials) {
        try {
            const credentials = JSON.parse(savedCredentials);
            document.getElementById('username').value = credentials.email || '';
            document.getElementById('password').value = credentials.password || '';
            document.getElementById('rememberMe').checked = true;
        } catch (e) {
            console.error('Error parsing saved credentials:', e);
        }
    }

    // Toggle dropdown
    dropdownTrigger.addEventListener('click', function(e) {
        e.stopPropagation();
        this.classList.toggle('active');
        dropdownMenu.classList.toggle('open');
        dropdownArrow.classList.toggle('rotated');
    });

    // Handle role selection
    dropdownItems.forEach(item => {
        item.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const title = this.querySelector('.dropdown-item-title').textContent;
            
            // Remove selected class from all items
            dropdownItems.forEach(i => i.classList.remove('selected'));
            
            // Add selected class to clicked item
            this.classList.add('selected');
            
            // Update displayed text
            selectedRole.textContent = title;
            selectedRole.classList.remove('placeholder');
            
            // Store selected value
            dropdownTrigger.setAttribute('data-selected', value);
            
            // Close dropdown
            dropdownTrigger.classList.remove('active');
            dropdownMenu.classList.remove('open');
            dropdownArrow.classList.remove('rotated');
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!dropdownTrigger.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownTrigger.classList.remove('active');
            dropdownMenu.classList.remove('open');
            dropdownArrow.classList.remove('rotated');
        }
    });

    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            if (type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    // Form submission
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');

    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const role = dropdownTrigger.getAttribute('data-selected');
            const email = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (!role) {
                showToast('Please select a role');
                return;
            }

            // Show loading state
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('role', role);
                formData.append('email', email);
                formData.append('password', password);

                const response = await fetch('../data/login_process.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Handle Remember Me functionality
                    const rememberMe = document.getElementById('rememberMe').checked;
                    if (rememberMe) {
                        localStorage.setItem('rememberMeCredentials', JSON.stringify({
                            email: email,
                            password: password
                        }));
                    } else {
                        localStorage.removeItem('rememberMeCredentials');
                    }
                    
                    showToast('Login Successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    showToast(result.message || 'Login failed. Please try again.');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.');
                console.error('Login error:', error);
            } finally {
                loginBtn.classList.remove('loading');
                loginBtn.disabled = false;
            }
        });
    }

    function showToast(message) {
        toastMessage.textContent = message;
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
});
