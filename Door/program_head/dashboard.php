<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon_atoms.ico" type="image/x-icon">
    <title>Program Head Dashboard - Faculty Evaluation System</title>
   <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="./style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="dashboard-body">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../media/LOGO.jpg" alt="Logo" class="sidebar-logo">
            <span class="sidebar-title">IBM</span>
        </div>
        
        <nav class="sidebar-nav">
            <a href="#" class="nav-item active" data-section="overview">
                <i class="fas fa-chart-pie"></i>
                <span>Overview</span>
            </a>
            <a href="#" class="nav-item" data-section="evaluations">
                <i class="fas fa-clipboard-check"></i>
                <span>Evaluations</span>
            </a>
            <a href="#" class="nav-item" data-section="instructors">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Instructors</span>
            </a>
            <a href="#" class="nav-item" data-section="courses">
                <i class="fas fa-book"></i>
                <span>Courses</span>
            </a>
            <a href="#" class="nav-item" data-section="departments">
                <i class="fas fa-building"></i>
                <span>Departments</span>
            </a>
            <a href="#" class="nav-item" data-section="reports">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="#" class="nav-item" data-section="settings">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
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
                    <span class="badge">3</span>
                </button>
                
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-info">
                        <span class="user-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'John Head'; ?></span>
                        <span class="user-role">Program Head</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=John+Head&background=D4A843&color=fff" alt="User" class="user-avatar">
                </div>
            </div>
        </header>

        <main class="dashboard-main">
            <div class="page-header">
                <h1>Dashboard Overview</h1>
                <p>Welcome back, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'John Head'; ?>!</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(212, 168, 67, 0.1);">
                        <i class="fas fa-chalkboard-teacher" style="color: var(--gold-primary);"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">24</span>
                        <span class="stat-label">Total Instructors</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(76, 175, 80, 0.1);">
                        <i class="fas fa-check-circle" style="color: #4CAF50;"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">156</span>
                        <span class="stat-label">Completed Evaluations</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(33, 150, 243, 0.1);">
                        <i class="fas fa-book" style="color: #2196F3;"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">48</span>
                        <span class="stat-label">Active Courses</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(255, 152, 0, 0.1);">
                        <i class="fas fa-star" style="color: #FF9800;"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">4.5</span>
                        <span class="stat-label">Avg. Rating</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Recent Evaluations</h3>
                        <a href="#" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Instructor</th>
                                    <th>Course</th>
                                    <th>Rating</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>Business Management 101</td>
                                    <td><span class="rating">4.8</span></td>
                                    <td>Mar 8, 2026</td>
                                </tr>
                                <tr>
                                    <td>Michael Brown</td>
                                    <td>Marketing Principles</td>
                                    <td><span class="rating">4.5</span></td>
                                    <td>Mar 7, 2026</td>
                                </tr>
                                <tr>
                                    <td>Sarah Johnson</td>
                                    <td>Financial Accounting</td>
                                    <td><span class="rating">4.2</span></td>
                                    <td>Mar 6, 2026</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Department Performance</h3>
                    </div>
                    <div class="card-body">
                        <div class="performance-list">
                            <div class="performance-item">
                                <div class="performance-info">
                                    <span class="performance-name">Business Administration</span>
                                    <span class="performance-value">4.6</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 92%;"></div>
                                </div>
                            </div>
                            <div class="performance-item">
                                <div class="performance-info">
                                    <span class="performance-name">Marketing</span>
                                    <span class="performance-value">4.3</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 86%;"></div>
                                </div>
                            </div>
                            <div class="performance-item">
                                <div class="performance-info">
                                    <span class="performance-name">Finance</span>
                                    <span class="performance-value">4.1</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 82%;"></div>
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
