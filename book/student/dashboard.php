<?php
$pageTitle = "Student Dashboard";
require_once __DIR__ . '/../includes/auth.php';
requireStudentLogin();

$student = getStudentData($conn, $_SESSION['student_id']);

// Get borrowed books stats
$totalBorrowed = $conn->prepare("SELECT COUNT(*) as total FROM borrowed_books WHERE student_id = ?");
$totalBorrowed->bind_param("i", $_SESSION['student_id']);
$totalBorrowed->execute();
$totalBorrowedCount = $totalBorrowed->get_result()->fetch_assoc()['total'];

$currentlyBorrowed = $conn->prepare("SELECT COUNT(*) as total FROM borrowed_books WHERE student_id = ? AND status = 'issued'");
$currentlyBorrowed->bind_param("i", $_SESSION['student_id']);
$currentlyBorrowed->execute();
$currentCount = $currentlyBorrowed->get_result()->fetch_assoc()['total'];

$overdueBooks = $conn->prepare("SELECT COUNT(*) as total FROM borrowed_books WHERE student_id = ? AND status = 'overdue'");
$overdueBooks->bind_param("i", $_SESSION['student_id']);
$overdueBooks->execute();
$overdueCount = $overdueBooks->get_result()->fetch_assoc()['total'];

$returnedBooks = $conn->prepare("SELECT COUNT(*) as total FROM borrowed_books WHERE student_id = ? AND status = 'returned'");
$returnedBooks->bind_param("i", $_SESSION['student_id']);
$returnedBooks->execute();
$returnedCount = $returnedBooks->get_result()->fetch_assoc()['total'];

// Recent borrowed books
$recentBooks = $conn->prepare("
    SELECT bb.*, b.title, b.author, b.isbn 
    FROM borrowed_books bb 
    JOIN books b ON bb.book_id = b.id 
    WHERE bb.student_id = ? 
    ORDER BY bb.issue_date DESC 
    LIMIT 5
");
$recentBooks->bind_param("i", $_SESSION['student_id']);
$recentBooks->execute();
$recentResult = $recentBooks->get_result();

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
            <li>
                <a href="<?php echo BASE_URL; ?>student/dashboard.php" class="active">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>student/borrowed_books.php">
                    <i class="fas fa-book"></i> My Books
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>student/profile.php">
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>student/change_password.php">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>student/logout.php" style="color: #FF6584;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>

        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-avatar">
                    <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                </div>
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
                <button class="btn btn-sm d-lg-none me-2" id="sidebarToggle">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <span class="top-bar-title">Dashboard</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted" style="font-size: 0.85rem;">
                    <i class="fas fa-calendar me-1"></i> <?php echo date('l, F j, Y'); ?>
                </span>
            </div>
        </div>

        <div class="content-area">
            <!-- Welcome Banner -->
            <div class="card-custom p-4 mb-4 animate-fade-up" style="background: var(--gradient-1); border-radius: 16px;">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-white fw-bold mb-2">Welcome back, <?php echo htmlspecialchars($student['full_name']); ?>! 👋</h3>
                        <p class="text-white-50 mb-0">Here's what's happening with your library account today.</p>
                    </div>
                    <div class="col-md-4 text-end d-none d-md-block">
                        <i class="fas fa-graduation-cap" style="font-size: 4rem; color: rgba(255,255,255,0.2);"></i>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6 animate-fade-up delay-1">
                    <div class="stat-card primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $totalBorrowedCount; ?></div>
                                <div class="stat-label">Total Borrowed</div>
                            </div>
                            <div class="stat-icon primary">
                                <i class="fas fa-books"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-2">
                    <div class="stat-card accent">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $currentCount; ?></div>
                                <div class="stat-label">Currently Issued</div>
                            </div>
                            <div class="stat-icon accent">
                                <i class="fas fa-book-open"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-3">
                    <div class="stat-card secondary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $overdueCount; ?></div>
                                <div class="stat-label">Overdue</div>
                            </div>
                            <div class="stat-icon secondary">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-4">
                    <div class="stat-card warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $returnedCount; ?></div>
                                <div class="stat-label">Returned</div>
                            </div>
                            <div class="stat-icon warning">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Books Table -->
            <div class="card-custom animate-fade-up delay-3">
                <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="fw-bold mb-0"><i class="fas fa-clock-rotate-left me-2 text-primary"></i>Recent Borrowings</h5>
                    <a href="<?php echo BASE_URL; ?>student/borrowed_books.php" class="btn btn-sm btn-primary-custom">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentResult->num_rows > 0): ?>
                                <?php while ($row = $recentResult->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['issue_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['due_date'])); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        switch ($row['status']) {
                                            case 'issued': $statusClass = 'badge-pending'; break;
                                            case 'returned': $statusClass = 'badge-returned'; break;
                                            case 'overdue': $statusClass = 'badge-overdue'; break;
                                        }
                                        ?>
                                        <span class="badge-custom <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No borrowing records found.
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