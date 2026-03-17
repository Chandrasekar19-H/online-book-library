<?php
$pageTitle = "Librarian Login";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-page">
    <div class="container position-relative" style="z-index: 2;">
        <div class="form-container animate-slide-in">
            <div class="form-card">
                <!-- Logo -->
                <div class="text-center mb-3">
                    <a href="<?php echo BASE_URL; ?>public/index.php" class="navbar-brand-custom text-decoration-none">
                        <i class="fas fa-book-open-reader"></i> BookNest
                    </a>
                </div>

                <div class="text-center mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" 
                         style="width: 70px; height: 70px; background: rgba(108, 99, 255, 0.1);">
                        <i class="fas fa-user-shield fa-2x" style="color: var(--primary);"></i>
                    </div>
                </div>

                <h2 class="form-title">Librarian Portal</h2>
                <p class="form-subtitle">Access the admin dashboard</p>

                <form action="<?php echo BASE_URL; ?>actions/librarian_login_action.php" method="POST" class="needs-validation" novalidate>
                    
                    <!-- Email -->
                    <div class="form-floating-custom">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" placeholder="Admin Email" required>
                    </div>

                    <!-- Password -->
                    <div class="form-floating-custom">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <button type="button" class="password-toggle" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary-custom w-100 py-3 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Login to Dashboard
                    </button>

                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Default: admin@booknest.com / password
                        </small>
                    </div>
                </form>
            </div>

            <div class="text-center mt-3">
                <a href="<?php echo BASE_URL; ?>public/index.php" class="text-white-50 text-decoration-none" style="font-size: 0.85rem;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>