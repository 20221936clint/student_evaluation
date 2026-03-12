<?php
session_start();
require_once '../../../data/config.php';

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'John Head';

// Fetch stats
$total_instructors = 0;
$active_instructors = 0;
$total_courses = 0;
$avg_rating = 0;

$sql = "SELECT COUNT(*) as cnt FROM instructors";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $total_instructors = $row['cnt']; }

$sql = "SELECT COUNT(*) as cnt FROM instructors WHERE status = 'active'";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $active_instructors = $row['cnt']; }

$sql = "SELECT COUNT(*) as cnt FROM courses WHERE status = 'active'";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $total_courses = $row['cnt']; }

$sql = "SELECT COALESCE(AVG(rating),0) as avg_r FROM evaluations";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $avg_rating = round($row['avg_r'], 1); }

// Fetch instructors with their details
$instructors = [];
$sql = "SELECT i.id, i.first_name, i.last_name, i.email, i.department, i.avatar_gradient_from, i.avatar_gradient_to, i.status,
        (SELECT COUNT(*) FROM courses c WHERE c.instructor_id = i.id) as course_count,
        (SELECT COALESCE(AVG(e.rating),0) FROM evaluations e WHERE e.instructor_id = i.id) as avg_rating
        FROM instructors i ORDER BY i.last_name";
