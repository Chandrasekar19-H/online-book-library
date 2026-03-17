<?php
$pageTitle = "Edit Book";
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();
$admin = getAdminData($conn, $_SESSION['admin_id']);

// Get book data
if (!isset($_GET['id'])) {
    $_SESSION['error_msg'] = "No book selected.";
    header("Location: " . BASE_URL . "admin/manage_books.php");
    exit();
}

$bookId = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $bookId);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    $_SESSION['error_msg'] = "Book not found.";
    header("Location: " . BASE_URL . "admin/manage_books.php");
    exit();
}

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
            <li><a href="<?php echo BASE_URL; ?>admin/manage_books.php" class="active"><i class="fas fa-book"></i> Manage Books</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/manage_students.php"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
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
                <span class="top-bar-title"><i class="fas fa-edit me-2 text-primary"></i>Edit Book</span>
            </div>
            <a href="<?php echo BASE_URL; ?>admin/manage_books.php" class="btn btn-secondary-custom btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="content-area">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-custom p-4 animate-fade-up">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                 style="width: 70px; height: 70px; background: rgba(108, 99, 255, 0.1);">
                                <i class="fas fa-edit fa-2x" style="color: var(--primary);"></i>
                            </div>
                            <h4 class="fw-bold">Edit Book Details</h4>
                            <p class="text-muted">Update the information for "<?php echo htmlspecialchars($book['title']); ?>"</p>
                        </div>

                        <form action="<?php echo BASE_URL; ?>actions/edit_book_action.php" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Book Title <span class="text-danger">*</span></label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-book"></i></span>
                                        <input type="text" class="form-control" name="title" 
                                               value="<?php echo htmlspecialchars($book['title']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Author <span class="text-danger">*</span></label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-pen-fancy"></i></span>
                                        <input type="text" class="form-control" name="author" 
                                               value="<?php echo htmlspecialchars($book['author']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">ISBN</label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-barcode"></i></span>
                                        <input type="text" class="form-control" name="isbn" 
                                               value="<?php echo htmlspecialchars($book['isbn']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Category</label>
                                    <select class="form-select form-select-custom" name="category" required>
                                        <?php
                                        $categories = ['Fiction', 'Non-Fiction', 'Technology', 'Science', 'History', 'Philosophy', 'Business', 'Education', 'Other'];
                                        foreach ($categories as $cat):
                                        ?>
                                            <option value="<?php echo $cat; ?>" <?php echo $book['category'] === $cat ? 'selected' : ''; ?>>
                                                <?php echo $cat; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Total Quantity</label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-layer-group"></i></span>
                                        <input type="number" class="form-control" name="quantity" min="1" 
                                               value="<?php echo $book['quantity']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Available Qty</label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-cubes"></i></span>
                                        <input type="number" class="form-control" name="available_qty" min="0" 
                                               value="<?php echo $book['available_qty']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Shelf Location</label>
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-map-pin"></i></span>
                                        <input type="text" class="form-control" name="shelf_location" 
                                               value="<?php echo htmlspecialchars($book['shelf_location']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select class="form-select form-select-custom" name="status">
                                        <option value="available" <?php echo $book['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                        <option value="unavailable" <?php echo $book['status'] === 'unavailable' ? 'selected' : ''; ?>>Unavailable</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control form-select-custom" name="description" rows="3" 
                                              style="resize: none;"><?php echo htmlspecialchars($book['description']); ?></textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <button type="submit" class="btn btn-primary-custom px-5 py-3">
                                    <i class="fas fa-save me-2"></i> Update Book
                                </button>
                                <a href="<?php echo BASE_URL; ?>admin/manage_books.php" class="btn btn-secondary-custom px-4 py-3">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>