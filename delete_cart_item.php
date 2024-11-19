<?php
session_start();
// Ensure the user is logged in
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

include 'db_connect.php';

if (isset($_POST['cart_item_id'])) {
    $cartItemId = $_POST['cart_item_id'];

    // Query to delete the cart item
    $sql = "DELETE FROM cart_items WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $cartItemId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete item.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare query.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No cart item ID provided.']);
}
