<?php
$pageTitle = "Student Login";
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

                <h2 class="form-title">Welcome Back!</h2>
                <p class="form-subtitle">Login to your student dashboard</p>

                <form action="<?php echo BASE_URL; ?>actions/student_login_action.php" method="POST" class="needs-validation" novalidate>
                    
                    <!-- Email -->
                    <div class="form-floating-custom">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                    </div>

                    <!-- Password -->
                    <div class="form-floating-custom">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <button type="button" class="password-toggle" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label" for="rememberMe" style="font-size: 0.85rem;">Remember me</label>
                        </div>
                        <a href="#" class="text-decoration-none" style="font-size: 0.85rem; color: var(--primary);">Forgot password?</a>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary-custom w-100 py-3 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>

                    <div class="text-center">
                        <span class="text-muted" style="font-size: 0.9rem;">Don't have an account?</span>
                        <a href="<?php echo BASE_URL; ?>public/student_register.php" class="text-decoration-none fw-bold" style="color: var(--primary);">
                            Register here
                        </a>
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