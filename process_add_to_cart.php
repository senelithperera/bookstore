<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

include 'db_connect.php';
$user_id = $_SESSION['user_id'];
$book_id = $_GET['book_id'];
$query = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

try {
    // Start a transaction
    $conn->begin_transaction();

    // Step 1: Check if the user already has an active cart
    $cart_query = "SELECT id FROM cart WHERE customer_id = ? LIMIT 1";
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Active cart exists, get the cart_id
        $cart = $result->fetch_assoc();
        $cart_id = $cart['id'];
    } else {
        // No active cart, create a new one
        $create_cart_query = "INSERT INTO cart (customer_id, created_at) VALUES (?, NOW())";
        $stmt = $conn->prepare($create_cart_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Get the new cart ID
        $cart_id = $conn->insert_id;
    }
    $qty = 1;
    $cart_item_query = "INSERT INTO cart_items (cart_id, book_id, quantity, total_amount) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($cart_item_query);
    $stmt->bind_param("iiid", $cart_id, $book_id, $qty, $book["price"]);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Book added to cart successfully']);
} catch (Exception $e) {
    // Rollback transaction in case of error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to add book to cart: ' . $e->getMessage()]);
}
?>
