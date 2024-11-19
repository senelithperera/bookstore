<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $serial_number = $_POST['serial'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category'];

    // Prepare an SQL update statement
    $query = "UPDATE books SET title = ?, author = ?, description = ?, serial_number = ?, price = ?, stock = ?, category_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssdiii", $title, $author, $description, $serial_number, $price, $stock, $category_id, $book_id);

    // Execute the update
    if ($stmt->execute()) {
        // Redirect to the dashboard or success page
        header("Location: dashboard.php?message=Book updated successfully");
        exit;
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to the form page if the request method is not POST
    header('Location: edit_book.php?id=' . $book_id);
    exit;
}
