<?php
session_start();
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
include 'db_connect.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            books.id as book_id, 
            books.title,
            books.author,
            books.serial_number,
            books.price as unit_price,
            books.stock as qty_on_hand,
            categories.name,
            books.description,
            cart.id as cart_id,
            cart_items.quantity,
            cart_items.total_amount,
            cart_items.id as cart_item_id
        FROM 
            cart
        JOIN 
            cart_items ON cart.id = cart_items.cart_id
        JOIN 
            books ON cart_items.book_id = books.id
        JOIN 
            categories ON books.category_id = categories.id
        WHERE 
            cart.customer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

function getBookCoverImageUrl($isbnNo)
{
    return "https://covers.openlibrary.org/b/isbn/{$isbnNo}-L.jpg";
}
?>

<?php include 'header.php'; ?>

<body>
    <?php include 'top_navbar.php'; ?>
    <div class="dashboard-container">
        <div class="container py-4">
            <div class="row" id="cart-container">
                <?php if ($result->num_rows > 0): ?>
                    <!-- Cart Items Section -->
                    <div class="col-md-8">
                        <!-- Cart Item -->
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div id="cart-item-<?php echo $row['cart_item_id']; ?>" class="card mb-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo getBookCoverImageUrl($row['serial_number']); ?>" alt="Product" class="img-fluid rounded"
                                            style="width: 90px">
                                        <div class="ms-3 flex-grow-1">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($row['title']); ?>
                                                <span
                                                    class="badge text-bg-dark"><?php echo htmlspecialchars($row['name']); ?></span>
                                            </h5>
                                            <p class="text-muted mb-1"><?php echo htmlspecialchars($row['author']); ?></p>
                                            <p class="text-muted fs-6"><?php echo htmlspecialchars($row['description']); ?></p>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <label for="quantity" class="form-label me-2">Quantity:</label>
                                                    <input type="number"
                                                        class="form-control form-control-sm d-inline-block quantity-input"
                                                        value="<?php echo htmlspecialchars($row['quantity']); ?>" min="1"
                                                        style="width: 70px;" data-book-id="<?php echo $row['book_id']; ?>"
                                                        data-cart-id="<?php echo $row['cart_id']; ?>"
                                                        data-unit-price="<?php echo $row['unit_price']; ?>"
                                                        data-cart-item-id="<?php echo $row['cart_item_id']; ?>">
                                                </div>
                                                <span id="cart-item-total-<?php echo htmlspecialchars($row['cart_item_id']); ?>"
                                                    class="text-dark fw-bold">Rs.
                                                    <?php echo htmlspecialchars($row['total_amount']); ?></span>
                                            </div>
                                        </div>
                                        <button class="btn btn-link text-danger ms-3"
                                            data-cart-item-id="<?php echo $row['cart_item_id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Order Summary Section -->
                    <div class="col-md-4" id="order-summary">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-4">
                                    <h5 class="card-title">Order Summary</h5>
                                    <div class="mb-2 d-flex justify-content-between">
                                        <span class="text-muted" id="sub-item-total-qty">Items Total (1 Items)</span>
                                        <span id="sub-item-total">Rs. 811</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">Total:</span>
                                        <span id="cart-total-amount" class="fs-5 fw-bold text-dark">Rs. 1,061</span>
                                    </div>
                                    <small class="text-muted d-block text-end">VAT Included, where applicable</small>
                                </div>
                                <a class="btn btn-dark w-100" href="place_order.php">Proceed to checkout</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Empty Cart Message -->
                    <div class="col-12 text-center mt-5">
                        <i class="bi bi-cart-x fs-1 text-muted"></i>
                        <h4 class="text-muted mt-3">Your cart is empty</h4>
                        <p class="text-muted">Explore our collection and add some items to your cart!</p>
                        <a href="customer_dashboard.php" class="btn btn-outline-dark rounded-pill py-2">Continue
                            Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let debounceTimeout;

        $(document).on("input", ".quantity-input", function () {
            clearTimeout(debounceTimeout);
            const quantity = $(this).val();
            if (quantity == null || quantity == undefined || quantity == 0 || quantity == "") {
                return;
            }
            const cartItemId = $(this).data("cart-item-id");
            const unitPrice = $(this).data("unit-price");
            const totalAmount = quantity * unitPrice;

            debounceTimeout = setTimeout(function () {
                updateCartItemQuantity(cartItemId, quantity, totalAmount);
            }, 1000);
        });

        function calculateCartTotal() {
            let totalQuantity = 0;
            let cartTotal = 0;

            $(".quantity-input").each(function () {
                const quantity = parseInt($(this).val(), 10);
                const unitPrice = parseFloat($(this).data("unit-price"));
                totalQuantity += quantity;
                cartTotal += quantity * unitPrice;
            });

            $("#sub-item-total-qty").text("Items Total (" + totalQuantity + " items )");
            $("#sub-item-total").text("Rs. " + cartTotal);
            $("#cart-total-amount").text("Rs. " + cartTotal);
        }

        function showEmptyCartMessage() {
            const emptyCartHTML = `
                <div class="col-12 text-center mt-5">
                    <i class="bi bi-cart-x fs-1 text-muted"></i>
                    <h4 class="text-muted mt-3">Your cart is empty</h4>
                    <p class="text-muted">Explore our collection and add some items to your cart!</p>
                    <a href="customer_dashboard.php" class="btn btn-outline-dark rounded-pill py-2">Continue Shopping</a>
                </div>
            `;
            $("#cart-container").html(emptyCartHTML);
        }

        $(document).ready(function () {
            calculateCartTotal();
        });

        function updateCartItemQuantity(cartItemId, quantity, totalAmount) {
            $.ajax({
                url: 'update_cart_item.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    cart_item_id: cartItemId,
                    quantity: quantity,
                    total_amount: totalAmount
                },
                success: function (response) {
                    if (response && response.success) {
                        $("#cart-item-total-" + cartItemId).text("Rs. " + totalAmount);
                        calculateCartTotal();
                    } else {
                        alert("Error updating cart item: " + response.error);
                    }
                },
                error: function () {
                    alert("Failed to update cart item. Please try again.");
                }
            });
        }

        $(document).on("click", ".btn-link.text-danger", function () {
            const cartItemId = $(this).data("cart-item-id");

            if (confirm("Are you sure you want to delete this item from your cart?")) {
                $.ajax({
                    url: 'delete_cart_item.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        cart_item_id: cartItemId
                    },
                    success: function (response) {
                        if (response.success) {
                            $("#cart-item-" + cartItemId).remove();

                            // Check if there are any remaining cart items
                            if ($(".card.mb-4").length === 0) {
                                showEmptyCartMessage();
                                $("#order-summary").remove();
                            } else {
                                calculateCartTotal();
                            }
                        } else {
                            alert("Error deleting cart item: " + response.error);
                        }
                    },
                    error: function (error) {
                        console.log(error);
                        alert("Failed to delete cart item. Please try again.");
                    }
                });
            }
        });
    </script>
</body>
<?php include 'client_footer.php'; ?>

</html>