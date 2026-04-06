<?php
require_once '../../data/config.php';

$recent_instructors = [];
$error_message = '';
$promoted_ids = [];
$program_head_emails = [];
$current_program_head = null;

if ($pdo) {
    try {
        // Get all instructors for Instructor List table
        $stmt = $pdo->query("SELECT * FROM instructors ORDER BY first_name ASC");
        $recent_instructors = $stmt->fetchAll();
        
        // Get promoted instructor IDs (Role = Program Head for these)
        try {
            $stmt = $pdo->query("SELECT instructor_id FROM admin_promotions WHERE promoted_to = 'program_head'");
            $promotions = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $promoted_ids = array_map('intval', is_array($promotions) ? $promotions : []);
        } catch (PDOException $e) {
            try {
                $pdo->exec("CREATE TABLE IF NOT EXISTS admin_promotions (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    instructor_id INT NOT NULL,
                    promoted_to VARCHAR(50) NOT NULL,
                    promoted_by INT NOT NULL,
                    promotion_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    status ENUM('active', 'revoked') DEFAULT 'active'
                )");
                $stmt = $pdo->query("SELECT instructor_id FROM admin_promotions WHERE promoted_to = 'program_head'");
                $promotions = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $promoted_ids = array_map('intval', is_array($promotions) ? $promotions : []);
            } catch (PDOException $e2) {
                $promoted_ids = [];
            }
        }
        
        // Get program head emails from program_heads table
        try {
            $stmt = $pdo->query("SELECT email FROM program_heads");
            $program_head_emails = array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);
        } catch (PDOException $e) {
            $program_head_emails = [];
        }
        
        // Get current program head details
        try {
            $stmt = $pdo->query("
                SELECT i.first_name, i.last_name, i.email, i.middle_name, i.suffix 
                FROM instructors i 
                INNER JOIN admin_promotions ap ON i.id = ap.instructor_id 
                WHERE ap.promoted_to = 'program_head' AND ap.status = 'active' 
                LIMIT 1
            ");
            $current_program_head = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$current_program_head) {
                // Check program_heads table as fallback
                $stmt = $pdo->query("SELECT first_name, last_name, email FROM program_heads ORDER BY id DESC LIMIT 1");
                $current_program_head = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $current_program_head = null;
        }
        
    } catch (PDOException $e) {
        $error_message = "Database connection failed. Please set up the database using data.sql";
    }
} else {
    $error_message = "Database connection failed. Please set up the database using data.sql";
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Overview</h1>
        <p class="page-subtitle">Welcome back, Admin!</p>
    </div>
</div>

<?php if ($error_message): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span><?php echo htmlspecialchars($error_message); ?></span>
</div>
<?php endif; ?>

 <!-- Stats Grid -->
 <div class="stats-grid">
     <div class="stat-card program-head-card">
         <?php if ($current_program_head): ?>
             <?php 
             $full_name = trim(($current_program_head['first_name'] ?? '') . ' ' . ($current_program_head['middle_name'] ?? '') . ' ' . ($current_program_head['last_name'] ?? ''));
             $full_name = preg_replace('/\s+/', ' ', $full_name);
             $initials = strtoupper(substr($current_program_head['first_name'], 0, 1) . ($current_program_head['middle_name'] ? substr($current_program_head['middle_name'], 0, 1) : '') . substr($current_program_head['last_name'], 0, 1));
             ?>
             <div style="display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 8px 0;">
                 <div style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 20px; box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);">
                     <?php echo $initials; ?>
                 </div>
                 <div style="text-align: center;">
                     <div style="font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 4px;"><?php echo htmlspecialchars($full_name); ?></div>
                     <div style="font-size: 13px; color: #6b7280; margin-bottom: 4px;"><?php echo htmlspecialchars($current_program_head['email'] ?? ''); ?></div>
                     <span class="status-badge" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15)); color: #059669; padding: 6px 14px; border-radius: 24px; font-size: 13px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.3);">Program Head</span>
                 </div>
             </div>
         <?php else: ?>
             <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px 0; color: #9ca3af;">
                 <i class="fas fa-user-tie" style="font-size: 48px; opacity: 0.5;"></i>
                 <div style="font-size: 14px; font-weight: 600;">No Program Head</div>
                 <div style="font-size: 12px; text-align: center;">Promote an instructor to become Program Head</div>
             </div>
         <?php endif; ?>
     </div>
     
     <div class="stat-card">
         <div class="stat-icon purple">
             <i class="fas fa-book-open"></i>
         </div>
         <div class="stat-value" style="font-size: 14px; line-height: 1.3; text-align: center;">
             <?php 
             if (!empty($majors)) {
                 echo htmlspecialchars(implode(', ', $majors));
             } else {
                 echo '<span style="color: #9ca3af;">No majors</span>';
             }
             ?>
         </div>
         <div class="stat-label">Majors</div>
     </div>
     
     <div class="stat-card">
         <div class="stat-icon blue">
             <i class="fas fa-users"></i>
         </div>
         <div class="stat-value"><?php echo count($recent_instructors); ?></div>
         <div class="stat-label">Total Instructors</div>
     </div>
 </div>
        <div class="stat-value"><?php echo $instructor_count; ?></div>
        <div class="stat-label">Total Instructors</div>
    </div>
    

    
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-value" style="font-size: 14px; line-height: 1.3; text-align: center;">
            <?php 
            if (!empty($majors)) {
                echo htmlspecialchars(implode(', ', $majors));
            } else {
                echo '<span style="color: #9ca3af;">No majors</span>';
            }
            ?>
        </div>
        <div class="stat-label">Majors</div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users"></i>
            Instructor List (<?php echo $instructor_count; ?>)
        </h3>
    </div>
    <div class="card-body">
        <?php if (empty($recent_instructors)): ?>
        <div class="empty-state">
            <i class="fas fa-user-plus"></i>
            <h3>No Instructors Yet</h3>
            <p>Click "Add Instructor" to get started.</p>
        </div>
        <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_instructors as $instructor): ?>
                <?php 
                    $initials = strtoupper(substr($instructor['first_name'], 0, 1) . substr($instructor['last_name'], 0, 1));
                    $joined_date = isset($instructor['created_at']) ? date('M j, Y', strtotime($instructor['created_at'])) : 'N/A';
                    $instructor_id_int = (int)$instructor['id'];
                    $email_raw = strtolower(trim($instructor['email'] ?? ''));
                    $is_program_head = in_array($instructor_id_int, $promoted_ids, true) || in_array($email_raw, $program_head_emails, true);
                ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #d4a843, #e8c768); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 12px;"><?php echo $initials; ?></div>
                            <div>
                                <div style="font-weight: 600;"><?php echo htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']); ?></div>
                                <div style="font-size: 12px; color: #6b7280;"><?php echo htmlspecialchars($instructor['email']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if ($is_program_head): ?>
                            <span class="status-badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Program Head</span>
                        <?php else: ?>
                            <span class="status-badge" style="background: rgba(99, 102, 241, 0.15); color: #4f46e5; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Instructor</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $joined_date; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
