<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo 'unauthorized';
    exit;
}

include 'db_connect.php';

// Get POST data
$id = $_POST['id'];
$name = $_POST['name'];
$description = $_POST['description'];

// Update category
$query = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssi", $name, $description, $id);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}

$stmt->close();
?>
