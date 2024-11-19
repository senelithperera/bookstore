<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Get the customer ID from the session
$customer_id = $_SESSION['user_id'];

// Fetch the orders for the logged-in customer
$query = "SELECT o.id AS order_id, o.total AS order_total, o.order_date AS order_date, 
                 oi.book_id, oi.quantity, b.title AS book_title, b.author AS book_author
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          JOIN books b ON oi.book_id = b.id
          WHERE o.customer_id = ?
          ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id); // Bind the user ID
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$grouped_orders = [];
foreach ($orders as $order) {
    $grouped_orders[$order['order_id']][] = $order;
}

?>
<?php include 'header.php'; ?>

<body>
    <?php include 'top_navbar.php'; ?>
    <div class="container my-5">
        <h2>Your Orders</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Book/s</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($grouped_orders)): ?>
                    <?php foreach ($grouped_orders as $order_id => $order_items): ?>
                        <!-- Each order row -->
                        <tr>
                            <td><?php echo $order_id; ?></td>
                            <td><?php echo $order_items[0]['order_date']; ?></td>
                            <td>
                                <?php foreach ($order_items as $item): ?>
                                    <?php echo $item['book_title'] . " by " . $item['book_author'] . " (x" . $item['quantity'] . ")<br>"; ?>
                                <?php endforeach; ?>
                            </td>
                            <td>Rs. <?php echo $order_items[0]['order_total']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
<?php include 'client_footer.php'; ?>

</html>