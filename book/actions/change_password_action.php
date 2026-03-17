<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_type = $_POST['user_type'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error_msg'] = "New passwords do not match.";
        $redirect = ($user_type === 'student') ? 'student/change_password.php' : 'admin/change_password.php';
        header("Location: " . BASE_URL . $redirect);
        exit();
    }

    if (strlen($new_password) < 6) {
        $_SESSION['error_msg'] = "Password must be at least 6 characters.";
        $redirect = ($user_type === 'student') ? 'student/change_password.php' : 'admin/change_password.php';
        header("Location: " . BASE_URL . $redirect);
        exit();
    }

    if ($user_type === 'student' && isset($_SESSION['student_id'])) {
        $table = 'students';
        $id = $_SESSION['student_id'];
        $redirect = 'student/change_password.php';
    } elseif ($user_type === 'admin' && isset($_SESSION['admin_id'])) {
        $table = 'librarians';
        $id = $_SESSION['admin_id'];
        $redirect = 'admin/change_password.php';
    } else {
        header("Location: " . BASE_URL . "public/index.php");
        exit();
    }

    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['error_msg'] = "Current password is incorrect.";
        header("Location: " . BASE_URL . $redirect);
        exit();
    }

    // Update password
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed, $id);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Password updated successfully!";
    } else {
        $_SESSION['error_msg'] = "Failed to update password.";
    }

    header("Location: " . BASE_URL . $redirect);
    exit();
}
?>