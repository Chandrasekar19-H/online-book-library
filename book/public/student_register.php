<?php
$pageTitle = "Student Registration";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-page">
    <div class="container position-relative" style="z-index: 2;">
        <div class="form-container animate-slide-in" style="max-width: 550px;">
            <div class="form-card">
                <!-- Logo -->
                <div class="text-center mb-3">
                    <a href="<?php echo BASE_URL; ?>public/index.php" class="navbar-brand-custom text-decoration-none">
                        <i class="fas fa-book-open-reader"></i> BookNest
                    </a>
                </div>
                
                <h2 class="form-title">Create Account</h2>
                <p class="form-subtitle">Join BookNest and start exploring our library</p>

                <form action="<?php echo BASE_URL; ?>actions/student_register_action.php" method="POST" class="needs-validation" novalidate>
                    
                    <!-- Full Name -->
                    <div class="form-floating-custom">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="full_name" placeholder="Full Name" required>
                    </div>

                    <!-- Email -->
                    <div class="form-floating-custom">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                    </div>

                    <!-- Student ID -->
                    <div class="form-floating-custom">
                        <span class="input-icon"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control" name="student_id" placeholder="Student ID (e.g., STU-2024-001)" required>
                    </div>

                    <!-- Phone -->
                    <div class="form-floating-custom">
                        <span class="input-icon"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control" name="phone" placeholder="Phone Number">
                    </div>

                    <!-- Department -->
                    <div class="mb-3">
                        <select class="form-select form-select-custom" name="department" required>
                            <option value="" disabled selected>Select Department</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Business">Business Administration</option>
                            <option value="Arts">Arts & Humanities</option>
                            <option value="Science">Natural Sciences</option>
                            <option value="Medicine">Medicine</option>
                            <option value="Law">Law</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Password -->
                    <div class="form-floating-custom">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" 
                               minlength="6" required id="regPassword">
                        <button type="button" class="password-toggle" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-floating-custom">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" 
                               minlength="6" required id="regConfirmPassword">
                        <button type="button" class="password-toggle" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Terms -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms" style="font-size: 0.85rem;">
                            I agree to the <a href="#" class="text-decoration-none" style="color: var(--primary);">Terms & Conditions</a>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary-custom w-100 py-3 mb-3">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </button>

                    <div class="text-center">
                        <span class="text-muted" style="font-size: 0.9rem;">Already have an account?</span>
                        <a href="<?php echo BASE_URL; ?>public/student_login.php" class="text-decoration-none fw-bold" style="color: var(--primary);">
                            Login here
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