<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

include 'db_connect.php';

$id = $_GET['id'];

// Delete category
$query = "DELETE FROM categories WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header('Location: categories.php?message=Category deleted successfully');
} else {
    echo "Error deleting category: " . $conn->error;
}

$stmt->close();
$conn->close();
