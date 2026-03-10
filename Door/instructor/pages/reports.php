<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon_atoms.ico" type="image/x-icon">
    <title>Reports - Faculty Evaluation System</title>
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="dashboard-page">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../media/LOGO.jpg" alt="Logo" class="sidebar-logo">
            <div class="sidebar-brand">
                <span class="sidebar-brand-name">IBM</span>
                <span class="sidebar-brand-sub">Evaluation System</span>
            </div>
        </div>
        
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Jane Teacher'; ?></span>
                <span class="sidebar-user-role">Instructor</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="sidebar-nav-label">Menu</div>
            <a href="../dashboard.php" class="sidebar-nav-item">
                <i class="fas fa-chart-pie"></i>
                <span>Overview</span>
            </a>
            <a href="evaluations.php" class="sidebar-nav-item">
                <i class="fas fa-clipboard-check"></i>
                <span>My Evaluations</span>
            </a>
            <a href="courses.php" class="sidebar-nav-item">
                <i class="fas fa-book"></i>
                <span>My Courses</span>
            </a>
            <a href="students.php" class="sidebar-nav-item">
                <i class="fas fa-user-graduate"></i>
                <span>Students</span>
            </a>
            <a href="feedback.php" class="sidebar-nav-item">
                <i class="fas fa-comment-dots"></i>
                <span>Feedback</span>
            </a>
            <a href="reports.php" class="sidebar-nav-item active">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="profile.php" class="                <i classsidebar-nav-item">
="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="topbar-title">Reports</div>
                    <div class="topbar-subtitle">Instructor Panel</div>
                </div>
            </div>
            
            <div class="topbar-right">
                <div class="topbar-date">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?php echo date('F j, Y'); ?></span>
                </div>
                <a href="../../data/logout.php" class="topbar-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="eval-page-header">
                <h2><i class="fas fa-file-alt"></i> Reports</h2>
            </div>
            
            <div class="eval-summary-row">
                <div class="eval-summary-card">
                    <div class="eval-summary-icon teal">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>5</h4>
                        <p>PDF Reports</p>
                    </div>
                </div>
                <div class="eval-summary-card">
                    <div class="eval-summary-icon amber">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>3</h4>
                        <p>Excel Reports</p>
                    </div>
                </div>
                <div class="eval-summary-card">
                    <div class="eval-summary-icon rose">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>23</h4>
                        <p>Downloads</p>
                    </div>
                </div>
            </div>
            
            <div class="content-grid">
                <div class="content-card">
                    <div class="content-card-header">
                        <h3><i class="fas fa-list"></i> Available Reports</h3>
                    </div>
                    <div class="content-card-body">
                        <div class="report-list">
                            <div class="report-item">
                                <div class="report-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="report-info">
                                    <h4>Evaluation Summary Report</h4>
                                    <p>Spring 2026 Semester</p>
                                </div>
                                <button class="btn-primary" style="padding: 8px 16px; font-size: 12px;">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            </div>
                            
                            <div class="report-item">
                                <div class="report-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="report-info">
                                    <h4>Course Performance Report</h4>
                                    <p>All Courses - Academic Year 2025-2026</p>
                                </div>
                                <button class="btn-primary" style="padding: 8px 16px; font-size: 12px;">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            </div>
                            
                            <div class="report-item">
                                <div class="report-icon">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <div class="report-info">
                                    <h4>Student Grades Export</h4>
                                    <p>Current Semester</p>
                                </div>
                                <button class="btn-primary" style="padding: 8px 16px; font-size: 12px;">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            </div>
                            
                            <div class="report-item">
                                <div class="report-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="report-info">
                                    <h4>Feedback Analysis</h4>
                                    <p>All Courses - Comprehensive</p>
                                </div>
                                <button class="btn-primary" style="padding: 8px 16px; font-size: 12px;">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="content-card">
                    <div class="content-card-header">
                        <h3><i class="fas fa-chart-bar"></i> Quick Stats</h3>
                    </div>
                    <div class="content-card-body">
                        <div class="performance-list">
                            <div class="performance-item">
                                <div class="performance-info">
                                    <span class="performance-name">BM101 - Business Management</span>
                                    <span class="performance-value">4.8</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 96%;"></div>
                                </div>
                            </div>
                            <div class="performance-item">
                                <div class="performance-info">
                                    <span class="performance-name">MKT201 - Marketing</span>
                                    <span class="performance-value">4.6</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 92%;"></div>
                                </div>
                            </div>
                            <div class="performance-item">
                                <div class="performance-info">
                                    <span class="performance-name">SM301 - Strategic</span>
                                    <span class="performance-value">4.7</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 94%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../function/dashboard.js"></script>
</body>
</html>
