<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

if (isset($_GET['id'])) {
    $borrow_id = (int)$_GET['id'];
    $fine = isset($_GET['fine']) ? (float)$_GET['fine'] : 0;
    $return_date = date('Y-m-d');

    // Get borrow record
    $stmt = $conn->prepare("SELECT * FROM borrowed_books WHERE id = ? AND status IN ('issued', 'overdue')");
    $stmt->bind_param("i", $borrow_id);
    $stmt->execute();
    $borrow = $stmt->get_result()->fetch_assoc();

    if (!$borrow) {
        $_SESSION['error_msg'] = "Borrow record not found or already returned.";
        header("Location: " . BASE_URL . "admin/return_book.php");
        exit();
    }

    $conn->begin_transaction();

    try {
        // Update borrow record
        $updateBorrow = $conn->prepare("UPDATE borrowed_books SET status = 'returned', return_date = ?, fine_amount = ? WHERE id = ?");
        $updateBorrow->bind_param("sdi", $return_date, $fine, $borrow_id);
        $updateBorrow->execute();

        // Increase available quantity
        $updateBook = $conn->prepare("UPDATE books SET available_qty = available_qty + 1 WHERE id = ?");
        $updateBook->bind_param("i", $borrow['book_id']);
        $updateBook->execute();

        // Update book status to available
        $conn->query("UPDATE books SET status = 'available' WHERE id = {$borrow['book_id']} AND available_qty > 0");

        $conn->commit();

        $_SESSION['success_msg'] = "Book returned successfully!" . ($fine > 0 ? " Fine: $" . number_format($fine, 2) : "");
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_msg'] = "Failed to process return: " . $e->getMessage();
    }

    header("Location: " . BASE_URL . "admin/return_book.php");
    exit();
}

$_SESSION['error_msg'] = "Invalid request.";
header("Location: " . BASE_URL . "admin/return_book.php");
exit();
?>