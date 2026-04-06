<div class="page-header">
    <div>
        <h1 class="page-title">Manage Instructors</h1>
        <p class="page-subtitle">View and manage instructor accounts</p>
    </div>
</div>

<?php
require_once '../../data/config.php';

$instructors = [];
$pending_instructors = [];
$error_message = '';
$promoted_ids = [];
$program_head_emails = [];
$current_program_head = null;

if ($pdo) {
    try {
        // Active instructors (exclude pending_instructors)
        $stmt = $pdo->query("SELECT * FROM instructors ORDER BY id DESC");
        $instructors = $stmt->fetchAll();
        
        // Pending self-registrations
        $stmt = $pdo->query("SELECT * FROM pending_instructors ORDER BY id DESC");
        $pending_instructors = $stmt->fetchAll();
        
        // Get all promoted instructor IDs
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
                    promotion_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                $stmt = $pdo->query("SELECT instructor_id FROM admin_promotions WHERE promoted_to = 'program_head'");
                $promotions = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $promoted_ids = array_map('intval', is_array($promotions) ? $promotions : []);
            } catch (PDOException $e2) {
                $promoted_ids = [];
            }
        }
        
        // Fetch Program Head emails
        try {
            $stmt = $pdo->query("SELECT email FROM program_heads");
            $program_head_emails = array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);
        } catch (PDOException $e) {
            $program_head_emails = [];
        }
        
        // Current Program Head
        try {
            $stmt = $pdo->query("SELECT first_name, last_name, email, department FROM program_heads ORDER BY id DESC LIMIT 1");
            $current_program_head = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            $current_program_head = null;
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
} else {
    $error_message = "Database connection failed.";
}
?>

<?php if ($error_message): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <span><?php echo htmlspecialchars($error_message); ?></span>
</div>
<?php endif; ?>

<?php if ($current_program_head): ?>
<div class="alert alert-info" style="background: rgba(59, 130, 246, 0.1); color: #1d4ed8; border: 1px solid rgba(59, 130, 246, 0.3); margin-bottom: 24px;">
    <i class="fas fa-thumbtack"></i>
    <strong>Current Program Head:</strong>
    <?php
        $ph_name = trim(($current_program_head['first_name'] ?? '') . ' ' . ($current_program_head['last_name'] ?? ''));
        $ph_dept = trim($current_program_head['department'] ?? '');
    ?>
    <?php echo htmlspecialchars($ph_name ?: 'Program Head'); ?>
    <?php if ($ph_dept): ?> — <strong>Department:</strong> <?php echo htmlspecialchars($ph_dept); ?><?php endif; ?>
    <br><strong>Only one Program Head at a time.</strong>
</div>
<?php endif; ?>

