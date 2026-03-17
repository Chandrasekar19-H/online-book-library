<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error_msg'] = "Please fill in all fields.";
        header("Location: " . BASE_URL . "public/student_login.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();

        if ($student['status'] !== 'active') {
            $_SESSION['error_msg'] = "Your account is suspended. Contact the librarian.";
            header("Location: " . BASE_URL . "public/student_login.php");
            exit();
        }

        if (password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['full_name'];
            $_SESSION['student_email'] = $student['email'];
            $_SESSION['success_msg'] = "Welcome back, " . $student['full_name'] . "!";
            header("Location: " . BASE_URL . "student/dashboard.php");
        } else {
            $_SESSION['error_msg'] = "Invalid email or password.";
            header("Location: " . BASE_URL . "public/student_login.php");
        }
    } else {
        $_SESSION['error_msg'] = "Invalid email or password.";
        header("Location: " . BASE_URL . "public/student_login.php");
    }
    exit();
}
?>