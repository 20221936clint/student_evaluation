<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructors - Program Head Dashboard</title>
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
        .card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid var(--border-light); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card-title { font-size: 16px; font-weight: 700; color: var(--dark-text); }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--light-text); background: var(--cream); border-bottom: 1px solid var(--border-light); }
        .data-table td { padding: 14px 16px; font-size: 14px; color: var(--dark-text); border-bottom: 1px solid var(--border-light); }
        .data-table tr:hover td { background: var(--cream); }
        .avatar { width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 12px; margin-right: 10px; }
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
            <a href="instructors.php" class="sidebar-nav-item active"><i class="fas fa-chalkboard-teacher"></i><span>Instructors</span></a>
            <a href="courses.php" class="sidebar-nav-item"><i class="fas fa-book"></i><span>Courses</span></a>
            <a href="departments.php" class="sidebar-nav-item"><i class="fas fa-building"></i><span>Departments</span></a>
            <a href="reports.php" class="sidebar-nav-item"><i class="fas fa-file-alt"></i><span>Reports</span></a>
            <a href="settings.php" class="sidebar-nav-item"><i class="fas fa-cog"></i><span>Settings</span></a>
        </nav>
    </aside>
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                <div><div class="topbar-title">Instructors</div><div class="topbar-subtitle">Program Head Panel</div></div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date"><i class="fas fa-calendar-alt"></i><span><?php echo date('F j, Y'); ?></span></div>
                <a href="../../data/logout.php" class="topbar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </header>
        <main class="dashboard-content">
            <div class="page-container">
                <div class="page-header">
                    <h1 class="page-title">All Instructors</h1>
                    <p class="page-subtitle">Manage instructors in your department</p>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users"></i> Instructor List</h3>
                        <button style="background: linear-gradient(135deg, var(--gold), #b8922f); color: white; padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer;"><i class="fas fa-plus"></i> Add Instructor</button>
                    </div>
                    <table class="data-table">
                        <thead><tr><th>Instructor</th><th>Email</th><th>Department</th><th>Courses</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <tr><td><span class="avatar" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">JS</span>Jane Smith</td><td>jane.smith@cjcm.edu</td><td>Operational Management</td><td>3</td><td><span style="color: #16a34a; font-weight: 600;">4.8</span></td><td><span style="color: #16a34a;">Active</span></td><td><button style="background: none; border: none; color: var(--gold); cursor: pointer;"><i class="fas fa-eye"></i></button></td></tr>
                            <tr><td><span class="avatar" style="background: linear-gradient(135deg, #16a34a, #4ade80);">MB</span>Michael Brown</td><td>michael.brown@cjcm.edu</td><td>Financial Management</td><td>2</td><td><span style="color: #16a34a; font-weight: 600;">4.5</span></td><td><span style="color: #16a34a;">Active</span></td><td><button style="background: none; border: none; color: var(--gold); cursor: pointer;"><i class="fas fa-eye"></i></button></td></tr>
                            <tr><td><span class="avatar" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">SJ</span>Sarah Johnson</td><td>sarah.johnson@cjcm.edu</td><td>Marketing Management</td><td>4</td><td><span style="color: #f59e0b; font-weight: 600;">4.2</span></td><td><span style="color: #16a34a;">Active</span></td><td><button style="background: none; border: none; color: var(--gold); cursor: pointer;"><i class="fas fa-eye"></i></button></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../../function/dashboard.js"></script>
</body>
</html>
 