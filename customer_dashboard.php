<?php
session_start();
// if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
//     header('Location: index.php');
//     exit;
// }

$isLoggedIn = isset($_SESSION['customer_logged_in']);

include 'db_connect.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ?";
$search_param = "%$search%";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();


$query = "SELECT * FROM categories";
$resultCat = $conn->query($query);
$all_categories = $resultCat->fetch_all(MYSQLI_ASSOC);

function getCategoryName($category_id, $all_categories)
{
    foreach ($all_categories as $category) {
        if ($category['id'] == $category_id) {
            return $category['name'];
        }
    }
    return 'Unknown Category';
}

function getBookCoverImageUrl($isbnNo)
{
    return "https://covers.openlibrary.org/b/isbn/{$isbnNo}-L.jpg";
}

?>

<?php include 'header.php'; ?>

<body>

    <?php include 'top_navbar.php'; ?>

    <div class="container my-5">
        <!-- <div class="d-flex justify-content-center mb-5">
            <form class="d-flex w-50" action="customer_dashboard.php" method="GET">
                <input type="text" class="form-control me-2" name="search" placeholder="Search books..."
                    value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-dark">Search</button>
            </form>
        </div> -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <form action="customer_dashboard.php" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg rounded-pill py-3 ps-4 pe-5"
                            name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>"
                            style="border-right: none;">
                        <button type="submit"
                            class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-3 z-1">
                            <i class="bi bi-search fs-5 text-secondary"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <?php while ($book = $result->fetch_assoc()): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <!-- Book Image with Category Badge -->
                        <div class="position-relative">
                            <img src="<?php echo getBookCoverImageUrl($book['serial_number']); ?>" class="card-img-top"
                                alt="<?php echo htmlspecialchars($book['title']); ?>"
                                style="height: 300px; object-fit: cover;">
                            <span class="position-absolute top-0 end-0 m-2 badge bg-white text-dark rounded-pill">
                                <?php echo htmlspecialchars(getCategoryName($book['category_id'], $all_categories)); ?>
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold mb-1">
                                <?php echo htmlspecialchars($book['title']); ?>
                            </h5>
                            <p class="text-muted small mb-2">
                                by <?php echo htmlspecialchars($book['author']); ?>
                            </p>
                            <p class="fs-5 fw-bold text-primary mb-4">
                                Rs. <?php echo htmlspecialchars($book['price']); ?>
                            </p>
                            <div class="mt-auto d-grid gap-2">
                                <button onclick="handleBuyNow(<?php echo $book['id']; ?>, <?php echo $book['price']; ?>)"
                                    class="btn btn-dark rounded-pill py-2">
                                    <i class="bi bi-bag me-2"></i>Buy Now
                                </button>
                                <button onclick="handleAddToCart(<?php echo $book['id']; ?>)"
                                    class="btn btn-outline-dark rounded-pill py-2">
                                    <i class="bi bi-cart me-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Login to continue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="p-3">
                        <div class="mb-4">
                            <label class="form-label text-muted small">Email or username</label>
                            <input type="text" class="form-control form-control-lg rounded-pill" name="username"
                                id="username">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small">Password</label>
                            <input type="password" id="password" name="password"
                                class="form-control form-control-lg rounded-pill">
                        </div>
                        <button type="button" onclick="login()" class="btn btn-dark w-100 rounded-pill py-3 mb-3">
                            Login
                        </button>
                        <div id="error-message" class="alert alert-danger p-2 small" style="display: none;"></div>
                        <p class="text-center text-muted small mb-0">
                            Don't have an account?
                            <a href="#" onclick="toggleModal('staticBackdrop', 'signupModal')"
                                class="text-dark text-decoration-underline">Create Account</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- signup modal -->
    <div class="modal fade" id="signupModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Create Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="p-3">
                        <div class="mb-4">
                            <label class="form-label text-muted small">Username</label>
                            <input type="text" class="form-control form-control-lg rounded-pill" name="signup-username"
                                id="signup-username">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small">Password</label>
                            <input type="password" id="signup-password" name="signup-password"
                                class="form-control form-control-lg rounded-pill">
                        </div>
                        <button type="button" onclick="signup()" class="btn btn-dark w-100 rounded-pill py-3 mb-3">
                            Create Account
                        </button>
                        <div id="signup-error-message" class="alert alert-danger p-2 small" style="display: none;">
                        </div>
                        <p class="text-center text-muted small mb-0">
                            Already have an account?
                            <a href="#" onclick="toggleModal('signupModal', 'staticBackdrop')"
                                class="text-dark text-decoration-underline">Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let intendedUrl = '';
        const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;

        function toggleModal(fromModal, toModal) {
            const currentModal = bootstrap.Modal.getInstance(document.getElementById(fromModal));
            currentModal.hide();

            setTimeout(() => {
                const targetModal = new bootstrap.Modal(document.getElementById(toModal));
                targetModal.show();
            }, 200);
        }

        function signup() {
            const username = document.getElementById('signup-username').value;
            const password = document.getElementById('signup-password').value;
            const errorMessage = document.getElementById('signup-error-message');

            errorMessage.style.display = 'none';
            errorMessage.textContent = '';

            fetch('customer_signup_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Account created successfully!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {
                                popup: 'rounded-4 shadow-sm',
                                title: 'fs-6 fw-bold',
                                htmlContainer: 'small text-muted'
                            },
                            iconColor: '#212529',
                            background: '#fff'
                        }).then(() => {
                            toggleModal('signupModal', 'staticBackdrop');
                        });
                    } else {
                        errorMessage.textContent = data.message;
                        errorMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    errorMessage.textContent = 'An error occurred. Please try again.';
                    errorMessage.style.display = 'block';
                });
        }

        function handleBuyNow(bookId, price) {
            if (!isLoggedIn) {
                intendedUrl = 'place_order.php?book_id=' + bookId + "&price=" + price;
                const modal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
                modal.show();
            } else {
                window.location.href = 'place_order.php?book_id=' + bookId + "&price=" + price;
            }
        }

        function handleAddToCart(bookId) {
            if (!isLoggedIn) {
                intendedUrl = 'process_add_to_cart.php?book_id=' + bookId;
                const modal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
                modal.show();
            } else {
                // window.location.href = 'process_add_to_cart.php?book_id=' + bookId;
                addBookToCart('process_add_to_cart.php?book_id=' + bookId)

            }
        }

        function addBookToCart(url) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Added to Cart',
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                            position: 'top',
                            customClass: {
                                popup: 'rounded-4 shadow-sm',
                                title: 'fs-6 fw-bold',
                                htmlContainer: 'small text-muted'
                            },
                            iconColor: '#212529',
                            background: '#fff',
                            showClass: {
                                popup: 'animate__animated animate__fadeInRight animate__faster'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutRight animate__faster'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Close',
                            customClass: {
                                popup: 'rounded-4 shadow-sm',
                                title: 'fs-5 fw-bold',
                                htmlContainer: 'text-muted',
                                confirmButton: 'btn btn-dark rounded-pill px-4 py-2'
                            },
                            buttonsStyling: false,
                            iconColor: '#dc3545',
                            background: '#fff'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'An unexpected error occurred.',
                        icon: 'error',
                        confirmButtonText: 'Close',
                        customClass: {
                            popup: 'rounded-4 shadow-sm',
                            title: 'fs-5 fw-bold',
                            htmlContainer: 'text-muted',
                            confirmButton: 'btn btn-dark rounded-pill px-4 py-2'
                        },
                        buttonsStyling: false,
                        iconColor: '#dc3545',
                        background: '#fff'
                    });
                });
        }



        function login() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Clear previous error message
            const errorMessage = document.getElementById('error-message');
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';

            // Send login data via AJAX
            fetch('customer_login_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to the intended URL if login is successful
                        // window.location.href = intendedUrl;
                        window.location.reload();
                    } else {
                        // Display error message in modal
                        errorMessage.textContent = data.message;
                        errorMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorMessage.textContent = 'An error occurred. Please try again.';
                    errorMessage.style.display = 'block';
                });
        }

        document.getElementById('username').addEventListener('input', clearErrorMessage);
        document.getElementById('password').addEventListener('input', clearErrorMessage);

        // Clear error message on modal close
        document.getElementById('staticBackdrop').addEventListener('hidden.bs.modal', clearErrorMessage);

        function clearErrorMessage() {
            const errorMessage = document.getElementById('error-message');
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
        }

        document.getElementById('signup-username').addEventListener('input', clearSignupErrorMessage);
        document.getElementById('signup-password').addEventListener('input', clearSignupErrorMessage);
        document.getElementById('signupModal').addEventListener('hidden.bs.modal', clearSignupErrorMessage);

        function clearSignupErrorMessage() {
            const errorMessage = document.getElementById('signup-error-message');
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
        }
    </script>
</body>
<?php include 'client_footer.php'; ?>

</html>

<!-- src="<?php echo getBookCoverImageUrl($book['serial_number']); ?>" -->