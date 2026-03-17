    <!-- Footer -->
    <footer class="footer" style="padding: 0;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
                <!-- Left Side - Version -->
                <div>
                    <small class="text-white-50">
                        <i class="fas fa-code-branch me-1"></i> Version 1.0.0
                    </small>
                </div>

                <!-- Right Side - Developer Credit -->
                <div>
                    <small class="text-white-50">
                        Developed with <i class="fas fa-heart text-danger"></i> by 
                        <a href="#" class="text-white text-decoration-none fw-semibold">Chandru</a>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

    <?php if (isset($_SESSION['success_msg'])): ?>
    <script>
        showAlert('success', 'Success!', '<?php echo addslashes($_SESSION['success_msg']); ?>');
    </script>
    <?php unset($_SESSION['success_msg']); endif; ?>

    <?php if (isset($_SESSION['error_msg'])): ?>
    <script>
        showAlert('error', 'Oops!', '<?php echo addslashes($_SESSION['error_msg']); ?>');
    </script>
    <?php unset($_SESSION['error_msg']); endif; ?>

</body>
</html>