<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['requester_name']));
    $email = htmlspecialchars(trim($_POST['requester_email']));
    $phone = htmlspecialchars(trim($_POST['requester_phone']));
    $book_title = htmlspecialchars(trim($_POST['book_title']));
    $author = htmlspecialchars(trim($_POST['author']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (empty($name) || empty($email) || empty($book_title)) {
        $_SESSION['error_msg'] = "Please fill in all required fields.";
        header("Location: " . BASE_URL . "public/order_book.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO book_orders (requester_name, requester_email, requester_phone, book_title, author, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $phone, $book_title, $author, $message);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Your book request has been submitted successfully! We'll get back to you soon.";
    } else {
        $_SESSION['error_msg'] = "Something went wrong. Please try again.";
    }

    header("Location: " . BASE_URL . "public/order_book.php");
    exit();
}
?>