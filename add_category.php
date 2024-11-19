<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo 'unauthorized';
    exit;
}

include 'db_connect.php';

// Get POST data
$name = $_POST['name'];
$description = $_POST['description'];

// Insert new category
$query = "INSERT INTO categories (name, description) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $name, $description);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}

$stmt->close();
?>
