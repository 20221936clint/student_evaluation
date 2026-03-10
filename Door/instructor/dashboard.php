<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon_atoms.ico" type="image/x-icon">
    <title>Instructor Dashboard - Faculty Evaluation System</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="dashboard-page">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../media/LOGO.jpg" alt="Logo" class="sidebar-logo" style="width: 64px; height: 64px; border-radius: 12px; object-fit: cover;">
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
            <a href="dashboard.php" class="sidebar-nav-item active">
                <i class="fas fa-chart-pie"></i>
                <span>Overview</span>
            </a>
            <a href="pages/evaluations.php" class="sidebar-nav-item">
                <i class="fas fa-clipboard-check"></i>
                <span>My Evaluations</span>
            </a>
            <a href="pages/courses.php" class="sidebar-nav-item">
                <i class="fas fa-book"></i>
                <span>My Courses</span>
            </a>
            <a href="pages/students.php" class="sidebar-nav-item">
                <i class="fas fa-user-graduate"></i>
                <span>Students</span>
            </a>
            <a href="pages/feedback.php" class="sidebar-nav-item">
                <i class="fas fa-comment-dots"></i>
                <span>Feedback</span>
            </a>
            <a href="pages/reports.php" class="sidebar-nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="pages/profile.php" class="sidebar-nav-item">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="topbar-title">Dashboard</div>
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

        <!-- Dashboard Content -->
        <main class="dashboard-content">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="welcome-banner-role">Instructor</div>
                <h1>Welcome back, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Jane Teacher'; ?>!</h1>
                <p>Track your course performance, view student evaluations, and improve your teaching methods.</p>
            </div>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon gold">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">6</div>
                    <div class="stat-card-label">My Courses</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon blue">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">180</div>
                    <div class="stat-card-label">Total Students</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon purple">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">4.7</div>
                    <div class="stat-card-label">My Rating</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon green">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">12</div>
                    <div class="stat-card-label">New Feedback</div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Recent Evaluations Card -->
                <div class="content-card">
                    <div class="content-card-header">
                        <h3><i class="fas fa-clipboard-list"></i> My Recent Evaluations</h3>
                        <a href="pages/evaluations.php" class="view-all">View All</a>
                    </div>
                    <div class="content-card-body">
                        <table class="eval-table">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Students</th>
                                    <th>Rating</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Business Management 101</td>
                                    <td>30</td>
                                    <td><span class="rating-badge excellent">4.8</span></td>
                                    <td>Mar 8, 2026</td>
                                </tr>
                                <tr>
                                    <td>Marketing Principles</td>
                                    <td>28</td>
                                    <td><span class="rating-badge good">4.6</span></td>
                                    <td>Mar 7, 2026</td>
                                </tr>
                                <tr>
                                    <td>Strategic Management</td>
                                    <td>25</td>
                                    <td><span class="rating-badge excellent">4.7</span></td>
                                    <td>Mar 6, 2026</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Feedback Card -->
                <div class="content-card">
                    <div class="content-card-header">
                        <h3><i class="fas fa-comment-alt"></i> Recent Feedback</h3>
                        <a href="pages/feedback.php" class="view-all">View All</a>
                    </div>
                    <div class="content-card-body">
                        <div class="feedback-list">
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <span class="feedback-course">Business Management 101</span>
                                    <span class="feedback-date">Mar 8, 2026</span>
                                </div>
                                <p class="feedback-text">"Excellent teaching methodology. Very engaging and practical."</p>
                                <div class="feedback-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <span class="feedback-course">Marketing Principles</span>
                                    <span class="feedback-date">Mar 5, 2026</span>
                                </div>
                                <p class="feedback-text">"Great examples and case studies. Would love more interactive sessions."</p>
                                <div class="feedback-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../function/dashboard.js"></script>
</body>
</html>
