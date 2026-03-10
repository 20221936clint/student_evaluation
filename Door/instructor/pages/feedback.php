<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon_atoms.ico" type="image/x-icon">
    <title>Feedback - Faculty Evaluation System</title>
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
            <a href="students.php" class="sidebar-nav-item">
                <i class="fas fa-user-graduate"></i>
                <span>Students</span>
            </a>
            <a href="feedback.php" class="sidebar-nav-item active">
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
                    <div class="topbar-title">Feedback</div>
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
                <h2><i class="fas fa-comment-dots"></i> Feedback</h2>
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
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>48</h4>
                        <p>Total Feedback</p>
                    </div>
                </div>
                <div class="eval-summary-card">
                    <div class="eval-summary-icon amber">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>12</h4>
                        <p>New This Month</p>
                    </div>
                </div>
                <div class="eval-summary-card">
                    <div class="eval-summary-icon rose">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="eval-summary-info">
                        <h4>4.7</h4>
                        <p>Average Rating</p>
                    </div>
                </div>
            </div>
            
            <div class="content-grid" style="grid-template-columns: 1fr;">
                <div class="content-card">
                    <div class="content-card-header">
                        <h3><i class="fas fa-star"></i> Recent Feedback</h3>
                    </div>
                    <div class="content-card-body">
                        <div class="feedback-list">
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <span class="feedback-course">Business Management 101</span>
                                    <span class="feedback-date">Mar 8, 2026</span>
                                </div>
                                <p class="feedback-text">"Excellent teaching methodology. Very engaging and practical examples that really help understand the concepts."</p>
                                <div class="feedback-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <span class="feedback-course">Marketing Principles</span>
                                    <span class="feedback-date">Mar 5, 2026</span>
                                </div>
                                <p class="feedback-text">"Great examples and case studies. Would love more interactive sessions in class."</p>
                                <div class="feedback-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <span class="feedback-course">Strategic Management</span>
                                    <span class="feedback-date">Mar 3, 2026</span>
                                </div>
                                <p class="feedback-text">"Very knowledgeable and explains complex concepts clearly. The group projects are very helpful."</p>
                                <div class="feedback-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <span class="feedback-course">Business Management 101</span>
                                    <span class="feedback-date">Feb 28, 2026</span>
                                </div>
                                <p class="feedback-text">"Good overall. Could use more real-world applications and guest speakers."</p>
                                <div class="feedback-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <span class="feedback-course">Marketing Principles</span>
                                    <span class="feedback-date">Feb 25, 2026</span>
                                </div>
                                <p class="feedback-text">"Love the interactive activities! Would appreciate more time for discussions."</p>
                                <div class="feedback-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../function/dashboard.js"></script>
</body>
</html>
