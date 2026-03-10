<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon_atoms.ico" type="image/x-icon">
    <title>My Evaluations - Faculty Evaluation System</title>
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
            <a href="evaluations.php" class="sidebar-nav-item active">
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
                    <div class="topbar-title">My Evaluations</div>
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
                <h2><i class="fas fa-clipboard-check"></i> My Evaluations</h2>
            </div>
            
            <div class="eval-summary-row">
                <div class="eval-summary-card">
                    <div class="eval-summary-icon teal">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>45</h4>
                        <p>Total Evaluations</p>
                    </div>
                </div>
                <div class="eval-summary-card">
                    <div class="eval-summary-icon amber">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>4.7</h4>
                        <p>Average Rating</p>
                    </div>
                </div>
                <div class="eval-summary-card">
                    <div class="eval-summary-icon rose">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>+0.2</h4>
                        <p>This Semester</p>
                    </div>
                </div>
            </div>
            
            <div class="content-card">
                <div class="content-card-header">
                    <h3><i class="fas fa-list"></i> All Evaluations</h3>
                </div>
                <div class="content-card-body">
                    <table class="eval-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Students</th>
                                <th>Rating</th>
                                <th>Semester</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Business Management 101</td>
                                <td>30</td>
                                <td><span class="rating-badge excellent">4.8</span></td>
                                <td>Spring 2026</td>
                                <td>Mar 8, 2026</td>
                            </tr>
                            <tr>
                                <td>Marketing Principles</td>
                                <td>28</td>
                                <td><span class="rating-badge good">4.6</span></td>
                                <td>Spring 2026</td>
                                <td>Mar 7, 2026</td>
                            </tr>
                            <tr>
                                <td>Strategic Management</td>
                                <td>25</td>
                                <td><span class="rating-badge excellent">4.7</span></td>
                                <td>Spring 2026</td>
                                <td>Mar 6, 2026</td>
                            </tr>
                            <tr>
                                <td>Business Ethics</td>
                                <td>32</td>
                                <td><span class="rating-badge good">4.5</span></td>
                                <td>Fall 2025</td>
                                <td>Dec 15, 2025</td>
                            </tr>
                            <tr>
                                <td>Entrepreneurship</td>
                                <td>22</td>
                                <td><span class="rating-badge average">4.3</span></td>
                                <td>Fall 2025</td>
                                <td>Dec 12, 2025</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../../function/dashboard.js"></script>
</body>
</html>
