<?php
$pageTitle = "Manage Book Orders";
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();
$admin = getAdminData($conn, $_SESSION['admin_id']);

// Handle status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $orderId = (int)$_GET['id'];
    $action = $_GET['action'];
    
    $allowedStatuses = ['approved', 'rejected', 'fulfilled', 'pending'];
    
    if (in_array($action, $allowedStatuses)) {
        $stmt = $conn->prepare("UPDATE book_orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $orderId);
        
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Order status updated to '" . ucfirst($action) . "' successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to update order status.";
        }
        header("Location: " . BASE_URL . "admin/manage_orders.php");
        exit();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM book_orders WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Order deleted successfully.";
    } else {
        $_SESSION['error_msg'] = "Failed to delete order.";
    }
    header("Location: " . BASE_URL . "admin/manage_orders.php");
    exit();
}

// Filter
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

if ($statusFilter !== 'all' && in_array($statusFilter, ['pending', 'approved', 'rejected', 'fulfilled'])) {
    $stmt = $conn->prepare("SELECT * FROM book_orders WHERE status = ? ORDER BY requested_at DESC");
    $stmt->bind_param("s", $statusFilter);
    $stmt->execute();
    $orders = $stmt->get_result();
} else {
    $orders = $conn->query("SELECT * FROM book_orders ORDER BY requested_at DESC");
}

// Counts for filter badges
$allCount = $conn->query("SELECT COUNT(*) as c FROM book_orders")->fetch_assoc()['c'];
$pendingCount = $conn->query("SELECT COUNT(*) as c FROM book_orders WHERE status='pending'")->fetch_assoc()['c'];
$approvedCount = $conn->query("SELECT COUNT(*) as c FROM book_orders WHERE status='approved'")->fetch_assoc()['c'];
$rejectedCount = $conn->query("SELECT COUNT(*) as c FROM book_orders WHERE status='rejected'")->fetch_assoc()['c'];
$fulfilledCount = $conn->query("SELECT COUNT(*) as c FROM book_orders WHERE status='fulfilled'")->fetch_assoc()['c'];

