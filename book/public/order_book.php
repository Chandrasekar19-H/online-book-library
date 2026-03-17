<?php
$pageTitle = "Order a Book";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div style="padding-top: 100px; min-height: 100vh; background: var(--light);">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="form-card animate-fade-up">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" 
                             style="width: 80px; height: 80px; background: rgba(108, 99, 255, 0.1);">
                            <i class="fas fa-cart-shopping fa-2x" style="color: var(--primary);"></i>
                        </div>
                        <h2 class="form-title">Order / Request a Book</h2>
                        <p class="form-subtitle mb-0">
                            Can't find a book? Fill in the form below and we'll get it for you!
                        </p>
                    </div>

                    <form action="<?php echo BASE_URL; ?>actions/order_book_action.php" method="POST" class="needs-validation" novalidate>
                        
                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <span class="input-icon"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" name="requester_name" placeholder="Your Name" required>
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="requester_email" placeholder="Your Email" required>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <span class="input-icon"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control" name="requester_phone" placeholder="Phone Number">
                                </div>
                            </div>

                            <!-- Book Title -->
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <span class="input-icon"><i class="fas fa-book"></i></span>
                                    <input type="text" class="form-control" name="book_title" placeholder="Book Title" required>
                                </div>
                            </div>

                            <!-- Author -->
                            <div class="col-12">
                                <div class="form-floating-custom">
                                    <span class="input-icon"><i class="fas fa-pen-fancy"></i></span>
                                    <input type="text" class="form-control" name="author" placeholder="Author Name (optional)">
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="col-12">
                                <textarea class="form-control form-select-custom" name="message" rows="4" 
                                          placeholder="Additional notes or message (optional)" 
                                          style="resize: none;"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 py-3 mt-4">
                            <i class="fas fa-paper-plane me-2"></i> Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>