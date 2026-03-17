<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error_msg'] = "Please fill in all fields.";
        header("Location: " . BASE_URL . "public/librarian_login.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM librarians WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['success_msg'] = "Welcome, " . $admin['full_name'] . "!";
            header("Location: " . BASE_URL . "admin/dashboard.php");
        } else {
            $_SESSION['error_msg'] = "Invalid email or password.";
            header("Location: " . BASE_URL . "public/librarian_login.php");
        }
    } else {
        $_SESSION['error_msg'] = "Invalid email or password.";
        header("Location: " . BASE_URL . "public/librarian_login.php");
    }
    exit();
}
?>