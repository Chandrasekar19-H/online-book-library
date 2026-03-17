<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = (int)$_POST['book_id'];
    $title = htmlspecialchars(trim($_POST['title']));
    $author = htmlspecialchars(trim($_POST['author']));
    $isbn = htmlspecialchars(trim($_POST['isbn']));
    $category = htmlspecialchars(trim($_POST['category']));
    $quantity = (int)$_POST['quantity'];
    $available_qty = (int)$_POST['available_qty'];
    $shelf_location = htmlspecialchars(trim($_POST['shelf_location']));
    $description = htmlspecialchars(trim($_POST['description']));
    $status = $_POST['status'];

    if (empty($title) || empty($author)) {
        $_SESSION['error_msg'] = "Title and Author are required.";
        header("Location: " . BASE_URL . "admin/edit_book.php?id=" . $book_id);
        exit();
    }

    if ($available_qty > $quantity) {
        $_SESSION['error_msg'] = "Available quantity cannot exceed total quantity.";
        header("Location: " . BASE_URL . "admin/edit_book.php?id=" . $book_id);
        exit();
    }

    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, isbn=?, category=?, quantity=?, available_qty=?, shelf_location=?, description=?, status=? WHERE id=?");
    $stmt->bind_param("ssssiisssi", $title, $author, $isbn, $category, $quantity, $available_qty, $shelf_location, $description, $status, $book_id);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Book '$title' updated successfully!";
        header("Location: " . BASE_URL . "admin/manage_books.php");
    } else {
        $_SESSION['error_msg'] = "Failed to update book.";
        header("Location: " . BASE_URL . "admin/edit_book.php?id=" . $book_id);
    }
    exit();
}
?>