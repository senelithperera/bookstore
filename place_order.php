<?php
session_start();
if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    header('Location: customer_dashboard.php');
    exit;
}
$user_id = $_SESSION['user_id'];

include 'db_connect.php';

// Your existing PHP logic remains the same
$books = [];
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $query = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $sql = "SELECT 
        books.id AS book_id, 
        books.title, 
        books.serial_number,
        books.price AS unit_price, 
        cart_items.quantity,
        (books.price * cart_items.quantity) AS total_amount
    FROM 
        cart_items
    JOIN 
        books ON cart_items.book_id = books.id
    WHERE 
        cart_items.cart_id = (
            SELECT id FROM cart WHERE customer_id = ?
        )";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC);
}

$total_items = 0;
$total_amount = 0;
foreach ($books as $book) {
    $total_items += isset($_GET['book_id']) ? 1 : $book['quantity'];
    $total_amount += isset($_GET['book_id']) ? $_GET['price'] : $book['total_amount'];
}
function getBookCoverImageUrl($isbnNo)
{
    return "https://covers.openlibrary.org/b/isbn/{$isbnNo}-L.jpg";
}
?>
<?php include 'header.php'; ?>

<body class="bg-light">
    <?php include 'top_navbar.php'; ?>

    <div class="container py-5">
        <div class="row g-4">
            <!-- Delivery Information Card -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h5 class="mb-4 fw-bold">Delivery Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="full_name" placeholder="Full Name">
                                    <label for="full_name">Full Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="province" placeholder="Province">
                                    <label for="province">Province</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control" id="phone_number" placeholder="Phone Number">
                                    <label for="phone_number">Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="district" placeholder="District">
                                    <label for="district">District</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="city" placeholder="City">
                                    <label for="city">City</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="address" placeholder="Address">
                                    <label for="address">Address</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Books Preview Section -->
                <div class="card shadow-sm border-0 rounded-3 mt-4">
                    <div class="card-body p-4">
                        <h5 class="mb-4 fw-bold">Order Items</h5>
                        <div class="row row-cols-2 row-cols-md-4 g-3">
                            <?php foreach ($books as $book): ?>
                                <div class="col">
                                    <div class="position-relative">
                                        <span
                                            class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger">
                                            <?php echo htmlspecialchars(isset($_GET['book_id']) ? 1 : $book['quantity']); ?>
                                        </span>
                                        <img src="<?php echo getBookCoverImageUrl($book['serial_number']); ?>"
                                            class="img-fluid rounded-3 mb-2"
                                            alt="<?php echo htmlspecialchars($book['title']); ?>">
                                        <h6 class="text-truncate mb-0 small"><?php echo htmlspecialchars($book['title']); ?>
                                        </h6>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Items Total (<?php echo $total_items; ?> Items)</span>
                            <span>Rs. <?php echo number_format($total_amount, 2); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Total Amount:</span>
                            <span class="fs-5 fw-bold">Rs. <?php echo number_format($total_amount, 2); ?></span>
                        </div>
                        <small class="text-muted d-block text-end mb-4">VAT Included, where applicable</small>

                        <div class="alert alert-danger align-items-center d-none" id="error-message" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Please fill in all delivery fields.
                        </div>

                        <button class="btn btn-dark w-100 py-3 fw-bold" id="proceed-to-pay-btn">
                            Proceed to Pay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Your existing JavaScript remains the same
        function checkFields() {
            let fields = ['full_name', 'province', 'phone_number', 'district', 'city', 'address'];
            let allFilled = fields.every(id => document.getElementById(id).value.trim() !== '');

            const errorMessage = document.getElementById('error-message');
            if (!allFilled) {
                errorMessage.classList.remove('d-none');
            } else {
                errorMessage.classList.add('d-none');
            }
        }

        document.querySelectorAll('#full_name, #province, #phone_number, #district, #city, #address').forEach(input => {
            input.addEventListener('input', checkFields);
        });

        document.getElementById('proceed-to-pay-btn').addEventListener('click', function (e) {
            e.preventDefault();
            let fields = ['full_name', 'province', 'phone_number', 'district', 'city', 'address'];
            let allFilled = fields.every(id => document.getElementById(id).value.trim() !== '');

            if (!allFilled) {
                document.getElementById('error-message').classList.remove('d-none');
                return;
            } else {
                document.getElementById('error-message').classList.add('d-none');
                placeOrder();
            }
        });

        function placeOrder() {
            const deliveryData = {
                full_name: document.getElementById('full_name').value,
                province: document.getElementById('province').value,
                phone_number: document.getElementById('phone_number').value,
                district: document.getElementById('district').value,
                city: document.getElementById('city').value,
                address: document.getElementById('address').value,
                total_amount: <?php echo isset($_GET['book_id']) ? $_GET['price'] : $total_amount; ?>,
                single_book_purchase: <?php echo isset($_GET['book_id']) ? 'true' : 'false'; ?>
            };

            <?php if (isset($_GET['book_id'])): ?>
                deliveryData.book_id = <?php echo $_GET['book_id']; ?>;
            <?php endif; ?>

            fetch('process_place_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(deliveryData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order placed successfully!');
                        window.location.href = 'customer_dashboard.php';
                    } else {
                        alert('Order failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your order.');
                });
        }
    </script>
</body>
<?php include 'client_footer.php'; ?>

</html>


<!-- src="<?php echo getBookCoverImageUrl($book['serial_number']); ?>" -->