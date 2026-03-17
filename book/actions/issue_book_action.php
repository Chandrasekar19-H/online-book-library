<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $book_id = (int)$_POST['book_id'];
    $issue_date = $_POST['issue_date'];
    $due_date = $_POST['due_date'];
    $issued_by = $_SESSION['admin_id'];

    // Validate
    if (empty($student_id) || empty($book_id) || empty($issue_date) || empty($due_date)) {
        $_SESSION['error_msg'] = "All fields are required.";
        header("Location: " . BASE_URL . "admin/issue_book.php");
        exit();
    }

    if (strtotime($due_date) <= strtotime($issue_date)) {
        $_SESSION['error_msg'] = "Due date must be after issue date.";
        header("Location: " . BASE_URL . "admin/issue_book.php");
        exit();
    }

    // Check book availability
    $bookCheck = $conn->prepare("SELECT available_qty, title FROM books WHERE id = ?");
    $bookCheck->bind_param("i", $book_id);
    $bookCheck->execute();
    $bookData = $bookCheck->get_result()->fetch_assoc();

    if (!$bookData || $bookData['available_qty'] <= 0) {
        $_SESSION['error_msg'] = "This book is not available for issuing.";
        header("Location: " . BASE_URL . "admin/issue_book.php");
        exit();
    }

    // Check if student already has this book issued
    $dupCheck = $conn->prepare("SELECT id FROM borrowed_books WHERE student_id = ? AND book_id = ? AND status = 'issued'");
    $dupCheck->bind_param("ii", $student_id, $book_id);
    $dupCheck->execute();
    if ($dupCheck->get_result()->num_rows > 0) {
        $_SESSION['error_msg'] = "This student already has this book issued.";
        header("Location: " . BASE_URL . "admin/issue_book.php");
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert borrow record
        $stmt = $conn->prepare("INSERT INTO borrowed_books (student_id, book_id, issue_date, due_date, status, issued_by) VALUES (?, ?, ?, ?, 'issued', ?)");
        $stmt->bind_param("iissi", $student_id, $book_id, $issue_date, $due_date, $issued_by);
        $stmt->execute();

        // Decrease available quantity
        $updateBook = $conn->prepare("UPDATE books SET available_qty = available_qty - 1 WHERE id = ?");
        $updateBook->bind_param("i", $book_id);
        $updateBook->execute();

        // Update book status if no more available
        $conn->query("UPDATE books SET status = 'unavailable' WHERE id = $book_id AND available_qty <= 0");

        $conn->commit();

        $_SESSION['success_msg'] = "Book '{$bookData['title']}' issued successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_msg'] = "Failed to issue book: " . $e->getMessage();
    }

    header("Location: " . BASE_URL . "admin/issue_book.php");
    exit();
}
?>