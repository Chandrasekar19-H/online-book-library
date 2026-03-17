<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $author = htmlspecialchars(trim($_POST['author']));
    $isbn = htmlspecialchars(trim($_POST['isbn']));
    $category = htmlspecialchars(trim($_POST['category']));
    $quantity = (int)$_POST['quantity'];
    $shelf_location = htmlspecialchars(trim($_POST['shelf_location']));
    $description = htmlspecialchars(trim($_POST['description']));
    $status = $_POST['status'];

    if (empty($title) || empty($author)) {
        $_SESSION['error_msg'] = "Title and Author are required.";
        header("Location: " . BASE_URL . "admin/add_book.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, category, quantity, available_qty, shelf_location, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiisss", $title, $author, $isbn, $category, $quantity, $quantity, $shelf_location, $description, $status);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Book '$title' added successfully!";
        header("Location: " . BASE_URL . "admin/manage_books.php");
    } else {
        $_SESSION['error_msg'] = "Failed to add book. " . $conn->error;
        header("Location: " . BASE_URL . "admin/add_book.php");
    }
    exit();
}
?>