<?php
session_start();
require_once '../../../data/config.php';

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'John Head';

// Fetch stats
$total_courses = 0;
$active_instructors = 0;
$total_students = 0;
$avg_rating = 0;

$sql = "SELECT COUNT(*) as cnt FROM courses WHERE status = 'active'";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $total_courses = $row['cnt']; }

$sql = "SELECT COUNT(*) as cnt FROM instructors WHERE status = 'active'";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $active_instructors = $row['cnt']; }

$sql = "SELECT COALESCE(SUM(student_count),0) as cnt FROM courses WHERE status = 'active'";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $total_students = $row['cnt']; }

$sql = "SELECT COALESCE(AVG(rating),0) as avg_r FROM evaluations";
$result = $conn->query($sql);
if ($result) { $row = $result->fetch_assoc(); $avg_rating = round($row['avg_r'], 1); }

// Fetch courses with instructor info
$courses = [];
$sql = "SELECT c.course_code, c.course_name, c.description, c.student_count, c.evaluated_count, c.department,
        CONCAT(i.first_name, ' ', i.last_name) as instructor_name, i.avatar_gradient_from, i.avatar_gradient_to, i.first_name, i.last_name,
        COALESCE(AVG(e.rating),0) as avg_rating
        FROM courses c
        LEFT JOIN instructors i ON c.instructor_id = i.id
        LEFT JOIN evaluations e ON e.course_id = c.id
        WHERE c.status = 'active'
        GROUP BY c.id
        ORDER BY c.course_code";
