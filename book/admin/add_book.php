<?php
$pageTitle = "Add Book";
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();
$admin = getAdminData($conn, $_SESSION['admin_id']);
require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="sidebar-brand"><i class="fas fa-book-open-reader"></i> BookNest</a>
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

    <main class="main-content">
        <div class="top-bar">
            <div>
                <button class="btn btn-sm d-lg-none me-2" id="sidebarToggle"><i class="fas fa-bars fa-lg"></i></button>
                <span class="top-bar-title">Add New Book</span>
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
                                <i class="fas fa-plus fa-2x" style="color: var(--primary);"></i>
                            </div>
                            <h4 class="fw-bold">Add a New Book</h4>
                            <p class="text-muted">Fill in the book details below</p>
                        </div>

                        <form action="<?php echo BASE_URL; ?>actions/add_book_action.php" method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-book"></i></span>
                                        <input type="text" class="form-control" name="title" placeholder="Book Title" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-pen-fancy"></i></span>
                                        <input type="text" class="form-control" name="author" placeholder="Author" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-barcode"></i></span>
                                        <input type="text" class="form-control" name="isbn" placeholder="ISBN">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select form-select-custom" name="category" required>
                                        <option value="" disabled selected>Select Category</option>
                                        <option value="Fiction">Fiction</option>
                                        <option value="Non-Fiction">Non-Fiction</option>
                                        <option value="Technology">Technology</option>
                                        <option value="Science">Science</option>
                                        <option value="History">History</option>
                                        <option value="Philosophy">Philosophy</option>
                                        <option value="Business">Business</option>
                                        <option value="Education">Education</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-layer-group"></i></span>
                                        <input type="number" class="form-control" name="quantity" placeholder="Quantity" min="1" value="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating-custom">
                                        <span class="input-icon"><i class="fas fa-map-pin"></i></span>
                                        <input type="text" class="form-control" name="shelf_location" placeholder="Shelf Location">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select form-select-custom" name="status">
                                        <option value="available">Available</option>
                                        <option value="unavailable">Unavailable</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control form-select-custom" name="description" rows="3" 
                                              placeholder="Book description (optional)" style="resize: none;"></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-custom w-100 py-3 mt-4">
                                <i class="fas fa-save me-2"></i> Add Book
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>