<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type']; // 'student', 'book', or 'borrow'
    $id = (int)$_POST['id'];
    $status = htmlspecialchars(trim($_POST['status']));

    switch ($type) {
        case 'student':
            $allowed = ['active', 'suspended', 'inactive'];
            if (!in_array($status, $allowed)) {
                $_SESSION['error_msg'] = "Invalid status.";
                break;
            }
            $stmt = $conn->prepare("UPDATE students SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) {
                $_SESSION['success_msg'] = "Student status updated to '$status'.";
            } else {
                $_SESSION['error_msg'] = "Failed to update student status.";
            }
            header("Location: " . BASE_URL . "admin/manage_students.php");
            exit();

        case 'book':
            $allowed = ['available', 'unavailable'];
            if (!in_array($status, $allowed)) {
                $_SESSION['error_msg'] = "Invalid status.";
                break;
            }
            $stmt = $conn->prepare("UPDATE books SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) {
                $_SESSION['success_msg'] = "Book status updated to '$status'.";
            } else {
                $_SESSION['error_msg'] = "Failed to update book status.";
            }
            header("Location: " . BASE_URL . "admin/manage_books.php");
            exit();

        case 'borrow':
            $allowed = ['issued', 'returned', 'overdue'];
            if (!in_array($status, $allowed)) {
                $_SESSION['error_msg'] = "Invalid status.";
                break;
            }
            $stmt = $conn->prepare("UPDATE borrowed_books SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);

            if ($status === 'returned') {
                $return_date = date('Y-m-d');
                $stmt = $conn->prepare("UPDATE borrowed_books SET status = ?, return_date = ? WHERE id = ?");
                $stmt->bind_param("ssi", $status, $return_date, $id);
            }

            if ($stmt->execute()) {
                // If returning, update book quantity
                if ($status === 'returned') {
                    $borrow = $conn->query("SELECT book_id FROM borrowed_books WHERE id = $id")->fetch_assoc();
                    if ($borrow) {
                        $conn->query("UPDATE books SET available_qty = available_qty + 1 WHERE id = {$borrow['book_id']}");
                        $conn->query("UPDATE books SET status = 'available' WHERE id = {$borrow['book_id']} AND available_qty > 0");
                    }
                }
                $_SESSION['success_msg'] = "Borrow status updated to '$status'.";
            } else {
                $_SESSION['error_msg'] = "Failed to update borrow status.";
            }
            header("Location: " . BASE_URL . "admin/return_book.php");
            exit();

        default:
            $_SESSION['error_msg'] = "Invalid request type.";
    }

    header("Location: " . BASE_URL . "admin/dashboard.php");
    exit();
}
?>