<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon_atoms.ico" type="image/x-icon">
    <title>Students - Faculty Evaluation System</title>
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
            <a href="students.php" class="sidebar-nav-item active">
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
                    <div class="topbar-title">Students</div>
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
                <h2><i class="fas fa-user-graduate"></i> Students</h2>
                <div class="mentees-header-right">
                    <select class="eval-filter-select">
                        <option>All Courses</option>
                        <option>BM101 - Business Management 101</option>
                        <option>MKT201 - Marketing Principles</option>
                        <option>SM301 - Strategic Management</option>
                    </select>
                </div>
            </div>
            
            <div class="eval-summary-row">
                <div class="eval-summary-card">
                    <div class="eval-summary-icon teal">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>180</h4>
                        <p>Total Students</p>
                    </div>
                </div>
                <div class="eval-summary-card">
                    <div class="eval-summary-icon amber">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>92%</h4>
                        <p>Avg Attendance</p>
                    </div>
                </div>
                <div class="eval-summary-card">
                    <div class="eval-summary-icon rose">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>15</h4>
                        <p>Late Submissions</p>
                    </div>
                </div>
            </div>
            
            <div class="content-card">
                <div class="content-card-header">
                    <h3><i class="fas fa-users"></i> All Students</h3>
                    <input type="text" class="eval-search-input" placeholder="Search students..." style="width: 250px;">
                </div>
                <div class="content-card-body">
                    <table class="eval-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Attendance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="student-item-inline">
                                        <div class="student-avatar-sm" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">JD</div>
                                        <span>John Doe</span>
                                    </div>
                                </td>
                                <td>john.doe@student.edu</td>
                                <td><span class="course-code">BM101</span></td>
                                <td>3rd Year</td>
                                <td><span class="status-badge completed">95%</span></td>
                                <td>
                                    <button class="action-btn action-btn-edit"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-item-inline">
                                        <div class="student-avatar-sm" style="background: linear-gradient(135deg, #10b981, #34d399);">JW</div>
                                        <span>Jane Wilson</span>
                                    </div>
                                </td>
                                <td>jane.wilson@student.edu</td>
                                <td><span class="course-code">BM101</span></td>
                                <td>2nd Year</td>
                                <td><span class="status-badge completed">92%</span></td>
                                <td>
                                    <button class="action-btn action-btn-edit"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-item-inline">
                                        <div class="student-avatar-sm" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">MJ</div>
                                        <span>Mike Johnson</span>
                                    </div>
                                </td>
                                <td>mike.j@student.edu</td>
                                <td><span class="course-code">MKT201</span></td>
                                <td>3rd Year</td>
                                <td><span class="status-badge pending">88%</span></td>
                                <td>
                                    <button class="action-btn action-btn-edit"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-item-inline">
                                        <div class="student-avatar-sm" style="background: linear-gradient(135deg, #f43f5e, #fb7185);">SW</div>
                                        <span>Sarah Williams</span>
                                    </div>
                                </td>
                                <td>sarah.w@student.edu</td>
                                <td><span class="course-code">SM301</span></td>
                                <td>4th Year</td>
                                <td><span class="status-badge completed">98%</span></td>
                                <td>
                                    <button class="action-btn action-btn-edit"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="student-item-inline">
                                        <div class="student-avatar-sm" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">TB</div>
                                        <span>Tom Brown</span>
                                    </div>
                                </td>
                                <td>tom.b@student.edu</td>
                                <td><span class="course-code">BM101</span></td>
                                <td>2nd Year</td>
                                <td><span class="status-badge completed">90%</span></td>
                                <td>
                                    <button class="action-btn action-btn-edit"><i class="fas fa-eye"></i></button>
                                </td>
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