$result = $conn->query($sql);
if ($result) { while ($row = $result->fetch_assoc()) { $instructors[] = $row; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructors - Program Head Dashboard</title>
    <link rel="stylesheet" href="../../../css/common.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --gold: #d4a843; --gold-light: #e8c768; --gold-dark: #b8922f; --cream: #faf8f5; --cream-light: #f5f2eb; --white: #ffffff; --dark-text: #2d3748; --dark-text-2: #4a5568; --light-text: #718096; --light-text-2: #a0aec0; --border-light: #e2e8f0; --border-soft: #edf2f7; --success: #38a169; --success-light: #c6f6d5; --info: #3182ce; --info-light: #bee3f8; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; color: var(--dark-text); }
        .page-container { padding: 32px; }
        .welcome-banner { background: linear-gradient(160deg, #B8860B 0%, #D4A843 40%, #F0D68A 100%); border-radius: 20px; padding: 36px 44px; color: white; margin-bottom: 32px; box-shadow: 0 8px 32px rgba(184, 134, 11, 0.3); position: relative; overflow: hidden; }
        .welcome-banner::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: rgba(255, 255, 255, 0.15); border-radius: 50%; }
        .welcome-banner-role { display: inline-block; background: rgba(255, 255, 255, 0.25); padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .welcome-banner h1 { font-size: 28px; font-weight: 800; margin: 0 0 12px 0; position: relative; z-index: 1; }
        .welcome-banner p { font-size: 15px; opacity: 0.95; margin: 0; max-width: 600px; position: relative; z-index: 1; }
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 32px; }
        .stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid var(--border-light); transition: all 0.3s ease; position: relative; overflow: hidden; }
        .stat-card:hover { transform: translateY(-6px); box-shadow: 0 4px 20px rgba(212, 168, 67, 0.3); }
        .stat-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .stat-card-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        .stat-card-icon.gold { background: linear-gradient(135deg, #D4A843, #FFD700); color: white; }
        .stat-card-icon.green { background: linear-gradient(135deg, #10b981, #34d399); color: white; }
        .stat-card-icon.blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); color: white; }
        .stat-card-icon.purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); color: white; }
        .stat-card-value { font-size: 38px; font-weight: 800; color: var(--dark-text); line-height: 1; margin-bottom: 8px; }
        .stat-card-label { font-size: 13px; color: var(--light-text); font-weight: 600; }
        .card { background: var(--white); border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid var(--border-soft); margin-bottom: 24px; transition: all 0.3s ease; }
        .card:hover { box-shadow: 0 4px 20px rgba(212, 168, 67, 0.2); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid var(--cream-light); }
        .card-title { font-size: 18px; font-weight: 600; color: var(--dark-text-2); display: flex; align-items: center; gap: 10px; }
        .card-title i { color: var(--gold-dark); }
        .btn-add { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: white; padding: 10px 18px; border: none; border-radius: 10px; cursor: pointer; font-weight: 500; font-size: 14px; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
        .btn-add:hover { transform: translateY(-2px); }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--light-text); background: var(--cream-light); border-bottom: 2px solid var(--border-light); }
        .data-table td { padding: 16px; font-size: 14px; color: var(--dark-text-2); border-bottom: 1px solid var(--border-soft); vertical-align: middle; }
        .data-table tbody tr { transition: all 0.2s ease; }
        .data-table tbody tr:hover { background: var(--cream-light); transform: translateX(4px); }
        .avatar { width: 40px; height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 13px; margin-right: 12px; }
        .instructor-cell { display: flex; align-items: center; }
        .instructor-name { font-weight: 600; color: var(--dark-text); }
        .status-badge { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-active { background: var(--success-light); color: var(--success); }
        .status-inactive { background: #fed7d7; color: #c53030; }
        .rating-badge { display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 14px; }
        .rating-high { background: #c6f6d5; color: #276749; }
        .rating-medium { background: #fefcbf; color: #975a16; }
        .dept-badge { display: inline-block; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; background: var(--info-light); color: #2c5282; }
        .courses-count { font-weight: 700; font-size: 16px; color: var(--dark-text-2); }
        .action-btn { background: none; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; color: var(--gold-dark); }
        .action-btn:hover { background: var(--gold); color: white; }
        .table-footer { padding: 16px; text-align: center; color: var(--light-text); font-size: 13px; background: var(--cream-light); border-radius: 0 0 12px 12px; border-top: 1px solid var(--border-soft); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .stat-card, .card { animation: fadeInUp 0.5s ease forwards; }
        .welcome-banner { animation: fadeInUp 0.5s ease forwards; }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../../media/LOGO.jpg" alt="Logo" class="sidebar-logo" style="width: 70px; height: 70px; border-radius: 16px; object-fit: cover; border: 3px solid white; background: white; padding: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
            <div class="sidebar-brand"><span class="sidebar-brand-name">IBM</span><span class="sidebar-brand-sub">Evaluation System</span></div>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar"><i class="fas fa-user"></i></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?php echo htmlspecialchars($user_name); ?></span>
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
    <div class="main-content" style="position: relative;">
        <div style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; bottom: 0; background-image: url('../../../media/LOGO.jpg'); background-size: 70%; background-position: center; background-repeat: no-repeat; opacity: 0.08; pointer-events: none; z-index: 0;"></div>
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                <div><div class="topbar-title">Instructors</div><div class="topbar-subtitle">Program Head Panel</div></div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date"><i class="fas fa-calendar-alt"></i><span><?php echo date('F j, Y'); ?></span></div>
                <a href="../../../data/logout.php" class="topbar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </header>
        <main class="dashboard-content">
            <div class="page-container">
                <div class="welcome-banner">
                    <div class="welcome-banner-role">Instructors</div>
                    <h1>Instructor Management</h1>
                    <p>Manage and monitor all instructors in your department, track their courses, ratings, and performance.</p>
                </div>

                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon gold"><i class="fas fa-chalkboard-teacher"></i></div></div>
                        <div class="stat-card-value"><?php echo $total_instructors; ?></div>
                        <div class="stat-card-label">Total Instructors</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon green"><i class="fas fa-check-circle"></i></div></div>
                        <div class="stat-card-value"><?php echo $active_instructors; ?></div>
                        <div class="stat-card-label">Active</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon blue"><i class="fas fa-book"></i></div></div>
                        <div class="stat-card-value"><?php echo $total_courses; ?></div>
                        <div class="stat-card-label">Total Courses</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon purple"><i class="fas fa-star"></i></div></div>
                        <div class="stat-card-value"><?php echo $avg_rating; ?></div>
                        <div class="stat-card-label">Avg. Rating</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users"></i> Instructor List</h3>
                        <button class="btn-add"><i class="fas fa-plus"></i> Add Instructor</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Courses</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($instructors as $inst): 
                                $initials = strtoupper(substr($inst['first_name'], 0, 1) . substr($inst['last_name'], 0, 1));
                                $rating = round($inst['avg_rating'], 1);
                                $rating_class = $rating >= 4.5 ? 'rating-high' : 'rating-medium';
                            ?>
                            <tr>
                                <td>
                                    <div class="instructor-cell">
                                        <span class="avatar" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($inst['avatar_gradient_from']); ?>, <?php echo htmlspecialchars($inst['avatar_gradient_to']); ?>);"><?php echo $initials; ?></span>
                                        <span class="instructor-name"><?php echo htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($inst['email']); ?></td>
                                <td><span class="dept-badge"><?php echo htmlspecialchars($inst['department']); ?></span></td>
                                <td><span class="courses-count"><?php echo $inst['course_count']; ?></span></td>
                                <td><span class="rating-badge <?php echo $rating_class; ?>"><i class="fas fa-star"></i> <?php echo $rating; ?></span></td>
                                <td><span class="status-badge status-<?php echo htmlspecialchars($inst['status']); ?>"><i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i><?php echo ucfirst(htmlspecialchars($inst['status'])); ?></span></td>
                                <td>
                                    <button class="action-btn" title="View Details"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn" title="Edit"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="table-footer">
                        Showing <?php echo count($instructors); ?> of <?php echo count($instructors); ?> instructors
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../../../function/dashboard.js"></script>
</body>
</html>