$totalOrders = $pendingCount; // For sidebar badge

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-wrapper">
    <!-- ===== Sidebar ===== -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="sidebar-brand">
                <i class="fas fa-book-open-reader"></i> BookNest
            </a>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo BASE_URL; ?>admin/dashboard.php">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/manage_books.php">
                    <i class="fas fa-book"></i> Manage Books
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/manage_students.php">
                    <i class="fas fa-user-graduate"></i> Manage Students
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/issue_book.php">
                    <i class="fas fa-hand-holding"></i> Issue Book
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/return_book.php">
                    <i class="fas fa-rotate-left"></i> Return Book
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/manage_orders.php" class="active">
                    <i class="fas fa-cart-shopping"></i> Book Orders
                    <?php if ($totalOrders > 0): ?>
                        <span class="badge bg-danger ms-auto rounded-pill"><?php echo $totalOrders; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/change_password.php">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>admin/logout.php" style="color: #FF6584;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>

        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-avatar" style="background: var(--gradient-2);">
                    <?php echo strtoupper(substr($admin['full_name'], 0, 1)); ?>
                </div>
                <div>
                    <div class="sidebar-user-name"><?php echo htmlspecialchars($admin['full_name']); ?></div>
                    <div class="sidebar-user-role">Librarian</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- ===== Main Content ===== -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div>
                <button class="btn btn-sm d-lg-none me-2" id="sidebarToggle">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <span class="top-bar-title">
                    <i class="fas fa-cart-shopping me-2 text-primary"></i>Book Orders / Requests
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size: 0.85rem;">
                    <i class="fas fa-calendar me-1"></i> <?php echo date('l, F j, Y'); ?>
                </span>
            </div>
        </div>

        <div class="content-area">

            <!-- ===== Stats Summary Cards ===== -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6 animate-fade-up delay-1">
                    <div class="stat-card primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $allCount; ?></div>
                                <div class="stat-label">Total Orders</div>
                            </div>
                            <div class="stat-icon primary">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-2">
                    <div class="stat-card warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $pendingCount; ?></div>
                                <div class="stat-label">Pending</div>
                            </div>
                            <div class="stat-icon warning">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-3">
                    <div class="stat-card accent">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $approvedCount; ?></div>
                                <div class="stat-label">Approved</div>
                            </div>
                            <div class="stat-icon accent">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 animate-fade-up delay-4">
                    <div class="stat-card secondary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number"><?php echo $fulfilledCount; ?></div>
                                <div class="stat-label">Fulfilled</div>
                            </div>
                            <div class="stat-icon secondary">
                                <i class="fas fa-box-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Filter & Search Bar ===== -->
            <div class="card-custom p-3 mb-4 animate-fade-up">
                <div class="row align-items-center g-3">
                    <!-- Search -->
                    <div class="col-lg-5">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" id="tableSearch" placeholder="Search orders by name, email, book...">
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="col-lg-7">
                        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                            <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?status=all" 
                               class="btn btn-sm <?php echo $statusFilter === 'all' ? 'btn-primary-custom' : 'btn-secondary-custom'; ?> px-3">
                                All <span class="badge bg-white text-dark ms-1"><?php echo $allCount; ?></span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?status=pending" 
                               class="btn btn-sm <?php echo $statusFilter === 'pending' ? 'btn-primary-custom' : 'btn-secondary-custom'; ?> px-3">
                                <i class="fas fa-clock me-1"></i> Pending 
                                <span class="badge bg-warning text-dark ms-1"><?php echo $pendingCount; ?></span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?status=approved" 
                               class="btn btn-sm <?php echo $statusFilter === 'approved' ? 'btn-primary-custom' : 'btn-secondary-custom'; ?> px-3">
                                <i class="fas fa-check me-1"></i> Approved 
                                <span class="badge bg-success ms-1"><?php echo $approvedCount; ?></span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?status=rejected" 
                               class="btn btn-sm <?php echo $statusFilter === 'rejected' ? 'btn-primary-custom' : 'btn-secondary-custom'; ?> px-3">
                                <i class="fas fa-times me-1"></i> Rejected 
                                <span class="badge bg-danger ms-1"><?php echo $rejectedCount; ?></span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?status=fulfilled" 
                               class="btn btn-sm <?php echo $statusFilter === 'fulfilled' ? 'btn-primary-custom' : 'btn-secondary-custom'; ?> px-3">
                                <i class="fas fa-box me-1"></i> Fulfilled 
                                <span class="badge bg-info ms-1"><?php echo $fulfilledCount; ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Orders Table ===== -->
            <div class="card-custom animate-fade-up delay-2">
                <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-list-check me-2 text-primary"></i>
                        <?php
                        switch ($statusFilter) {
                            case 'pending': echo 'Pending Orders'; break;
                            case 'approved': echo 'Approved Orders'; break;
                            case 'rejected': echo 'Rejected Orders'; break;
                            case 'fulfilled': echo 'Fulfilled Orders'; break;
                            default: echo 'All Orders';
                        }
                        ?>
                    </h5>
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        <?php echo $orders->num_rows; ?> record(s)
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Requester</th>
                                <th>Contact</th>
                                <th>Book Requested</th>
                                <th>Author</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th style="min-width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders->num_rows > 0): ?>
                                <?php $i = 1; while ($order = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo $i++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="sidebar-avatar me-2" style="width: 36px; height: 36px; font-size: 0.75rem; flex-shrink: 0;">
                                                <?php echo strtoupper(substr($order['requester_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold" style="font-size: 0.9rem;">
                                                    <?php echo htmlspecialchars($order['requester_name']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem;">
                                            <div><i class="fas fa-envelope me-1 text-muted" style="width: 14px;"></i> <?php echo htmlspecialchars($order['requester_email']); ?></div>
                                            <?php if (!empty($order['requester_phone'])): ?>
                                                <div class="mt-1"><i class="fas fa-phone me-1 text-muted" style="width: 14px;"></i> <?php echo htmlspecialchars($order['requester_phone']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($order['book_title']); ?></td>
                                    <td>
                                        <?php echo !empty($order['author']) ? htmlspecialchars($order['author']) : '<span class="text-muted">N/A</span>'; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($order['message'])): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#msgModal<?php echo $order['id']; ?>">
                                                <i class="fas fa-eye me-1"></i> View
                                            </button>

                                            <!-- Message Modal -->
                                            <div class="modal fade" id="msgModal<?php echo $order['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title fw-bold">
                                                                <i class="fas fa-message me-2 text-primary"></i>Message from <?php echo htmlspecialchars($order['requester_name']); ?>
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="p-3 rounded-3" style="background: var(--light); font-size: 0.93rem; line-height: 1.7;">
                                                                <?php echo nl2br(htmlspecialchars($order['message'])); ?>
                                                            </div>
                                                            <div class="mt-3" style="font-size: 0.82rem; color: var(--text-muted);">
                                                                <strong>Book:</strong> <?php echo htmlspecialchars($order['book_title']); ?><br>
                                                                <strong>Requested:</strong> <?php echo date('M d, Y \a\t h:i A', strtotime($order['requested_at'])); ?>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-secondary-custom btn-sm" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size: 0.82rem;">No message</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.82rem;">
                                            <div class="fw-semibold"><?php echo date('M d, Y', strtotime($order['requested_at'])); ?></div>
                                            <div class="text-muted"><?php echo date('h:i A', strtotime($order['requested_at'])); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = '';
                                        $badgeIcon = '';
                                        switch ($order['status']) {
                                            case 'pending':
                                                $badgeClass = 'badge-pending';
                                                $badgeIcon = 'fa-clock';
                                                break;
                                            case 'approved':
                                                $badgeClass = 'badge-active';
                                                $badgeIcon = 'fa-check-circle';
                                                break;
                                            case 'rejected':
                                                $badgeClass = 'badge-overdue';
                                                $badgeIcon = 'fa-times-circle';
                                                break;
                                            case 'fulfilled':
                                                $badgeClass = 'badge-returned';
                                                $badgeIcon = 'fa-box-check';
                                                break;
                                        }
                                        ?>
                                        <span class="badge-custom <?php echo $badgeClass; ?>">
                                            <i class="fas <?php echo $badgeIcon; ?> me-1"></i>
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php if ($order['status'] === 'pending'): ?>
                                                <!-- Approve Button -->
                                                <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?action=approved&id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-success rounded-pill px-2 py-1 btn-approve-confirm"
                                                   data-name="<?php echo htmlspecialchars($order['book_title']); ?>"
                                                   data-bs-toggle="tooltip" title="Approve">
                                                    <i class="fas fa-check" style="font-size: 0.75rem;"></i>
                                                </a>
                                                <!-- Reject Button -->
                                                <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?action=rejected&id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-danger rounded-pill px-2 py-1 btn-reject-confirm"
                                                   data-name="<?php echo htmlspecialchars($order['book_title']); ?>"
                                                   data-bs-toggle="tooltip" title="Reject">
                                                    <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($order['status'] === 'approved'): ?>
                                                <!-- Mark Fulfilled -->
                                                <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?action=fulfilled&id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-info text-white rounded-pill px-2 py-1"
                                                   data-bs-toggle="tooltip" title="Mark Fulfilled">
                                                    <i class="fas fa-box-check" style="font-size: 0.75rem;"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($order['status'] === 'rejected'): ?>
                                                <!-- Re-open / Set Pending -->
                                                <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?action=pending&id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-warning rounded-pill px-2 py-1"
                                                   data-bs-toggle="tooltip" title="Reopen">
                                                    <i class="fas fa-rotate-left" style="font-size: 0.75rem;"></i>
                                                </a>
                                            <?php endif; ?>

                                            <!-- Delete Button -->
                                            <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?delete=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 btn-delete-confirm"
                                               data-name="order from <?php echo htmlspecialchars($order['requester_name']); ?>"
                                               data-url="<?php echo BASE_URL; ?>admin/manage_orders.php?delete=<?php echo $order['id']; ?>"
                                               data-bs-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="animate-fade-up">
                                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" 
                                                 style="width: 80px; height: 80px; background: rgba(108, 99, 255, 0.1);">
                                                <i class="fas fa-inbox fa-2x" style="color: var(--primary);"></i>
                                            </div>
                                            <h5 class="fw-bold text-muted mb-1">No Orders Found</h5>
                                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                                <?php if ($statusFilter !== 'all'): ?>
                                                    No <?php echo $statusFilter; ?> orders at the moment.
                                                    <br>
                                                    <a href="<?php echo BASE_URL; ?>admin/manage_orders.php?status=all" class="text-primary text-decoration-none mt-2 d-inline-block">
                                                        <i class="fas fa-arrow-left me-1"></i> View all orders
                                                    </a>
                                                <?php else: ?>
                                                    No book orders have been placed yet.
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

<!-- ===== Extra SweetAlert for Approve/Reject Confirmations ===== -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Approve Confirmation
    document.querySelectorAll('.btn-approve-confirm').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const bookName = this.dataset.name || 'this order';

            Swal.fire({
                title: 'Approve Order?',
                html: `Are you sure you want to <strong class="text-success">approve</strong> the request for "<strong>${bookName}</strong>"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00D4AA',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, Approve',
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

    // Reject Confirmation
    document.querySelectorAll('.btn-reject-confirm').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const bookName = this.dataset.name || 'this order';

            Swal.fire({
                title: 'Reject Order?',
                html: `Are you sure you want to <strong class="text-danger">reject</strong> the request for "<strong>${bookName}</strong>"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF6584',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-times me-1"></i> Yes, Reject',
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

 