<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments - Program Head Dashboard</title>
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --gold: #d4a843; --gold-light: #e8c768; --cream: #fef9f3; --white: #ffffff; --dark-text: #1a1a2e; --light-text: #6b7280; --border-light: #e5e7eb; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--cream); }
        .page-container { padding: 24px; }
        .page-header { margin-bottom: 24px; }
        .page-title { font-size: 24px; font-weight: 700; color: var(--dark-text); }
        .page-subtitle { font-size: 13px; color: var(--light-text); margin-top: 4px; }
        .card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid var(--border-light); margin-bottom: 20px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card-title { font-size: 16px; font-weight: 700; color: var(--dark-text); }
        .dept-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .dept-card { background: var(--cream); border-radius: 12px; padding: 20px; border: 1px solid var(--border-light); display: flex; align-items: center; gap: 16px; }
        .dept-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: white; }
        .dept-info { flex: 1; }
        .dept-name { font-size: 16px; font-weight: 700; color: var(--dark-text); }
        .dept-meta { font-size: 13px; color: var(--light-text); margin-top: 4px; }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../media/LOGO.jpg" alt="Logo" class="sidebar-logo" style="width: 64px; height: 64px; border-radius: 12px; object-fit: cover;">
            <div class="sidebar-brand"><span class="sidebar-brand-name">IBM</span><span class="sidebar-brand-sub">Evaluation System</span></div>
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
            <a href="../dashboard.php" class="sidebar-nav-item"><i class="fas fa-chart-pie"></i><span>Overview</span></a>
            <a href="evaluations.php" class="sidebar-nav-item"><i class="fas fa-clipboard-check"></i><span>Evaluations</span></a>
            <a href="instructors.php" class="sidebar-nav-item"><i class="fas fa-chalkboard-teacher"></i><span>Instructors</span></a>
            <a href="courses.php" class="sidebar-nav-item"><i class="fas fa-book"></i><span>Courses</span></a>
            <a href="departments.php" class="sidebar-nav-item active"><i class="fas fa-building"></i><span>Departments</span></a>
            <a href="reports.php" class="sidebar-nav-item"><i class="fas fa-file-alt"></i><span>Reports</span></a>
            <a href="settings.php" class="sidebar-nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
        </nav>
    </aside>
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                <div><div class="topbar-title">Departments</div><div class="topbar-subtitle">Program Head Panel</div></div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date"><i class="fas fa-calendar-alt"></i><span><?php echo date('F j, Y'); ?></span></div>
                <a href="../../data/logout.php" class="topbar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </header>
        <main class="dashboard-content">
            <div class="page-container">
                <div class="page-header">
                    <h1 class="page-title">Departments</h1>
                    <p class="page-subtitle">View all departments in the institution</p>
                </div>
                <div class="dept-grid">
                    <div class="dept-card">
                        <div class="dept-icon" style="background: linear-gradient(135deg, #d4a843, #e8c768);"><i class="fas fa-cogs"></i></div>
                        <div class="dept-info"><div class="dept-name">Operational Management</div><div class="dept-meta">8 Instructors | 12 Courses</div></div>
                    </div>
                    <div class="dept-card">
                        <div class="dept-icon" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);"><i class="fas fa-dollar-sign"></i></div>
                        <div class="dept-info"><div class="dept-name">Financial Management</div><div class="dept-meta">5 Instructors | 8 Courses</div></div>
                    </div>
                    <div class="dept-card">
                        <div class="dept-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);"><i class="fas fa-chart-line"></i></div>
                        <div class="dept-info"><div class="dept-name">Marketing Management</div><div class="dept-meta">3 Instructors | 5 Courses</div></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../../function/dashboard.js"></script>
</body>
</html>
