<?php
$pageTitle = "My Profile";
require_once __DIR__ . '/../includes/auth.php';
requireStudentLogin();
$student = getStudentData($conn, $_SESSION['student_id']);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $department = htmlspecialchars(trim($_POST['department']));

    if (empty($full_name)) {
        $_SESSION['error_msg'] = "Full name is required.";
        header("Location: " . BASE_URL . "student/profile.php");
        exit();
    }

    $stmt = $conn->prepare("UPDATE students SET full_name = ?, phone = ?, department = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $phone, $department, $_SESSION['student_id']);

    if ($stmt->execute()) {
        $_SESSION['student_name'] = $full_name;
        $_SESSION['success_msg'] = "Profile updated successfully!";
    } else {
        $_SESSION['error_msg'] = "Failed to update profile.";
    }
    header("Location: " . BASE_URL . "student/profile.php");
    exit();
}

// Get stats
$totalBorrowed = $conn->prepare("SELECT COUNT(*) as c FROM borrowed_books WHERE student_id = ?");
$totalBorrowed->bind_param("i", $_SESSION['student_id']);
$totalBorrowed->execute();
$borrowedCount = $totalBorrowed->get_result()->fetch_assoc()['c'];

$currentIssued = $conn->prepare("SELECT COUNT(*) as c FROM borrowed_books WHERE student_id = ? AND status='issued'");
$currentIssued->bind_param("i", $_SESSION['student_id']);
$currentIssued->execute();
$issuedCount = $currentIssued->get_result()->fetch_assoc()['c'];

$finesTotal = $conn->prepare("SELECT COALESCE(SUM(fine_amount), 0) as total FROM borrowed_books WHERE student_id = ?");
$finesTotal->bind_param("i", $_SESSION['student_id']);
$finesTotal->execute();
$totalFines = $finesTotal->get_result()->fetch_assoc()['total'];

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
            <li><a href="<?php echo BASE_URL; ?>student/borrowed_books.php"><i class="fas fa-book"></i> My Books</a></li>
            <li><a href="<?php echo BASE_URL; ?>student/profile.php" class="active"><i class="fas fa-user"></i> Profile</a></li>
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
                <span class="top-bar-title"><i class="fas fa-user me-2 text-primary"></i>My Profile</span>
            </div>
        </div>

        <div class="content-area">
            <div class="row g-4">
                <!-- Profile Card -->
                <div class="col-lg-4 animate-fade-up">
                    <div class="card-custom text-center p-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
                             style="width: 100px; height: 100px; background: var(--gradient-1); font-size: 2.5rem; color: white; font-weight: 700;">
                            <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                        </div>
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h4>
                        <p class="text-muted mb-1"><?php echo htmlspecialchars($student['email']); ?></p>
                        <span class="badge-custom <?php echo $student['status'] === 'active' ? 'badge-active' : 'badge-suspended'; ?>">
                            <?php echo ucfirst($student['status']); ?>
                        </span>

                        <hr class="my-4">

                        <div class="text-start">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted"><i class="fas fa-id-card me-2"></i>Student ID</span>
                                <span class="fw-semibold"><?php echo htmlspecialchars($student['student_id']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted"><i class="fas fa-building me-2"></i>Department</span>
                                <span class="fw-semibold"><?php echo htmlspecialchars($student['department']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted"><i class="fas fa-phone me-2"></i>Phone</span>
                                <span class="fw-semibold"><?php echo htmlspecialchars($student['phone'] ?: 'N/A'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted"><i class="fas fa-calendar me-2"></i>Joined</span>
                                <span class="fw-semibold"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Quick Stats -->
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="p-2 rounded-3" style="background: rgba(108, 99, 255, 0.08);">
                                    <div class="fw-bold text-primary"><?php echo $borrowedCount; ?></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Borrowed</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded-3" style="background: rgba(0, 212, 170, 0.08);">
                                    <div class="fw-bold" style="color: var(--accent);"><?php echo $issuedCount; ?></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Active</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded-3" style="background: rgba(255, 101, 132, 0.08);">
                                    <div class="fw-bold" style="color: var(--secondary);">$<?php echo number_format($totalFines, 2); ?></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Fines</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <div class="col-lg-8 animate-fade-up delay-2">
                    <div class="card-custom p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="fas fa-edit me-2 text-primary"></i>Edit Profile
                        </h5>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" name="full_name" 
                                               value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" disabled>
                                    </div>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Student ID</label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['student_id']); ?>" disabled>
                                    </div>
                                    <small class="text-muted">Student ID cannot be changed</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" name="phone" 
                                               value="<?php echo htmlspecialchars($student['phone']); ?>" placeholder="Phone Number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Department</label>
                                    <select class="form-select form-select-custom" name="department">
                                        <option value="" disabled>Select Department</option>
                                        <?php
                                        $departments = ['Computer Science', 'Engineering', 'Business', 'Arts', 'Science', 'Medicine', 'Law', 'Other'];
                                        foreach ($departments as $dept):
                                        ?>
                                            <option value="<?php echo $dept; ?>" <?php echo $student['department'] === $dept ? 'selected' : ''; ?>>
                                                <?php echo $dept; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary-custom px-5 py-3">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>