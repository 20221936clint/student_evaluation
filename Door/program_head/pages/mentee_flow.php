<?php
require_once '../../../data/session_security.php';

// Check role access
$role_access = check_role_access('program_head');
$show_role_modal = !$role_access['allowed'];

$user_name = $_SESSION['user_name'] ?? 'Program Head';

if (!$show_role_modal) {
    require_once '../../../data/config.php';
    
    // Fetch all students with student_id
    $students = [];
    try {
        $stmt = $pdo->query("SELECT s.id, s.student_id, s.first_name, s.last_name, s.email, m.major_name 
                             FROM students s 
                             LEFT JOIN majors m ON s.major_id = m.id 
                             ORDER BY s.last_name");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $students = [];
    }
    
    // Fetch all instructors
    $instructors = [];
    try {
        $sql = "SELECT i.id, i.first_name, i.middle_name, i.last_name, i.suffix, i.email, i.position, i.avatar_gradient_from, i.avatar_gradient_to 
                FROM instructors i 
                ORDER BY i.last_name";
        $stmt = $pdo->query($sql);
        $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $instructors = [];
    }
    
    // Fetch existing mentee assignments
    $menteeAssignments = []; // instructor_id => [student data]
    try {
        $stmt = $pdo->query("SELECT m.id, m.first_name, m.last_name, m.email, m.mentor_id 
                             FROM mentees m 
                             ORDER BY m.last_name");
        $mentees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($mentees as $mentee) {
            $menteeAssignments[$mentee['mentor_id']][] = $mentee;
        }
    } catch (PDOException $e) {
        $menteeAssignments = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MenteeFlow - Program Head Dashboard</title>
    <link rel="stylesheet" href="../../../css/common.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { 
            --gold: #B8860B; 
            --gold-light: #D4A843; 
            --gold-dark: #8B6914; 
            --gold-lighter: #F5E6B8;
            --cream: #f7f5ef; 
            --cream-light: #f0ebe3; 
            --white: #ffffff; 
            --dark-text: #1f1f1f; 
            --dark-text-2: #2d3748; 
            --light-text: #666666; 
            --light-text-2: #a0aec0; 
            --border-light: #d4cfc5; 
            --border-soft: #e8e4da; 
            --success: #059669; 
            --success-light: #c6f6d5; 
            --info: #0284c7; 
            --info-light: #bae6fd; 
            --danger: #dc2626;
            --danger-light: #fee2e2;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            color: var(--dark-text); 
            overflow-x: hidden;
            background: var(--cream);
        }
        .page-container { 
            padding: 32px; 
            max-width: 1600px;
            margin: 0 auto;
        }
        .welcome-banner { 
            background: linear-gradient(160deg, #6b5a00 0%, var(--gold-light) 40%, var(--gold-dark) 100%); 
            border-radius: 20px; 
            padding: 36px 44px; 
            color: var(--white); 
            margin-bottom: 32px; 
            box-shadow: 0 8px 32px rgba(139, 105, 20, 0.4); 
            position: relative; 
            overflow: hidden; 
        }
        .welcome-banner::before { 
            content: ''; 
            position: absolute; 
            top: -50%; 
            right: -10%; 
            width: 300px; 
            height: 300px; 
            background: rgba(255, 255, 255, 0.1); 
            border-radius: 50%; 
        }
        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }
        .welcome-banner-role { 
            display: inline-block; 
            background: rgba(255, 255, 255, 0.2); 
            padding: 6px 14px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            margin-bottom: 12px; 
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 1;
        }
        .welcome-banner h1 { 
            font-size: 28px; 
            font-weight: 800; 
            margin: 0 0 12px 0; 
            position: relative; 
            z-index: 1; 
        }
        .welcome-banner p { 
            font-size: 15px; 
            opacity: 0.95; 
            margin: 0; 
            max-width: 600px; 
            position: relative; 
            z-index: 1; 
        }
        .grid-2 { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 24px; 
            margin-bottom: 32px;
        }
        @media (max-width: 1200px) {
            .grid-2 { grid-template-columns: 1fr; }
        }
        .card { 
            background: var(--white); 
            border-radius: 16px; 
            padding: 24px; 
            box-shadow: 0 4px 20px rgba(184, 134, 11, 0.12); 
            border: 1px solid var(--border-soft); 
            margin-bottom: 24px; 
        }
        .card-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
            padding-bottom: 16px; 
            border-bottom: 2px solid var(--cream-light); 
        }
        .card-title { 
            font-size: 18px; 
            font-weight: 700; 
            color: var(--dark-text-2); 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        .card-title i { 
            color: var(--gold); 
            font-size: 20px; 
        }
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        .search-box input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 2px solid var(--border-light);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: var(--dark-text);
            background: var(--cream-light);
            transition: all 0.3s ease;
        }
        .search-box input:focus {
            outline: none;
            border-color: var(--gold-light);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(212, 168, 67, 0.15);
        }
        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
        }
        .list-item { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 16px 20px; 
            background: var(--cream-light); 
            border-radius: 14px; 
            margin-bottom: 12px; 
            border: 1px solid var(--border-light); 
            transition: all 0.25s ease; 
        }
        .list-item:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(184, 134, 11, 0.18); 
            border-color: var(--gold-light); 
        }
        .student-info {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 0;
        }
        .student-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }
        .student-details {
            flex: 1;
            min-width: 0;
        }
        .student-name { 
            font-weight: 700; 
            font-size: 15px; 
            color: var(--dark-text); 
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .student-meta { 
            font-size: 13px; 
            color: var(--light-text); 
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .student-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .student-id-badge {
            background: var(--gold-lighter);
            color: var(--gold-dark);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .list-item-actions { 
            display: flex; 
            gap: 10px; 
            align-items: center; 
            flex-shrink: 0;
            margin-left: 12px;
        }
        .instructor-select { 
            padding: 10px 14px; 
            border: 2px solid var(--border-light); 
            border-radius: 10px; 
            font-family: 'Poppins', sans-serif; 
            font-size: 13px; 
            color: var(--dark-text); 
            background: var(--white); 
            min-width: 200px; 
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .instructor-select:focus {
            outline: none;
            border-color: var(--gold-light);
        }
        .btn-assign { 
            padding: 10px 20px; 
            background: linear-gradient(135deg, var(--gold), var(--gold-light)); 
            color: var(--white); 
            border: none; 
            border-radius: 10px; 
            font-family: 'Poppins', sans-serif; 
            font-size: 13px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-assign:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(184, 134, 11, 0.35); 
        }
        .btn-assign:disabled { 
            opacity: 0.5; 
            cursor: not-allowed; 
            transform: none; 
            box-shadow: none;
        }
        .btn-assign.success {
            background: linear-gradient(135deg, var(--success), #34d399);
        }
        .mentee-count { 
            background: var(--gold-light); 
            color: var(--white); 
            padding: 6px 14px; 
            border-radius: 20px; 
            font-size: 13px; 
            font-weight: 700; 
        }
        .mentees-panel { 
            margin-top: 14px; 
            padding: 16px; 
            background: var(--cream-light); 
            border-radius: 12px; 
            border: 1px solid var(--border-light); 
            display: none; 
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .mentees-panel.show { 
            display: block; 
        }
        .mentees-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 300px;
            overflow-y: auto;
        }
        .mentee-item { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 12px 14px; 
            background: var(--white); 
            border-radius: 10px; 
            border: 1px solid var(--border-light);
            transition: all 0.2s ease;
        }
        .mentee-item:hover {
            border-color: var(--gold-light);
        }
        .mentee-item-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .mentee-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }
        .mentee-item-name { 
            font-weight: 600; 
            font-size: 14px; 
            color: var(--dark-text); 
        }
        .mentee-item-email { 
            font-size: 12px; 
            color: var(--light-text); 
        }
        .mentee-item-meta {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .btn-remove-mentee { 
            padding: 6px 12px; 
            background: var(--danger-light); 
            color: var(--danger); 
            border: none; 
            border-radius: 8px; 
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .btn-remove-mentee:hover { 
            background: var(--danger);
            color: white;
        }
        .instructor-item {
            margin-bottom: 16px;
        }
        .instructor-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: var(--cream-light);
            border-radius: 14px;
            border: 1px solid var(--border-light);
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .instructor-header:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(184, 134, 11, 0.18);
            border-color: var(--gold-light);
        }
        .instructor-main {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .instructor-avatar { 
            width: 48px; 
            height: 48px; 
            border-radius: 50%; 
            background: linear-gradient(135deg, #667eea, #764ba2); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            font-weight: 800; 
            font-size: 16px;
        }
        .instructor-info { 
            flex: 1;
        }
        .instructor-name { 
            font-weight: 700; 
            font-size: 16px; 
            color: var(--dark-text); 
        }
        .instructor-position { 
            font-size: 13px; 
            color: var(--light-text); 
        }
        .instructor-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-toggle {
            padding: 10px 14px; 
            background: var(--white); 
            color: var(--gold); 
            border: 2px solid var(--gold-light);
            border-radius: 10px; 
            font-size: 13px; 
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-toggle:hover { 
            background: var(--gold-light);
            color: white;
        }
        .btn-toggle.active {
            background: var(--gold-light);
            color: white;
        }
        .btn-toggle i {
            transition: transform 0.3s ease;
        }
        .btn-toggle.active i {
            transform: rotate(180deg);
        }
        .empty-state { 
            text-align: center; 
            padding: 40px 20px; 
            color: var(--light-text);
            font-size: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        .empty-state i {
            font-size: 48px;
            opacity: 0.4;
            color: var(--gold-light);
        }
        .empty-state p {
            font-style: italic;
        }
        
        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 90px;
            right: 24px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .toast {
            padding: 16px 20px;
            border-radius: 12px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.4s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 280px;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .toast.success {
            background: linear-gradient(135deg, var(--success), #34d399);
        }
        .toast.error {
            background: linear-gradient(135deg, var(--danger), #f87171);
        }
        .toast.info {
            background: linear-gradient(135deg, var(--info), #38bdf8);
        }
        .toast i {
            font-size: 18px;
        }
        .toast-close {
            margin-left: auto;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            opacity: 0.8;
            font-size: 18px;
        }
        .toast-close:hover {
            opacity: 1;
        }
        
        /* Stats Badge */
        .stats-row {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .stat-card {
            background: var(--white);
            border-radius: 14px;
            padding: 20px 24px;
            border: 1px solid var(--border-soft);
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .stat-icon.blue {
            background: var(--info-light);
            color: var(--info);
        }
        .stat-icon.green {
            background: var(--success-light);
            color: var(--success);
        }
        .stat-icon.gold {
            background: var(--gold-lighter);
            color: var(--gold-dark);
        }
        .stat-info h4 {
            font-size: 24px;
            font-weight: 800;
            color: var(--dark-text);
        }
        .stat-info p {
            font-size: 13px;
            color: var(--light-text);
        }
        
        /* Loading */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Hide assigned students */
        .assigned-indicator {
            display: none;
        }
        
        /* Confirmation Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 32px;
            max-width: 400px;
            text-align: center;
            transform: scale(0.8);
            transition: all 0.3s ease;
        }
        .modal-overlay.show .modal-content {
            transform: scale(1);
        }
        .modal-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--danger-light);
            color: var(--danger);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px;
        }
        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark-text);
            margin-bottom: 12px;
        }
        .modal-message {
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 24px;
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .btn-modal {
            padding: 12px 24px;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }
        .btn-modal-cancel {
            background: var(--cream-light);
            color: var(--dark-text);
        }
        .btn-modal-cancel:hover {
            background: var(--border-light);
        }
        .btn-modal-confirm {
            background: var(--danger);
            color: white;
        }
        .btn-modal-confirm:hover {
            background: #b91c1c;
        }
        
        /* Tab Navigation */
        .tab-nav {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border-soft);
            padding-bottom: 12px;
        }
        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--light-text);
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .tab-btn:hover {
            background: var(--cream-light);
            color: var(--dark-text);
        }
        .tab-btn.active {
            background: var(--gold-lighter);
            color: var(--gold-dark);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../../../media/LOGO.jpg" alt="Logo" class="sidebar-logo" style="width: 50px; height: 50px; border-radius: 12px; object-fit: cover; border: 2px solid white; background: white; padding: 2px;">
            <div class="sidebar-brand">
                <span class="sidebar-brand-name">IBM</span>
                <span class="sidebar-brand-sub">Evaluation System</span>
            </div>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar"><i class="fas fa-user"></i></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?php echo htmlspecialchars($user_name); ?></span>
                <span class="sidebar-user-role">Program Head</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-nav-label">Menu</div>
            <a href="../dashboard.php" class="sidebar-nav-item"><i class="fas fa-chart-pie"></i><span>Overview</span></a>
            <a href="instructors.php" class="sidebar-nav-item"><i class="fas fa-chalkboard-teacher"></i><span>Instructors</span></a>
            <a href="student_enrollment.php" class="sidebar-nav-item"><i class="fas fa-user-graduate"></i><span>Enrollment</span></a>
            <a href="mentee_flow.php" class="sidebar-nav-item active"><i class="fas fa-users"></i><span>MenteeFlow</span></a>
            <a href="departments.php" class="sidebar-nav-item"><i class="fas fa-building"></i><span>Departments</span></a>
            <a href="reports.php" class="sidebar-nav-item"><i class="fas fa-file-alt"></i><span>Reports</span></a>
            <a href="settings.php" class="sidebar-nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
        </nav>
    </div>
    <div class="main-content" style="position: relative;">
        <div style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; bottom: 0; background-image: url('../../../media/LOGO.jpg'); background-size: 70%; background-position: center; background-repeat: no-repeat; opacity: 0.08; pointer-events: none; z-index: 0;"></div>
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                <div><div class="topbar-title">MenteeFlow</div><div class="topbar-subtitle">Program Head Panel</div></div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date"><i class="fas fa-calendar-alt"></i><span><?php echo date('F j, Y'); ?></span></div>
                <a href="../../../data/logout.php" class="topbar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </header>
<main class="dashboard-content">
            <div class="page-container">
                <div class="welcome-banner">
                    <div class="welcome-banner-role"><i class="fas fa-user-graduate"></i> Mentee Management</div>
                    <h1>MenteeFlow</h1>
                    <p>Assign students to instructors and manage mentee relationships. Search for students and assign them to their preferred mentors.</p>
                </div>
                
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fas fa-user-graduate"></i></div>
                        <div class="stat-info">
                            <h4><?php echo count($students); ?></h4>
                            <p>Total Students</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-users"></i></div>
                        <div class="stat-info">
                            <h4><?php echo array_sum(array_map('count', $menteeAssignments)); ?></h4>
                            <p>Assigned Mentees</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon gold"><i class="fas fa-user-plus"></i></div>
                        <div class="stat-info">
                            <h4><?php echo count($students) - array_sum(array_map('count', $menteeAssignments)); ?></h4>
                            <p>Unassigned Students</p>
                        </div>
                    </div>
                </div>
                
                <div class="grid-2">
                    <!-- Left Container: Students -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-graduate"></i> Unassigned Students</h3>
                            <span class="mentee-count" id="studentsCount"><?php echo count($students) - array_sum(array_map('count', $menteeAssignments)); ?></span>
                        </div>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="studentSearch" placeholder="Search students by name, email, or ID..." onkeyup="filterStudents()">
                        </div>
                        <div id="studentsList">
                            <?php 
                            $assignedEmails = [];
                            foreach ($menteeAssignments as $assignments) {
                                foreach ($assignments as $mentee) {
                                    $assignedEmails[] = $mentee['email'];
                                }
                            }
                            $unassignedCount = 0;
                            foreach ($students as $student): 
                                $isAssigned = in_array($student['email'], $assignedEmails);
                                if ($isAssigned) continue;
                                $unassignedCount++;
                                $initials = strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1));
                             ?>
                            <div class="list-item student-row" data-name="<?php echo strtolower($student['first_name'] . ' ' . $student['last_name']); ?>" data-email="<?php echo strtolower($student['email']); ?>" data-id="<?php echo strtolower($student['student_id'] ?? ''); ?>">
                                <div class="student-info">
                                    <div class="student-avatar"><?php echo $initials; ?></div>
                                    <div class="student-details">
                                        <div class="student-name"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></div>
                                        <div class="student-meta">
                                            <?php if (!empty($student['student_id'])): ?>
                                            <span class="student-id-badge"><i class="fas fa-id-card"></i> <?php echo htmlspecialchars($student['student_id']); ?></span>
                                            <?php endif; ?>
                                            <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($student['email']); ?></span>
                                            <span><i class="fas fa-book"></i> <?php echo htmlspecialchars($student['major_name'] ?? 'N/A'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-item-actions">
                                    <select class="instructor-select" id="instructor-select-<?php echo $student['id']; ?>">
                                        <option value="">Select Instructor...</option>
                                        <?php foreach ($instructors as $inst): 
                                            $displayName = $inst['first_name'] . ' ' . $inst['last_name'] . ' (' . ($inst['position'] ?? 'Instructor') . ')';
                                        ?>
                                        <option value="<?php echo $inst['id']; ?>"><?php echo htmlspecialchars($displayName); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn-assign" onclick="assignMentee(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars(addslashes($student['first_name'] . ' ' . $student['last_name'])); ?>')">
                                        <i class="fas fa-plus"></i> Assign
                                    </button>
                                </div>
                            </div>
                              <?php endforeach; ?>
                              <?php if ($unassignedCount == 0): ?>
                                  <div class="empty-state">
                                    <i class="fas fa-check-circle"></i>
                                    <p>All students are assigned to mentors!</p>
                                  </div>
                              <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Right Container: Instructors -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chalkboard-teacher"></i> Instructors</h3>
                            <span class="mentee-count" id="instructorsCount"><?php echo count($instructors); ?></span>
                        </div>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="instructorSearch" placeholder="Search instructors..." onkeyup="filterInstructors()">
                        </div>
                        <div id="instructorsList">
                            <?php foreach ($instructors as $inst): 
                                $initials = strtoupper(substr($inst['first_name'], 0, 1) . substr($inst['last_name'], 0, 1));
                                $assignedMentees = $menteeAssignments[$inst['id']] ?? [];
                                $gradientFrom = $inst['avatar_gradient_from'] ?? '#667eea';
                                $gradientTo = $inst['avatar_gradient_to'] ?? '#764ba2';
                            ?>
                            <div class="instructor-item" data-name="<?php echo strtolower($inst['first_name'] . ' ' . $inst['last_name']); ?>">
                                <div class="instructor-header" onclick="toggleMentees(<?php echo $inst['id']; ?>)">
                                    <div class="instructor-main">
                                        <div class="instructor-avatar" style="background: linear-gradient(135deg, <?php echo $gradientFrom; ?>, <?php echo $gradientTo; ?>);">
                                            <?php echo $initials; ?>
                                        </div>
                                        <div class="instructor-info">
                                            <div class="instructor-name"><?php echo htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']); ?></div>
                                            <div class="instructor-position"><?php echo htmlspecialchars($inst['position'] ?? 'Instructor'); ?></div>
                                        </div>
                                    </div>
                                    <div class="instructor-right">
                                        <span class="mentee-count"><?php echo count($assignedMentees); ?> mentees</span>
                                        <button class="btn-toggle" id="toggle-btn-<?php echo $inst['id']; ?>">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mentees-panel" id="mentees-panel-<?php echo $inst['id']; ?>">
                                    <?php if (empty($assignedMentees)): ?>
                                        <div class="empty-state" style="padding: 20px;">
                                            <i class="fas fa-user-slash"></i>
                                            <p>No mentees assigned yet</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="mentees-list">
                                            <?php foreach ($assignedMentees as $mentee): 
                                                $mInitials = strtoupper(substr($mentee['first_name'], 0, 1) . substr($mentee['last_name'], 0, 1));
                                            ?>
                                            <div class="mentee-item">
                                                <div class="mentee-item-info">
                                                    <div class="mentee-avatar"><?php echo $mInitials; ?></div>
                                                    <div class="mentee-item-meta">
                                                        <div class="mentee-item-name"><?php echo htmlspecialchars($mentee['first_name'] . ' ' . $mentee['last_name']); ?></div>
                                                        <div class="mentee-item-email"><?php echo htmlspecialchars($mentee['email']); ?></div>
                                                    </div>
                                                </div>
                                                <button class="btn-remove-mentee" onclick="removeMentee(<?php echo $mentee['id']; ?>, <?php echo $inst['id']; ?>, '<?php echo htmlspecialchars(addslashes($mentee['first_name'] . ' ' . $mentee['last_name'])); ?>')">
                                                    <i class="fas fa-times"></i> Remove
                                                </button>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>
    <script>
        // Global variable for modal
        let currentRemoveData = null;
        
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            let icon = 'info-circle';
            if (type === 'success') icon = 'check-circle';
            if (type === 'error') icon = 'exclamation-circle';
            
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
        
        function showRemoveModal(menteeId, instructorId, menteeName) {
            currentRemoveData = { menteeId, instructorId, menteeName };
            document.getElementById('modal-mentee-name').textContent = menteeName;
            document.getElementById('removeModal').classList.add('show');
        }
        
        function hideRemoveModal() {
            document.getElementById('removeModal').classList.remove('show');
            currentRemoveData = null;
        }
        
        function confirmRemoveMentee() {
            if (!currentRemoveData) return;
            
            const { menteeId, instructorId, menteeName } = currentRemoveData;
            hideRemoveModal();
            
            fetch('../../data/remove_mentee.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'mentee_id=' + encodeURIComponent(menteeId) + '&instructor_id=' + encodeURIComponent(instructorId)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(`<strong>${menteeName}</strong> removed successfully!`, 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(data.message || 'Failed to remove mentee', 'error');
                }
            })
            .catch(err => {
                showToast('Error: ' + err.message, 'error');
            });
        }
        
        function assignMentee(studentId, studentName) {
            const select = document.getElementById('instructor-select-' + studentId);
            const instructorId = select.value;
            if (!instructorId) {
                showToast('Please select an instructor first.', 'error');
                return;
            }
            
            const btn = select.nextElementSibling;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="loading"></span> Assigning...';
            btn.disabled = true;
            
            fetch('../../data/assign_mentee.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'instructor_id=' + encodeURIComponent(instructorId) + '&student_id=' + encodeURIComponent(studentId)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(`<strong>${studentName}</strong> assigned successfully!`, 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(data.message || 'Failed to assign mentee', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                showToast('Error: ' + err.message, 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
        
        function removeMentee(menteeId, instructorId, menteeName) {
            showRemoveModal(menteeId, instructorId, menteeName);
        }
        
        function toggleMentees(instructorId) {
            const panel = document.getElementById('mentees-panel-' + instructorId);
            const btn = document.getElementById('toggle-btn-' + instructorId);
            panel.classList.toggle('show');
            btn.classList.toggle('active');
        }
        
        function filterStudents() {
            const search = document.getElementById('studentSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.student-row');
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                const id = row.dataset.id || '';
                
                if (name.includes(search) || email.includes(search) || id.includes(search)) {
                    row.style.display = 'flex';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        function filterInstructors() {
            const search = document.getElementById('instructorSearch').value.toLowerCase();
            const items = document.querySelectorAll('.instructor-item');
            items.forEach(item => {
                const name = item.dataset.name || '';
                if (name.includes(search)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        // Update counts on page load
        document.addEventListener('DOMContentLoaded', function() {
            const studentRows = document.querySelectorAll('.student-row');
            document.getElementById('studentsCount').textContent = studentRows.length;
        });
    </script>
    
    <!-- Remove Confirmation Modal -->
    <div class="modal-overlay" id="removeModal">
        <div class="modal-content">
            <div class="modal-icon"><i class="fas fa-user-minus"></i></div>
            <div class="modal-title">Remove Mentee</div>
            <div class="modal-message">Are you sure you want to remove <strong id="modal-mentee-name"></strong> from this instructor?</div>
            <div class="modal-actions">
                <button class="btn-modal btn-modal-cancel" onclick="hideRemoveModal()">Cancel</button>
                <button class="btn-modal btn-modal-confirm" onclick="confirmRemoveMentee()">Remove</button>
            </div>
        </div>
    </div>
</body>
</html>