<!-- Instructors Table -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chalkboard-teacher"></i>
            Instructors (<span id="instructorCount"><?php echo count($instructors); ?></span>)
        </h3>
    </div>
    <div class="card-body">
        <?php if (empty($instructors)): ?>
        <div class="empty-state" style="padding: 40px; text-align: center; color: var(--light-text);">
            <i class="fas fa-user-plus" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
            <h3>No Instructors</h3>
            <p>No active instructors in the system.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x: auto;">
        <table class="data-table" id="instructorsTable">
            <thead>
                <tr>
                    <th>Instructor</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="instructorsTableBody">
                <?php foreach ($instructors as $instructor): ?>
                <?php 
                    $instructor_id_int = (int)$instructor['id'];
                    $email_raw = strtolower(trim($instructor['email'] ?? ''));
                    $is_program_head = in_array($instructor_id_int, $promoted_ids, true) || in_array($email_raw, $program_head_emails, true);
                    $initials = strtoupper(substr($instructor['first_name'], 0, 1) . ($instructor['middle_name'] ? substr($instructor['middle_name'], 0, 1) : '') . substr($instructor['last_name'], 0, 1));
                    $full_name = trim($instructor['first_name'] . ' ' . ($instructor['middle_name'] ?? '') . ' ' . $instructor['last_name'] . ($instructor['suffix'] ? ', ' . $instructor['suffix'] : ''));
                    $created_date = isset($instructor['created_at']) ? date('M j, Y', strtotime($instructor['created_at'])) : date('M j, Y');
                ?>
                <tr data-id="<?php echo $instructor['id']; ?>"
                    data-first-name="<?php echo htmlspecialchars($instructor['first_name']); ?>"
                    data-middle-name="<?php echo htmlspecialchars($instructor['middle_name'] ?? ''); ?>"
                    data-last-name="<?php echo htmlspecialchars($instructor['last_name']); ?>"
                    data-suffix="<?php echo htmlspecialchars($instructor['suffix'] ?? ''); ?>"
                    data-email="<?php echo htmlspecialchars($instructor['email']); ?>">
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, #d4a843, #e8c768); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px;"><?php echo $initials; ?></div>
                            <div>
                                <div style="font-weight: 600;"><?php echo htmlspecialchars($full_name); ?></div>
                                <div style="font-size: 12px; color: var(--light-text);"><?php echo $is_program_head ? 'Program Head' : 'Instructor'; ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($instructor['email']); ?></td>
                    <td>
                        <?php if ($is_program_head): ?>
                            <span class="status-badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Program Head</span>
                        <?php else: ?>
                            <span class="status-badge" style="background: rgba(99, 102, 241, 0.15); color: #4f46e5; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Instructor</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size: 13px; color: var(--light-text);"><?php echo $created_date; ?></td>
                    <td>
                        <button class="actions-btn" onclick="openActionsModal(<?php echo $instructor_id_int; ?>, '<?php echo htmlspecialchars($full_name); ?>', <?php echo $is_program_head ? 'true' : 'false'; ?>, <?php echo $current_program_head ? 'false' : 'true'; ?>)" title="Actions">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pending Registrations Table -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-clock"></i>
            Pending Registrations (<span id="pendingCount"><?php echo count($pending_instructors); ?></span>)
        </h3>
    </div>
    <div class="card-body">
        <?php if (empty($pending_instructors)): ?>
        <div class="empty-state" style="padding: 40px; text-align: center; color: var(--light-text);">
            <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
            <h3>No Pending Registrations</h3>
            <p>All registrations have been processed.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x: auto;">
        <table class="data-table" id="pendingTable">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Email</th>
                    <th>Applied</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="pendingTableBody">
                <?php foreach ($pending_instructors as $pending): ?>
                <?php
                    $full_name = trim(($pending['first_name'] ?? '') . ' ' . ($pending['middle_name'] ?? '') . ' ' . ($pending['last_name'] ?? ''));
                    $full_name = preg_replace('/\s+/', ' ', $full_name);
                    $applied_date = isset($pending['created_at']) ? date('M j, Y', strtotime($pending['created_at'])) : date('M j, Y');
                ?>
                <tr data-id="<?php echo $pending['id']; ?>">
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, #d4a843, #e8c768); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px;">
                                <?php echo strtoupper(substr($pending['first_name'], 0, 1) . ($pending['middle_name'] ? substr($pending['middle_name'], 0, 1) : '') . substr($pending['last_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <div style="font-weight: 600;"><?php echo htmlspecialchars($full_name); ?></div>
                                <div style="font-size: 12px; color: var(--light-text);">Pending</div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($pending['email']); ?></td>
                    <td style="font-size: 13px; color: var(--light-text);"><?php echo $applied_date; ?></td>
                    <td>
                        <button class="actions-btn" onclick="openPendingActions(<?php echo $pending['id']; ?>, '<?php echo htmlspecialchars(addslashes($full_name)); ?>')" title="Actions">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Promote Modal (for instructors to Program Head) -->
<div class="modal-overlay" id="promoteModal">
    <div class="modal" style="max-width: 450px;">
        <div class="modal-header" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 20px; font-weight: 700; color: var(--dark-text);">Promote to Program Head</h3>
            <button onclick="closePromoteModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--light-text);">&times;</button>
        </div>
        <p style="margin-bottom: 20px; color: var(--light-text);">
            Set a new password for <strong id="promoteInstructorName"></strong> to log in as <strong>Program Head</strong>. They will use their <strong>same email</strong> with this new password <strong>only when choosing "Program Head"</strong> on the login page. Their instructor login remains unchanged.
        </p>
        <form method="POST" action="../../data/admin_process.php?action=promote_instructor" id="promoteForm">
            <input type="hidden" name="instructor_id" id="promoteInstructorId">
            <input type="hidden" name="promote_to" value="program_head">
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Program Head Password</label>
                <input type="password" class="form-input" name="password" id="promotePassword" placeholder="Enter password" required minlength="6" style="padding-right: 42px;">
                <button type="button" class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--light-text); cursor: pointer; padding: 4px; display: inline-block; z-index:10;" onclick="togglePasswordField('promotePassword', this)"><i class="fas fa-eye"></i></button>
                <small style="color: var(--light-text); font-size: 12px;">Used only for Program Head login</small>
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-input" name="confirm_password" id="promoteConfirmPassword" placeholder="Confirm password" required style="padding-right: 42px;">
                <button type="button" class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--light-text); cursor: pointer; padding: 4px; display: inline-block; z-index:10;" onclick="togglePasswordField('promoteConfirmPassword', this)"><i class="fas fa-eye"></i></button>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn" style="background: var(--cream); color: var(--dark-text);" onclick="closePromoteModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Promote
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Pending Actions Modal -->
<div class="modal-overlay" id="pendingActionsModal">
    <div class="modal" style="max-width: 400px;">
        <div class="modal-header" style="margin-bottom: 16px; text-align: center;">
            <h3 style="font-size: 20px; font-weight: 700; color: var(--dark-text);">Pending Applicant</h3>
            <p id="pendingApplicantName" style="color: var(--light-text); margin-top: 4px;"></p>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <button type="button" class="action-btn action-btn-accept" onclick="acceptPending()">
                <div class="action-btn-icon"><i class="fas fa-check"></i></div>
                <div class="action-btn-text">
                    <span class="action-btn-title">Accept Application</span>
                    <span class="action-btn-desc">Create instructor account</span>
                </div>
                <i class="fas fa-chevron-right" style="color: #10b981;"></i>
            </button>
            <button type="button" class="action-btn action-btn-reject" onclick="rejectPending()">
                <div class="action-btn-icon"><i class="fas fa-times"></i></div>
                <div class="action-btn-text">
                    <span class="action-btn-title">Decline Application</span>
                    <span class="action-btn-desc">Reject and remove</span>
                </div>
                <i class="fas fa-chevron-right" style="color: #ef4444;"></i>
            </button>
        </div>
        <div style="margin-top: 20px; text-align: center;">
            <button type="button" class="btn" style="background: var(--cream); color: var(--dark-text);" onclick="closePendingActions()">Cancel</button>
        </div>
    </div>
</div>

<!-- No Program Head Modal -->
<div class="modal-overlay" id="noProgramHeadModal">
    <div class="modal" style="max-width: 400px;">
        <div class="alert alert-error" style="margin-bottom: 0;">
            <i class="fas fa-exclamation-circle"></i>
            <span>Another instructor is already promoted as Program Head. Remove their promotion first.</span>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <button type="button" class="btn btn-primary" onclick="closeNoProgramHeadModal()">OK</button>
        </div>
    </div>
</div>

<!-- Remove Program Head Modal -->
<div class="modal-overlay" id="removeProgramHeadModal">
    <div class="modal" style="max-width: 400px;">
        <div class="modal-header" style="margin-bottom: 16px; text-align: center;">
            <i class="fas fa-user-minus" style="font-size: 48px; color: #ef4444; margin-bottom: 12px;"></i>
            <h3 style="font-size: 20px; font-weight: 700; color: var(--dark-text);">Remove Program Head</h3>
        </div>
        <p style="text-align: center; color: var(--light-text); margin-bottom: 20px;" id="removeProgramHeadMsg">
            Remove this instructor as Program Head?
        </p>
        <div style="text-align: center; display: flex; gap: 12px; justify-content: center;">
            <button type="button" class="btn" style="background: var(--cream); color: var(--dark-text);" onclick="closeRemoveProgramHeadModal()">Cancel</button>
            <button type="button" class="btn btn-primary" style="background: #ef4444;" onclick="confirmRemovePromotion()">Remove</button>
        </div>
    </div>
</div>

<style>
.actions-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    border: none;
    color: #6b7280;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 16px;
}
.actions-btn:hover {
    background: linear-gradient(135deg, #d4a843, #e8c768);
    color: white;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(212, 168, 67, 0.4);
}
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}
.modal-overlay.show { opacity: 1; visibility: visible; }
.action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border: none;
    border-radius: 12px;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
}
.action-btn:hover { background: #f3f4f6; transform: translateX(4px); }
.action-btn .action-btn-icon {
    width: 42px; height: 42px; border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #818cf8);
    display: flex; align-items: center; justify-content: center; color: white; font-size: 16px;
}
.action-btn-accept .action-btn-icon { background: linear-gradient(135deg, #10b981, #059669) !important; }
.action-btn-reject .action-btn-icon { background: linear-gradient(135deg, #ef4444, #f87171) !important; }
.action-btn-text { flex: 1; text-align: left; }
.action-btn-title { display: block; font-weight: 600; color: #1f2937; font-size: 14px; }
.action-btn-desc { display: block; font-size: 12px; color: #6b7280; margin-top: 2px; }
</style>

<script>
let currentInstructorId = null;
let currentInstructorName = '';
let currentPendingId = null;
let currentPendingName = '';

function openActionsModal(id, name, isPromoted, canPromote) {
    currentInstructorId = id;
    currentInstructorName = name;
    var currentPH = <?php echo json_encode($current_program_head); ?>;
    // If already promoted as Program Head, show remove modal
    if (isPromoted) {
        document.getElementById('removeProgramHeadMsg').textContent = 'Remove ' + name + ' as Program Head?';
        document.getElementById('removeProgramHeadModal').classList.add('show');
        return;
    }
    // If not promoted and there's already a current Program Head (someone else), show error
    if (currentPH) {
        document.getElementById('noProgramHeadModal').classList.add('show');
        return;
    }
    // No Program Head yet - allow promoting
    showPromoteModal();
}

function closeRemoveProgramHeadModal() {
    document.getElementById('removeProgramHeadModal').classList.remove('show');
}

function confirmRemovePromotion() {
    closeRemoveProgramHeadModal();
    removePromotion(currentInstructorId, currentInstructorName);
}

function closeNoProgramHeadModal() {
    document.getElementById('noProgramHeadModal').classList.remove('show');
}

function closeActionsModal() {
    document.getElementById('actionsModal').classList.remove('show');
}

function editInstructorFromModal() {
    closeActionsModal();
    editInstructor(currentInstructorId);
}

function showPromoteModal() {
    document.getElementById('promoteInstructorId').value = currentInstructorId;
    document.getElementById('promoteInstructorName').textContent = currentInstructorName;
    document.getElementById('promotePassword').value = '';
    document.getElementById('promoteConfirmPassword').value = '';
    document.getElementById('promoteModal').classList.add('show');
}

function closePromoteModal() {
    document.getElementById('promoteModal').classList.remove('show');
}

function removePromotion(id, name) {
    window.location.href = '../../data/admin_process.php?action=remove_promotion&id=' + id;
}

function openPendingActions(id, name) {
    currentPendingId = id;
    currentPendingName = name;
    document.getElementById('pendingApplicantName').textContent = name;
    document.getElementById('pendingActionsModal').classList.add('show');
}

function closePendingActions() {
    document.getElementById('pendingActionsModal').classList.remove('show');
}

function acceptPending() {
    closePendingActions();
    if (confirm('Accept ' + currentPendingName + ' as an instructor?')) {
        window.location.href = '../../data/admin_process.php?action=accept_instructor&id=' + currentPendingId;
    }
}

function rejectPending() {
    closePendingActions();
    if (confirm('Decline ' + currentPendingName + '? This action cannot be undone.')) {
        window.location.href = '../../data/admin_process.php?action=decline_instructor&id=' + currentPendingId;
    }
}

function togglePasswordField(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Close modals on backdrop click
document.getElementById('promoteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closePromoteModal();
});
document.getElementById('pendingActionsModal')?.addEventListener('click', function(e) {
    if (e.target === this) closePendingActions();
});
document.getElementById('noProgramHeadModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeNoProgramHeadModal();
});
document.getElementById('removeProgramHeadModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRemoveProgramHeadModal();
});

// Promote form validation
document.getElementById('promoteForm')?.addEventListener('submit', function(e) {
    const p1 = document.getElementById('promotePassword').value;
    const p2 = document.getElementById('promoteConfirmPassword').value;
    if (p1 !== p2) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    if (p1.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters!');
        return false;
    }
});
</script>