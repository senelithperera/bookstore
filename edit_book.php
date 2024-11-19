<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
include 'db_connect.php';

$query = "SELECT * FROM categories ORDER BY name";
$result = $conn->query($query);

// Fetch all results as an associative array
if ($result) {
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    // Now $categories contains all the category rows as an associative array
} else {
    echo "Error: " . $conn->error;
}

$book_id = $_GET['id'];
$query = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
?>
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
                            <h4 class="card-title mb-0 fw-bold">Edit Book</h4>
                        </div>
                        <div class="card-body">
                                <form action="update_book.php" method="POST" class="needs-validation" novalidate>
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">

                                    <div class="mb-3">
                                        <label for="title" class="form-label">Book Title</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                            value="<?php echo htmlspecialchars($book['title']); ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter a book title.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="author" class="form-label">Author</label>
                                        <input type="text" class="form-control" id="author" name="author"
                                            value="<?php echo htmlspecialchars($book['author']); ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter an author name.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" 
                                            required><?php echo htmlspecialchars($book['description']); ?></textarea>
                                        <div class="invalid-feedback">
                                            Please enter a description.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="serial" class="form-label">Serial Number</label>
                                        <input type="text" class="form-control" id="serial" name="serial"
                                            value="<?php echo htmlspecialchars($book['serial_number']); ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter a serial number.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price:</label>
                                        <input type="text" class="form-control" id="price" name="price" required
                                        value="<?php echo htmlspecialchars($book['price']); ?>">
                                        <div class="invalid-feedback">
                                            Please enter a price.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Stock:</label>
                                        <input type="text" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($book['stock']); ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter stock quantity.
                                        </div>
                                    </div>


                                    <div class="mb-4">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" 
                                                    <?php echo $category['id'] == $book['category_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a category.
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">Update Book</button>
                                        <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Bootstrap validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })();
    </script>
</body>

</html>