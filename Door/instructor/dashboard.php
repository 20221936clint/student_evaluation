<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon_atoms.ico" type="image/x-icon">
    <title>Instructor Dashboard - Faculty Evaluation System</title>
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="./style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="dashboard-body">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../media/LOGO.jpg" alt="Logo" class="sidebar-logo">
            <span class="sidebar-title">IBM</span>
        </div>
        
        <nav class="sidebar-nav">
            <a href="#" class="nav-item active" data-section="overview">
                <i class="fas fa-chart-pie"></i>
                <span>Overview</span>
            </a>
            <a href="#" class="nav-item" data-section="my-evaluations">
                <i class="fas fa-clipboard-check"></i>
                <span>My Evaluations</span>
            </a>
            <a href="#" class="nav-item" data-section="my-courses">
                <i class="fas fa-book"></i>
                <span>My Courses</span>
            </a>
            <a href="#" class="nav-item" data-section="students">
                <i class="fas fa-user-graduate"></i>
                <span>Students</span>
            </a>
            <a href="#" class="nav-item" data-section="feedback">
                <i class="fas fa-comment-dots"></i>
                <span>Feedback</span>
            </a>
            <a href="#" class="nav-item" data-section="reports">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="#" class="nav-item" data-section="profile">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../../data/logout.php" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
            
            <div class="topbar-actions">
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="badge">2</span>
                </button>
                
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-info">
                        <span class="user-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Jane Teacher'; ?></span>
                        <span class="user-role">Instructor</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Jane+Teacher&background=D4A843&color=fff" alt="User" class="user-avatar">
                </div>
            </div>
        </header>

        <main class="dashboard-main">
            <div class="page-header">
                <h1>Dashboard Overview</h1>
                <p>Welcome back, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Jane Teacher'; ?>!</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(212, 168, 67, 0.1);">
                        <i class="fas fa-book" style="color: var(--gold-primary);"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">6</span>
                        <span class="stat-label">My Courses</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-user-graduate" style="color: #4CAF50;"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">180</span>
                        <span class="stat-label">Total Students</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(33, 150, 243, 0.1);">
                        <i class="fas fa-star" style="color: #2196F3;"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">4.7</span>
                        <span class="stat-label">My Rating</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(255, 152, 0, 0.1);">
                        <i class="fas fa-comment-dots" style="color: #FF9800;"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">12</span>
                        <span class="stat-label">New Feedback</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>My Recent Evaluations</h3>
                        <a href="#" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <table class="data-table">
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
                                    <td><span class="rating">4.8</span></td>
                                    <td>Mar 8, 2026</td>
                                </tr>
                                <tr>
                                    <td>Marketing Principles</td>
                                    <td>28</td>
                                    <td><span class="rating">4.6</span></td>
                                    <td>Mar 7, 2026</td>
                                </tr>
                                <tr>
                                    <td>Strategic Management</td>
                                    <td>25</td>
                                    <td><span class="rating">4.7</span></td>
                                    <td>Mar 6, 2026</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Recent Feedback</h3>
                    </div>
                    <div class="card-body">
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
