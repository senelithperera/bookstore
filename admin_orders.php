<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
include 'db_connect.php';

$query = "
    SELECT 
        orders.id AS order_id,
        orders.order_date,
        orders.total,
        users.id AS user_id,
        users.username,
        books.title AS book_title,
        books.author as book_author,
        order_items.quantity
    FROM 
        orders
    JOIN 
        users ON orders.customer_id = users.id
    JOIN 
        order_items ON orders.id = order_items.order_id
    JOIN 
        books ON order_items.book_id = books.id
    ORDER BY 
        orders.order_date DESC
";

$result = $conn->query($query);

$orders = $result->fetch_all(MYSQLI_ASSOC);

$grouped_orders = [];
foreach ($orders as $order) {
    $grouped_orders[$order['order_id']][] = $order;
}




?>
<?php foreach ($orders as $index => $orderData): ?>
    <? echo $orderData['total']; ?>
<?php endforeach; ?>
<!DOCTYPE html>
<html lang="en">

<?php include 'header.php'; ?>

<body class="bg-light">
    <div class="dashboard-container">
        <!-- Sidebar -->

        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4" style="margin-left: 250px;">
            <div class="container-fluid">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0 fw-bold">Order Details</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="text-secondary fw-semibold">Order Id</th>
                                        <th scope="col" class="text-secondary fw-semibold">Customer Id</th>
                                        <th scope="col" class="text-secondary fw-semibold">Customer Name</th>
                                        <th scope="col" class="text-secondary fw-semibold">Order Details</th>
                                        <th scope="col" class="text-secondary fw-semibold">Order Date</th>
                                        <th scope="col" class="text-secondary fw-semibold">Order Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($grouped_orders)): ?>
                                        <?php foreach ($grouped_orders as $order_id => $order_items): ?>
                                            <tr id="row-<?php echo $index; ?>" class="table-light">
                                                <td class="fw-medium"><?php echo $order_id; ?></td>
                                                <td><?php echo $order_items[0]['user_id'] ?></td>
                                                <td><?php echo htmlspecialchars($order_items[0]['username']); ?></td>
                                                <td>
                                                    <?php foreach ($order_items as $item): ?>
                                                        <?php echo $item['book_title'] . " by " . $item['book_author'] . " (x" . $item['quantity'] . ")<br>"; ?>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td><?php echo $order_items[0]['order_date']; ?></td>
                                                <td>Rs. <?php echo $order_items[0]['total']; ?></td>
                                            </tr>
                                        <?php endforeach;
                                    else: ?>
                                        <!-- Message row if no orders are found -->
                                        <tr>
                                            <td colspan="6" class="text-center">No orders found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>