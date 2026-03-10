<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon_atoms.ico" type="image/x-icon">
    <title>My Courses - Faculty Evaluation System</title>
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
            <a href="courses.php" class="sidebar-nav-item active">
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
            <a href="reports.php" class="sidebar-nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="profile.php" class="sidebar-nav-item">
                <i class="fas fa-user"></i>
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
                    <div class="topbar-title">My Courses</div>
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
                <h2><i class="fas fa-book"></i> My Courses</h2>
            </div>
            
            <div class="mentees-grid">
                <div class="mentee-card">
                    <div class="mentee-card-top">
                        <div class="mentee-avatar bg-1"><i class="fas fa-book-open"></i></div>
                        <div class="mentee-info">
                            <h4>BM101</h4>
                            <p>Business Management 101</p>
                        </div>
                    </div>
                    <div class="mentee-details">
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Students</span>
                            <span class="mentee-detail-value">30</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Rating</span>
                            <span class="mentee-detail-value gpa-high">4.8</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Schedule</span>
                            <span class="mentee-detail-value">Mon/Wed 9AM</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Room</span>
                            <span class="mentee-detail-value">Room 101</span>
                        </div>
                    </div>
                    <div class="mentee-card-actions">
                        <button class="action-btn action-btn-edit">
                            <i class="fas fa-edit"></i> View
                        </button>
                        <button class="action-btn action-btn-delete">
                            <i class="fas fa-eye"></i> Students
                        </button>
                    </div>
                </div>
                
                <div class="mentee-card">
                    <div class="mentee-card-top">
                        <div class="mentee-avatar bg-2"><i class="fas fa-book-open"></i></div>
                        <div class="mentee-info">
                            <h4>MKT201</h4>
                            <p>Marketing Principles</p>
                        </div>
                    </div>
                    <div class="mentee-details">
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Students</span>
                            <span class="mentee-detail-value">28</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Rating</span>
                            <span class="mentee-detail-value gpa-mid">4.6</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Schedule</span>
                            <span class="mentee-detail-value">Tue/Thu 11AM</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Room</span>
                            <span class="mentee-detail-value">Room 203</span>
                        </div>
                    </div>
                    <div class="mentee-card-actions">
                        <button class="action-btn action-btn-edit">
                            <i class="fas fa-edit"></i> View
                        </button>
                        <button class="action-btn action-btn-delete">
                            <i class="fas fa-eye"></i> Students
                        </button>
                    </div>
                </div>
                
                <div class="mentee-card">
                    <div class="mentee-card-top">
                        <div class="mentee-avatar bg-3"><i class="fas fa-book-open"></i></div>
                        <div class="mentee-info">
                            <h4>SM301</h4>
                            <p>Strategic Management</p>
                        </div>
                    </div>
                    <div class="mentee-details">
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Students</span>
                            <span class="mentee-detail-value">25</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Rating</span>
                            <span class="mentee-detail-value gpa-high">4.7</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Schedule</span>
                            <span class="mentee-detail-value">Fri 2PM</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Room</span>
                            <span class="mentee-detail-value">Room 305</span>
                        </div>
                    </div>
                    <div class="mentee-card-actions">
                        <button class="action-btn action-btn-edit">
                            <i class="fas fa-edit"></i> View
                        </button>
                        <button class="action-btn action-btn-delete">
                            <i class="fas fa-eye"></i> Students
                        </button>
                    </div>
                </div>
                
                <div class="mentee-card">
                    <div class="mentee-card-top">
                        <div class="mentee-avatar bg-4"><i class="fas fa-book-open"></i></div>
                        <div class="mentee-info">
                            <h4>BE201</h4>
                            <p>Business Ethics</p>
                        </div>
                    </div>
                    <div class="mentee-details">
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Students</span>
                            <span class="mentee-detail-value">32</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Rating</span>
                            <span class="mentee-detail-value gpa-mid">4.5</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Schedule</span>
                            <span class="mentee-detail-value">Wed 4PM</span>
                        </div>
                        <div class="mentee-detail-row">
                            <span class="mentee-detail-label">Room</span>
                            <span class="mentee-detail-value">Room 102</span>
                        </div>
                    </div>
                    <div class="mentee-card-actions">
                        <button class="action-btn action-btn-edit">
                            <i class="fas fa-edit"></i> View
                        </button>
                        <button class="action-btn action-btn-delete">
                            <i class="fas fa-eye"></i> Students
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../function/dashboard.js"></script>
</body>
</html>
