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

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <span><?php echo htmlspecialchars($_GET['success']); ?></span>
</div>
<?php endif; ?>

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
                <label class="form-label">Middle Name</label>
                <input type="text" class="form-input" name="middle_name" placeholder="Enter middle name">
            </div>
            <div class="form-group">
                <label class="form-label">Last Name</label>
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
