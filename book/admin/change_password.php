<?php
$pageTitle = "Change Password";
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();
$admin = getAdminData($conn, $_SESSION['admin_id']);

$totalOrders = $conn->query("SELECT COUNT(*) as c FROM book_orders WHERE status='pending'")->fetch_assoc()['c'];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="sidebar-brand">
                <i class="fas fa-book-open-reader"></i> BookNest
            </a>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo BASE_URL; ?>admin/dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/manage_books.php"><i class="fas fa-book"></i> Manage Books</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/manage_students.php"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/issue_book.php"><i class="fas fa-hand-holding"></i> Issue Book</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/return_book.php"><i class="fas fa-rotate-left"></i> Return Book</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/manage_orders.php"><i class="fas fa-cart-shopping"></i> Book Orders
                <?php if ($totalOrders > 0): ?><span class="badge bg-danger ms-auto rounded-pill"><?php echo $totalOrders; ?></span><?php endif; ?></a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/change_password.php" class="active"><i class="fas fa-key"></i> Change Password</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/logout.php" style="color: #FF6584;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-avatar" style="background: var(--gradient-2);"><?php echo strtoupper(substr($admin['full_name'], 0, 1)); ?></div>
                <div><div class="sidebar-user-name"><?php echo htmlspecialchars($admin['full_name']); ?></div><div class="sidebar-user-role">Librarian</div></div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div>
                <button class="btn btn-sm d-lg-none me-2" id="sidebarToggle"><i class="fas fa-bars fa-lg"></i></button>
                <span class="top-bar-title"><i class="fas fa-key me-2 text-primary"></i>Change Password</span>
            </div>
        </div>

        <div class="content-area">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card-custom p-4 animate-fade-up">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                 style="width: 70px; height: 70px; background: rgba(108, 99, 255, 0.1);">
                                <i class="fas fa-shield-halved fa-2x" style="color: var(--primary);"></i>
                            </div>
                            <h4 class="fw-bold">Update Admin Password</h4>
                            <p class="text-muted">Keep your account secure with a strong password</p>
                        </div>

                        <form action="<?php echo BASE_URL; ?>actions/change_password_action.php" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="user_type" value="admin">

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

                            <div class="alert alert-warning border-0 rounded-3 mt-3" style="font-size: 0.85rem; background: rgba(243, 156, 18, 0.08);">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Tips:</strong> Use at least 6 characters with a mix of letters, numbers, and symbols.
                            </div>

                            <button type="submit" class="btn btn-primary-custom w-100 py-3">
                                <i class="fas fa-save me-2"></i> Update Password
                            </button>
                        </form>
                    </div>

                    <!-- Account Info Card -->
                    <div class="card-custom p-4 mt-4 animate-fade-up delay-2">
                        <h6 class="fw-bold mb-3"><i class="fas fa-user-shield me-2 text-primary"></i>Account Information</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Name</span>
                            <span class="fw-semibold"><?php echo htmlspecialchars($admin['full_name']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Email</span>
                            <span class="fw-semibold"><?php echo htmlspecialchars($admin['email']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Role</span>
                            <span class="badge-custom badge-active">Administrator</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>