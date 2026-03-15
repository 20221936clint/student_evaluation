<?php
// Start session to check login status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to their dashboard
if (isset($_SESSION['user_role']) && !empty($_SESSION['user_role'])) {
    $redirect = match($_SESSION['user_role']) {
        'admin' => 'admin/dashboard.php',
        'program_head' => 'program_head/dashboard.php',
        'instructor' => 'instructor/dashboard.php',
        default => 'login.php'
    };
    header('Location: ' . $redirect);
    exit;
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../media/LOGO.jpg" type="image/jpeg">
    <title>Register Instructor - Faculty Evaluation System</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 1;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .register-header .avatar-circle i {
            font-size: 36px;
            color: white;
        }
        
        .register-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }
        
        .register-header p {
            font-size: 14px;
            color: #6b7280;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .input-group {
            margin-bottom: 20px;
        }
        
        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .input-group .input-wrapper {
            position: relative;
        }
        
        .input-group .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
        }
        
        .input-group input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #1a1a2e;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .input-group input::placeholder {
            color: #9ca3af;
        }
        
        .register-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .register-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .register-btn.loading .btn-text {
            display: none;
        }
        
        .register-btn .btn-loader {
            display: none;
        }
        
        .register-btn.loading .btn-loader {
            display: inline-block;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .login-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            z-index: 10;
        }
        
        .back-home:hover {
            text-decoration: underline;
        }
        
        .toast {
            position: fixed;
            top: 24px;
            right: 24px;
            padding: 16px 24px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            transform: translateX(400px);
            transition: all 0.3s ease;
        }
        
        .toast.show {
            transform: translateX(0);
        }
        
        .toast.success {
            border-left: 4px solid #10b981;
        }
        
        .toast.error {
            border-left: 4px solid #dc2626;
        }
        
        .toast i {
            font-size: 20px;
        }
        
        .toast.success i {
            color: #10b981;
        }
        
        .toast.error i {
            color: #dc2626;
        }
        
        .toast-message {
            font-size: 14px;
            font-weight: 500;
            color: #1a1a2e;
        }
    </style>
</head>

<body>
    <a href="login.php" class="back-home">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Login</span>
    </a>

    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="avatar-circle">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h2>Register as Instructor</h2>
                <p>Create your instructor account</p>
            </div>

            <form id="registerForm">
                <div class="form-row">
                    <div class="input-group">
                        <label>First Name</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="firstName" placeholder="First name" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Last Name</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="lastName" placeholder="Last name" required>
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Employee ID</label>
                    <div class="input-wrapper">
                        <i class="fas fa-id-badge"></i>
                        <input type="text" id="employeeId" placeholder="Employee ID" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Department</label>
                    <div class="input-wrapper">
                        <i class="fas fa-building"></i>
                        <input type="text" id="department" placeholder="Department" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" placeholder="Create password" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirmPassword" placeholder="Confirm password" required>
                    </div>
                </div>

                <button type="submit" class="register-btn" id="registerBtn">
                    <span class="btn-text"><i class="fas fa-user-plus"></i> Create Account</span>
                    <span class="btn-loader">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Sign In</a>
            </div>
        </div>
    </div>

    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span class="toast-message" id="toastMessage">Success!</span>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('email').value;
            const employeeId = document.getElementById('employeeId').value;
            const department = document.getElementById('department').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const registerBtn = document.getElementById('registerBtn');
            
            if (password !== confirmPassword) {
                showToast('Passwords do not match!', 'error');
                return;
            }
            
            if (password.length < 6) {
                showToast('Password must be at least 6 characters!', 'error');
                return;
            }
            
            // Show loading state
            registerBtn.classList.add('loading');
            registerBtn.disabled = true;
            
            try {
                const formData = new FormData();
                formData.append('action', 'register_instructor');
                formData.append('first_name', firstName);
                formData.append('last_name', lastName);
                formData.append('email', email);
                formData.append('employee_id', employeeId);
                formData.append('department', department);
                formData.append('password', password);
                
                const response = await fetch('../data/admin_process.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message || 'Registration successful!', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1500);
                } else {
                    showToast(result.message || 'Registration failed!', 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
                console.error('Registration error:', error);
            } finally {
                registerBtn.classList.remove('loading');
                registerBtn.disabled = false;
            }
        });
        
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const toastIcon = toast.querySelector('i');
            
            toastMessage.textContent = message;
            toast.className = 'toast ' + type;
            
            if (type === 'success') {
                toastIcon.className = 'fas fa-check-circle';
            } else {
                toastIcon.className = 'fas fa-exclamation-circle';
            }
            
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>

</html>
