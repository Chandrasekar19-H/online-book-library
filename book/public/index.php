<?php
$pageTitle = "Home";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Get counts
$bookCount = $conn->query("SELECT COUNT(*) as count FROM books")->fetch_assoc()['count'];
$studentCount = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$borrowedCount = $conn->query("SELECT COUNT(*) as count FROM borrowed_books WHERE status='issued'")->fetch_assoc()['count'];
?>

<!-- Hero Section -->
<section class="hero-section" style="padding-top: 80px;">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 animate-fade-up">
                <span class="badge bg-primary bg-opacity-25 text-white px-3 py-2 mb-3 rounded-pill">
                    <i class="fas fa-sparkles me-1"></i> Modern Library System
                </span>
                <h1 class="hero-title mb-4">
                    Your Digital <br>
                    <span class="hero-highlight">Library Hub</span> <br>
                    Starts Here
                </h1>
                <p class="hero-subtitle mb-4">
                    BookNest makes library management effortless. Browse books, track borrowings, 
                    and manage everything from one beautiful dashboard.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?php echo BASE_URL; ?>public/order_book.php" class="btn btn-primary-custom">
                        <i class="fas fa-book me-2"></i> Order a Book
                    </a>
                    <a href="<?php echo BASE_URL; ?>public/student_register.php" class="btn btn-secondary-custom" style="border-color: white; color: white;">
                        <i class="fas fa-user-plus me-2"></i> Register Now
                    </a>
                </div>

                <!-- Stats Row -->
                <div class="row mt-5 g-3">
                    <div class="col-4">
                        <div class="card-glass p-3 text-center">
                            <h3 class="text-white fw-bold mb-0 counter" data-target="<?php echo $bookCount; ?>">0</h3>
                            <small class="text-white-50">Total Books</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card-glass p-3 text-center">
                            <h3 class="text-white fw-bold mb-0 counter" data-target="<?php echo $studentCount; ?>">0</h3>
                            <small class="text-white-50">Students</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card-glass p-3 text-center">
                            <h3 class="text-white fw-bold mb-0 counter" data-target="<?php echo $borrowedCount; ?>">0</h3>
                            <small class="text-white-50">Active Borrows</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center animate-fade-right delay-2">
                <div style="font-size: 15rem; opacity: 0.15; color: white; animation: float 4s ease-in-out infinite;">
                    <i class="fas fa-book-open-reader"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section-padding bg-white" id="features">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title animate-on-scroll">Why Choose <span class="text-gradient">BookNest</span>?</h2>
            <p class="section-subtitle animate-on-scroll">
                Everything you need to run a modern library, all in one place.
            </p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 animate-on-scroll">
                <div class="feature-card h-100">
                    <div class="feature-icon purple">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Easy Book Discovery</h5>
                    <p class="text-muted mb-0">Search and browse through our entire catalog. Find exactly what you need in seconds.</p>
                </div>
            </div>
            <div class="col-md-4 animate-on-scroll">
                <div class="feature-card h-100">
                    <div class="feature-icon pink">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Track Borrowings</h5>
                    <p class="text-muted mb-0">Monitor your borrowed books, due dates, and return history all from your dashboard.</p>
                </div>
            </div>
            <div class="col-md-4 animate-on-scroll">
                <div class="feature-card h-100">
                    <div class="feature-icon green">
                        <i class="fas fa-cart-shopping"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Online Ordering</h5>
                    <p class="text-muted mb-0">Request books online without visiting the library. We'll notify you when it's ready.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section-padding" style="background: var(--gradient-3);">
    <div class="container text-center">
        <h2 class="text-white mb-3 animate-on-scroll" style="font-family: 'Playfair Display', serif; font-size: 2.5rem;">
            Ready to Get Started?
        </h2>
        <p class="text-white-50 mb-4 animate-on-scroll">
            Join BookNest today and experience the future of library management.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap animate-on-scroll">
            <a href="<?php echo BASE_URL; ?>public/student_register.php" class="btn btn-primary-custom btn-lg">
                <i class="fas fa-rocket me-2"></i> Create Account
            </a>
            <a href="<?php echo BASE_URL; ?>public/order_book.php" class="btn btn-accent-custom btn-lg">
                <i class="fas fa-book me-2"></i> Order a Book
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>