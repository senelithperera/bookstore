<?php
session_start();
include 'db_connect.php';

// Get the raw POST data sent via AJAX
$data = json_decode(file_get_contents("php://input"), true);

$user_id = $_SESSION['user_id']; // Get the logged-in user ID
$full_name = $data['full_name'];
$phone_number = $data['phone_number'];
$province = $data['province'];
$district = $data['district'];
$city = $data['city'];
$address = $data['address'];
$total_amount = $data['total_amount']; // The total amount passed from the frontend
$single_book_purchase = $data['single_book_purchase']; // Check if it's a single book purchase

// Start a database transaction
$conn->begin_transaction();

try {
    // Step 1: Insert into `orders` table with the total amount passed from the frontend
    $query = "INSERT INTO orders (customer_id, total) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("id", $user_id, $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get the order ID of the inserted order

    // Step 2: Handle order items
    if ($single_book_purchase) {
        // Fetch the book ID and quantity from $_GET['book_id']
        $book_id = $data['book_id'];
        $quantity = 1; // Default to 1 quantity if it's a single book purchase

        // Insert into `order_items` (no need to include price column)
        $query = "INSERT INTO order_items (order_id, book_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $order_id, $book_id, $quantity);
        $stmt->execute();

        // Update the stock for the book in `books` table
        $query = "UPDATE books SET stock = stock - ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $quantity, $book_id);
        $stmt->execute();
    } else {
        // Step 3: Fetch all cart items for the logged-in user using a subquery
        $query = "SELECT book_id, quantity, total_amount 
                  FROM cart_items 
                  WHERE cart_id = (SELECT id FROM cart WHERE customer_id = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Insert each cart item into `order_items` and update stock
        foreach ($cart_items as $item) {
            $book_id = $item['book_id'];
            $quantity = $item['quantity'];

            // Insert into `order_items` (no need to include price column)
            $query = "INSERT INTO order_items (order_id, book_id, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $order_id, $book_id, $quantity);
            $stmt->execute();

            // Update the stock for the book in `books` table
            $query = "UPDATE books SET stock = stock - ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $quantity, $book_id);
            $stmt->execute();
        }
    }

    // Step 4: Insert delivery information into `delivery_info` table
    $query = "INSERT INTO delivery_info (customer_id, order_id, full_name, phone_number, province, district, city, address) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssss", $user_id, $order_id, $full_name, $phone_number, $province, $district, $city, $address);
    $stmt->execute();

    // Step 5: Delete cart and cart_items if it's a full cart purchase (not single book)
    if (!$single_book_purchase) {
        // Delete from `cart_items`
        $query = "DELETE FROM cart_items WHERE cart_id = (SELECT id FROM cart WHERE customer_id = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Delete from `cart`
        $query = "DELETE FROM cart WHERE customer_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }

    // Commit the transaction
    $conn->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    // Rollback transaction if any error occurs
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
