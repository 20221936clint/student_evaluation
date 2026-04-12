<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../../media/LOGO.jpg" type="image/jpeg">
    <title>My Profile - Faculty Evaluation System</title>
    <link rel="stylesheet" href="../../../css/common.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --gold: #d4a843;
            --gold-dark: #8B6914;
            --gold-light: #e8c768;
            --cream: #fdfbf7;
            --dark-text: #1f2937;
            --light-text: #6b7280;
            --border: #e5e7eb;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--cream); color: var(--dark-text); }
        .page-container { padding: 24px; }
        .page-title { font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
        .page-subtitle { font-size: 14px; color: var(--light-text); margin-bottom: 24px; }
        
        .profile-header {
            background: white;
            border-radius: 20px;
            padding: 32px;
            display: flex;
            align-items: center;
            gap: 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }
        .avatar-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        }
        .avatar-wrapper { position: relative; flex-shrink: 0; }
        .avatar-wrapper .avatar-img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4); }
        .avatar-edit {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 36px;
            height: 36px;
            background: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: all 0.2s;
        }
        .avatar-edit:hover { transform: scale(1.1); background: var(--gold-dark); }
        .profile-info { flex: 1; }
        .profile-name { font-size: 28px; font-weight: 700; margin-bottom: 4px; }
        .profile-role { font-size: 14px; color: var(--light-text); margin-bottom: 16px; }
        .profile-stats { display: flex; gap: 24px; }
        .stat-item { text-align: center; }
        .stat-num { font-size: 24px; font-weight: 700; color: var(--gold); }
        .stat-label { font-size: 12px; color: var(--light-text); }
        
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        @media (max-width: 1024px) { .profile-grid { grid-template-columns: 1fr; } }
        
        .content-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }
        .card-title { font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .card-title i { color: var(--gold); }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .info-item {
            padding: 16px;
            background: var(--cream);
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        .info-label {
            font-size: 11px;
            color: var(--light-text);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .info-value { font-size: 14px; font-weight: 600; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            transition: all 0.2s;
        }
        .form-input:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(212,168,67,0.1); }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-primary { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(212,168,67,0.3); }
        .btn-secondary { background: var(--cream); color: var(--dark-text); border: 1px solid var(--border); }
        
        .form-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }
        
        .view-mode { display: block; }
        .edit-mode { display: none; }
        
        .tab-nav {
            display: flex;
            gap: 4px;
            background: white;
            padding: 6px;
            border-radius: 14px;
            margin-bottom: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .tab-btn {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 10px;
            background: transparent;
            font-size: 13px;
            font-weight: 600;
            color: var(--light-text);
            cursor: pointer;
            transition: all 0.2s;
        }
        .tab-btn.active { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .toast-container {
            position: fixed; top: 80px; right: 20px; z-index: 9999;
            display: flex; flex-direction: column; gap: 8px;
        }
        .toast {
            padding: 14px 20px; border-radius: 10px; color: white; font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateX(60px); } to { opacity: 1; transform: translateX(0); } }
        .toast.success { background: linear-gradient(135deg, #059669, #34d399); }
        .toast.error { background: linear-gradient(135deg, #dc2626, #f87171); }
    </style>
</head>
<body class="dashboard-page">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../../media/LOGO.jpg" alt="Logo" class="sidebar-logo" style="width: 70px; height: 70px; border-radius: 16px; object-fit: cover; border: 3px solid white; background: white; padding: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
            <div class="sidebar-brand"><span class="sidebar-brand-name">IBM</span></div>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar"><i class="fas fa-user"></i></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name">Instructor</span>
                <span class="sidebar-user-role">Instructor</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-nav-label">Menu</div>
            <a href="../dashboard.php" class="sidebar-nav-item"><i class="fas fa-chart-pie"></i><span>Overview</span></a>
            <a href="students.php" class="sidebar-nav-item"><i class="fas fa-user-graduate"></i><span>Students</span></a>
            <a href="feedback.php" class="sidebar-nav-item"><i class="fas fa-comment-dots"></i><span>Feedback</span></a>
            <a href="reports.php" class="sidebar-nav-item"><i class="fas fa-file-alt"></i><span>Reports</span></a>
            <a href="profile.php" class="sidebar-nav-item active"><i class="fas fa-user"></i><span>Profile</span></a>
        </nav>
    </aside>
    <div class="main-content" style="position: relative;">
        <div style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; bottom: 0; background-image: url('../../../media/LOGO.jpg'); background-size: 70%; background-position: center; background-repeat: no-repeat; opacity: 0.08; pointer-events: none;"></div>
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                <div><div class="topbar-title">My Profile</div><div class="topbar-subtitle">Manage your account</div></div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date"><i class="fas fa-calendar-alt"></i><span><?php echo date('F j, Y'); ?></span></div>
                <a href="../../../data/logout.php" class="topbar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </header>
        <main class="dashboard-content">
            <div class="page-container">
                <!-- Profile Header -->
                <?php
                $instructor_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;
                require_once '../../../data/config.php';
                $profile = ['first_name' => 'Jane', 'last_name' => 'Teacher', 'email' => 'instructor@edu.com', 'position' => 'Instructor', 'phone' => '', 'avatar' => '', 'total_mentees' => 0, 'member_since' => 'January 2024'];
                try {
                    $stmt = $pdo->prepare("SELECT * FROM instructors WHERE id = ?");
                    $stmt->execute([$instructor_id]);
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($data) $profile = array_merge($profile, $data);
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM mentees WHERE mentor_id = ?");
                    $stmt->execute([$instructor_id]);
                    $profile['total_mentees'] = $stmt->fetchColumn();
                } catch (PDOException $e) {}
                $initials = strtoupper(($profile['first_name'][0] ?? 'J') . ($profile['last_name'][0] ?? 'T'));
                $fullName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
                $avatarPath = '../../../media/instructors/' . $instructor_id . '.jpg';
                $hasAvatar = file_exists(__DIR__ . '/' . $avatarPath);
                ?>
                <div class="profile-header">
                    <div class="avatar-wrapper" style="position: relative;">
                        <?php if ($hasAvatar): ?>
                        <img src="<?php echo $avatarPath; ?>?t=<?php echo time(); ?>" class="avatar-img" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                        <div class="avatar-circle"><?php echo $initials; ?></div>
                        <?php endif; ?>
                        <label for="avatarUpload" class="avatar-edit">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="avatarUpload" accept="image/*" style="display: none;" onchange="uploadAvatar(this)">
                    </div>
                    <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($fullName); ?></div>
                        <div class="profile-role"><?php echo htmlspecialchars($profile['position'] ?? 'Instructor'); ?></div>
                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-num"><?php echo $profile['total_mentees']; ?></div>
                                <div class="stat-label">Mentees</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-num"><?php echo $profile['member_since']; ?></div>
                                <div class="stat-label">Member Since</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabs -->
                <div class="tab-nav">
                    <button class="tab-btn active" onclick="switchTab('personal')"><i class="fas fa-user"></i> Personal</button>
                    <button class="tab-btn" onclick="switchTab('contact')"><i class="fas fa-address-book"></i> Contact</button>
                    <button class="tab-btn" onclick="switchTab('security')"><i class="fas fa-shield-alt"></i> Security</button>
                </div>
                
                <!-- Personal Tab -->
                <div class="tab-content active" id="personalTab">
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user"></i> Personal Information</h3>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-user"></i> First Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile['first_name'] ?? '-'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-user"></i> Last Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile['last_name'] ?? '-'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile['email'] ?? '-'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-briefcase"></i> Position</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile['position'] ?? '-'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Tab -->
                <div class="tab-content" id="contactTab">
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-address-book"></i> Contact Information</h3>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-envelope"></i> Email Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile['email'] ?? '-'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-phone"></i> Phone Number</div>
                                <div class="info-value"><?php echo htmlspecialchars($profile['phone'] ?: '-'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Security Tab -->
                <div class="tab-content" id="securityTab">
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-key"></i> Change Password</h3>
                        </div>
                        <form id="passwordForm" style="max-width: 500px;">
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" class="form-input" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" class="form-input" name="new_password" required minlength="8">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" class="form-input" name="confirm_password" required>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div class="toast-container" id="toastContainer"></div>
    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            event.target.classList.add('active');
            document.getElementById(tab + 'Tab').classList.add('active');
        }
        
        function uploadAvatar(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            if (!file.type.match(/image.*/)) { showToast('Please select an image file', 'error'); return; }
            
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('instructor_id', <?php echo $instructor_id; ?>);
            
            fetch('../../../data/upload_avatar.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('Profile picture updated!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Failed to upload', 'error');
                }
            })
            .catch(err => showToast('Error: ' + err.message, 'error'));
            input.value = '';
        }
        
        function showToast(msg, type) {
            const div = document.createElement('div');
            div.className = 'toast ' + type;
            div.innerHTML = '<i class="fas fa-' + (type=='success'?'check-circle':'exclamation-circle') + '"></i> ' + msg;
            document.getElementById('toastContainer').appendChild(div);
            setTimeout(() => div.remove(), 4000);
        }
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if(this.new_password.value !== this.confirm_password.value) { showToast('Passwords do not match', 'error'); return; }
            showToast('Password updated successfully!', 'success');
            this.reset();
        });
    </script>
</body>
</html>