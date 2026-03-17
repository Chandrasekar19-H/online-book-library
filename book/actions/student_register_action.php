<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $student_id = sanitize($_POST['student_id']);
    $phone = sanitize($_POST['phone']);
    $department = sanitize($_POST['department']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($full_name) || empty($email) || empty($student_id) || empty($password)) {
        $_SESSION['error_msg'] = "All required fields must be filled.";
        header("Location: " . BASE_URL . "public/student_register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_msg'] = "Passwords do not match.";
        header("Location: " . BASE_URL . "public/student_register.php");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['error_msg'] = "Password must be at least 6 characters.";
        header("Location: " . BASE_URL . "public/student_register.php");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM students WHERE email = ? OR student_id = ?");
    $stmt->bind_param("ss", $email, $student_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_msg'] = "Email or Student ID already registered.";
        header("Location: " . BASE_URL . "public/student_register.php");
        exit();
    }

    // Hash password and insert
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO students (full_name, email, student_id, phone, department, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $full_name, $email, $student_id, $phone, $department, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Registration successful! You can now login.";
        header("Location: " . BASE_URL . "public/student_login.php");
    } else {
        $_SESSION['error_msg'] = "Registration failed. Please try again.";
        header("Location: " . BASE_URL . "public/student_register.php");
    }
    exit();
}

function sanitize($data) {
    return htmlspecialchars(trim(stripslashes($data)));
}
?>