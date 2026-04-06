<?php
require_once '../../../data/session_security.php';

// Check role access - returns array with access status
$role_access = check_role_access('program_head');
$show_role_modal = !$role_access['allowed'];

$user_name = $_SESSION['user_name'] ?? 'Program Head';

// Only fetch data if access is allowed
if (!$show_role_modal) {
    require_once '../../../data/config.php';
    
    // Fetch stats with error handling
    $total_instructors = 0;
    $active_instructors = 0;
    $total_courses = 0;
    $avg_rating = 0;
    $instructors = [];
    
    // Total instructors
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM instructors");
        $result = $stmt->fetch();
        $total_instructors = $result['cnt'] ?? 0;
    } catch (PDOException $e) {
        $total_instructors = 0;
    }
    
    // Active instructors
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM instructors WHERE status = 'active'");
        $result = $stmt->fetch();
        $active_instructors = $result['cnt'] ?? 0;
    } catch (PDOException $e) {
        $active_instructors = 0;
    }
    
    // Total courses - check if courses table exists
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'courses'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM courses WHERE status = 'active'");
            $result = $stmt->fetch();
            $total_courses = $result['cnt'] ?? 0;
        } else {
            // Fallback: count majors as courses
            $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM majors WHERE is_active = 1");
            $result = $stmt->fetch();
            $total_courses = $result['cnt'] ?? 0;
        }
    } catch (PDOException $e) {
        $total_courses = 0;
    }
    
    // Average rating - check if evaluations table exists
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'evaluations'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->query("SELECT COALESCE(AVG(rating),0) as avg_r FROM evaluations");
            $result = $stmt->fetch();
            $avg_rating = round($result['avg_r'], 1);
        }
    } catch (PDOException $e) {
        $avg_rating = 0;
    }
    
     // Fetch instructors with their details
     $promoted_ids = [];
     $program_head_emails = [];
     
     try {
         // Get promoted instructor IDs from admin_promotions
         $stmt = $pdo->query("SELECT instructor_id FROM admin_promotions WHERE promoted_to = 'program_head'");
         $promotions = $stmt->fetchAll(PDO::FETCH_COLUMN);
         $promoted_ids = array_map('intval', is_array($promotions) ? $promotions : []);
         
         // Get program head emails from program_heads table
         $stmt = $pdo->query("SELECT email FROM program_heads");
         $program_head_emails = array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);
         
         // Check which tables exist for subqueries
         $has_courses = $pdo->query("SHOW TABLES LIKE 'courses'")->rowCount() > 0;
         $has_evaluations = $pdo->query("SHOW TABLES LIKE 'evaluations'")->rowCount() > 0;
         
         $sql = "SELECT 
             i.id, 
             i.first_name, 
             i.last_name, 
             i.email, 
             i.department, 
             i.avatar_gradient_from, 
             i.avatar_gradient_to, 
             i.status";
         
         if ($has_courses) {
             $sql .= ", (SELECT COUNT(*) FROM courses c WHERE c.instructor_id = i.id) as course_count";
         } else {
             $sql .= ", 0 as course_count";
         }
         
         if ($has_evaluations) {
             $sql .= ", (SELECT COALESCE(AVG(e.rating),0) FROM evaluations e WHERE e.instructor_id = i.id) as avg_rating";
         } else {
             $sql .= ", 0 as avg_rating";
         }
         
         $sql .= " FROM instructors i ORDER BY i.last_name";
         
         $stmt = $pdo->query($sql);
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             $instructors[] = $row;
         }
     } catch (PDOException $e) {
         $instructors = [];
     }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructors - Program Head Dashboard</title>
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
            --cream-light: #f0ebe3; 
            --white: #ffffff; 
            --dark-text: #1f1f1f; 
            --dark-text-2: #4a5568; 
            --light-text: #666666; 
            --light-text-2: #a0aec0; 
            --border-light: #d4cfc5; 
            --border-soft: #e8e4da; 
            --success: #059669; 
            --success-light: #c6f6d5; 
            --info: #0284c7; 
            --info-light: #bae6fd; 
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            color: var(--dark-text); 
            overflow-x: hidden;
        }
        .page-container { 
            padding: 32px; 
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
        .stats-row { 
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 24px; 
            margin-bottom: 32px; 
        }
        .stat-card { 
            background: var(--white); 
            border-radius: 16px; 
            padding: 24px; 
            box-shadow: var(--shadow-sm); 
            border: 1px solid var(--border-light); 
            transition: all 0.3s ease; 
            position: relative; 
            overflow: hidden; 
        }
        .stat-card:hover { 
            transform: translateY(-6px); 
            box-shadow: 0 4px 20px rgba(184, 134, 11, 0.3); 
            border-color: var(--gold-light); 
        }
        .stat-card-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            margin-bottom: 16px; 
        }
        .stat-card-icon { 
            width: 52px; 
            height: 52px; 
            border-radius: 14px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 22px; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); 
        }
        .stat-card-icon.gold { 
            background: linear-gradient(135deg, var(--gold), var(--gold-light)); 
            color: var(--white); 
        }
        .stat-card-icon.green { 
            background: linear-gradient(135deg, #059669, #34d399); 
            color: var(--white); 
        }
        .stat-card-icon.blue { 
            background: linear-gradient(135deg, #0284c7, #38bdf8); 
            color: var(--white); 
        }
        .stat-card-icon.purple { 
            background: linear-gradient(135deg, #7c3aed, #a78bfa); 
            color: var(--white); 
        }
        .stat-card-value { 
            font-size: 38px; 
            font-weight: 800; 
            color: var(--dark-text); 
            line-height: 1; 
            margin-bottom: 8px; 
        }
        .stat-card-label { 
            font-size: 13px; 
            color: var(--light-text); 
            font-weight: 600; 
        }
        .card { 
            background: var(--white); 
            border-radius: 16px; 
            padding: 24px; 
            box-shadow: var(--shadow-sm); 
            border: 1px solid var(--border-soft); 
            margin-bottom: 24px; 
            transition: all 0.3s ease; 
            overflow-x: hidden;
        }
        .card:hover { 
            box-shadow: 0 4px 20px rgba(184, 134, 11, 0.2); 
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
            font-weight: 600; 
            color: var(--dark-text-2); 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        .card-title i { 
            color: var(--gold-dark); 
        }
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
          .btn-export { 
              background: var(--white); 
              color: var(--dark-text-2); 
              padding: 10px 18px; 
              border: 2px solid var(--border-light); 
              border-radius: 10px; 
              cursor: pointer; 
              display: inline-flex; 
              align-items: center; 
              gap: 8px; 
              transition: all 0.2s ease; 
              font-weight: 500;
              font-size: 14px;
          }
          .btn-export:hover { 
              border-color: var(--gold);
              color: var(--gold-dark);
          }
         .table-responsive {
             overflow-x: auto;
         }
         .btn-pagination {
             padding: 6px 12px;
             background: var(--white);
             border: 1px solid var(--border-light);
             border-radius: 6px;
             cursor: pointer;
             color: var(--dark-text-2);
             font-size: 13px;
             transition: all 0.2s ease;
         }
         .btn-pagination:hover:not(:disabled) {
             background: var(--gold);
             color: white;
             border-color: var(--gold);
         }
         .btn-pagination:disabled {
             opacity: 0.5;
             cursor: not-allowed;
         }
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed;
        }
        .data-table th { 
            padding: 14px 16px; 
            text-align: left; 
            font-size: 12px; 
            font-weight: 600; 
            text-transform: uppercase; 
            color: var(--light-text); 
            background: var(--cream-light); 
            border-bottom: 2px solid var(--border-light); 
        }
        .data-table td { 
            padding: 16px; 
            font-size: 14px; 
            color: var(--dark-text-2); 
            border-bottom: 1px solid var(--border-soft); 
            vertical-align: middle; 
            word-wrap: break-word;
        }
        .data-table tbody tr { 
            transition: all 0.2s ease; 
        }
        .data-table tbody tr:hover { 
            background: var(--cream-light); 
            transform: translateX(4px); 
        }
        .data-table tbody tr:last-child td { 
            border-bottom: none; 
        }
        .avatar { 
            width: 40px; 
            height: 40px; 
            border-radius: 10px; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            font-weight: 600; 
            font-size: 13px; 
            margin-right: 12px; 
            flex-shrink: 0;
        }
        .instructor-cell { 
            display: flex; 
            align-items: center; 
            gap: 12px;
        }
        .instructor-name { 
            font-weight: 600; 
            color: var(--dark-text); 
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        .status-badge { 
            display: inline-flex; 
            align-items: center; 
            padding: 6px 14px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600; 
        }
        .status-active { 
            background: var(--success-light); 
            color: var(--success); 
        }
        .status-inactive { 
            background: #fed7d7; 
            color: #c53030; 
        }
        .rating-badge { 
            display: inline-flex; 
            align-items: center; 
            gap: 4px; 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-weight: 700; 
            font-size: 14px; 
        }
        .rating-high { 
            background: #c6f6d5; 
            color: #276749; 
        }
        .rating-medium { 
            background: #fefcbf; 
            color: #975a16; 
        }
        .dept-badge { 
            display: inline-block; 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-size: 12px; 
            font-weight: 600; 
            background: var(--info-light); 
            color: #2c5282; 
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        .courses-count { 
            font-weight: 700; 
            font-size: 16px; 
            color: var(--dark-text-2); 
        }
        .action-btn { 
            background: none; 
            border: none; 
            padding: 8px 12px; 
            border-radius: 8px; 
            cursor: pointer; 
            transition: all 0.2s ease; 
            color: var(--gold-dark); 
        }
        .action-btn:hover { 
            background: var(--gold); 
            color: white; 
        }
        .table-footer { 
            padding: 16px; 
            text-align: center; 
            color: var(--light-text); 
            font-size: 13px; 
            background: var(--cream-light); 
            border-radius: 0 0 12px 12px; 
            border-top: 1px solid var(--border-soft); 
        }
        @keyframes fadeInUp { 
            from { opacity: 0; transform: translateY(20px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        .stat-card, .card { 
            animation: fadeInUp 0.5s ease forwards; 
        }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .welcome-banner { 
            animation: fadeInUp 0.5s ease forwards; 
        }
        
        /* Prevent horizontal scroll */
        body, html {
            overflow-x: hidden;
            max-width: 100vw;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .page-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../../media/LOGO.jpg" alt="Logo" class="sidebar-logo" style="width: 70px; height: 70px; border-radius: 16px; object-fit: cover; border: 3px solid white; background: white; padding: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
            <div class="sidebar-brand"><span class="sidebar-brand-name">IBM</span><span class="sidebar-brand-sub">Evaluation System</span></div>
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
            <a href="instructors.php" class="sidebar-nav-item active"><i class="fas fa-chalkboard-teacher"></i><span>Instructors</span></a>
            <a href="departments.php" class="sidebar-nav-item"><i class="fas fa-building"></i><span>Departments</span></a>
            <a href="reports.php" class="sidebar-nav-item"><i class="fas fa-file-alt"></i><span>Reports</span></a>
            <a href="settings.php" class="sidebar-nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
        </nav>
    </aside>
    <div class="main-content" style="position: relative;">
        <div style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; bottom: 0; background-image: url('../../../media/LOGO.jpg'); background-size: 70%; background-position: center; background-repeat: no-repeat; opacity: 0.08; pointer-events: none; z-index: 0;"></div>
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                <div><div class="topbar-title">Instructors</div><div class="topbar-subtitle">Program Head Panel</div></div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date"><i class="fas fa-calendar-alt"></i><span><?php echo date('F j, Y'); ?></span></div>
                <a href="../../../data/logout.php" class="topbar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </header>
        <main class="dashboard-content">
            <div class="page-container">
                <div class="welcome-banner">
                    <div class="welcome-banner-role">Instructors</div>
                    <h1>Instructor Management</h1>
                    <p>Manage and monitor all instructors in your department, track their courses, ratings, and performance.</p>
                </div>

                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon gold"><i class="fas fa-chalkboard-teacher"></i></div></div>
                        <div class="stat-card-value"><?php echo $total_instructors; ?></div>
                        <div class="stat-card-label">Total Instructors</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon green"><i class="fas fa-check-circle"></i></div></div>
                        <div class="stat-card-value"><?php echo $active_instructors; ?></div>
                        <div class="stat-card-label">Active</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon blue"><i class="fas fa-book"></i></div></div>
                        <div class="stat-card-value"><?php echo $total_courses; ?></div>
                        <div class="stat-card-label">Total Courses</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon purple"><i class="fas fa-star"></i></div></div>
                        <div class="stat-card-value"><?php echo $avg_rating; ?></div>
                        <div class="stat-card-label">Avg. Rating</div>
                    </div>
                </div>

                 <div class="card">
                     <div class="card-header">
                         <h3 class="card-title"><i class="fas fa-users"></i> Instructor List</h3>
                         <div style="display: flex; gap: 12px; align-items: center;">
                             <button class="btn-add" id="addInstructorBtn"><i class="fas fa-plus"></i> Add Instructor</button>
                             <button class="btn-export" id="exportBtn" style="background: var(--white); color: var(--dark-text-2); padding: 10px 18px; border: 2px solid var(--border-light); border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                                 <i class="fas fa-download"></i> Export
                             </button>
                         </div>
                     </div>
                     
                     <!-- Search and Filters -->
                     <div style="display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; align-items: center;">
                         <div style="flex: 1; min-width: 200px; position: relative;">
                             <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--light-text);"></i>
                             <input type="text" id="searchInput" placeholder="Search by name or email..." style="width: 100%; padding: 10px 12px 10px 36px; border: 2px solid var(--border-light); border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; color: var(--dark-text); background: var(--white);">
                         </div>
                         <div style="display: flex; gap: 8px; align-items: center;">
                             <label style="font-size: 13px; font-weight: 600; color: var(--dark-text-2);">Department:</label>
                             <select id="deptFilter" style="padding: 10px 16px; border: 2px solid var(--border-light); border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; color: var(--dark-text); background: var(--white); cursor: pointer; min-width: 150px;">
                                 <option value="">All Departments</option>
                                 <?php 
                                 // Get unique departments for filter
                                 $dept_list = [];
                                 foreach ($instructors as $inst) {
                                     if (!empty($inst['department']) && !in_array($inst['department'], $dept_list)) {
                                         $dept_list[] = $inst['department'];
                                     }
                                 }
                                 sort($dept_list);
                                 foreach ($dept_list as $dept): 
                                 ?>
                                 <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div style="display: flex; gap: 8px; align-items: center;">
                             <label style="font-size: 13px; font-weight: 600; color: var(--dark-text-2);">Status:</label>
                             <select id="statusFilter" style="padding: 10px 16px; border: 2px solid var(--border-light); border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; color: var(--dark-text); background: var(--white); cursor: pointer;">
                                 <option value="">All Status</option>
                                 <option value="active">Active</option>
                                 <option value="inactive">Inactive</option>
                                 <option value="program_head">Program Head</option>
                             </select>
                         </div>
                         <button id="clearFilters" style="padding: 10px 16px; background: var(--white); border: 2px solid var(--border-light); border-radius: 10px; cursor: pointer; color: var(--dark-text-2); font-size: 14px; font-weight: 500;">
                             <i class="fas fa-times"></i> Clear
                         </button>
                     </div>
                     
                     <div class="table-responsive">
                         <table class="data-table" id="instructorsTable">
                             <thead>
                                 <tr>
                                     <th style="width: 50px;"><input type="checkbox" id="selectAll" title="Select All"></th>
                                     <th data-sort="name">Instructor <i class="fas fa-sort"></i></th>
                                     <th data-sort="email">Email <i class="fas fa-sort"></i></th>
                                     <th data-sort="department">Department <i class="fas fa-sort"></i></th>
                                     <th data-sort="courses">Courses <i class="fas fa-sort"></i></th>
                                     <th data-sort="rating">Rating <i class="fas fa-sort"></i></th>
                                     <th data-sort="status">Status <i class="fas fa-sort"></i></th>
                                     <th>Actions</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php foreach ($instructors as $inst): 
                                     $initials = strtoupper(substr($inst['first_name'], 0, 1) . substr($inst['last_name'], 0, 1));
                                     $rating = round($inst['avg_rating'], 1);
                                     $rating_class = $rating >= 4.5 ? 'rating-high' : 'rating-medium';
                                     $is_program_head = in_array((int)$inst['id'], $promoted_ids ?? [], true) || 
                                                        (isset($program_head_emails) && in_array(strtolower($inst['email']), $program_head_emails ?? [], true));
                                     $status = $is_program_head ? 'program_head' : ($inst['status'] ?? 'active');
                                 ?>
                                 <tr data-instructor-id="<?php echo $inst['id']; ?>" data-status="<?php echo htmlspecialchars($status); ?>" data-department="<?php echo htmlspecialchars($inst['department'] ?? ''); ?>">
                                     <td><input type="checkbox" class="instructor-select" value="<?php echo $inst['id']; ?>"></td>
                                     <td>
                                         <div class="instructor-cell">
                                             <span class="avatar" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($inst['avatar_gradient_from'] ?? '#B8860B'); ?>, <?php echo htmlspecialchars($inst['avatar_gradient_to'] ?? '#D4A843'); ?>);"><?php echo $initials; ?></span>
                                             <span class="instructor-name"><?php echo htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']); ?></span>
                                         </div>
                                     </td>
                                     <td><?php echo htmlspecialchars($inst['email']); ?></td>
                                     <td><span class="dept-badge"><?php echo htmlspecialchars($inst['department'] ?? 'N/A'); ?></span></td>
                                     <td><span class="courses-count"><?php echo $inst['course_count'] ?? 0; ?></span></td>
                                     <td><span class="rating-badge <?php echo $rating_class; ?>"><i class="fas fa-star"></i> <?php echo $rating; ?></span></td>
                                     <td>
                                         <?php if ($is_program_head): ?>
                                             <span class="status-badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Program Head</span>
                                         <?php else: ?>
                                             <span class="status-badge status-<?php echo htmlspecialchars($inst['status'] ?? 'active'); ?>"><i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i><?php echo ucfirst(htmlspecialchars($inst['status'] ?? 'active')); ?></span>
                                         <?php endif; ?>
                                     </td>
                                     <td>
                                         <button class="action-btn view-instructor" data-id="<?php echo $inst['id']; ?>" title="View Details"><i class="fas fa-eye"></i></button>
                                         <button class="action-btn edit-instructor" data-id="<?php echo $inst['id']; ?>" title="Edit"><i class="fas fa-edit"></i></button>
                                         <?php if (!$is_program_head): ?>
                                         <button class="action-btn promote-instructor" data-id="<?php echo $inst['id']; ?>" data-name="<?php echo htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']); ?>" title="Promote to Program Head" style="color: #059669;"><i class="fas fa-user-tie"></i></button>
                                         <?php endif; ?>
                                     </td>
                                 </tr>
                                 <?php endforeach; ?>
                             </tbody>
                         </table>
                     </div>
                     
                     <!-- Table Footer with Pagination -->
                     <div class="table-footer" style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: var(--cream-light); border-radius: 0 0 12px 12px; border-top: 1px solid var(--border-soft);">
                         <div style="font-size: 13px; color: var(--light-text);">
                             Showing <span id="showingCount">0</span> of <span id="totalCount"><?php echo count($instructors); ?></span> instructors
                         </div>
                         <div style="display: flex; gap: 8px; align-items: center;">
                             <button id="prevPage" class="btn-pagination" style="padding: 6px 12px; background: var(--white); border: 1px solid var(--border-light); border-radius: 6px; cursor: pointer; color: var(--dark-text-2); font-size: 13px;" disabled>
                                 <i class="fas fa-chevron-left"></i> Previous
                             </button>
                             <span style="font-size: 13px; color: var(--light-text);">Page <span id="currentPage">1</span> of <span id="totalPages">1</span></span>
                             <button id="nextPage" class="btn-pagination" style="padding: 6px 12px; background: var(--white); border: 1px solid var(--border-light); border-radius: 6px; cursor: pointer; color: var(--dark-text-2); font-size: 13px;" disabled>
                                 Next <i class="fas fa-chevron-right"></i>
                             </button>
                         </div>
                     </div>
                 </div>
             </div>
         </main>
     </div>
     
     <script>
     // Instructor data for client-side filtering
     const instructorsData = <?php echo json_encode($instructors); ?>;
     const promotedIds = <?php echo json_encode($promoted_ids ?? []); ?>;
     const programHeadEmails = <?php echo json_encode($program_head_emails ?? []); ?>;
     
     document.addEventListener('DOMContentLoaded', function() {
         // Initialize
         let currentPage = 1;
         const rowsPerPage = 10;
         let filteredData = [...instructorsData];
         let sortColumn = 'name';
         let sortDirection = 'asc';
         
         // Filter functions
         function applyFilters() {
             const searchTerm = document.getElementById('searchInput').value.toLowerCase();
             const deptFilter = document.getElementById('deptFilter').value;
             const statusFilter = document.getElementById('statusFilter').value;
             
             filteredData = instructorsData.filter(instr => {
                 // Search filter
                 if (searchTerm && !(
                     (instr.first_name + ' ' + instr.last_name).toLowerCase().includes(searchTerm) ||
                     instr.email.toLowerCase().includes(searchTerm)
                 )) return false;
                 
                 // Department filter
                 if (deptFilter && (instr.department || '') !== deptFilter) return false;
                 
                 // Status filter
                 const isProgHead = promotedIds.includes(instr.id) || programHeadEmails.includes(instr.email?.toLowerCase());
                 const actualStatus = isProgHead ? 'program_head' : (instr.status || 'active');
                 if (statusFilter && actualStatus !== statusFilter) return false;
                 
                 return true;
             });
             currentPage = 1;
             renderTable();
         }
         
         // Sort function
         function sortData(column) {
             if (sortColumn === column) {
                 sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
             } else {
                 sortColumn = column;
                 sortDirection = 'asc';
             }
             
             filteredData.sort((a, b) => {
                 let valA, valB;
                 switch(column) {
                     case 'name': valA = (a.first_name + ' ' + a.last_name).toLowerCase(); valB = (b.first_name + ' ' + b.last_name).toLowerCase(); break;
                     case 'email': valA = a.email.toLowerCase(); valB = b.email.toLowerCase(); break;
                     case 'department': valA = (a.department || '').toLowerCase(); valB = (b.department || '').toLowerCase(); break;
                     case 'courses': valA = a.course_count || 0; valB = b.course_count || 0; break;
                     case 'rating': valA = a.avg_rating || 0; valB = b.avg_rating || 0; break;
                     case 'status': valA = a.status || ''; valB = b.status || ''; break;
                     default: return 0;
                 }
                 
                 if (valA < valB) return sortDirection === 'asc' ? -1 : 1;
                 if (valA > valB) return sortDirection === 'asc' ? 1 : -1;
                 return 0;
             });
             renderTable();
         }
         
         // Render table with pagination
         function renderTable() {
             const tbody = document.querySelector('#instructorsTable tbody');
             if (!tbody) return;
             
             const totalPages = Math.ceil(filteredData.length / rowsPerPage);
             const startIndex = (currentPage - 1) * rowsPerPage;
             const endIndex = startIndex + rowsPerPage;
             const pageData = filteredData.slice(startIndex, endIndex);
             
             tbody.innerHTML = '';
             
             if (pageData.length === 0) {
                 tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px; color: var(--light-text);">No instructors found matching your criteria</td></tr>';
             } else {
                 pageData.forEach((inst, idx) => {
                     const globalIdx = startIndex + idx;
                     const initials = (inst.first_name?.charAt(0) + inst.last_name?.charAt(0)).toUpperCase() || '??';
                     const rating = Math.round((inst.avg_rating || 0) * 10) / 10;
                     const rating_class = rating >= 4.5 ? 'rating-high' : 'rating-medium';
                     const isProgHead = promotedIds.includes(inst.id) || programHeadEmails.includes(inst.email?.toLowerCase());
                     const status = isProgHead ? 'program_head' : (inst.status || 'active');
                     
                     const tr = document.createElement('tr');
                     tr.dataset.instructorId = inst.id;
                     tr.dataset.status = status;
                     tr.dataset.department = inst.department || '';
                     
                     tr.innerHTML = `
                         <td><input type="checkbox" class="instructor-select" value="${inst.id}"></td>
                         <td>
                             <div class="instructor-cell">
                                 <span class="avatar" style="background: linear-gradient(135deg, ${inst.avatar_gradient_from || '#B8860B'}, ${inst.avatar_gradient_to || '#D4A843'});">${initials}</span>
                                 <span class="instructor-name">${escapeHtml(inst.first_name + ' ' + inst.last_name)}</span>
                             </div>
                         </td>
                         <td>${escapeHtml(inst.email)}</td>
                         <td><span class="dept-badge">${escapeHtml(inst.department || 'N/A')}</span></td>
                         <td><span class="courses-count">${inst.course_count || 0}</span></td>
                         <td><span class="rating-badge ${rating_class}"><i class="fas fa-star"></i> ${rating}</span></td>
                         <td>
                             ${isProgHead 
                                 ? '<span class="status-badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Program Head</span>'
                                 : `<span class="status-badge status-${escapeHtml(inst.status || 'active')}"><i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i>${escapeHtml(ucfirst(inst.status || 'active'))}</span>`
                             }
                         </td>
                         <td>
                             <button class="action-btn view-instructor" data-id="${inst.id}" title="View Details"><i class="fas fa-eye"></i></button>
                             <button class="action-btn edit-instructor" data-id="${inst.id}" title="Edit"><i class="fas fa-edit"></i></button>
                             ${!isProgHead ? `<button class="action-btn promote-instructor" data-id="${inst.id}" data-name="${escapeHtml(inst.first_name + ' ' + inst.last_name)}" title="Promote to Program Head" style="color: #059669;"><i class="fas fa-user-tie"></i></button>` : ''}
                         </td>
                     `;
                     tbody.appendChild(tr);
                 });
             }
             
             // Update pagination controls
             document.getElementById('showingCount').textContent = pageData.length;
             document.getElementById('totalCount').textContent = filteredData.length;
             document.getElementById('currentPage').textContent = totalPages > 0 ? currentPage : 0;
             document.getElementById('totalPages').textContent = totalPages;
             document.getElementById('prevPage').disabled = currentPage <= 1;
             document.getElementById('nextPage').disabled = currentPage >= totalPages;
         }
         
         // Utility functions
         function escapeHtml(text) {
             if (!text) return '';
             const div = document.createElement('div');
             div.textContent = text;
             return div.innerHTML;
         }
         
         function ucfirst(str) {
             return str.charAt(0).toUpperCase() + str.slice(1);
         }
         
         // Event Listeners
         document.getElementById('searchInput').addEventListener('input', applyFilters);
         document.getElementById('deptFilter').addEventListener('change', applyFilters);
         document.getElementById('statusFilter').addEventListener('change', applyFilters);
         document.getElementById('clearFilters').addEventListener('click', function() {
             document.getElementById('searchInput').value = '';
             document.getElementById('deptFilter').value = '';
             document.getElementById('statusFilter').value = '';
             applyFilters();
         });
         
         document.getElementById('prevPage').addEventListener('click', function() {
             if (currentPage > 1) {
                 currentPage--;
                 renderTable();
             }
         });
         
         document.getElementById('nextPage').addEventListener('click', function() {
             const totalPages = Math.ceil(filteredData.length / rowsPerPage);
             if (currentPage < totalPages) {
                 currentPage++;
                 renderTable();
             }
         });
         
         document.getElementById('selectAll').addEventListener('change', function() {
             const checkboxes = document.querySelectorAll('.instructor-select');
             checkboxes.forEach(cb => cb.checked = this.checked);
         });
         
         // Sortable headers
         document.querySelectorAll('th[data-sort]').forEach(th => {
             th.style.cursor = 'pointer';
             th.addEventListener('click', function() {
                 const column = this.dataset.sort;
                 sortData(column);
                 // Update sort indicator
                 document.querySelectorAll('th i.fa-sort').forEach(icon => {
                     icon.className = 'fas fa-sort';
                 });
                 const icon = this.querySelector('i');
                 if (sortDirection === 'asc') {
                     icon.className = 'fas fa-sort-up';
                 } else {
                     icon.className = 'fas fa-sort-down';
                 }
             });
         });
         
         // Action buttons
         document.addEventListener('click', function(e) {
             const btn = e.target.closest('.view-instructor');
             if (btn) {
                 const id = btn.dataset.id;
                 window.location.href = `view_instructor.php?id=${id}`;
             }
             
             const editBtn = e.target.closest('.edit-instructor');
             if (editBtn) {
                 const id = editBtn.dataset.id;
                 window.location.href = `edit_instructor.php?id=${id}`;
             }
             
             const promoteBtn = e.target.closest('.promote-instructor');
             if (promoteBtn) {
                 if (confirm('Promote ' + promoteBtn.dataset.name + ' to Program Head? This will replace the current Program Head.')) {
                     fetch('../../data/admin_promote_instructor.php', {
                         method: 'POST',
                                                 headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                                 body: 'instructor_id=' + encodeURIComponent(promoteBtn.dataset.id)
                                             })
                                             .then(r => r.json())
                                             .then(data => {
                                                 alert(data.message);
                                                 if (data.success) location.reload();
                                             });
                 }
             }
         });
         
         // Export button
         document.getElementById('exportBtn').addEventListener('click', function() {
             const selected = Array.from(document.querySelectorAll('.instructor-select:checked')).map(cb => cb.value);
             if (selected.length === 0) {
                 alert('Please select at least one instructor to export.');
                 return;
             }
             // Simple CSV export
             const headers = ['Name', 'Email', 'Department', 'Courses', 'Rating', 'Status'];
             const rows = filteredData.filter(instr => selected.includes(String(instr.id))).map(instr => [
                 instr.first_name + ' ' + instr.last_name,
                 instr.email,
                 instr.department || '',
                 instr.course_count || 0,
                 Math.round((instr.avg_rating || 0) * 10) / 10,
                 promotedIds.includes(instr.id) || programHeadEmails.includes(instr.email?.toLowerCase()) ? 'Program Head' : (instr.status || 'active')
             ]);
             
             let csvContent = headers.join(',') + '\n';
             rows.forEach(row => {
                 csvContent += row.map(cell => `"${cell}"`).join(',') + '\n';
             });
             
             const blob = new Blob([csvContent], { type: 'text/csv' });
             const url = window.URL.createObjectURL(blob);
             const a = document.createElement('a');
             a.href = url;
             a.download = `instructors_${new Date().toISOString().split('T')[0]}.csv`;
             a.click();
             window.URL.revokeObjectURL(url);
         });
         
         // Add Instructor button
         document.getElementById('addInstructorBtn').addEventListener('click', function() {
             window.location.href = 'add_instructor.php';
         });
         
         // Initial render
         renderTable();
     });
     </script>
            </div>
        </main>
    </div>
    <script src="../../../function/dashboard.js"></script>
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
