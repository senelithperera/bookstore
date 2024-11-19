<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $quantity = intval($_POST['quantity']);
    $customer_id = $_SESSION['user_id']; // Assuming user_id is the customer_id
    $status = 'pending'; // Default status for new orders

    // Fetch book details to check stock
    $query = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if (!$book) {
        // Book not found
        echo "Book not found!";
        exit;
    }

    $available_stock = intval($book['stock']);
    if ($quantity > $available_stock) {
        // Insufficient stock
        echo "Error: Insufficient stock available. You requested $quantity but only $available_stock are available.";
        exit;
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Calculate total price for the order
        $total_price = number_format($quantity * $book['price'], 2, '.', '');

        // Insert order into orders table
        $order_query = "INSERT INTO orders (customer_id, order_date, status, total) VALUES (?, NOW(), ?, ?)";
        $order_stmt = $conn->prepare($order_query);
        $order_stmt->bind_param("isd", $customer_id, $status, $total_price);

        if (!$order_stmt->execute()) {
            throw new Exception("Error inserting order.");
        }

        // Get the last inserted order ID
        $order_id = $conn->insert_id;

        // Insert item details into order_items table
        $item_price = $book['price']; // Price of a single book
        $order_item_query = "INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)";
        $order_item_stmt = $conn->prepare($order_item_query);
        $order_item_stmt->bind_param("iiid", $order_id, $book_id, $quantity, $item_price);

        if (!$order_item_stmt->execute()) {
            throw new Exception("Error inserting order item.");
        }

        // Update the stock of the book
        $new_stock = $available_stock - $quantity;
        $update_query = "UPDATE books SET stock = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ii", $new_stock, $book_id);

        if (!$update_stmt->execute()) {
            throw new Exception("Error updating book stock.");
        }

        // Commit the transaction
        $conn->commit();
        header('Location: customer_dashboard.php'); // Change this to the actual customer dashboard page
        exit; // Always call exit after header redirection
    } catch (Exception $e) {
        // An error occurred, roll back the transaction
        $conn->rollback();
        echo "Failed to place order: " . $e->getMessage();
    }
} else {
    // Invalid request method
    header('Location: index.php');
    exit;
}
?>