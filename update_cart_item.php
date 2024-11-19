<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

// Get POST data from AJAX request
// $book_id = $_POST['book_id'];
// $cart_id = $_POST['cart_id'];
$cart_item_id = $_POST['cart_item_id'];
$quantity = $_POST['quantity'];
$total_amount = $_POST['total_amount']; // Received from frontend

// Validate input (you can add more validation here)
if (!is_numeric($quantity) || $quantity < 1 || !is_numeric($total_amount) || $total_amount < 1) {
    echo json_encode(["error" => "Invalid quantity or total amount"]);
    exit;
}

// Update the quantity and total amount in the cart_items table
$sql = "UPDATE cart_items SET quantity = ?, total_amount = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $quantity, $total_amount, $cart_item_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(["success" => "Cart item updated successfully"]);
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Failed to update cart item"]);
}

$stmt->close();
