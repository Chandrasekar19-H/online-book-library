<?php
$pageTitle = "Manage Students";
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();
$admin = getAdminData($conn, $_SESSION['admin_id']);

$students = $conn->query("SELECT * FROM students ORDER BY id DESC");
$totalOrders = $conn->query("SELECT COUNT(*) as c FROM book_orders WHERE status='pending'")->fetch_assoc()['c'];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="sidebar-brand"><i class="fas fa-book-open-reader"></i> BookNest</a>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo BASE_URL; ?>admin/dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/manage_books.php"><i class="fas fa-book"></i> Manage Books</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/manage_students.php" class="active"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/issue_book.php"><i class="fas fa-hand-holding"></i> Issue Book</a></li>
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

    <main class="main-content">
        <div class="top-bar">
            <div>
                <button class="btn btn-sm d-lg-none me-2" id="sidebarToggle"><i class="fas fa-bars fa-lg"></i></button>
                <span class="top-bar-title">Manage Students</span>
            </div>
        </div>

        <div class="content-area">
            <div class="card-custom p-3 mb-4 animate-fade-up">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" id="tableSearch" placeholder="Search students...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="text-muted">Total: <strong><?php echo $students->num_rows; ?></strong> students</span>
                    </div>
                </div>
            </div>

            <div class="card-custom animate-fade-up delay-1">
                <div class="table-responsive">
                    <table class="table table-custom mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Student ID</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($students->num_rows > 0): ?>
                                <?php $i = 1; while ($stu = $students->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($stu['full_name']); ?></td>
                                    <td><code><?php echo htmlspecialchars($stu['student_id']); ?></code></td>
                                    <td><?php echo htmlspecialchars($stu['email']); ?></td>
                                    <td><?php echo htmlspecialchars($stu['department']); ?></td>
                                    <td><?php echo htmlspecialchars($stu['phone']); ?></td>
                                    <td>
                                        <?php
                                        $sClass = $stu['status'] == 'active' ? 'badge-active' : ($stu['status'] == 'suspended' ? 'badge-suspended' : 'badge-unavailable');
                                        ?>
                                        <span class="badge-custom <?php echo $sClass; ?>"><?php echo ucfirst($stu['status']); ?></span>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>admin/edit_student.php?id=<?php echo $stu['id']; ?>" 
                                           class="btn-action edit" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>actions/delete_student_action.php?id=<?php echo $stu['id']; ?>" 
                                           class="btn-action delete btn-delete-confirm" 
                                           data-name="<?php echo htmlspecialchars($stu['full_name']); ?>"
                                           data-bs-toggle="tooltip" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No students registered yet.
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