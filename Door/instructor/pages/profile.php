<?php
session_start();
require_once '../../../data/config.php';

$instructor_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Jane Teacher';

// Fetch instructor profile
$profile = [
    'first_name' => 'Jane',
    'last_name' => 'Teacher',
    'email' => 'teacher@test.com',
    'department' => 'Business Management',
    'employee_id' => 'EMP001',
    'phone' => '+1 234 567 8900',
    'office_location' => 'Room 305, Building A'
];

$sql = "SELECT first_name, last_name, email, department, employee_id, phone, office_location FROM instructors WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $instructor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $profile = $row;
    }
    $stmt->close();
}

$full_name = $profile['first_name'] . ' ' . $profile['last_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon_atoms.ico" type="image/x-icon">
    <title>Profile - Faculty Evaluation System</title>
    <link rel="stylesheet" href="../../../css/common.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="dashboard-page">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../../media/LOGO.jpg" alt="Logo" class="sidebar-logo" style="width: 70px; height: 70px; border-radius: 16px; object-fit: cover; border: 3px solid white; background: white; padding: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
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
                <span class="sidebar-user-name"><?php echo htmlspecialchars($user_name); ?></span>
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
            <a href="students.php" class="sidebar-nav-item">
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
            <a href="profile.php" class="sidebar-nav-item active">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content" style="position: relative;">
        <div style="position: fixed; top: 0; left: var(--sidebar-width); right: 0; bottom: 0; background-image: url('../../../media/LOGO.jpg'); background-size: 70%; background-position: center; background-repeat: no-repeat; opacity: 0.08; pointer-events: none; z-index: 0;"></div>
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="topbar-title">Profile</div>
                    <div class="topbar-subtitle">Instructor Panel</div>
                </div>
            </div>
            
            <div class="topbar-right">
                <div class="topbar-date">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?php echo date('F j, Y'); ?></span>
                </div>
                <a href="../../../data/logout.php" class="topbar-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="eval-page-header">
                <h2><i class="fas fa-user"></i> Profile</h2>
            </div>
            
            <div class="content-grid" style="grid-template-columns: 1fr 2fr;">
                <div class="content-card">
                    <div class="content-card-header">
                        <h3><i class="fas fa-id-card"></i> Profile Photo</h3>
                    </div>
                    <div class="content-card-body" style="text-align: center;">
                        <div style="margin-bottom: 20px;">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($full_name); ?>&size=150&background=D4A843&color=fff" alt="Profile" style="width: 150px; height: 150px; border-radius: 50%; border: 4px solid var(--gold-primary);">
                        </div>
                        <button class="btn-primary" style="width: 100%;">
                            <i class="fas fa-camera"></i> Change Photo
                        </button>
                    </div>
                </div>
                
                <div class="content-card">
                    <div class="content-card-header">
                        <h3><i class="fas fa-user-cog"></i> Account Settings</h3>
                    </div>
                    <div class="content-card-body">
                        <form class="class-form">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Full Name</label>
                                <input type="text" class="form-input" value="<?php echo htmlspecialchars($full_name); ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email Address</label>
                                <input type="email" class="form-input" value="<?php echo htmlspecialchars($profile['email']); ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-building"></i> Department</label>
                                <input type="text" class="form-input" value="<?php echo htmlspecialchars($profile['department']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-id-card"></i> Employee ID</label>
                                <input type="text" class="form-input" value="<?php echo htmlspecialchars($profile['employee_id']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="tel" class="form-input" value="<?php echo htmlspecialchars($profile['phone']); ?>">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-map-marker-alt"></i> Office Location</label>
                                <input type="text" class="form-input" value="<?php echo htmlspecialchars($profile['office_location']); ?>">
                            </div>
                            <button type="button" class="btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../../function/dashboard.js"></script>
</body>
</html>
