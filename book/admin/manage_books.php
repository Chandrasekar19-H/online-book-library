<?php
$pageTitle = "Manage Books";
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();
$admin = getAdminData($conn, $_SESSION['admin_id']);

$books = $conn->query("SELECT * FROM books ORDER BY id DESC");
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
            <li><a href="<?php echo BASE_URL; ?>admin/manage_books.php" class="active"><i class="fas fa-book"></i> Manage Books</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/manage_students.php"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/issue_book.php"><i class="fas fa-hand-holding"></i> Issue Book</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/return_book.php"><i class="fas fa-rotate-left"></i> Return Book</a></li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/manage_orders.php"><i class="fas fa-cart-shopping"></i> Book Orders
                <?php if ($totalOrders > 0): ?><span class="badge bg-danger ms-auto rounded-pill"><?php echo $totalOrders; ?></span><?php endif; ?>
                </a>
            </li>
            <li><a href="<?php echo BASE_URL; ?>admin/change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/logout.php" style="color: #FF6584;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-avatar" style="background: var(--gradient-2);"><?php echo strtoupper(substr($admin['full_name'], 0, 1)); ?></div>
                <div>
                    <div class="sidebar-user-name"><?php echo htmlspecialchars($admin['full_name']); ?></div>
                    <div class="sidebar-user-role">Librarian</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <button class="btn btn-sm d-lg-none me-2" id="sidebarToggle"><i class="fas fa-bars fa-lg"></i></button>
                <span class="top-bar-title">Manage Books</span>
            </div>
            <a href="<?php echo BASE_URL; ?>admin/add_book.php" class="btn btn-primary-custom btn-sm">
                <i class="fas fa-plus me-1"></i> Add Book
            </a>
        </div>

        <div class="content-area">
            <!-- Search Bar -->
            <div class="card-custom p-3 mb-4 animate-fade-up">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" id="tableSearch" placeholder="Search books...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="text-muted">Total: <strong><?php echo $books->num_rows; ?></strong> books</span>
                    </div>
                </div>
            </div>

            <!-- Books Table -->
            <div class="card-custom animate-fade-up delay-1">
                <div class="table-responsive">
                    <table class="table table-custom mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Category</th>
                                <th>Qty</th>
                                <th>Available</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($books->num_rows > 0): ?>
                                <?php $i = 1; while ($book = $books->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><code><?php echo htmlspecialchars($book['isbn']); ?></code></td>
                                    <td><?php echo htmlspecialchars($book['category']); ?></td>
                                    <td><?php echo $book['quantity']; ?></td>
                                    <td><?php echo $book['available_qty']; ?></td>
                                    <td>
                                        <span class="badge-custom <?php echo $book['status'] == 'available' ? 'badge-available' : 'badge-unavailable'; ?>">
                                            <?php echo ucfirst($book['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>admin/edit_book.php?id=<?php echo $book['id']; ?>" 
                                           class="btn-action edit" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>actions/delete_book_action.php?id=<?php echo $book['id']; ?>" 
                                           class="btn-action delete btn-delete-confirm" 
                                           data-name="<?php echo htmlspecialchars($book['title']); ?>"
                                           data-bs-toggle="tooltip" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No books found. Add your first book!
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