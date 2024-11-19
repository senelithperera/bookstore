<?php
session_start();
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
include 'db_connect.php';

$book_id = $_GET['id'];
$query = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

if (!$book) {
    echo "Book not found!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Book - Online Bookstore</title>
    <link rel="stylesheet" href="css/global.css">
    <script>
        function validateQuantity(input) {
            const availableStock = parseInt(input.dataset.stock);
            const quantity = parseInt(input.value);
            const errorMessage = document.getElementById('error-message');
            const totalPriceElement = document.getElementById('total-price');
            const orderButton = document.getElementById('order-button');

            // Clear previous error messages
            errorMessage.textContent = '';

            // Validate quantity
            if (quantity > availableStock) {
                errorMessage.textContent = 'Error: Quantity exceeds available stock.';
                orderButton.disabled = true;
            } else {
                // Update total price
                const pricePerUnit = parseFloat(input.dataset.price);
                totalPriceElement.textContent = 'Total Price: $' + (pricePerUnit * quantity).toFixed(2);
                orderButton.disabled = quantity <= 0; // Disable button if quantity is 0 or less
            }
        }
    </script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2></h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Books</a></li>
                    <li><a href="#">Orders</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2>Order Book</h2>
            <div class="book-details">
                <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                <p>Author: <?php echo htmlspecialchars($book['author']); ?></p>
                <p>Category: <?php echo htmlspecialchars($book['category_id']); ?></p>
                <p>Price: $<?php echo number_format($book['price'], 2); ?></p>
                <p>Available Stock: <?php echo htmlspecialchars($book['stock']); ?></p>
                
                <form action="process_order.php" method="POST" class="order-form">
                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" min="1" max="<?php echo htmlspecialchars($book['stock']); ?>" data-stock="<?php echo htmlspecialchars($book['stock']); ?>" data-price="<?php echo htmlspecialchars($book['price']); ?>" required oninput="validateQuantity(this)">
                        <div id="error-message" style="color: red;"></div>
                    </div>
                    
                    <div id="total-price" style="margin: 10px 0;">Total Price: $0.00</div>
                    
                    <button type="submit" id="order-button" disabled>Order</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
