<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluations - Program Head Dashboard</title>
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --gold: #d4a843;
            --gold-light: #e8c768;
            --cream: #fef9f3;
            --white: #ffffff;
            --dark-text: #1a1a2e;
            --light-text: #6b7280;
            --border-light: #e5e7eb;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--cream); }
        .page-container { padding: 24px; }
        .page-header { margin-bottom: 24px; }
        .page-title { font-size: 24px; font-weight: 700; color: var(--dark-text); }
        .page-subtitle { font-size: 13px; color: var(--light-text); margin-top: 4px; }
        .card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid var(--border-light); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card-title { font-size: 16px; font-weight: 700; color: var(--dark-text); }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--light-text); background: var(--cream); border-bottom: 1px solid var(--border-light); }
        .data-table td { padding: 14px 16px; font-size: 14px; color: var(--dark-text); border-bottom: 1px solid var(--border-light); }
        .data-table tr:hover td { background: var(--cream); }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-badge.completed { background: rgba(22, 163, 74, 0.1); color: #16a34a; }
        .status-badge.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .status-badge.overdue { background: rgba(220, 38, 38, 0.1); color: #dc2626; }
    </style>
</head>
<body>
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
            <div class="sidebar-avatar"><i class="fas fa-user"></i></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'John Head'; ?></span>
                <span class="sidebar-user-role">Program Head</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-nav-label">Menu</div>
            <a href="../dashboard.php" class="sidebar-nav-item">
                <i class="fas fa-chart-pie"></i><span>Overview</span>
            </a>
            <a href="evaluations.php" class="sidebar-nav-item active">
                <i class="fas fa-clipboard-check"></i><span>Evaluations</span>
            </a>
            <a href="instructors.php" class="sidebar-nav-item">
                <i class="fas fa-chalkboard-teacher"></i><span>Instructors</span>
            </a>
            <a href="courses.php" class="sidebar-nav-item">
                <i class="fas fa-book"></i><span>Courses</span>
            </a>
            <a href="departments.php" class="sidebar-nav-item">
                <i class="fas fa-building"></i><span>Departments</span>
            </a>
            <a href="reports.php" class="sidebar-nav-item">
                <i class="fas fa-file-alt"></i><span>Reports</span>
            </a>
            <a href="settings.php" class="sidebar-nav-item">
                <i class="fas fa-cog"></i><span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="topbar-title">Evaluations</div>
                    <div class="topbar-subtitle">Program Head Panel</div>
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date"><i class="fas fa-calendar-alt"></i><span><?php echo date('F j, Y'); ?></span></div>
                <a href="../../data/logout.php" class="topbar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="page-container">
                <div class="page-header">
                    <h1 class="page-title">All Evaluations</h1>
                    <p class="page-subtitle">View and manage all faculty evaluations</p>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Evaluation Records</h3>
                        <button class="btn btn-primary" style="background: linear-gradient(135deg, var(--gold), #b8922f); color: white; padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer;">
                            <i class="fas fa-plus"></i> New Evaluation
                        </button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th>Course</th>
                                <th>Department</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jane Smith</td>
                                <td>Business Management 101</td>
                                <td>Operational Management</td>
                                <td><span style="color: #16a34a; font-weight: 600;">4.8</span></td>
                                <td><span class="status-badge completed">Completed</span></td>
                                <td>Mar 8, 2026</td>
                                <td><button style="background: none; border: none; color: var(--gold); cursor: pointer;"><i class="fas fa-eye"></i></button></td>
                            </tr>
                            <tr>
                                <td>Michael Brown</td>
                                <td>Marketing Principles</td>
                                <td>Marketing Management</td>
                                <td><span style="color: #16a34a; font-weight: 600;">4.5</span></td>
                                <td><span class="status-badge completed">Completed</span></td>
                                <td>Mar 7, 2026</td>
                                <td><button style="background: none; border: none; color: var(--gold); cursor: pointer;"><i class="fas fa-eye"></i></button></td>
                            </tr>
                            <tr>
                                <td>Sarah Johnson</td>
                                <td>Office Management 201</td>
                                <td>Financial Management</td>
                                <td><span style="color: #f59e0b; font-weight: 600;">4.2</span></td>
                                <td><span class="status-badge pending">Pending</span></td>
                                <td>Mar 6, 2026</td>
                                <td><button style="background: none; border: none; color: var(--gold); cursor: pointer;"><i class="fas fa-eye"></i></button></td>
                            </tr>
                            <tr>
                                <td>David Lee</td>
                                <td>Business Management 201</td>
                                <td>Marketing Management</td>
                                <td><span style="color: #dc2626; font-weight: 600;">3.8</span></td>
                                <td><span class="status-badge overdue">Overdue</span></td>
                                <td>Mar 5, 2026</td>
                                <td><button style="background: none; border: none; color: var(--gold); cursor: pointer;"><i class="fas fa-eye"></i></button></td>
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
