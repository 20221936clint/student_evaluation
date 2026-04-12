<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../../data/session_security.php';

$role_access = check_role_access('program_head');
$show_role_modal = !$role_access['allowed'];

$user_name = $_SESSION['user_name'] ?? 'Program Head';

if (!$show_role_modal) {
    require_once '../../../data/config.php';
    
    $majors = [];
    $has_major_subjects = false;
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'major_subjects'");
        $has_major_subjects = $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        $has_major_subjects = false;
    }
    
    try {
        $stmt = $pdo->query("SELECT * FROM majors ORDER BY sort_order, display_name");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subject_count = 0;
            $student_count = 0;
            try {
                if ($has_major_subjects) {
                    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM major_subjects WHERE major_id = ?");
                    $stmt2->execute([$row['id']]);
                    $subject_count = intval($stmt2->fetchColumn());
                }
            } catch (PDOException $e) {}
            try {
                $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM students WHERE major_id = ?");
                $stmt2->execute([$row['id']]);
                $student_count = intval($stmt2->fetchColumn());
            } catch (PDOException $e) {}
            $row['subject_count'] = $subject_count;
            $row['student_count'] = $student_count;
            $majors[] = $row;
        }
    } catch (PDOException $e) {
        // Table may not exist, try simple query
        try {
            $stmt = $pdo->query("SELECT * FROM majors ORDER BY display_name");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $row['subject_count'] = 0;
                $row['student_count'] = 0;
                $majors[] = $row;
            }
        } catch (PDOException $e2) {
            $majors = [];
        }
    }
    
    $all_subjects = [];
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'subjects'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->query("SELECT * FROM subjects WHERE is_active = 1 ORDER BY subject_name");
            $all_subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $all_subjects = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Major Management - Program Head Dashboard</title>
    <link rel="stylesheet" href="../../../css/common.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { 
            --gold: #B8860B; 
            --gold-light: #D4A843; 
            --gold-dark: #8B6914; 
            --cream: #f7f5ef; 
            --white: #ffffff; 
            --dark-text: #1f1f1f; 
            --light-text: #666666; 
            --border-light: #d4cfc5; 
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background: var(--cream);
            overflow-x: hidden;
        }
        .page-container { padding: 24px; }
        .page-header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 24px; font-weight: 700; color: var(--dark-text); }
        .page-subtitle { font-size: 13px; color: var(--light-text); margin-top: 4px; }
        .card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid var(--border-light); margin-bottom: 20px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card-title { font-size: 16px; font-weight: 700; color: var(--dark-text); display: flex; align-items: center; gap: 8px; }
        .card-title i { color: var(--gold-dark); }
        
        .btn-add {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(184, 134, 11, 0.3); }
        
        .major-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .major-card { 
            background: var(--cream); 
            border-radius: 12px; 
            padding: 20px; 
            border: 1px solid var(--border-light); 
            transition: all 0.2s ease;
        }
        .major-card:hover { transform: translateY(-4px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .major-card.inactive { opacity: 0.6; }
        .major-header { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
        .major-icon { 
            width: 48px; height: 48px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 20px; color: white; flex-shrink: 0;
        }
        .major-info { flex: 1; min-width: 0; }
        .major-name { font-size: 16px; font-weight: 700; color: var(--dark-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .major-meta { font-size: 13px; color: var(--light-text); margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .major-desc { font-size: 13px; color: var(--light-text); margin: 12px 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .major-actions { display: flex; gap: 8px; border-top: 1px solid var(--border-light); padding-top: 12px; margin-top: 12px; }
        .btn-action { flex: 1; padding: 8px; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s; }
        .btn-view { background: var(--white); color: var(--dark-text); border: 1px solid var(--border-light); }
        .btn-view:hover { background: var(--cream); }
        .btn-edit { background: var(--gold-light); color: white; }
        .btn-edit:hover { background: var(--gold-dark); }
        .btn-delete { background: #ef4444; color: white; }
        .btn-delete:hover { background: #dc2626; }
        
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 9999;
        }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 16px; padding: 24px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-title { font-size: 18px; font-weight: 700; color: var(--dark-text); }
        .modal-close { width: 32px; height: 32px; border: none; background: var(--cream); border-radius: 8px; cursor: pointer; font-size: 18px; color: var(--light-text); }
        
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--dark-text); margin-bottom: 6px; }
        .form-input, .form-select, .form-textarea { 
            width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); 
            border-radius: 10px; font-size: 14px; font-family: 'Poppins', sans-serif; 
            transition: all 0.2s;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(184, 134, 11, 0.1); }
        .form-textarea { min-height: 80px; resize: vertical; }
        .form-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; }
        .btn-submit { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: white; padding: 12px 24px; border: none; border-radius: 10px; cursor: pointer; font-weight: 500; font-size: 14px; }
        .btn-cancel { background: var(--cream); color: var(--dark-text); padding: 12px 24px; border: 1px solid var(--border-light); border-radius: 10px; cursor: pointer; font-weight: 500; font-size: 14px; }
        
        .subject-list { margin-top: 16px; }
        .subject-item { 
            display: flex; align-items: center; gap: 12px; padding: 12px; 
            background: var(--cream); border-radius: 10px; margin-bottom: 8px; 
        }
        .subject-item.prerequisite { border-left: 3px solid #ef4444; }
        .subject-icon { 
            width: 40px; height: 40px; border-radius: 10px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 18px; color: white; flex-shrink: 0;
        }
        .subject-info { flex: 1; min-width: 0; }
        .subject-name { font-size: 14px; font-weight: 600; color: var(--dark-text); }
        .subject-meta { font-size: 12px; color: var(--light-text); margin-top: 2px; }
        .subject-badge { 
            font-size: 10px; padding: 5px 10px; border-radius: 20px; 
            font-weight: 600; text-transform: uppercase; white-space: nowrap;
        }
        .badge-prereq { background: linear-gradient(135deg, #fef3c7, #fde68a); color: var(--gold-dark); border: 1px solid #fbbf24; }
        .badge-required { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8; border: 1px solid #3b82f6; }
        
        .year-header { 
            font-size: 13px; font-weight: 700; color: var(--gold-dark); 
            margin: 20px 0 12px 0; padding-bottom: 8px; 
            border-bottom: 2px solid var(--gold); display: flex; align-items: center; gap: 8px;
        }
        .year-header::before { content: ''; display: inline-block; width: 4px; height: 16px; background: var(--gold); border-radius: 2px; }
        
        .subject-row {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            background: var(--cream); border-radius: 12px; margin-bottom: 8px;
            transition: all 0.2s ease;
        }
        .subject-row:hover { transform: translateX(4px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .subject-row.prerequisite { 
            border-left: 4px solid #ef4444; 
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
        }
        .subject-row.prerequisite .subject-icon { box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3); }
        .subject-details { flex: 1; display: flex; align-items: center; gap: 12px; }
        .subject-actions { display: flex; gap: 6px; }
        .btn-icon { 
            width: 32px; height: 32px; border: none; border-radius: 8px; 
            cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .btn-star { background: #fef3c7; color: var(--gold-dark); }
        .btn-star:hover { background: #fde68a; }
        .btn-star.active { background: var(--gold-dark); color: white; }
        .btn-remove { background: #fee2e2; color: #ef4444; }
        .btn-remove:hover { background: #ef4444; color: white; }
        
        .prereq-chain { 
            background: linear-gradient(135deg, #fef3c7, #fde68a); 
            border-radius: 12px; padding: 16px; border: 1px solid #fbbf24; 
        }
        .prereq-chain-title { 
            font-size: 12px; font-weight: 700; color: var(--gold-dark); 
            text-transform: uppercase; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
        }
        .prereq-item { 
            display: flex; align-items: center; gap: 8px; padding: 8px 0; 
            border-bottom: 1px dashed rgba(184, 134, 11, 0.3); 
        }
        .prereq-item:last-child { border-bottom: none; }
        .prereq-arrow { color: var(--gold-dark); font-size: 12px; }
        .prereq-empty { color: var(--light-text); font-size: 13px; font-style: italic; }
        
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-icon { font-size: 64px; color: var(--light-text); opacity: 0.3; margin-bottom: 16px; }
        .empty-title { font-size: 18px; font-weight: 700; color: var(--dark-text); margin-bottom: 8px; }
        .empty-desc { color: var(--light-text); max-width: 400px; margin: 0 auto; }
        
        .tab-container { display: flex; gap: 4px; background: var(--cream); padding: 4px; border-radius: 10px; margin-bottom: 20px; }
        .tab { flex: 1; padding: 10px 16px; border: none; background: transparent; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 500; color: var(--light-text); transition: all 0.2s; }
        .tab.active { background: white; color: var(--dark-text); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        
        .detail-section { margin-bottom: 20px; }
        .detail-title { font-size: 14px; font-weight: 700; color: var(--dark-text); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../../media/LOGO.jpg" alt="Logo" class="sidebar-logo" style="width: 70px; height: 70px; border-radius: 16px; object-fit: cover; border: 3px solid white; background: white; padding: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
            <div class="sidebar-brand"><span class="sidebar-brand-name">IBM</span></div>
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
            <a href="mentee_flow.php" class="sidebar-nav-item"><i class="fas fa-users"></i><span>MenteeFlow</span></a>
            <a href="departments.php" class="sidebar-nav-item active"><i class="fas fa-graduation-cap"></i><span>Majors</span></a>
            <a href="reports.php" class="sidebar-nav-item"><i class="fas fa-file-alt"></i><span>Reports</span></a>
            <a href="settings.php" class="sidebar-nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
        </nav>
    </aside>
    <div class="main-content" style="position: relative;">
        <div style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; bottom: 0; background-image: url('../../../media/LOGO.jpg'); background-size: 70%; background-position: center; background-repeat: no-repeat; opacity: 0.08; pointer-events: none; z-index: 0;"></div>
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                <div><div class="topbar-title">Major Management</div><div class="topbar-subtitle">Program Head Panel</div></div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date"><i class="fas fa-calendar-alt"></i><span><?php echo date('F j, Y'); ?></span></div>
                <a href="../../../data/logout.php" class="topbar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </header>
        <main class="dashboard-content">
            <div class="page-container">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Major Management</h1>
                        <p class="page-subtitle">Manage majors, subjects, and prerequisites</p>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button class="btn-add" style="background: #6366f1;" onclick="setupSampleData()"><i class="fas fa-magic"></i> Quick Setup</button>
                        <button class="btn-add" onclick="showSubjectModal()"><i class="fas fa-book"></i> Add Subject</button>
                        <button class="btn-add" onclick="showMajorModal()"><i class="fas fa-plus"></i> Add Major</button>
                    </div>
                </div>
                
                <div class="card">
                    <div class="tab-container">
                        <button class="tab active" onclick="switchTab('majors')"><i class="fas fa-graduation-cap"></i> Majors</button>
                        <button class="tab" onclick="switchTab('subjects')"><i class="fas fa-book"></i> All Subjects</button>
                    </div>
                    
                    <div id="majorsTab">
                        <?php if (empty($majors)): ?>
                        <div class="empty-state">
                            <i class="fas fa-graduation-cap empty-icon"></i>
                            <h3 class="empty-title">No Majors Configured</h3>
                            <p class="empty-desc">Create your first major to get started. Each major can have subjects and prerequisites assigned to it.</p>
                            <button class="btn-add" style="margin: 20px auto 0;" onclick="showMajorModal()"><i class="fas fa-plus"></i> Add Major</button>
                        </div>
                        <?php else: ?>
                        <div class="major-grid">
                            <?php foreach ($majors as $major): ?>
                            <div class="major-card <?php echo $major['is_active'] ? '' : 'inactive'; ?>" data-id="<?php echo $major['id']; ?>">
                                <div class="major-header">
                                    <div class="major-icon" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($major['gradient_from']); ?>, <?php echo htmlspecialchars($major['gradient_to']); ?>);">
                                        <i class="<?php echo htmlspecialchars($major['icon_class']); ?>"></i>
                                    </div>
                                    <div class="major-info">
                                        <div class="major-name"><?php echo htmlspecialchars($major['display_name']); ?></div>
                                        <div class="major-meta">
                                            <?php echo $major['subject_count']; ?> Subject<?php echo $major['subject_count'] != 1 ? 's' : ''; ?> | 
                                            <?php echo $major['student_count']; ?> Student<?php echo $major['student_count'] != 1 ? 's' : ''; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($major['description']): ?>
                                <div class="major-desc"><?php echo htmlspecialchars($major['description']); ?></div>
                                <?php endif; ?>
                                <div class="major-actions">
                                    <button class="btn-action btn-view" onclick="viewMajorSubjects(<?php echo $major['id']; ?>, '<?php echo htmlspecialchars($major['display_name']); ?>')">
                                        <i class="fas fa-eye"></i> Subjects
                                    </button>
                                    <button class="btn-action btn-edit" onclick="editMajor(<?php echo $major['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn-action btn-delete" onclick="deleteMajor(<?php echo $major['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div id="subjectsTab" style="display: none;">
                        <?php if (empty($all_subjects)): ?>
                        <div class="empty-state">
                            <i class="fas fa-book empty-icon"></i>
                            <h3 class="empty-title">No Subjects Created</h3>
                            <p class="empty-desc">Create subjects that can be assigned to majors as requirements or prerequisites.</p>
                            <button class="btn-add" style="margin: 20px auto 0;" onclick="showSubjectModal()"><i class="fas fa-plus"></i> Add Subject</button>
                        </div>
                        <?php else: ?>
                        <div class="subject-list">
                            <?php foreach ($all_subjects as $subject): ?>
                            <div class="subject-item" data-id="<?php echo $subject['id']; ?>">
                                <div class="subject-icon" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($subject['color']); ?>, <?php echo htmlspecialchars($subject['color']); ?>);">
                                    <i class="<?php echo htmlspecialchars($subject['icon_class']); ?>"></i>
                                </div>
                                <div class="subject-info">
                                    <div class="subject-name"><?php echo htmlspecialchars($subject['subject_code']); ?> - <?php echo htmlspecialchars($subject['subject_name']); ?></div>
                                    <div class="subject-meta"><?php echo htmlspecialchars($subject['units']); ?> Units</div>
                                </div>
                                <button class="btn-action btn-edit" style="padding: 6px 12px;" onclick="editSubject(<?php echo $subject['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action btn-delete" style="padding: 6px 12px;" onclick="deleteSubject(<?php echo $subject['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Major Modal -->
    <div class="modal-overlay" id="majorModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="majorModalTitle">Add Major</h3>
                <button class="modal-close" onclick="closeMajorModal()">&times;</button>
            </div>
            <form id="majorForm" onsubmit="saveMajor(event)">
                <input type="hidden" id="majorId" name="id" value="0">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Major Code *</label>
                        <input type="text" class="form-input" id="majorName" name="major_name" placeholder="e.g., opm" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Display Name *</label>
                        <input type="text" class="form-input" id="displayName" name="display_name" placeholder="e.g., Operational Management" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" id="majorDesc" name="description" placeholder="Enter major description"></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Icon Class</label>
                        <select class="form-select" id="majorIcon" name="icon_class">
                            <option value="fas fa-graduation-cap">Graduation Cap</option>
                            <option value="fas fa-cogs">Cogs</option>
                            <option value="fas fa-dollar-sign">Dollar Sign</option>
                            <option value="fas fa-chart-line">Chart</option>
                            <option value="fas fa-briefcase">Briefcase</option>
                            <option value="fas fa-users">Users</option>
                            <option value="fas fa-book">Book</option>
                            <option value="fas fa-laptop">Laptop</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="majorActive" name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Gradient From</label>
                        <input type="color" class="form-input" id="gradientFrom" name="gradient_from" value="#d4a843">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gradient To</label>
                        <input type="color" class="form-input" id="gradientTo" name="gradient_to" value="#e8c768">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeMajorModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Save Major</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Subject Modal -->
    <div class="modal-overlay" id="subjectModal">
        <div class="modal" style="max-width: 600px;">
            <div class="modal-header">
                <h3 class="modal-title" id="subjectModalTitle">Add Subject</h3>
                <button class="modal-close" onclick="closeSubjectModal()">&times;</button>
            </div>
            <form id="subjectForm" onsubmit="saveSubject(event)">
                <input type="hidden" id="subjectId" name="id" value="0">
                <div style="background: var(--cream); padding: 16px; border-radius: 12px; margin-bottom: 16px;">
                    <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; color: var(--gold-dark);"><i class="fas fa-info-circle"></i> Basic Information</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Subject Code *</label>
                            <input type="text" class="form-input" id="subjectCode" name="subject_code" placeholder="e.g., OPM 101" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subject Name *</label>
                            <input type="text" class="form-input" id="subjectName" name="subject_name" placeholder="e.g., Introduction to Operations" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-textarea" id="subjectDesc" name="description" placeholder="Enter detailed subject description" style="min-height: 60px;"></textarea>
                    </div>
                </div>
                
                <div style="background: var(--cream); padding: 16px; border-radius: 12px; margin-bottom: 16px;">
                    <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; color: var(--gold-dark);"><i class="fas fa-clock"></i> Credit & Hours</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Credit Units *</label>
                            <input type="number" class="form-input" id="subjectUnits" name="units" value="3" step="0.5" min="0" max="10">
                            <small style="color: var(--light-text); font-size: 11px;">Number of credit hours for this subject</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lecture Hours/Week</label>
                            <input type="number" class="form-input" id="subjectLecture" name="lecture_hours" value="2" min="0" max="10">
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Lab Hours/Week</label>
                            <input type="number" class="form-input" id="subjectLab" name="lab_hours" value="0" min="0" max="10">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Credit Type</label>
                            <select class="form-select" id="subjectCreditType" name="credit_type">
                                <option value="lec">Lecture Only</option>
                                <option value="lec-lab">Lecture + Lab</option>
                                <option value="practical">Practical/Clinical</option>
                                <option value="project">Project-Based</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div style="background: var(--cream); padding: 16px; border-radius: 12px; margin-bottom: 16px;">
                    <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; color: var(--gold-dark);"><i class="fas fa-palette"></i> Appearance</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Icon</label>
                            <select class="form-select" id="subjectIcon" name="icon_class">
                                <option value="fas fa-book">Book</option>
                                <option value="fas fa-calculator">Calculator</option>
                                <option value="fas fa-chart-bar">Chart Bar</option>
                                <option value="fas fa-cogs">Cogs</option>
                                <option value="fas fa-file-alt">File</option>
                                <option value="fas fa-lightbulb">Lightbulb</option>
                                <option value="fas fa-pencil-alt">Pencil</option>
                                <option value="fas fa-laptop">Laptop</option>
                                <option value="fas fa-industry">Industry</option>
                                <option value="fas fa-truck">Truck</option>
                                <option value="fas fa-check-double">Check Double</option>
                                <option value="fas fa-chess">Chess</option>
                                <option value="fas fa-users">Users</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Color</label>
                            <input type="color" class="form-input" id="subjectColor" name="color" value="#3b82f6" style="height: 42px;">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="subjectActive" name="is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeSubjectModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Major Subjects Detail Modal -->
    <div class="modal-overlay" id="detailModal">
        <div class="modal" style="max-width: 800px;">
            <div class="modal-header">
                <h3 class="modal-title" id="detailModalTitle">Major Subjects</h3>
                <button class="modal-close" onclick="closeDetailModal()">&times;</button>
            </div>
            <div style="background: var(--cream); padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; color: var(--light-text);">
                <i class="fas fa-info-circle"></i> Click on a subject to mark it as a prerequisite. Prerequisite subjects will be highlighted with a red border.
            </div>
            <div style="margin-bottom: 16px;">
                <button class="btn-add" onclick="showAddSubjectToMajor()"><i class="fas fa-plus"></i> Add Subject</button>
            </div>
            <div id="majorSubjectsList" class="subject-list"></div>
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-light);">
                <h4 style="font-size: 14px; margin-bottom: 12px;"><i class="fas fa-sitemap"></i> Prerequisite Chain</h4>
                <div id="prereqChain" style="font-size: 13px; color: var(--light-text);"></div>
            </div>
        </div>
    </div>
    
    <!-- Add Subject to Major Modal -->
    <div class="modal-overlay" id="addSubjectModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add Subject to Major</h3>
                <button class="modal-close" onclick="closeAddSubjectModal()">&times;</button>
            </div>
            <form id="addSubjectForm" onsubmit="saveMajorSubject(event)">
                <input type="hidden" id="addMajorId" name="major_id" value="0">
                <div class="form-group">
                    <label class="form-label">Select Subject *</label>
                    <select class="form-select" id="addSubjectId" name="subject_id" required onchange="updatePrereqOptions()">
                        <option value="">Choose a subject...</option>
                        <?php foreach ($all_subjects as $subject): ?>
                        <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['subject_code']); ?> - <?php echo htmlspecialchars($subject['subject_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" id="addIsPrerequisite" name="is_prerequisite" style="width: 18px; height: 18px;" onchange="togglePrereqFor()">
                        <span style="font-size: 14px; font-weight: 500;">This is a Prerequisite Subject</span>
                    </label>
                    <p style="font-size: 12px; color: var(--light-text); margin-top: 4px;">Check this if students must pass this subject before taking other subjects.</p>
                </div>
                <div class="form-group" id="prereqForGroup" style="display: none;">
                    <label class="form-label">This subject is a prerequisite for:</label>
                    <select class="form-select" id="addPrereqFor" name="prerequisite_for">
                        <option value="">Select subject...</option>
                    </select>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Year Level</label>
                        <select class="form-select" id="addYearLevel" name="year_level">
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Semester</label>
                        <select class="form-select" id="addSemester" name="semester">
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" id="addIsPrerequisite" name="is_prerequisite" style="width: 18px; height: 18px;">
                        <span style="font-size: 14px; font-weight: 500;">Is Prerequisite Subject</span>
                    </label>
                    <p style="font-size: 12px; color: var(--light-text); margin-top: 4px;">Check this if this subject is a prerequisite for other subjects in this major.</p>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeAddSubjectModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Add Subject</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../../function/dashboard.js"></script>
    <script>
        let currentMajorId = 0;
        let majorsData = <?php echo json_encode($majors); ?>;
        let subjectsData = <?php echo json_encode($all_subjects); ?>;
        
        console.log('Majors loaded:', majorsData);
        console.log('Subjects loaded:', subjectsData);
        
        function setupSampleData() {
            if (confirm('This will add sample subjects and link them to the Operational Management major. Continue?')) {
                fetch('../../../data/setup_subjects.php')
                .then(r => r.json())
                .then(data => {
                    alert(data.message + '\nSubjects: ' + data.results.subjects_added + '\nMajor Subjects: ' + data.results.majors_subjects_added);
                    if (data.success && (data.results.subjects_added > 0 || data.results.majors_subjects_added > 0)) {
                        location.reload();
                    }
                })
                .catch(err => alert('Error: ' + err));
            }
        }
        
        function switchTab(tab) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(t => {
                if (t.textContent.toLowerCase().includes(tab)) t.classList.add('active');
            });
            document.getElementById('majorsTab').style.display = tab === 'majors' ? 'block' : 'none';
            document.getElementById('subjectsTab').style.display = tab === 'subjects' ? 'block' : 'none';
        }
        
        function showMajorModal(id = 0) {
            document.getElementById('majorModal').classList.add('active');
            document.getElementById('majorModalTitle').textContent = id ? 'Edit Major' : 'Add Major';
            document.getElementById('majorId').value = id;
            if (id) {
                const major = majorsData.find(m => m.id == id);
                if (major) {
                    document.getElementById('majorName').value = major.major_name;
                    document.getElementById('displayName').value = major.display_name;
                    document.getElementById('majorDesc').value = major.description || '';
                    document.getElementById('majorIcon').value = major.icon_class;
                    document.getElementById('gradientFrom').value = major.gradient_from;
                    document.getElementById('gradientTo').value = major.gradient_to;
                    document.getElementById('majorActive').value = major.is_active ? '1' : '0';
                }
            } else {
                document.getElementById('majorForm').reset();
            }
        }
        
        function closeMajorModal() {
            document.getElementById('majorModal').classList.remove('active');
        }
        
        function saveMajor(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('majorForm'));
            formData.append('action', document.getElementById('majorId').value ? 'update_major' : 'add_major');
            if (document.getElementById('majorId').value) {
                formData.append('id', document.getElementById('majorId').value);
            }
            
            fetch('../../../data/major_process.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    closeMajorModal();
                    location.reload();
                }
            });
        }
        
        function editMajor(id) {
            showMajorModal(id);
        }
        
        function deleteMajor(id) {
            if (confirm('Are you sure you want to delete this major? This will also remove all subject associations.')) {
                const formData = new FormData();
                formData.append('action', 'delete_major');
                formData.append('id', id);
                
                fetch('../../../data/major_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                });
            }
        }
        
        function viewMajorSubjects(majorId, majorName) {
            currentMajorId = majorId;
            document.getElementById('detailModalTitle').textContent = majorName + ' - Subjects';
            document.getElementById('detailModal').classList.add('active');
            loadMajorSubjects(majorId);
        }
        
        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('active');
        }
        
        function loadMajorSubjects(majorId) {
            const formData = new FormData();
            formData.append('action', 'get_major_subjects');
            formData.append('major_id', majorId);
            
            fetch('../../../data/major_process.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                const container = document.getElementById('majorSubjectsList');
                if (!data.success || data.subjects.length === 0) {
                    container.innerHTML = '<div class="empty-state" style="padding: 40px 20px;"><p class="empty-desc">No subjects assigned to this major yet.</p></div>';
                    return;
                }
                
                // Group by year level for better display
                const byYear = {};
                data.subjects.forEach(s => {
                    if (!byYear[s.year_level]) byYear[s.year_level] = [];
                    byYear[s.year_level].push(s);
                });
                
                let html = '';
                const sortedYears = Object.keys(byYear).sort((a, b) => {
                    const aNum = parseInt(a.replace(/\D/g, ''));
                    const bNum = parseInt(b.replace(/\D/g, ''));
                    return aNum - bNum;
                });
                
                sortedYears.forEach(year => {
                    html += `<div class="year-header">${year} <span style="font-weight: 400; font-size: 11px; color: var(--light-text);">(${byYear[year].length} subjects)</span></div>`;
                    byYear[year].forEach(s => {
                        html += `
                        <div class="subject-row ${s.is_prerequisite ? 'prerequisite' : ''}">
                            <div class="subject-icon" style="background: linear-gradient(135deg, ${s.color}, ${s.color});">
                                <i class="${s.icon_class}"></i>
                            </div>
                            <div class="subject-details">
                                <div class="subject-info">
                                    <div class="subject-name">${s.subject_code} - ${s.subject_name}</div>
                                    <div class="subject-meta"><i class="fas fa-clock" style="font-size: 10px;"></i> ${s.semester} &nbsp;|&nbsp; <i class="fas fa-hourglass-half" style="font-size: 10px;"></i> ${s.units} Units</div>
                                </div>
                            </div>
                            <span class="subject-badge ${s.is_prerequisite ? 'badge-prereq' : 'badge-required'}">
                                <i class="fas fa-${s.is_prerequisite ? 'star' : 'check'}"></i> ${s.is_prerequisite ? 'Prerequisite' : 'Required'}
                            </span>
                            <div class="subject-actions">
                                <button class="btn-icon btn-star ${s.is_prerequisite ? 'active' : ''}" onclick="togglePrerequisite(${majorId}, ${s.id}, ${s.is_prerequisite ? 'false' : 'true'})" title="${s.is_prerequisite ? 'Remove prerequisite' : 'Mark as prerequisite'}">
                                    <i class="fas fa-star"></i>
                                </button>
                                <button class="btn-icon btn-remove" onclick="removeMajorSubject(${majorId}, ${s.id})" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>`;
                    });
                });
                container.innerHTML = html;
                
                // Build prerequisite chain visual
                const prereqs = data.subjects.filter(s => s.is_prerequisite);
                let chainHtml = '';
                if (prereqs.length > 0) {
                    chainHtml = '<div class="prereq-chain">';
                    chainHtml += '<div class="prereq-chain-title"><i class="fas fa-sitemap"></i> Prerequisite Chain</div>';
                    prereqs.forEach((p, i) => {
                        chainHtml += `<div class="prereq-item">
                            <i class="fas fa-star" style="color: #ef4444;"></i>
                            <strong>${p.subject_code}</strong> - ${p.subject_name}
                        </div>`;
                        if (i < prereqs.length - 1) {
                            chainHtml += `<div class="prereq-arrow" style="padding-left: 14px;"><i class="fas fa-arrow-down" style="font-size: 10px;"></i> must pass first</div>`;
                        }
                    });
                    chainHtml += '</div>';
                } else {
                    chainHtml = '<div class="prereq-empty">No prerequisites set for this major.</div>';
                }
                document.getElementById('prereqChain').innerHTML = chainHtml;
            });
        }
        
        function showAddSubjectToMajor() {
            document.getElementById('addMajorId').value = currentMajorId;
            document.getElementById('addSubjectModal').classList.add('active');
            
            const select = document.getElementById('addSubjectId');
            select.innerHTML = '<option value="">Choose a subject...</option>';
            subjectsData.forEach(s => {
                select.innerHTML += `<option value="${s.id}">${s.subject_code} - ${s.subject_name}</option>`;
            });
        }
        
        function closeAddSubjectModal() {
            document.getElementById('addSubjectModal').classList.remove('active');
        }
        
        function saveMajorSubject(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('addSubjectForm'));
            formData.append('action', 'add_major_subject');
            formData.append('is_required', 'true');
            
            fetch('../../../data/major_process.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    closeAddSubjectModal();
                    loadMajorSubjects(currentMajorId);
                }
            });
        }
        
        function removeMajorSubject(majorId, subjectId) {
            if (confirm('Remove this subject from the major?')) {
                const formData = new FormData();
                formData.append('action', 'remove_major_subject');
                formData.append('major_id', majorId);
                formData.append('subject_id', subjectId);
                
                fetch('../../../data/major_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) loadMajorSubjects(majorId);
                });
            }
}
        
        function togglePrerequisite(majorId, subjectId, isPrereq) {
            const formData = new FormData();
            formData.append('action', 'update_major_subject_flag');
            formData.append('major_id', majorId);
            formData.append('subject_id', subjectId);
            formData.append('is_prerequisite', isPrereq);
            
            fetch('../../../data/major_process.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                if (data.success) loadMajorSubjects(majorId);
            });
        }
        
        function togglePrereqFor() {
            const isPrereq = document.getElementById('addIsPrerequisite').checked;
            document.getElementById('prereqForGroup').style.display = isPrereq ? 'block' : 'none';
            if (isPrereq) updatePrereqOptions();
        }
        
        function updatePrereqOptions() {
            const selectedId = document.getElementById('addSubjectId').value;
            const prereqForSelect = document.getElementById('addPrereqFor');
            prereqForSelect.innerHTML = '<option value="">Select subject...</option>';
            
            if (subjectsData && selectedId) {
                subjectsData.forEach(s => {
                    if (s.id != selectedId) {
                        prereqForSelect.innerHTML += `<option value="${s.id}">${s.subject_code} - ${s.subject_name}</option>`;
                    }
                });
            }
        }
         
        function showSubjectModal(id = 0) {
            document.getElementById('subjectModal').classList.add('active');
            document.getElementById('subjectModalTitle').textContent = id ? 'Edit Subject' : 'Add Subject';
            document.getElementById('subjectId').value = id;
            if (id) {
                const subject = subjectsData.find(s => s.id == id);
                if (subject) {
                    document.getElementById('subjectCode').value = subject.subject_code;
                    document.getElementById('subjectName').value = subject.subject_name;
                    document.getElementById('subjectDesc').value = subject.description || '';
                    document.getElementById('subjectUnits').value = subject.units;
                    document.getElementById('subjectIcon').value = subject.icon_class;
                    document.getElementById('subjectColor').value = subject.color;
                    document.getElementById('subjectActive').value = subject.is_active ? '1' : '0';
                }
            } else {
                document.getElementById('subjectForm').reset();
            }
        }
        
        function closeSubjectModal() {
            document.getElementById('subjectModal').classList.remove('active');
        }
        
        function saveSubject(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('subjectForm'));
            formData.append('action', document.getElementById('subjectId').value ? 'update_subject' : 'add_subject');
            if (document.getElementById('subjectId').value) {
                formData.append('id', document.getElementById('subjectId').value);
            }
            
            fetch('../../../data/major_process.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    closeSubjectModal();
                    location.reload();
                }
            });
        }
        
        function editSubject(id) {
            showSubjectModal(id);
        }
        
        function deleteSubject(id) {
            if (confirm('Are you sure you want to delete this subject?')) {
                const formData = new FormData();
                formData.append('action', 'delete_subject');
                formData.append('id', id);
                
                fetch('../../../data/major_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                });
            }
        }
    </script>
    <?php if ($show_role_modal): ?>
    <div class="modal-overlay" id="roleMismatchModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
        <div style="background: white; border-radius: 16px; padding: 32px; max-width: 450px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(220, 38, 38, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: #dc2626;"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 12px;">Access Restricted</h3>
            <p id="roleModalMessage" style="font-size: 14px; color: #6b7280; margin-bottom: 20px;"></p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <a href="../../../data/logout.php" style="background: #dc2626; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 500;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <a href="../../../Door/login.php" style="background: linear-gradient(135deg, #d4a843, #b8922f); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 500;">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            document.getElementById('roleModalMessage').textContent = <?php echo json_encode($role_access['message']); ?>;
            document.getElementById('roleMismatchModal').style.display = 'flex';
        });
    </script>
    <?php endif; ?>
</body>
</html>