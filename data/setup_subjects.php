<?php
header('Content-Type: application/json');
require_once 'config.php';

$results = ['subjects_added' => 0, 'majors_subjects_added' => 0, 'bridging_added' => 0, 'message' => ''];

try {
    // Check if subjects table has data
    $stmt = $pdo->query("SELECT COUNT(*) FROM subjects");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Insert sample subjects
        $subjects = [
            ['OPM 101', 'Introduction to Operations Management', 'Fundamental concepts of operations management including process design, capacity planning, and inventory management.', 3.0, 'fas fa-cogs', '#d4a843'],
            ['OPM 201', 'Production and Operations Management', 'Advanced topics in production planning, scheduling, and quality control.', 3.0, 'fas fa-industry', '#e8c768'],
            ['OPM 301', 'Supply Chain Management', 'End-to-end supply chain coordination, logistics, and procurement strategies.', 3.0, 'fas fa-truck', '#3b82f6'],
            ['OPM 302', 'Quality Management', 'Total quality management, Six Sigma methodologies, and continuous improvement.', 3.0, 'fas fa-check-double', '#10b981'],
            ['OPM 401', 'Strategic Operations', 'Strategic planning for operations, lean management, and business process reengineering.', 3.0, 'fas fa-chess', '#ec4899'],
            ['MATH 101', 'Business Mathematics', 'Mathematical techniques for business decision-making including calculus and statistics.', 3.0, 'fas fa-calculator', '#8b5cf6'],
            ['STAT 201', 'Business Statistics', 'Statistical analysis methods for business research and decision making.', 3.0, 'fas fa-chart-bar', '#6366f1'],
            ['MGMT 101', 'Principles of Management', 'Foundational management principles, organizational behavior, and leadership.', 3.0, 'fas fa-users', '#f59e0b']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO subjects (subject_code, subject_name, description, units, icon_class, color) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($subjects as $s) {
            $stmt->execute($s);
            $results['subjects_added']++;
        }
    }
    
    // Check if major_subjects has data
    $stmt = $pdo->query("SELECT COUNT(*) FROM major_subjects");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $majorSubjects = [
            [1, 1, '1st Year', '1st Semester', TRUE, FALSE],
            [1, 6, '1st Year', '1st Semester', TRUE, FALSE],
            [1, 7, '1st Year', '2nd Semester', TRUE, FALSE],
            [1, 8, '1st Year', '2nd Semester', TRUE, FALSE],
            [1, 2, '2nd Year', '1st Semester', TRUE, TRUE],
            [1, 3, '2nd Year', '2nd Semester', TRUE, FALSE],
            [1, 4, '3rd Year', '1st Semester', TRUE, FALSE],
            [1, 5, '4th Year', '1st Semester', TRUE, FALSE]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO major_subjects (major_id, subject_id, year_level, semester, is_required, is_prerequisite) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($majorSubjects as $ms) {
            $stmt->execute($ms);
            $results['majors_subjects_added']++;
        }
    }
    
    // Add bridging subjects (15 units) - check if they exist
    $bridgingSubjects = [
        ['ACCTG 1', 'FUNDAMENTALS OF ACCOUNTING', 'Introduction to basic accounting principles, concepts, and practices. Covers the accounting cycle, journals, ledgers, and financial statements.', 3.0, 'fas fa-calculator', '#16a34a', 'SHS NON ABM'],
        ['MKTG 1', 'PRINCIPLES OF MARKETING', 'Fundamental concepts of marketing including market analysis, product development, pricing, promotion, and distribution strategies.', 3.0, 'fas fa-bullhorn', '#3b82f6', 'SHS NON ABM'],
        ['MNGT 1', 'PRINCIPLES OF MANAGEMENT', 'Basic management principles covering planning, organizing, leading, and controlling. Includes organizational structure and management functions.', 3.0, 'fas fa-briefcase', '#f59e0b', 'SHS NON ABM'],
        ['ENG 1', 'STUDY AND THINKING SKILLS', 'Development of academic reading, writing, and critical thinking skills for college success.', 3.0, 'fas fa-book-open', '#8b5cf6', NULL],
        ['MATH 1', 'COLLEGE ALGEBRA', 'Fundamental algebraic operations, equations, inequalities, functions, and graphs. Prepares students for higher mathematics courses.', 3.0, 'fas fa-function', '#ec4899', NULL]
    ];
    
    // First, add bridging_for column if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE subjects ADD COLUMN bridging_for VARCHAR(100) DEFAULT NULL");
    } catch (Exception $e) {}
    
    $stmtCheck = $pdo->prepare("SELECT id FROM subjects WHERE subject_code = ?");
    $stmtInsert = $pdo->prepare("INSERT INTO subjects (subject_code, subject_name, description, units, icon_class, color, bridging_for) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $bridgingIds = [];
    
    foreach ($bridgingSubjects as $bs) {
        $stmtCheck->execute([$bs[0]]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if (!$existing) {
            $stmtInsert->execute([$bs[0], $bs[1], $bs[2], $bs[3], $bs[4], $bs[5], $bs[6]]);
            $bridgingIds[$bs[0]] = $pdo->lastInsertId();
            $results['bridging_added']++;
        } else {
            $bridgingIds[$bs[0]] = $existing['id'];
        }
    }
    
    // Add bridging subjects to major_subjects with year_level = 'Bridging'
    if (!empty($bridgingIds)) {
        $stmtCheckMajor = $pdo->prepare("SELECT id FROM major_subjects WHERE major_id = ? AND subject_id = ?");
        $stmtInsertMajor = $pdo->prepare("INSERT INTO major_subjects (major_id, subject_id, year_level, semester, is_required, is_prerequisite, sort_order) VALUES (?, ?, 'Bridging', '1st Semester', TRUE, FALSE, 0)");
        
        foreach ($bridgingIds as $code => $sid) {
            $stmtCheckMajor->execute([1, $sid]);
            if (!$stmtCheckMajor->fetch(PDO::FETCH_ASSOC)) {
                $stmtInsertMajor->execute([1, $sid]);
                $results['bridging_added']++;
            }
        }
    }
    
    $results['message'] = 'Sample data setup complete';
    echo json_encode(['success' => true, 'results' => $results]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}