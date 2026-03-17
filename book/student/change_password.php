<?php
$pageTitle = "Change Password";
require_once __DIR__ . '/../includes/auth.php';
requireStudentLogin();
$student = getStudentData($conn, $_SESSION['student_id']);
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <!-- Sidebar (same as dashboard) -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo BASE_URL; ?>student/dashboard.php" class="sidebar-brand">
                <i class="fas fa-book-open-reader"></i> BookNest
            </a>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo BASE_URL; ?>student/dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>student/borrowed_books.php"><i class="fas fa-book"></i> My Books</a></li>
            <li><a href="<?php echo BASE_URL; ?>student/profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="<?php echo BASE_URL; ?>student/change_password.php" class="active"><i class="fas fa-key"></i> Change Password</a></li>
            <li><a href="<?php echo BASE_URL; ?>student/logout.php" style="color: #FF6584;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-avatar"><?php echo strtoupper(substr($student['full_name'], 0, 1)); ?></div>
                <div>
                    <div class="sidebar-user-name"><?php echo htmlspecialchars($student['full_name']); ?></div>
                    <div class="sidebar-user-role">Student</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <button class="btn btn-sm d-lg-none me-2" id="sidebarToggle"><i class="fas fa-bars fa-lg"></i></button>
                <span class="top-bar-title">Change Password</span>
            </div>
        </div>

        <div class="content-area">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card-custom p-4 animate-fade-up">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" 
                                 style="width: 70px; height: 70px; background: rgba(108, 99, 255, 0.1);">
                                <i class="fas fa-key fa-2x" style="color: var(--primary);"></i>
                            </div>
                            <h4 class="fw-bold">Update Your Password</h4>
                            <p class="text-muted">Ensure your account stays secure</p>
                        </div>

                        <form action="<?php echo BASE_URL; ?>actions/change_password_action.php" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="user_type" value="student">
                            
                            <div class="form-floating-custom">
                                <span class="input-icon"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="current_password" placeholder="Current Password" required>
                                <button type="button" class="password-toggle" tabindex="-1"><i class="fas fa-eye"></i></button>
                            </div>

                            <div class="form-floating-custom">
                                <span class="input-icon"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="new_password" placeholder="New Password" minlength="6" required>
                                <button type="button" class="password-toggle" tabindex="-1"><i class="fas fa-eye"></i></button>
                            </div>

                            <div class="form-floating-custom">
                                <span class="input-icon"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm New Password" minlength="6" required>
                                <button type="button" class="password-toggle" tabindex="-1"><i class="fas fa-eye"></i></button>
                            </div>

                            <button type="submit" class="btn btn-primary-custom w-100 py-3">
                                <i class="fas fa-save me-2"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>