<?php
$pageTitle = "Edit Student";
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();
$admin = getAdminData($conn, $_SESSION['admin_id']);

if (!isset($_GET['id'])) {
    $_SESSION['error_msg'] = "No student selected.";
    header("Location: " . BASE_URL . "admin/manage_students.php");
    exit();
}

$studentId = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$stu = $stmt->get_result()->fetch_assoc();

if (!$stu) {
    $_SESSION['error_msg'] = "Student not found.";
    header("Location: " . BASE_URL . "admin/manage_students.php");
    exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $department = htmlspecialchars(trim($_POST['department']));
    $student_id_val = htmlspecialchars(trim($_POST['student_id']));
    $status = $_POST['status'];

    if (empty($full_name) || empty($email)) {
        $_SESSION['error_msg'] = "Name and Email are required.";
        header("Location: " . BASE_URL . "admin/edit_student.php?id=" . $studentId);
        exit();
    }

    // Check duplicate email (exclude current student)
    $checkStmt = $conn->prepare("SELECT id FROM students WHERE (email = ? OR student_id = ?) AND id != ?");
    $checkStmt->bind_param("ssi", $email, $student_id_val, $studentId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        $_SESSION['error_msg'] = "Email or Student ID already exists for another student.";
        header("Location: " . BASE_URL . "admin/edit_student.php?id=" . $studentId);
        exit();
    }

    $stmt = $conn->prepare("UPDATE students SET full_name=?, email=?, phone=?, department=?, student_id=?, status=? WHERE id=?");
    $stmt->bind_param("ssssssi", $full_name, $email, $phone, $department, $student_id_val, $status, $studentId);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Student '$full_name' updated successfully!";
        header("Location: " . BASE_URL . "admin/manage_students.php");
    } else {
        $_SESSION['error_msg'] = "Failed to update student.";
        header("Location: " . BASE_URL . "admin/edit_student.php?id=" . $studentId);
    }
    exit();
}

// Borrowed books by this student
$borrowedStmt = $conn->prepare("
    SELECT bb.*, b.title as book_title 
    FROM borrowed_books bb 
    JOIN books b ON bb.book_id = b.id 
    WHERE bb.student_id = ? 
    ORDER BY bb.id DESC LIMIT 10
");
$borrowedStmt->bind_param("i", $studentId);
$borrowedStmt->execute();
$borrowedBooks = $borrowedStmt->get_result();

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
            <li><a href="<?php echo BASE_URL; ?>admin/manage_students.php" class="active"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/issue_book.php"><i class="fas fa-hand-holding"></i> Issue Book</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/return_book.php"><i class="fas fa-rotate-left"></i> Return Book</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/manage_orders.php"><i class="fas fa-cart-shopping"></i> Book Orders</a></li>
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
                <span class="top-bar-title"><i class="fas fa-user-edit me-2 text-primary"></i>Edit Student</span>
            </div>
            <a href="<?php echo BASE_URL; ?>admin/manage_students.php" class="btn btn-secondary-custom btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="content-area">
            <div class="row g-4">
                <!-- Edit Form -->
                <div class="col-lg-7 animate-fade-up">
                    <div class="card-custom p-4">
                        <h5 class="fw-bold mb-4"><i class="fas fa-edit me-2 text-primary"></i>Student Information</h5>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" name="full_name" 
                                               value="<?php echo htmlspecialchars($stu['full_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?php echo htmlspecialchars($stu['email']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Student ID</label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control" name="student_id" 
                                               value="<?php echo htmlspecialchars($stu['student_id']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" name="phone" 
                                               value="<?php echo htmlspecialchars($stu['phone']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Department</label>
                                    <select class="form-select form-select-custom" name="department">
                                        <?php
                                        $departments = ['Computer Science', 'Engineering', 'Business', 'Arts', 'Science', 'Medicine', 'Law', 'Other'];
                                        foreach ($departments as $dept):
                                        ?>
                                            <option value="<?php echo $dept; ?>" <?php echo $stu['department'] === $dept ? 'selected' : ''; ?>>
                                                <?php echo $dept; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select class="form-select form-select-custom" name="status">
                                        <option value="active" <?php echo $stu['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="suspended" <?php echo $stu['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                        <option value="inactive" <?php echo $stu['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary-custom px-5 py-3">
                                    <i class="fas fa-save me-2"></i> Update Student
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Student Info + Borrow History -->
                <div class="col-lg-5 animate-fade-up delay-2">
                    <!-- Info Card -->
                    <div class="card-custom p-4 mb-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
                             style="width: 80px; height: 80px; background: var(--gradient-1); font-size: 2rem; color: white; font-weight: 700;">
                            <?php echo strtoupper(substr($stu['full_name'], 0, 1)); ?>
                        </div>
                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($stu['full_name']); ?></h5>
                        <p class="text-muted mb-2"><?php echo htmlspecialchars($stu['student_id']); ?></p>
                        <span class="badge-custom <?php echo $stu['status'] === 'active' ? 'badge-active' : 'badge-suspended'; ?>">
                            <?php echo ucfirst($stu['status']); ?>
                        </span>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>Joined: <?php echo date('M d, Y', strtotime($stu['created_at'])); ?>
                            </small>
                        </div>
                    </div>

                    <!-- Borrow History -->
                    <div class="card-custom">
                        <div class="p-3 border-bottom">
                            <h6 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Borrow History</h6>
                        </div>
                        <div class="p-3">
                            <?php if ($borrowedBooks->num_rows > 0): ?>
                                <?php while ($bb = $borrowedBooks->fetch_assoc()): ?>
                                    <div class="d-flex align-items-center p-2 mb-2 rounded-3" style="background: var(--light);">
                                        <div class="me-3">
                                            <div class="d-flex align-items-center justify-content-center rounded-2"
                                                 style="width: 40px; height: 40px; background: rgba(108,99,255,0.1);">
                                                <i class="fas fa-book" style="color: var(--primary); font-size: 0.85rem;"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold" style="font-size: 0.85rem;"><?php echo htmlspecialchars($bb['book_title']); ?></div>
                                            <small class="text-muted"><?php echo date('M d, Y', strtotime($bb['issue_date'])); ?></small>
                                        </div>
                                        <div>
                                            <?php
                                            $bClass = '';
                                            switch ($bb['status']) {
                                                case 'issued': $bClass = 'badge-pending'; break;
                                                case 'returned': $bClass = 'badge-returned'; break;
                                                case 'overdue': $bClass = 'badge-overdue'; break;
                                            }
                                            ?>
                                            <span class="badge-custom <?php echo $bClass; ?>" style="font-size: 0.7rem;">
                                                <?php echo ucfirst($bb['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-3 text-muted">
                                    <i class="fas fa-inbox mb-2 d-block"></i>
                                    <small>No borrowing history</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>