$result = $conn->query($sql);
if ($result) { while ($row = $result->fetch_assoc()) { $courses[] = $row; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - Program Head Dashboard</title>
    <link rel="stylesheet" href="../../../css/common.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --gold: #d4a843; --gold-light: #e8c768; --gold-dark: #b8922f; --cream: #faf8f5; --cream-light: #f5f2eb; --white: #ffffff; --dark-text: #2d3748; --dark-text-2: #4a5568; --light-text: #718096; --border-light: #e2e8f0; --border-soft: #edf2f7; --success: #38a169; --success-light: #c6f6d5; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; color: var(--dark-text); }
        .page-container { padding: 32px; }
        .welcome-banner { background: linear-gradient(160deg, #B8860B 0%, #D4A843 40%, #F0D68A 100%); border-radius: 20px; padding: 36px 44px; color: white; margin-bottom: 32px; box-shadow: 0 8px 32px rgba(184, 134, 11, 0.3); position: relative; overflow: hidden; }
        .welcome-banner::before { content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: rgba(255, 255, 255, 0.15); border-radius: 50%; }
        .welcome-banner h1 { font-size: 28px; font-weight: 800; margin: 0 0 12px 0; position: relative; z-index: 1; }
        .welcome-banner p { font-size: 15px; opacity: 0.95; margin: 0; max-width: 600px; position: relative; z-index: 1; }
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 32px; }
        .stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid var(--border-soft); transition: all 0.3s ease; position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; }
        .stat-card.gold::before { background: linear-gradient(90deg, var(--gold), var(--gold-light)); }
        .stat-card.green::before { background: linear-gradient(90deg, #10b981, #34d399); }
        .stat-card.blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .stat-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
        .stat-card:hover { transform: translateY(-6px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
        .stat-card-value { font-size: 36px; font-weight: 800; color: var(--dark-text); line-height: 1; margin-bottom: 8px; }
        .stat-card-label { font-size: 13px; color: var(--light-text); font-weight: 600; }
        .card { background: var(--white); border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid var(--border-soft); margin-bottom: 24px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid var(--cream-light); }
        .card-title { font-size: 18px; font-weight: 700; color: var(--dark-text); display: flex; align-items: center; gap: 10px; }
        .card-title i { color: var(--gold-dark); }
        .btn-add { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: white; padding: 10px 18px; border: none; border-radius: 10px; cursor: pointer; font-weight: 500; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
        .course-card { background: var(--white); border-radius: 16px; padding: 24px; border: 1px solid var(--border-soft); transition: all 0.3s ease; position: relative; overflow: hidden; }
        .course-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--gold), var(--gold-light)); }
        .course-card:hover { transform: translateY(-8px); box-shadow: 0 12px 32px rgba(212, 168, 67, 0.2); border-color: var(--gold-light); }
        .course-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .course-code { font-size: 13px; color: var(--gold-dark); font-weight: 700; background: rgba(212, 168, 67, 0.1); padding: 6px 12px; border-radius: 8px; }
        .course-rating { display: flex; align-items: center; gap: 4px; font-size: 14px; font-weight: 700; color: var(--gold-dark); }
        .course-rating i { color: var(--gold); }
        .course-name { font-size: 18px; font-weight: 700; color: var(--dark-text); margin: 12px 0 8px; }
        .course-info { font-size: 14px; color: var(--light-text); margin-bottom: 16px; }
        .course-instructor { display: flex; align-items: center; gap: 10px; padding: 12px; background: var(--cream-light); border-radius: 10px; margin-bottom: 16px; }
        .instructor-avatar { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px; }
        .instructor-name { font-size: 14px; font-weight: 600; color: var(--dark-text); }
        .instructor-role { font-size: 12px; color: var(--light-text); }
        .course-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; padding-top: 16px; border-top: 1px solid var(--border-light); }
        .course-stat { text-align: center; }
        .course-stat-value { font-size: 20px; font-weight: 700; color: var(--dark-text); }
        .course-stat-label { font-size: 11px; color: var(--light-text); text-transform: uppercase; }
        .progress-bar { height: 8px; background: var(--border-light); border-radius: 8px; overflow: hidden; margin-top: 16px; }
        .progress { height: 100%; border-radius: 8px; background: linear-gradient(90deg, var(--gold), var(--gold-light)); }
        .progress-label { display: flex; justify-content: space-between; font-size: 12px; color: var(--light-text); margin-top: 8px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .status-active { background: var(--success-light); color: var(--success); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .stat-card, .course-card, .card { animation: fadeInUp 0.5s ease forwards; }
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
            <a href="instructors.php" class="sidebar-nav-item"><i class="fas fa-chalkboard-teacher"></i><span>Instructors</span></a>
            <a href="courses.php" class="sidebar-nav-item active"><i class="fas fa-book"></i><span>Courses</span></a>
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
                <div><div class="topbar-title">Courses</div><div class="topbar-subtitle">Program Head Panel</div></div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date"><i class="fas fa-calendar-alt"></i><span><?php echo date('F j, Y'); ?></span></div>
                <a href="../../../data/logout.php" class="topbar-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </header>
        <main class="dashboard-content">
            <div class="page-container">
                <div class="welcome-banner">
                    <h1>Course Management</h1>
                    <p>View and manage all courses, track instructor performance, and monitor student evaluations across departments.</p>
                </div>

                <div class="stats-row">
                    <div class="stat-card gold">
                        <div class="stat-card-value"><?php echo $total_courses; ?></div>
                        <div class="stat-card-label">Total Courses</div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-card-value"><?php echo $active_instructors; ?></div>
                        <div class="stat-card-label">Active Instructors</div>
                    </div>
                    <div class="stat-card blue">
                        <div class="stat-card-value"><?php echo number_format($total_students); ?></div>
                        <div class="stat-card-label">Total Students</div>
                    </div>
                    <div class="stat-card purple">
                        <div class="stat-card-value"><?php echo $avg_rating; ?></div>
                        <div class="stat-card-label">Avg. Rating</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-book"></i> All Courses</h3>
                        <button class="btn-add"><i class="fas fa-plus"></i> Add Course</button>
                    </div>
                    
                    <div class="course-grid">
                        <?php foreach ($courses as $course):
                            $rating = round($course['avg_rating'], 1);
                            $eval_rate = $course['student_count'] > 0 ? round(($course['evaluated_count'] / $course['student_count']) * 100) : 0;
                            $initials = strtoupper(substr($course['first_name'] ?? '', 0, 1) . substr($course['last_name'] ?? '', 0, 1));
                        ?>
                        <div class="course-card">
                            <div class="course-header">
                                <span class="course-code"><?php echo htmlspecialchars($course['course_code']); ?></span>
                                <div class="course-rating"><i class="fas fa-star"></i> <?php echo $rating; ?></div>
                            </div>
                            <h3 class="course-name"><?php echo htmlspecialchars($course['course_name']); ?></h3>
                            <p class="course-info"><?php echo htmlspecialchars($course['description']); ?></p>
                            
                            <?php if ($course['instructor_name']): ?>
                            <div class="course-instructor">
                                <span class="instructor-avatar" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($course['avatar_gradient_from']); ?>, <?php echo htmlspecialchars($course['avatar_gradient_to']); ?>);"><?php echo $initials; ?></span>
                                <div>
                                    <span class="instructor-name"><?php echo htmlspecialchars($course['instructor_name']); ?></span><br>
                                    <span class="instructor-role"><?php echo htmlspecialchars($course['department']); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="course-stats">
                                <div class="course-stat">
                                    <div class="course-stat-value"><?php echo $course['student_count']; ?></div>
                                    <div class="course-stat-label">Students</div>
                                </div>
                                <div class="course-stat">
                                    <div class="course-stat-value"><?php echo $course['evaluated_count']; ?></div>
                                    <div class="course-stat-label">Evaluated</div>
                                </div>
                                <div class="course-stat">
                                    <div class="course-stat-value"><?php echo $eval_rate; ?>%</div>
                                    <div class="course-stat-label">Rate</div>
                                </div>
                            </div>
                            
                            <div class="progress-bar">
                                <div class="progress" style="width: <?php echo $eval_rate; ?>%;"></div>
                            </div>
                            <div class="progress-label">
                                <span><?php echo $eval_rate; ?>% Evaluated</span>
                                <span class="status-badge status-active">Active</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../../../function/dashboard.js"></script>
</body>
</html>
