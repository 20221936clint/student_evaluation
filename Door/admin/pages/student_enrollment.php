<div class="page-header">
    <div>
        <h1 class="page-title">Student Enrollment</h1>
        <p class="page-subtitle">Register new student accounts</p>
    </div>
    <a href="dashboard.php?page=manage_students" class="btn" style="background: var(--cream); color: var(--dark-text);">
        <i class="fas fa-users"></i>
        View Students
    </a>
</div>

<?php
require_once '../../data/config.php';

$majors = [];
$error_message = '';
$success_message = '';

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, major_name, display_name FROM majors WHERE is_active = 1 ORDER BY sort_order");
        $majors = $stmt->fetchAll();
    } catch (PDOException $e) {
        $majors = [];
    }
}
?>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span><?php echo htmlspecialchars($_GET['error']); ?></span>
</div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <span><?php echo htmlspecialchars($_GET['success']); ?></span>
</div>
<?php endif; ?>

<?php if ($error_message): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span><?php echo htmlspecialchars($error_message); ?></span>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-graduate"></i>
            Student Information
        </h3>
    </div>
    <form method="POST" action="../../data/admin_process.php?action=add_student" id="studentForm">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">First Name <span style="color: #dc2626;">*</span></label>
                <input type="text" class="form-input" name="first_name" placeholder="Enter first name" required>
            </div>
            <div class="form-group">
                <label class="form-label">Middle Name</label>
                <input type="text" class="form-input" name="middle_name" placeholder="Enter middle name">
            </div>
            <div class="form-group">
                <label class="form-label">Last Name <span style="color: #dc2626;">*</span></label>
                <input type="text" class="form-input" name="last_name" placeholder="Enter last name" required>
            </div>
            <div class="form-group">
                <label class="form-label">Suffix</label>
                <select class="form-select" name="suffix">
                    <option value="">None</option>
                    <option value="Jr.">Jr.</option>
                    <option value="Sr.">Sr.</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                    <option value="V">V</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Student ID <span style="color: #dc2626;">*</span></label>
                <input type="text" class="form-input" name="student_id" placeholder="e.g., STU-001" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address <span style="color: #dc2626;">*</span></label>
                <input type="email" class="form-input" name="email" placeholder="student@edu" required>
            </div>
            <div class="form-group">
                <label class="form-label">Major/Program <span style="color: #dc2626;">*</span></label>
                <select class="form-select" name="major_id" required>
                    <option value="">Select Major</option>
                    <?php foreach ($majors as $major): ?>
                    <option value="<?php echo $major['id']; ?>"><?php echo htmlspecialchars($major['display_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Year Level <span style="color: #dc2626;">*</span></label>
                <select class="form-select" name="year_level" required>
                    <option value="">Select Year</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                    <option value="5th Year">5th Year</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                Enroll Student
            </button>
            <button type="reset" class="btn" style="background: var(--cream); color: var(--dark-text);">
                <i class="fas fa-redo"></i>
                Reset
            </button>
        </div>
    </form>
</div>

<!-- Quick Stats -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 24px;">
    <?php
    $total_students = 0;
    $this_year = date('Y');
    
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM students");
            $total_students = $stmt->fetchColumn();
        } catch (PDOException $e) {
            $total_students = 0;
        }
    }
    ?>
    <div class="card" style="padding: 20px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 20px;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div style="font-size: 24px; font-weight: 700; color: var(--dark-text);"><?php echo $total_students; ?></div>
                <div style="font-size: 13px; color: var(--light-text);">Total Students</div>
            </div>
        </div>
    </div>
    <div class="card" style="padding: 20px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(212, 168, 67, 0.1); display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 20px;">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <div style="font-size: 24px; font-weight: 700; color: var(--dark-text);"><?php echo count($majors); ?></div>
                <div style="font-size: 13px; color: var(--light-text);">Active Majors</div>
            </div>
        </div>
    </div>
</div>

<style>
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    padding: 24px;
}
.form-group {
    margin-bottom: 0;
}
.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 8px;
}
.form-input, .form-select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--border-light);
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: var(--dark-text);
    background: var(--cream);
    transition: all 0.2s ease;
}
.form-input:focus, .form-select:focus {
    outline: none;
    border-color: var(--gold);
    background: white;
    box-shadow: 0 0 0 3px rgba(212, 168, 67, 0.1);
}
.form-actions {
    padding: 0 24px 24px;
    display: flex;
    gap: 12px;
}
.btn {
    padding: 12px 20px;
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}
.btn-primary {
    background: var(--gold);
    color: white;
}
.btn-primary:hover {
    background: var(--gold-dark);
}
.card {
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-light);
}
.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.card-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--dark-text);
    display: flex;
    align-items: center;
    gap: 10px;
}
.card-title i {
    color: var(--gold);
}
.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin: 20px 24px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.alert-success {
    background: rgba(22, 163, 74, 0.1);
    color: #16a34a;
    border: 1px solid rgba(22, 163, 74, 0.2);
}
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--dark-text);
}
.page-subtitle {
    font-size: 14px;
    color: var(--light-text);
    margin-top: 4px;
}
</style>