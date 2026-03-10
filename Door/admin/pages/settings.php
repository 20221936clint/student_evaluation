<div class="page-header">
    <div>
        <h1 class="page-title">Settings</h1>
        <p class="page-subtitle">Configure system settings</p>
    </div>
</div>

<!-- System Settings -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-cog"></i>
            System Settings
        </h3>
    </div>
    <div class="form-grid">
        <div class="form-group">
            <label class="form-label">System Name</label>
            <input type="text" class="form-input" value="Faculty Evaluation System" disabled>
        </div>
        <div class="form-group">
            <label class="form-label">Version</label>
            <input type="text" class="form-input" value="1.0.0" disabled>
        </div>
        <div class="form-group">
            <label class="form-label">Database Status</label>
            <input type="text" class="form-input" value="Connected" disabled>
        </div>
        <div class="form-group">
            <label class="form-label">Last Backup</label>
            <input type="text" class="form-input" value="Today, 12:00 PM" disabled>
        </div>
    </div>
    <div class="form-actions">
        <button class="btn btn-primary" disabled>
            <i class="fas fa-save"></i>
            Save Settings
        </button>
    </div>
</div>

<!-- Account Settings -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-cog"></i>
            Admin Account
        </h3>
    </div>
    <form>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Admin Name</label>
                <input type="text" class="form-input" value="Administrator" disabled>
            </div>
            <div class="form-group">
                <label class="form-label">Admin Email</label>
                <input type="email" class="form-input" value="admin@cjcm.edu" disabled>
            </div>
        </div>
        <div class="form-actions">
            <a href="#" class="btn" style="background: var(--cream); color: var(--dark-text);">
                <i class="fas fa-key"></i>
                Change Password
            </a>
        </div>
    </form>
</div>

<!-- Quick Info -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle"></i>
            System Information
        </h3>
    </div>
    <div class="card-body" style="padding: 24px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div style="padding: 16px; background: var(--cream); border-radius: 12px;">
                <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;">Total Users</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--dark-text);">54</div>
            </div>
            <div style="padding: 16px; background: var(--cream); border-radius: 12px;">
                <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;">Evaluations</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--dark-text);">156</div>
            </div>
            <div style="padding: 16px; background: var(--cream); border-radius: 12px;">
                <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;">Departments</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--dark-text);">3</div>
            </div>
            <div style="padding: 16px; background: var(--cream); border-radius: 12px;">
                <div style="font-size: 12px; color: var(--light-text); margin-bottom: 4px;">Courses</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--dark-text);">42</div>
            </div>
        </div>
    </div>
</div>
