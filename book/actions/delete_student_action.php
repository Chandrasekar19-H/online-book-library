<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Student deleted successfully.";
    } else {
        $_SESSION['error_msg'] = "Failed to delete student.";
    }
}
header("Location: " . BASE_URL . "admin/manage_students.php");
exit();
?>