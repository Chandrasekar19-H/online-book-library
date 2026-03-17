<?php
$pageTitle = "Return Book";
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();
$admin = getAdminData($conn, $_SESSION['admin_id']);

// Get all currently issued books
$issuedBooks = $conn->query("
    SELECT bb.*, s.full_name as student_name, s.student_id as stu_id, b.title as book_title, b.author as book_author
    FROM borrowed_books bb 
    JOIN students s ON bb.student_id = s.id 
    JOIN books b ON bb.book_id = b.id 
    WHERE bb.status IN ('issued', 'overdue')
    ORDER BY bb.due_date ASC
");

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
            <li><a href="<?php echo BASE_URL; ?>admin/return_book.php" class="active"><i class="fas fa-rotate-left"></i> Return Book</a></li>
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
                <span class="top-bar-title"><i class="fas fa-rotate-left me-2 text-primary"></i>Return Book</span>
            </div>
        </div>

        <div class="content-area">
            <!-- Search -->
            <div class="card-custom p-3 mb-4 animate-fade-up">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" id="tableSearch" placeholder="Search by student name, book title...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="text-muted">
                            <i class="fas fa-book-open me-1"></i> 
                            <strong><?php echo $issuedBooks->num_rows; ?></strong> books currently issued
                        </span>
                    </div>
                </div>
            </div>

            <!-- Issued Books Table -->
            <div class="card-custom animate-fade-up delay-1">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0"><i class="fas fa-list me-2 text-primary"></i>Currently Issued Books</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Book</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Days Left</th>
                                <th>Status</th>
                                <th>Fine</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($issuedBooks->num_rows > 0): ?>
                                <?php $i = 1; while ($row = $issuedBooks->fetch_assoc()): ?>
                                <?php
                                    $dueDate = strtotime($row['due_date']);
                                    $today = strtotime(date('Y-m-d'));
                                    $daysLeft = ($dueDate - $today) / (60 * 60 * 24);
                                    $isOverdue = $daysLeft < 0;
                                    $finePerDay = 1.00; // $1 per day
                                    $calculatedFine = $isOverdue ? abs($daysLeft) * $finePerDay : 0;
                                ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="sidebar-avatar me-2" style="width: 36px; height: 36px; font-size: 0.75rem; flex-shrink: 0;">
                                                <?php echo strtoupper(substr($row['student_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold" style="font-size: 0.88rem;"><?php echo htmlspecialchars($row['student_name']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($row['stu_id']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold" style="font-size: 0.88rem;"><?php echo htmlspecialchars($row['book_title']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['book_author']); ?></small>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['issue_date'])); ?></td>
                                    <td>
                                        <span class="<?php echo $isOverdue ? 'text-danger fw-bold' : ''; ?>">
                                            <?php echo date('M d, Y', $dueDate); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($isOverdue): ?>
                                            <span class="text-danger fw-bold">
                                                <i class="fas fa-exclamation-triangle me-1"></i><?php echo abs((int)$daysLeft); ?> days overdue
                                            </span>
                                        <?php elseif ($daysLeft == 0): ?>
                                            <span class="text-warning fw-bold">Due Today</span>
                                        <?php else: ?>
                                            <span class="text-success"><?php echo (int)$daysLeft; ?> days</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isOverdue): ?>
                                            <span class="badge-custom badge-overdue">Overdue</span>
                                        <?php else: ?>
                                            <span class="badge-custom badge-pending">Issued</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($calculatedFine > 0): ?>
                                            <span class="text-danger fw-bold">$<?php echo number_format($calculatedFine, 2); ?></span>
                                        <?php else: ?>
                                            <span class="text-success">$0.00</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>actions/return_book_action.php?id=<?php echo $row['id']; ?>&fine=<?php echo $calculatedFine; ?>" 
                                           class="btn btn-sm btn-accent-custom rounded-pill px-3 btn-return-confirm"
                                           data-name="<?php echo htmlspecialchars($row['book_title']); ?>"
                                           data-student="<?php echo htmlspecialchars($row['student_name']); ?>"
                                           data-fine="<?php echo number_format($calculatedFine, 2); ?>">
                                            <i class="fas fa-rotate-left me-1"></i> Return
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="animate-fade-up">
                                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                                 style="width: 80px; height: 80px; background: rgba(0, 212, 170, 0.1);">
                                                <i class="fas fa-check-double fa-2x" style="color: var(--accent);"></i>
                                            </div>
                                            <h5 class="fw-bold text-muted">All Clear!</h5>
                                            <p class="text-muted mb-0">No books are currently issued.</p>
                                        </div>
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

<!-- Return Confirmation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-return-confirm').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const bookName = this.dataset.name;
            const studentName = this.dataset.student;
            const fine = this.dataset.fine;

            let fineHtml = '';
            if (parseFloat(fine) > 0) {
                fineHtml = `<br><br><span class="text-danger"><strong>Fine Amount: $${fine}</strong></span>`;
            }

            Swal.fire({
                title: 'Confirm Return',
                html: `Return "<strong>${bookName}</strong>" from <strong>${studentName}</strong>?${fineHtml}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00D4AA',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, Return It',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'animate__animated animate__fadeInDown'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>