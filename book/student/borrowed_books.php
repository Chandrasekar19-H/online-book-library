<?php
$pageTitle = "My Borrowed Books";
require_once __DIR__ . '/../includes/auth.php';
requireStudentLogin();
$student = getStudentData($conn, $_SESSION['student_id']);

// Filter
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

if ($statusFilter !== 'all' && in_array($statusFilter, ['issued', 'returned', 'overdue'])) {
    $stmt = $conn->prepare("
        SELECT bb.*, b.title, b.author, b.isbn, b.category, b.shelf_location 
        FROM borrowed_books bb 
        JOIN books b ON bb.book_id = b.id 
        WHERE bb.student_id = ? AND bb.status = ?
        ORDER BY bb.issue_date DESC
    ");
    $stmt->bind_param("is", $_SESSION['student_id'], $statusFilter);
    $stmt->execute();
    $books = $stmt->get_result();
} else {
    $stmt = $conn->prepare("
        SELECT bb.*, b.title, b.author, b.isbn, b.category, b.shelf_location 
        FROM borrowed_books bb 
        JOIN books b ON bb.book_id = b.id 
        WHERE bb.student_id = ?
        ORDER BY bb.issue_date DESC
    ");
    $stmt->bind_param("i", $_SESSION['student_id']);
    $stmt->execute();
    $books = $stmt->get_result();
}

// Counts
$allCount = $conn->prepare("SELECT COUNT(*) as c FROM borrowed_books WHERE student_id = ?");
$allCount->bind_param("i", $_SESSION['student_id']);
$allCount->execute();
$allTotal = $allCount->get_result()->fetch_assoc()['c'];

$issuedCount = $conn->prepare("SELECT COUNT(*) as c FROM borrowed_books WHERE student_id = ? AND status='issued'");
$issuedCount->bind_param("i", $_SESSION['student_id']);
$issuedCount->execute();
$issuedTotal = $issuedCount->get_result()->fetch_assoc()['c'];

$returnedCount = $conn->prepare("SELECT COUNT(*) as c FROM borrowed_books WHERE student_id = ? AND status='returned'");
$returnedCount->bind_param("i", $_SESSION['student_id']);
$returnedCount->execute();
$returnedTotal = $returnedCount->get_result()->fetch_assoc()['c'];

$overdueCount = $conn->prepare("SELECT COUNT(*) as c FROM borrowed_books WHERE student_id = ? AND status='overdue'");
$overdueCount->bind_param("i", $_SESSION['student_id']);
$overdueCount->execute();
$overdueTotal = $overdueCount->get_result()->fetch_assoc()['c'];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo BASE_URL; ?>student/dashboard.php" class="sidebar-brand">
                <i class="fas fa-book-open-reader"></i> BookNest
            </a>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo BASE_URL; ?>student/dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>student/borrowed_books.php" class="active"><i class="fas fa-book"></i> My Books</a></li>
            <li><a href="<?php echo BASE_URL; ?>student/profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="<?php echo BASE_URL; ?>student/change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
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

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div>
                <button class="btn btn-sm d-lg-none me-2" id="sidebarToggle"><i class="fas fa-bars fa-lg"></i></button>
                <span class="top-bar-title"><i class="fas fa-book me-2 text-primary"></i>My Borrowed Books</span>
            </div>
        </div>

        <div class="content-area">
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6 animate-fade-up delay-1">
                    <div class="stat-card primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $allTotal; ?></div>
                                <div class="stat-label">Total Borrowed</div>
                            </div>
                            <div class="stat-icon primary"><i class="fas fa-layer-group"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-2">
                    <div class="stat-card accent">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $issuedTotal; ?></div>
                                <div class="stat-label">Currently Issued</div>
                            </div>
                            <div class="stat-icon accent"><i class="fas fa-book-open"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-3">
                    <div class="stat-card secondary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $overdueTotal; ?></div>
                                <div class="stat-label">Overdue</div>
                            </div>
                            <div class="stat-icon secondary"><i class="fas fa-exclamation-triangle"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-4">
                    <div class="stat-card warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $returnedTotal; ?></div>
                                <div class="stat-label">Returned</div>
                            </div>
                            <div class="stat-icon warning"><i class="fas fa-check-double"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="card-custom p-3 mb-4 animate-fade-up">
                <div class="row align-items-center g-3">
                    <div class="col-lg-5">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" id="tableSearch" placeholder="Search books...">
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                            <a href="?status=all" class="btn btn-sm <?php echo $statusFilter === 'all' ? 'btn-primary-custom' : 'btn-secondary-custom'; ?> px-3">
                                All <span class="badge bg-white text-dark ms-1"><?php echo $allTotal; ?></span>
                            </a>
                            <a href="?status=issued" class="btn btn-sm <?php echo $statusFilter === 'issued' ? 'btn-primary-custom' : 'btn-secondary-custom'; ?> px-3">
                                <i class="fas fa-book-open me-1"></i> Issued
                                <span class="badge bg-warning text-dark ms-1"><?php echo $issuedTotal; ?></span>
                            </a>
                            <a href="?status=overdue" class="btn btn-sm <?php echo $statusFilter === 'overdue' ? 'btn-primary-custom' : 'btn-secondary-custom'; ?> px-3">
                                <i class="fas fa-exclamation me-1"></i> Overdue
                                <span class="badge bg-danger ms-1"><?php echo $overdueTotal; ?></span>
                            </a>
                            <a href="?status=returned" class="btn btn-sm <?php echo $statusFilter === 'returned' ? 'btn-primary-custom' : 'btn-secondary-custom'; ?> px-3">
                                <i class="fas fa-check me-1"></i> Returned
                                <span class="badge bg-success ms-1"><?php echo $returnedTotal; ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Books Table -->
            <div class="card-custom animate-fade-up delay-2">
                <div class="table-responsive">
                    <table class="table table-custom mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Category</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Fine</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($books->num_rows > 0): ?>
                                <?php $i = 1; while ($row = $books->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($row['title']); ?></div>
                                        <small class="text-muted"><i class="fas fa-map-pin me-1"></i><?php echo htmlspecialchars($row['shelf_location']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                                    <td><code><?php echo htmlspecialchars($row['isbn']); ?></code></td>
                                    <td>
                                        <span class="badge bg-light text-dark"><?php echo htmlspecialchars($row['category']); ?></span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['issue_date'])); ?></td>
                                    <td>
                                        <?php
                                        $dueDate = strtotime($row['due_date']);
                                        $isOverdue = ($row['status'] === 'issued' && $dueDate < time());
                                        ?>
                                        <span class="<?php echo $isOverdue ? 'text-danger fw-bold' : ''; ?>">
                                            <?php echo date('M d, Y', $dueDate); ?>
                                            <?php if ($isOverdue): ?>
                                                <br><small class="text-danger"><i class="fas fa-exclamation-circle"></i> Overdue!</small>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $row['return_date'] ? date('M d, Y', strtotime($row['return_date'])) : '<span class="text-muted">—</span>'; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['fine_amount'] > 0): ?>
                                            <span class="text-danger fw-bold">$<?php echo number_format($row['fine_amount'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="text-success">$0.00</span>
                                        <?php endif; ?>
                                    </td>
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
                                    <td colspan="10" class="text-center py-5">
                                        <div class="animate-fade-up">
                                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                                 style="width: 80px; height: 80px; background: rgba(108, 99, 255, 0.1);">
                                                <i class="fas fa-book-open fa-2x" style="color: var(--primary);"></i>
                                            </div>
                                            <h5 class="fw-bold text-muted">No Books Found</h5>
                                            <p class="text-muted mb-0">
                                                <?php if ($statusFilter !== 'all'): ?>
                                                    No <?php echo $statusFilter; ?> books.
                                                    <a href="?status=all" class="text-primary">View all</a>
                                                <?php else: ?>
                                                    You haven't borrowed any books yet.
                                                <?php endif; ?>
                                            </p>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>