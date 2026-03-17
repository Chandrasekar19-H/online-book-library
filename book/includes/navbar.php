<!-- Public Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand navbar-brand-custom" href="<?php echo BASE_URL; ?>public/index.php">
            <i class="fas fa-book-open-reader"></i> BookNest
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                       href="<?php echo BASE_URL; ?>public/index.php">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'order_book.php' ? 'active' : ''; ?>" 
                       href="<?php echo BASE_URL; ?>public/order_book.php">
                        <i class="fas fa-cart-shopping me-1"></i> Order Book
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?php echo basename($_SERVER['PHP_SELF']) == 'student_login.php' ? 'active' : ''; ?>" 
                       href="<?php echo BASE_URL; ?>public/student_login.php">
                        <i class="fas fa-user-graduate me-1"></i> Student Login
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <a class="btn btn-primary-custom btn-sm px-3 py-2" 
                       href="<?php echo BASE_URL; ?>public/librarian_login.php">
                        <i class="fas fa-user-shield me-1"></i> Librarian
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>