<div class="page-header">
    <div>
        <h1 class="page-title">Add Instructor</h1>
        <p class="page-subtitle">Create a new instructor account</p>
    </div>
    <a href="dashboard.php?page=manage_program_heads" class="btn" style="background: var(--cream); color: var(--dark-text);">
        <i class="fas fa-arrow-left"></i>
        Back to List
    </a>
</div>

<!-- Success Message -->
<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <span><?php echo htmlspecialchars($_GET['success']); ?></span>
</div>
<?php endif; ?>

<!-- Add Form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-plus"></i>
            Instructor Information
        </h3>
    </div>
    <form method="POST" action="../../data/admin_process.php?action=add_instructor">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">First Name</label>
                <input type="text" class="form-input" name="first_name" placeholder="Enter first name" required>
            </div>
            <div class="form-group">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-input" name="last_name" placeholder="Enter last name" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-input" name="email" placeholder="Enter email address" required>
            </div>
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" class="form-input" name="phone" placeholder="Enter phone number">
            </div>
            <div class="form-group">
                <label class="form-label">Department</label>
                <select class="form-select" name="department" required>
                    <option value="">Select Department</option>
                    <option value="Operational Management">Operational Management (OM)</option>
                    <option value="Financial Management">Financial Management (FM)</option>
                    <option value="Marketing Management">Marketing Management (MM)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Position</label>
                <input type="text" class="form-input" name="position" placeholder="e.g., Instructor" value="Instructor" required>
            </div>
            <div class="form-group">
                <label class="form-label">Temporary Password</label>
                <input type="password" class="form-input" name="password" placeholder="Enter temporary password" required>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-input" name="confirm_password" placeholder="Confirm password" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Instructor
            </button>
            <button type="reset" class="btn" style="background: var(--cream); color: var(--dark-text);">
                <i class="fas fa-redo"></i>
                Reset
            </button>
        </div>
    </form>
</div>

<!-- Quick Add Options -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-bolt"></i>
            Quick Add
        </h3>
    </div>
    <div class="card-body" style="padding: 24px;">
        <p style="color: var(--light-text); margin-bottom: 16px;">Quickly add demo program heads for testing:</p>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="../../data/admin_process.php?action=quick_add&type=demo" class="btn btn-success">
                <i class="fas fa-magic"></i>
                Add Demo Instructors
            </a>
            <a href="../../data/admin_process.php?action=quick_add&type=sample" class="btn" style="background: var(--cream); color: var(--dark-text);">
                <i class="fas fa-file-import"></i>
                Import Sample Data
            </a>
        </div>
    </div>
</div>
