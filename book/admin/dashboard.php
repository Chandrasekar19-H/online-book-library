<?php
$pageTitle = "Admin Dashboard";
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$admin = getAdminData($conn, $_SESSION['admin_id']);

// Stats
$totalBooks = $conn->query("SELECT COUNT(*) as c FROM books")->fetch_assoc()['c'];
$totalStudents = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$totalIssued = $conn->query("SELECT COUNT(*) as c FROM borrowed_books WHERE status='issued'")->fetch_assoc()['c'];
$totalOverdue = $conn->query("SELECT COUNT(*) as c FROM borrowed_books WHERE status='overdue'")->fetch_assoc()['c'];
$totalOrders = $conn->query("SELECT COUNT(*) as c FROM book_orders WHERE status='pending'")->fetch_assoc()['c'];

// Recent activities
$recentBorrows = $conn->query("
    SELECT bb.*, s.full_name as student_name, b.title as book_title 
    FROM borrowed_books bb 
    JOIN students s ON bb.student_id = s.id 
    JOIN books b ON bb.book_id = b.id 
    ORDER BY bb.id DESC LIMIT 8
");

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="sidebar-brand">
                <i class="fas fa-book-open-reader"></i> BookNest
            </a>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="active">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/manage_books.php">
                    <i class="fas fa-book"></i> Manage Books
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/manage_students.php">
                    <i class="fas fa-user-graduate"></i> Manage Students
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/issue_book.php">
                    <i class="fas fa-hand-holding"></i> Issue Book
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/return_book.php">
                    <i class="fas fa-rotate-left"></i> Return Book
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/manage_orders.php">
                    <i class="fas fa-cart-shopping"></i> Book Orders
                    <?php if ($totalOrders > 0): ?>
                        <span class="badge bg-danger ms-auto rounded-pill"><?php echo $totalOrders; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/change_password.php">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/logout.php" style="color: #FF6584;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>

        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-avatar" style="background: var(--gradient-2);">
                    <?php echo strtoupper(substr($admin['full_name'], 0, 1)); ?>
                </div>
                <div>
                    <div class="sidebar-user-name"><?php echo htmlspecialchars($admin['full_name']); ?></div>
                    <div class="sidebar-user-role">Librarian</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div>
                <button class="btn btn-sm d-lg-none me-2" id="sidebarToggle"><i class="fas fa-bars fa-lg"></i></button>
                <span class="top-bar-title">Admin Dashboard</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted" style="font-size: 0.85rem;">
                    <i class="fas fa-calendar me-1"></i> <?php echo date('l, F j, Y'); ?>
                </span>
            </div>
        </div>

        <div class="content-area">
            <!-- Welcome -->
            <div class="card-custom p-4 mb-4 animate-fade-up" style="background: var(--gradient-2); border-radius: 16px;">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white fw-bold mb-2">Hello, <?php echo htmlspecialchars($admin['full_name']); ?>! 🎯</h3>
                        <p class="text-white-50 mb-0">Here's your library overview for today.</p>
                    </div>
                    <div class="col-md-4 text-end d-none d-md-block">
                        <i class="fas fa-chart-line" style="font-size: 4rem; color: rgba(255,255,255,0.2);"></i>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6 animate-fade-up delay-1">
                    <div class="stat-card primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $totalBooks; ?></div>
                                <div class="stat-label">Total Books</div>
                            </div>
                            <div class="stat-icon primary"><i class="fas fa-book"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-2">
                    <div class="stat-card accent">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $totalStudents; ?></div>
                                <div class="stat-label">Students</div>
                            </div>
                            <div class="stat-icon accent"><i class="fas fa-user-graduate"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-3">
                    <div class="stat-card warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $totalIssued; ?></div>
                                <div class="stat-label">Books Issued</div>
                            </div>
                            <div class="stat-icon warning"><i class="fas fa-hand-holding"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-4">
                    <div class="stat-card secondary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $totalOverdue; ?></div>
                                <div class="stat-label">Overdue</div>
                            </div>
                            <div class="stat-icon secondary"><i class="fas fa-exclamation-circle"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row g-4 mb-4">
                <div class="col-md-3 animate-fade-up">
                    <a href="<?php echo BASE_URL; ?>admin/add_book.php" class="card-custom p-4 text-center text-decoration-none d-block">
                        <div class="feature-icon purple mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.3rem;">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Add Book</h6>
                        <small class="text-muted">Add new books</small>
                    </a>
                </div>
                <div class="col-md-3 animate-fade-up delay-1">
                    <a href="<?php echo BASE_URL; ?>admin/issue_book.php" class="card-custom p-4 text-center text-decoration-none d-block">
                        <div class="feature-icon green mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.3rem;">
                            <i class="fas fa-hand-holding"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Issue Book</h6>
                        <small class="text-muted">Issue to student</small>
                    </a>
                </div>
                <div class="col-md-3 animate-fade-up delay-2">
                    <a href="<?php echo BASE_URL; ?>admin/return_book.php" class="card-custom p-4 text-center text-decoration-none d-block">
                        <div class="feature-icon pink mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.3rem;">
                            <i class="fas fa-rotate-left"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Return Book</h6>
                        <small class="text-muted">Process returns</small>
                    </a>
                </div>
                <div class="col-md-3 animate-fade-up delay-3">
                    <a href="<?php echo BASE_URL; ?>admin/manage_orders.php" class="card-custom p-4 text-center text-decoration-none d-block">
                        <div class="feature-icon purple mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.3rem;">
                            <i class="fas fa-cart-shopping"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Orders</h6>
                        <small class="text-muted"><?php echo $totalOrders; ?> pending</small>
                    </a>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="card-custom animate-fade-up delay-4">
                <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="fw-bold mb-0"><i class="fas fa-clock-rotate-left me-2 text-primary"></i>Recent Activity</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Book</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentBorrows->num_rows > 0): ?>
                                <?php $i = 1; while ($row = $recentBorrows->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['issue_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['due_date'])); ?></td>
                                    <td>
                                        <?php
                                        $sc = '';
                                        switch ($row['status']) {
                                            case 'issued': $sc = 'badge-pending'; break;
                                            case 'returned': $sc = 'badge-returned'; break;
                                            case 'overdue': $sc = 'badge-overdue'; break;
                                        }
                                        ?>
                                        <span class="badge-custom <?php echo $sc; ?>"><?php echo ucfirst($row['status']); ?></span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No recent activity found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>