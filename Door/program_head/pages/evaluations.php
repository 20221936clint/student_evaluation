<?php
session_start();
require_once '../../../data/config.php';

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'John Head';

// Fetch stats
$total_evaluations = 0;
$completed = 0;
$pending = 0;
$avg_rating = 0;

$sql = "SELECT COUNT(*) as cnt FROM evaluations";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $total_evaluations = $row['cnt']; }

$sql = "SELECT COUNT(*) as cnt FROM evaluations WHERE status = 'completed'";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $completed = $row['cnt']; }

$sql = "SELECT COUNT(*) as cnt FROM evaluations WHERE status = 'pending'";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $pending = $row['cnt']; }

$sql = "SELECT COALESCE(AVG(rating),0) as avg_r FROM evaluations";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $avg_rating = round($row['avg_r'], 1); }

// Fetch evaluation records
$evaluations = [];
$sql = "SELECT CONCAT(i.first_name, ' ', i.last_name) as instructor_name, c.course_name, e.department, e.rating, e.status, e.evaluation_date FROM evaluations e JOIN instructors i ON e.instructor_id = i.id JOIN courses c ON e.course_id = c.id ORDER BY e.evaluation_date DESC";
$result = $conn->query($sql);
if ($result) { while ($row = $result->fetch_assoc()) { $evaluations[] = $row; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluations - Program Head Dashboard</title>
    <link rel="stylesheet" href="../../../css/common.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --gold: #d4a843; --gold-light: #e8c768; --gold-dark: #b8922f; --cream: #fef9f3; --white: #ffffff; --dark-text: #1a1a2e; --light-text: #6b7280; --border-light: #e5e7eb; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; }
        .page-container { padding: 32px; }
        .welcome-banner { background: linear-gradient(160deg, #B8860B 0%, #D4A843 40%, #F0D68A 100%); border-radius: 20px; padding: 36px 44px; color: white; margin-bottom: 32px; box-shadow: 0 8px 32px rgba(184, 134, 11, 0.3); position: relative; overflow: hidden; }
        .welcome-banner::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: rgba(255, 255, 255, 0.15); border-radius: 50%; }
        .welcome-banner::after { content: ''; position: absolute; bottom: -30%; right: 20%; width: 200px; height: 200px; background: rgba(255, 255, 255, 0.08); border-radius: 50%; }
        .welcome-banner-role { display: inline-block; background: rgba(255, 255, 255, 0.25); padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; border: 1px solid rgba(255, 255, 255, 0.3); }
        .welcome-banner h1 { font-size: 28px; font-weight: 800; margin: 0 0 12px 0; position: relative; z-index: 1; }
        .welcome-banner p { font-size: 15px; opacity: 0.95; margin: 0; max-width: 600px; position: relative; z-index: 1; }
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 32px; }
        .stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid var(--border-light); transition: all 0.3s ease; position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: linear-gradient(135deg, #D4A843, #FFD700, #B8860B); opacity: 0; transition: opacity 0.3s ease; }
        .stat-card:hover { transform: translateY(-6px); box-shadow: 0 4px 20px rgba(212, 168, 67, 0.3); border-color: var(--gold-light); }
        .stat-card:hover::before { opacity: 1; }
        .stat-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .stat-card-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        .stat-card-icon.gold { background: linear-gradient(135deg, #D4A843, #FFD700); color: white; }
        .stat-card-icon.green { background: linear-gradient(135deg, #10b981, #34d399); color: white; }
        .stat-card-icon.blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); color: white; }
        .stat-card-icon.purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); color: white; }
        .stat-card-value { font-size: 38px; font-weight: 800; color: var(--dark-text); line-height: 1; margin-bottom: 8px; letter-spacing: -1px; }
        .stat-card-label { font-size: 13px; color: var(--light-text); font-weight: 600; }
        .card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid var(--border-light); transition: all 0.3s ease; }
        .card:hover { box-shadow: 0 4px 20px rgba(212, 168, 67, 0.2); border-color: var(--gold-light); transform: translateY(-2px); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid var(--cream); }
        .card-title { font-size: 18px; font-weight: 700; color: var(--dark-text); display: flex; align-items: center; gap: 10px; }
        .card-title i { color: var(--gold-dark); }
        .btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: white; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(212, 168, 67, 0.3); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(212, 168, 67, 0.4); }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--light-text); background: var(--cream); border-bottom: 2px solid var(--border-light); }
        .data-table td { padding: 16px; font-size: 14px; color: var(--dark-text); border-bottom: 1px solid var(--border-light); font-weight: 500; }
        .data-table tbody tr { transition: all 0.2s ease; }
        .data-table tbody tr:hover { background: var(--cream); transform: translateX(4px); }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .status-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-badge.completed { background: rgba(22, 163, 74, 0.1); color: #16a34a; }
        .status-badge.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .status-badge.overdue { background: rgba(220, 38, 38, 0.1); color: #dc2626; }
        .action-btn { background: none; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; color: var(--gold-dark); }
        .action-btn:hover { background: var(--gold); color: white; transform: scale(1.1); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .stat-card { animation: fadeInUp 0.5s ease forwards; }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .card { animation: fadeInUp 0.5s ease forwards; animation-delay: 0.5s; opacity: 0; }
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
            <a href="evaluations.php" class="sidebar-nav-item active"><i class="fas fa-clipboard-check"></i><span>Evaluations</span></a>
            <a href="instructors.php" class="sidebar-nav-item"><i class="fas fa-chalkboard-teacher"></i><span>Instructors</span></a>
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
                <div><div class="topbar-title">Evaluations</div><div class="topbar-subtitle">Program Head Panel</div></div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date"><i class="fas fa-calendar-alt"></i><span><?php echo date('F j, Y'); ?></span></div>
                <a href="../../../data/logout.php" class="topbar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </header>

        <main class="dashboard-content" style="position: relative; z-index: 1;">
            <div class="page-container">
                <div class="welcome-banner">
                    <div class="welcome-banner-role">Evaluations</div>
                    <h1>Faculty Evaluation Overview</h1>
                    <p>Monitor and manage all faculty evaluations, track performance ratings, and ensure timely completion of evaluation cycles.</p>
                </div>

                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon gold"><i class="fas fa-clipboard-check"></i></div></div>
                        <div class="stat-card-value"><?php echo $total_evaluations; ?></div>
                        <div class="stat-card-label">Total Evaluations</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon green"><i class="fas fa-check-circle"></i></div></div>
                        <div class="stat-card-value"><?php echo $completed; ?></div>
                        <div class="stat-card-label">Completed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon blue"><i class="fas fa-clock"></i></div></div>
                        <div class="stat-card-value"><?php echo $pending; ?></div>
                        <div class="stat-card-label">Pending</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header"><div class="stat-card-icon purple"><i class="fas fa-star"></i></div></div>
                        <div class="stat-card-value"><?php echo $avg_rating; ?></div>
                        <div class="stat-card-label">Avg. Rating</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Evaluation Records</h3>
                        <button class="btn-primary"><i class="fas fa-plus"></i> New Evaluation</button>
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
                            <?php foreach ($evaluations as $eval): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($eval['instructor_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($eval['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($eval['department']); ?></td>
                                <td>
                                    <?php
                                    $color = '#16a34a';
                                    if ($eval['rating'] < 4.0) $color = '#dc2626';
                                    elseif ($eval['rating'] < 4.5) $color = '#f59e0b';
                                    ?>
                                    <span style="color: <?php echo $color; ?>; font-weight: 700; font-size: 16px;"><?php echo number_format($eval['rating'], 1); ?></span>
                                </td>
                                <td><span class="status-badge <?php echo htmlspecialchars($eval['status']); ?>"><?php echo ucfirst(htmlspecialchars($eval['status'])); ?></span></td>
                                <td><?php echo date('M j, Y', strtotime($eval['evaluation_date'])); ?></td>
                                <td>
                                    <button class="action-btn" title="View Details"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn" title="Edit"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../../../function/dashboard.js"></script>
</body>
</html>
