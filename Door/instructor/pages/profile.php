<?php
require_once '../../../data/session_security.php';

$role_access = check_role_access('instructor');
$show_role_modal = !$role_access['allowed'];

$instructor_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Jane Teacher';

$profile = [
    'id' => $instructor_id,
    'first_name' => '',
    'middle_name' => '',
    'last_name' => '',
    'suffix' => '',
    'email' => '',
    'phone' => '',
    'birthday' => '',
    'position' => '',
    'status' => '',
    'avatar' => '',
    'avatar_gradient_from' => '#667eea',
    'avatar_gradient_to' => '#764ba2',
    'total_mentees' => 0,
    'total_courses' => 0,
    'avg_rating' => 0,
    'member_since' => ''
];

$avatarCheckPath = realpath(__DIR__ . '/../../../media/instructors/' . $profile['avatar']);
$hasAvatar = !empty($profile['avatar']) && $avatarCheckPath && file_exists($avatarCheckPath);
$avatarSrc = $hasAvatar ? '../../../media/instructors/' . htmlspecialchars($profile['avatar']) : '';

function getAvatarUrl($profile) {
    if (!empty($profile['avatar'])) {
        return '../../../media/instructors/' . $profile['avatar'];
    }
    return null;
}

if (!$show_role_modal) {
    require_once '../../../data/config.php';
    
    try {
        // Fetch instructor data
        $stmt = $pdo->prepare("
            SELECT i.*, 
                   (SELECT COUNT(*) FROM mentees WHERE mentor_id = i.id) as total_mentees
            FROM instructors i
            WHERE i.id = ?
        ");
        $stmt->execute([$instructor_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            $profile = array_merge($profile, $data);
        } else {
            // Check if avatar column exists
            try {
                $check = $pdo->query("SELECT avatar FROM instructors WHERE id = " . intval($instructor_id));
                $avatar = $check->fetchColumn();
                if ($avatar) {
                    $profile['avatar'] = $avatar;
                }
            } catch (Exception $e) {
                // Column might not exist
            }
        }
        
        // Get member since date
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at, '%M %Y') as member_since FROM instructors WHERE id = ?");
        $stmt->execute([$instructor_id]);
        $date_data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($date_data) {
            $profile['member_since'] = $date_data['member_since'];
        }
        
    } catch (PDOException $e) {
        // Fallback to default profile
        $profile['first_name'] = $user_name;
        $profile['last_name'] = '';
    }
}

function getInitials($profile) {
    $initials = '';
    if (!empty($profile['first_name'])) $initials .= strtoupper($profile['first_name'][0]);
    if (!empty($profile['middle_name'])) $initials .= strtoupper($profile['middle_name'][0]);
    if (!empty($profile['last_name'])) $initials .= strtoupper($profile['last_name'][0]);
    if (!empty($profile['suffix'])) $initials .= strtoupper($profile['suffix'][0]);
    return $initials ?: '??';
}

function getFullName($profile) {
    $name = trim(($profile['first_name'] ?? '') . ' ' . ($profile['middle_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
    $suffix = $profile['suffix'] ?? '';
    return trim($name . ' ' . $suffix);
}
?>
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
            --gold-light: #e8c768;
            --gold-lighter: #f5e8c8;
            --dark-text: #1f2937;
            --light-text: #6b7280;
            --border-light: #e5e7eb;
            --cream: #fdfbf7;
        }
        
        .page-header {
            margin-bottom: 32px;
        }
        
        .page-title-area h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-text);
            margin: 0 0 4px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-title-area p {
            color: var(--light-text);
            margin: 0;
            font-size: 14px;
        }
        
        .profile-container {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }
        
        @media (max-width: 1024px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }
        
        .profile-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .profile-card {
            background: white;
            border-radius: 50%;
            border: 1px solid var(--border-light);
            overflow: hidden;
        }
        
        .profile-card-header {
            padding: 24px;
            <?php if ($hasAvatar): ?>
            background: url('<?php echo $avatarSrc; ?>') center/cover no-repeat !important;
            position: relative;
            <?php else: ?>
            background: linear-gradient(135deg, <?php echo $profile['avatar_gradient_from']; ?>, <?php echo $profile['avatar_gradient_to']; ?>);
            <?php endif; ?>
            color: white;
            text-align: center;
            position: relative;
            min-height: 320px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        
        <?php if ($hasAvatar): ?>
        .profile-card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.3);
        }
        <?php endif; ?>
        
        .profile-avatar {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 700;
            margin: 0 auto 16px;
            border: 4px solid rgba(255,255,255,0.5);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            position: relative;
            z-index: 1;
        }
        
        .profile-name {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }
        
        .profile-role {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }
        
        .profile-card-body {
            padding: 24px;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-light);
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--gold-lighter);
            color: var(--gold-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-label {
            font-size: 12px;
            color: var(--light-text);
            margin-bottom: 2px;
        }
        
        .stat-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--dark-text);
        }
        
        .profile-content {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        
        .content-section {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--border-light);
            overflow: hidden;
        }
        
        .section-header {
            padding: 20px 24px;
            background: var(--cream);
            border-bottom: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-text);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-body {
            padding: 24px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--dark-text);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .form-control {
            padding: 12px 16px;
            border: 2px solid var(--border-light);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: var(--dark-text);
            background: white;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 168, 67, 0.1);
        }
        
        .form-control[readonly] {
            background: var(--cream);
            color: var(--light-text);
        }
        
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border-light);
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            border: none;
        }
        
        .btn-primary {
            background: var(--gold);
            color: white;
        }
        
        .btn-primary:hover {
            background: #b8922f;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: white;
            color: var(--dark-text);
            border: 1px solid var(--border-light);
        }
        
        .btn-secondary:hover {
            background: var(--cream);
        }
        
        .toast-container {
            position: fixed;
            top: 80px;
            right: 16px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .toast {
            padding: 12px 16px;
            border-radius: 10px;
            color: white;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 280px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(60px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .toast.success { background: linear-gradient(135deg, #059669, #34d399); }
        .toast.error { background: linear-gradient(135deg, #dc2626, #f87171); }
        .toast.info { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
        
        .toast-close {
            margin-left: auto;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            opacity: 0.7;
            font-size: 16px;
        }
        .toast-close:hover { opacity: 1; }
        
        .password-section {
            margin-top: 24px;
        }
        
        .performance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        
        .performance-card {
            background: var(--cream);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-light);
            text-align: center;
        }
        
        .performance-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--gold-dark);
            margin-bottom: 4px;
        }
        
        .performance-label {
            font-size: 13px;
            color: var(--light-text);
        }
    </style>
</head>

<body class="dashboard-page">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../../media/LOGO.jpg" alt="Logo" class="sidebar-logo" style="width: 70px; height: 70px; border-radius: 16px; object-fit: cover; border: 3px solid white; background: white; padding: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
            <div class="sidebar-brand">
                <span class="sidebar-brand-name">IBM</span>
            </div>
        </div>
        
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?php echo htmlspecialchars($user_name); ?></span>
                <span class="sidebar-user-role">Instructor</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="sidebar-nav-label">Menu</div>
            <a href="../dashboard.php" class="sidebar-nav-item">
                <i class="fas fa-chart-pie"></i>
                <span>Overview</span>
            </a>
            <a href="students.php" class="sidebar-nav-item">
                <i class="fas fa-user-graduate"></i>
                <span>Students mentees</span>
            </a>
            <a href="feedback.php" class="sidebar-nav-item">
                <i class="fas fa-comment-dots"></i>
                <span>Feedback</span>
            </a>
            <a href="reports.php" class="sidebar-nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="profile.php" class="sidebar-nav-item active">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </nav>
    </aside>

    <div class="main-content" style="position: relative;">
        <div style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; bottom: 0; background-image: url('../../../media/LOGO.jpg'); background-size: 70%; background-position: center; background-repeat: no-repeat; opacity: 0.08; pointer-events: none; z-index: 0;"></div>
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="topbar-title">My Profile</div>
                    <div class="topbar-subtitle">Manage your account</div>
                </div>
            </div>
            
            <div class="topbar-right">
                <div class="topbar-date">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?php echo date('F j, Y'); ?></span>
                </div>
                <a href="../../../data/logout.php" class="topbar-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="page-header">
                <div class="page-title-area">
                    <h1><i class="fas fa-user-circle"></i> My Profile</h1>
                    <p>Manage your personal information and account settings</p>
                </div>
            </div>

            <div class="profile-container">
                <!-- Sidebar Card -->
                <div class="profile-sidebar">
                    <div class="profile-card">
                        <div class="profile-card-header" style="padding: 30px 24px 100px; position: relative;">
                            <?php if ($hasAvatar): ?>
                            <div class="profile-avatar" style="display: none;"></div>
                            <?php else: ?>
                            <div class="profile-avatar" style="font-size: 72px; padding: 60px 0;">
                                <?php echo htmlspecialchars(getInitials($profile)); ?>
                            </div>
                            <?php endif; ?>
                            <label for="avatarUpload" style="position: absolute; bottom: 16px; right: 16px; background: rgba(255,255,255,0.9); padding: 10px; border-radius: 50%; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.2); z-index: 10;" onmouseover="this.style.background='white'; this.style.transform='scale(1.1)'" onmouseout="this.style.background='rgba(255,255,255,0.9)'; this.style.transform='scale(1)'">
                                <i class="fas fa-camera" style="color: #d4a843; font-size: 16px;"></i>
                            </label>
                            <input type="file" id="avatarUpload" accept="image/*" style="display: none;" onchange="uploadAvatar(this)">
                        </div>
                        <div class="profile-card-body">
                            <div class="stat-item">
                                <div class="stat-icon"><i class="fas fa-users"></i></div>
                                <div class="stat-info">
                                    <div class="stat-label">Mentees</div>
                                    <div class="stat-value"><?php echo number_format($profile['total_mentees']); ?></div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon"><i class="fas fa-book"></i></div>
                                <div class="stat-info">
                                    <div class="stat-label">Courses</div>
                                    <div class="stat-value"><?php echo number_format($profile['total_courses']); ?></div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon"><i class="fas fa-star"></i></div>
                                <div class="stat-info">
                                    <div class="stat-label">Avg Rating</div>
                                    <div class="stat-value"><?php echo $profile['avg_rating'] ? number_format($profile['avg_rating'], 1) : 'N/A'; ?>/5.0</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                                <div class="stat-info">
                                    <div class="stat-label">Member Since</div>
                                    <div class="stat-value"><?php echo $profile['member_since'] ?? 'N/A'; ?></div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon"><i class="fas fa-id-card"></i></div>
                                <div class="stat-info">
                                    <div class="stat-label">Instructor ID</div>
                                    <div class="stat-value">#<?php echo str_pad($profile['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="profile-content">
                    <!-- Personal Information -->
                    <div class="content-section">
                        <div class="section-header">
                            <h3 class="section-title"><i class="fas fa-user"></i> Personal Information</h3>
                            <button class="btn btn-secondary" onclick="enableEdit('personalForm')" id="editPersonalBtn">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                        <div class="section-body">
                            <!-- View Mode -->
                            <div id="personalViewMode">
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                                    <div style="padding: 12px; background: var(--cream); border-radius: 8px; border: 1px solid var(--border-light);">
                                        <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;"><i class="fas fa-user"></i> First Name</div>
                                        <div style="font-size: 14px; font-weight: 600; color: var(--dark-text);"><?php echo htmlspecialchars($profile['first_name'] ?? '-'); ?></div>
                                    </div>
                                    <div style="padding: 12px; background: var(--cream); border-radius: 8px; border: 1px solid var(--border-light);">
                                        <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;"><i class="fas fa-user"></i> Middle Name</div>
                                        <div style="font-size: 14px; font-weight: 600; color: var(--dark-text);"><?php echo htmlspecialchars($profile['middle_name'] ?? '-'); ?></div>
                                    </div>
                                    <div style="padding: 12px; background: var(--cream); border-radius: 8px; border: 1px solid var(--border-light);">
                                        <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;"><i class="fas fa-user"></i> Last Name</div>
                                        <div style="font-size: 14px; font-weight: 600; color: var(--dark-text);"><?php echo htmlspecialchars($profile['last_name'] ?? '-'); ?></div>
                                    </div>
                                    <div style="padding: 12px; background: var(--cream); border-radius: 8px; border: 1px solid var(--border-light);">
                                        <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;"><i class="fas fa-asterisk"></i> Suffix</div>
                                        <div style="font-size: 14px; font-weight: 600; color: var(--dark-text);"><?php echo htmlspecialchars($profile['suffix'] ?? '-'); ?></div>
                                    </div>
                                    <div style="padding: 12px; background: var(--cream); border-radius: 8px; border: 1px solid var(--border-light);">
                                        <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;"><i class="fas fa-envelope"></i> Email Address</div>
                                        <div style="font-size: 14px; font-weight: 600; color: var(--dark-text);"><?php echo htmlspecialchars($profile['email'] ?? '-'); ?></div>
                                    </div>
                                    <div style="padding: 12px; background: var(--cream); border-radius: 8px; border: 1px solid var(--border-light);">
                                        <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;"><i class="fas fa-phone"></i> Phone Number</div>
                                        <div style="font-size: 14px; font-weight: 600; color: var(--dark-text);"><?php echo htmlspecialchars($profile['phone'] ?? '-'); ?></div>
                                    </div>
                                    <div style="padding: 12px; background: var(--cream); border-radius: 8px; border: 1px solid var(--border-light);">
                                        <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;"><i class="fas fa-birthday-cake"></i> Birthday</div>
                                        <div style="font-size: 14px; font-weight: 600; color: var(--dark-text);"><?php echo !empty($profile['birthday']) ? htmlspecialchars($profile['birthday']) : '-'; ?></div>
                                    </div>
                                    <div style="padding: 12px; background: var(--cream); border-radius: 8px; border: 1px solid var(--border-light);">
                                        <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;"><i class="fas fa-calendar"></i> Position</div>
                                        <div style="font-size: 14px; font-weight: 600; color: var(--dark-text);"><?php echo htmlspecialchars($profile['position'] ?? 'Instructor'); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Edit Mode -->
                            <form id="personalForm" style="display: none;">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label><i class="fas fa-user"></i> First Name</label>
                                        <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-user"></i> Middle Name</label>
                                        <input type="text" class="form-control" name="middle_name" value="<?php echo htmlspecialchars($profile['middle_name'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-user"></i> Last Name</label>
                                        <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-asterisk"></i> Suffix</label>
                                        <input type="text" class="form-control" name="suffix" value="<?php echo htmlspecialchars($profile['suffix'] ?? ''); ?>" placeholder="Jr., Sr., III">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-envelope"></i> Email Address</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-phone"></i> Phone Number</label>
                                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-birthday-cake"></i> Birthday</label>
                                        <input type="date" class="form-control" name="birthday" value="<?php echo htmlspecialchars($profile['birthday'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar"></i> Position</label>
                                        <input type="text" class="form-control" name="position" value="<?php echo htmlspecialchars($profile['position'] ?? 'Instructor'); ?>">
                                    </div>
                                </div>
                                <div class="form-actions" id="personalFormActions">
                                    <button type="button" class="btn btn-secondary" onclick="cancelEdit('personalForm')">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="content-section password-section">
                        <div class="section-header">
                            <h3 class="section-title"><i class="fas fa-key"></i> Change Password</h3>
                        </div>
                        <div class="section-body">
                            <form id="passwordForm">
                                <div class="form-grid" style="max-width: 600px;">
                                    <div class="form-group">
                                        <label><i class="fas fa-lock"></i> Current Password</label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-key"></i> New Password</label>
                                        <input type="password" class="form-control" name="new_password" id="newPassword" required minlength="8">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-check-circle"></i> Confirm New Password</label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-sync-alt"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 32px 48px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            <div id="uploadStatus" style="font-size: 18px; font-weight: 600; color: #3b82f6;">
                <i class="fas fa-spinner fa-spin"></i> Uploading...
            </div>
        </div>
    </div>

    <script>
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
            toast.innerHTML = `
                <i class="fas fa-${icon}"></i>
                <span>${message}</span>
                <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideIn 0.4s ease reverse';
                setTimeout(() => toast.remove(), 400);
            }, 4000);
        }

        function enableEdit(formId) {
            document.getElementById('personalViewMode').style.display = 'none';
            document.getElementById(formId).style.display = 'block';
            document.getElementById('editPersonalBtn').style.display = 'none';
        }

        function cancelEdit(formId) {
            document.getElementById('personalViewMode').style.display = 'block';
            document.getElementById(formId).style.display = 'none';
            document.getElementById('editPersonalBtn').style.display = 'flex';
        }

        function uploadAvatar(input) {
            if (!input.files || !input.files[0]) return;
            
            const file = input.files[0];
            
            document.getElementById('uploadModal').style.display = 'flex';
            document.getElementById('uploadStatus').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            document.getElementById('uploadStatus').style.color = '#3b82f6';
            
            const formData = new FormData();
            formData.append('avatar', file);
            
            fetch('../../../Door/data/upload_avatar.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                console.log('Upload response:', data);
                if (data.success) {
                    document.getElementById('uploadStatus').innerHTML = '<i class="fas fa-check-circle"></i> Profile picture updated!';
                    document.getElementById('uploadStatus').style.color = '#059669';
                    setTimeout(() => {
                        document.getElementById('uploadModal').style.display = 'none';
                        location.reload();
                    }, 1500);
                } else {
                    document.getElementById('uploadStatus').innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.message || 'Failed to upload');
                    document.getElementById('uploadStatus').style.color = '#dc2626';
                    setTimeout(() => {
                        document.getElementById('uploadModal').style.display = 'none';
                    }, 2000);
                }
            })
            .catch(err => {
                console.error('Upload error:', err);
                document.getElementById('uploadStatus').innerHTML = '<i class="fas fa-exclamation-circle"></i> Error: ' + err.message;
                document.getElementById('uploadStatus').style.color = '#dc2626';
                setTimeout(() => {
                    document.getElementById('uploadModal').style.display = 'none';
                }, 2000);
            });
            
            input.value = '';
        }

        document.getElementById('personalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('first_name', this.first_name.value);
            formData.append('middle_name', this.middle_name.value);
            formData.append('last_name', this.last_name.value);
            formData.append('suffix', this.suffix.value);
            formData.append('email', this.email.value);
            formData.append('phone', this.phone.value);
            formData.append('birthday', this.birthday.value);
            formData.append('position', this.position.value);
            
            fetch('../../../data/update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('Profile updated successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Failed to update profile', 'error');
                }
            })
            .catch(err => {
                showToast('Error: ' + err.message, 'error');
            });
        });

        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = this.confirm_password.value;
            
            if (newPassword !== confirmPassword) {
                showToast('Passwords do not match', 'error');
                return;
            }
            
            if (newPassword.length < 8) {
                showToast('Password must be at least 8 characters', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('current_password', this.current_password.value);
            formData.append('new_password', newPassword);
            
            fetch('../../../data/change_password.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('Password changed successfully!', 'success');
                    this.reset();
                } else {
                    showToast(data.message || 'Failed to change password', 'error');
                }
            })
            .catch(err => {
                showToast('Error: ' + err.message, 'error');
            });
        });

        <?php if ($show_role_modal): ?>
        window.addEventListener('DOMContentLoaded', function() {
            showToast('Access restricted. Redirecting...', 'error');
            setTimeout(() => window.location.href = '../../../Door/login.php', 2000);
        });
        <?php endif; ?>
    </script>
</body>
</html>