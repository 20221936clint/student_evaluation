<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon_atoms.ico" type="image/x-icon">
    <title>Program Head Dashboard - Faculty Evaluation System</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="./style/dashboard.css">
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
                <span class="sidebar-user-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'John Head'; ?></span>
                <span class="sidebar-user-role">Program Head</span>
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
                <span>Evaluations</span>
            </a>
            <a href="pages/instructors.php" class="sidebar-nav-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Instructors</span>
            </a>
            <a href="pages/courses.php" class="sidebar-nav-item">
                <i class="fas fa-book"></i>
                <span>Courses</span>
            </a>
            <a href="pages/departments.php" class="sidebar-nav-item">
                <i class="fas fa-building"></i>
                <span>Departments</span>
            </a>
            <a href="pages/reports.php" class="sidebar-nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="pages/settings.php" class="sidebar-nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
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
                    <div class="topbar-subtitle">Program Head Panel</div>
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
                <div class="welcome-banner-role">Program Head</div>
                <h1>Welcome back, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'John Head'; ?>!</h1>
                <p>Monitor instructor performance, manage evaluations, and track department progress all in one place.</p>
            </div>

            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon gold">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">24</div>
                    <div class="stat-card-label">Total Instructors</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">156</div>
                    <div class="stat-card-label">Completed Evaluations</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon blue">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">48</div>
                    <div class="stat-card-label">Active Courses</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon purple">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">4.5</div>
                    <div class="stat-card-label">Avg. Rating</div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Recent Evaluations Card -->
                <div class="content-card">
                    <div class="content-card-header">
                        <h3><i class="fas fa-clipboard-list"></i> Recent Evaluations</h3>
                        <a href="pages/evaluations.php" class="view-all">View All</a>
                    </div>
                    <div class="content-card-body">
                        <table class="eval-table">
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
                                    <td><span class="rating-badge excellent">4.8</span></td>
                                    <td>Mar 8, 2026</td>
                                </tr>
                                <tr>
                                    <td>Michael Brown</td>
                                    <td>Marketing Principles</td>
                                    <td><span class="rating-badge good">4.5</span></td>
                                    <td>Mar 7, 2026</td>
                                </tr>
                                <tr>
                                    <td>Sarah Johnson</td>
                                    <td>Financial Accounting</td>
                                    <td><span class="rating-badge average">4.2</span></td>
                                    <td>Mar 6, 2026</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Department Performance Card -->
                <div class="content-card">
                    <div class="content-card-header">
                        <h3><i class="fas fa-chart-bar"></i> Department Performance</h3>
                    </div>
                    <div class="content-card-body">
                        <div class="performance-list">
                            <div class="performance-item">
                                <div class="performance-info">
                                    <span class="performance-name">Operational Management</span>
                                    <span class="performance-value">4.6</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 92%;"></div>
                                </div>
                            </div>
                            <div class="performance-item">
                                <div class="performance-info">
                                    <span class="performance-name">Financial Management</span>
                                    <span class="performance-value">4.3</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 86%;"></div>
                                </div>
                            </div>
                            <div class="performance-item">
                                <div class="performance-info">
                                    <span class="performance-name">Marketing Management</span>
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
