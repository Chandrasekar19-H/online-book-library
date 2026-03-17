<?php
$pageTitle = "Issue Book";
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();
$admin = getAdminData($conn, $_SESSION['admin_id']);

// Get active students
$students = $conn->query("SELECT id, full_name, student_id, email FROM students WHERE status='active' ORDER BY full_name ASC");

// Get available books
$availableBooks = $conn->query("SELECT id, title, author, isbn, available_qty FROM books WHERE status='available' AND available_qty > 0 ORDER BY title ASC");

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
            <li><a href="<?php echo BASE_URL; ?>admin/issue_book.php" class="active"><i class="fas fa-hand-holding"></i> Issue Book</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/return_book.php"><i class="fas fa-rotate-left"></i> Return Book</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/manage_orders.php"><i class="fas fa-cart-shopping"></i> Book Orders
                <?php if ($totalOrders > 0): ?><span class="badge bg-danger ms-auto rounded-pill"><?php echo $totalOrders; ?></span><?php endif; ?></a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
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
                <span class="top-bar-title"><i class="fas fa-hand-holding me-2 text-primary"></i>Issue Book to Student</span>
            </div>
        </div>

        <div class="content-area">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card-custom p-4 animate-fade-up">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                 style="width: 80px; height: 80px; background: rgba(0, 212, 170, 0.1);">
                                <i class="fas fa-hand-holding fa-2x" style="color: var(--accent);"></i>
                            </div>
                            <h4 class="fw-bold">Issue a Book</h4>
                            <p class="text-muted">Select a student and book to issue</p>
                        </div>

                        <form action="<?php echo BASE_URL; ?>actions/issue_book_action.php" method="POST" class="needs-validation" novalidate>
                            
                            <!-- Select Student -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold"><i class="fas fa-user-graduate me-2 text-primary"></i>Select Student <span class="text-danger">*</span></label>
                                <select class="form-select form-select-custom" name="student_id" required>
                                    <option value="" disabled selected>-- Choose Student --</option>
                                    <?php while ($s = $students->fetch_assoc()): ?>
                                        <option value="<?php echo $s['id']; ?>">
                                            <?php echo htmlspecialchars($s['full_name']); ?> (<?php echo htmlspecialchars($s['student_id']); ?>) - <?php echo htmlspecialchars($s['email']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Select Book -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold"><i class="fas fa-book me-2 text-primary"></i>Select Book <span class="text-danger">*</span></label>
                                <select class="form-select form-select-custom" name="book_id" required>
                                    <option value="" disabled selected>-- Choose Book --</option>
                                    <?php while ($b = $availableBooks->fetch_assoc()): ?>
                                        <option value="<?php echo $b['id']; ?>">
                                            <?php echo htmlspecialchars($b['title']); ?> by <?php echo htmlspecialchars($b['author']); ?> 
                                            (Available: <?php echo $b['available_qty']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Issue Date -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="fas fa-calendar me-2 text-primary"></i>Issue Date <span class="text-danger">*</span></label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-calendar-day"></i></span>
                                        <input type="date" class="form-control" name="issue_date" 
                                               value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="fas fa-calendar-check me-2 text-primary"></i>Due Date <span class="text-danger">*</span></label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-calendar-xmark"></i></span>
                                        <input type="date" class="form-control" name="due_date" 
                                               value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-3 border-0 rounded-3" style="background: rgba(108, 99, 255, 0.08); font-size: 0.85rem;">
                                <i class="fas fa-info-circle me-2"></i>
                                Default due date is set to <strong>14 days</strong> from today. You can change it as needed.
                            </div>

                            <button type="submit" class="btn btn-accent-custom w-100 py-3 mt-3">
                                <i class="fas fa-paper-plane me-2"></i> Issue Book
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Issuances -->
            <div class="card-custom mt-4 animate-fade-up delay-2">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0"><i class="fas fa-clock-rotate-left me-2 text-primary"></i>Recent Issuances</h5>
                </div>
                <div class="table-responsive">
                    <?php
                    $recentIssues = $conn->query("
                        SELECT bb.*, s.full_name as student_name, s.student_id as stu_id, b.title as book_title 
                        FROM borrowed_books bb 
                        JOIN students s ON bb.student_id = s.id 
                        JOIN books b ON bb.book_id = b.id 
                        WHERE bb.status='issued' 
                        ORDER BY bb.id DESC LIMIT 10
                    ");
                    ?>
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
                            <?php if ($recentIssues->num_rows > 0): ?>
                                <?php $i = 1; while ($ri = $recentIssues->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($ri['student_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($ri['stu_id']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($ri['book_title']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($ri['issue_date'])); ?></td>
                                    <td>
                                        <?php
                                        $due = strtotime($ri['due_date']);
                                        $isOD = $due < time();
                                        ?>
                                        <span class="<?php echo $isOD ? 'text-danger fw-bold' : ''; ?>">
                                            <?php echo date('M d, Y', $due); ?>
                                        </span>
                                    </td>
                                    <td><span class="badge-custom badge-pending">Issued</span></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No active issuances.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>