<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $serial_number = $_POST['serial_number'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    echo $serial_number;    

    $query = "INSERT INTO books (title, author, description, serial_number, category_id, price, stock) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssidi", $title, $author, $description, $serial_number, $category_id, $price, $stock);
    
    if ($stmt->execute()) {
        header('Location: dashboard.php?success=1');
    } else {
        header('Location: add_book.php?error=1');
    }
} else {
    header('Location: dashboard.php');
}
?>