<?php
// phpinfo();
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
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
<!DOCTYPE html>
<html lang="en">

<?php include 'header.php'; ?>

<body class="bg-light">
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4" style="margin-left: 250px;">
            <!-- Search and Add Book Header -->
            <div class="container-fluid mb-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-8">
                        <form action="dashboard.php" method="GET" class="d-flex gap-2">
                            <div class="flex-grow-1">
                                <div class="input-group input-group-lg">
                                    <input type="text" class="form-control" name="search" placeholder="Search books..."
                                        value="<?php echo htmlspecialchars($search); ?>" aria-label="Search books">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="add_book.php" class="btn btn-success btn-lg">
                            <i class="bi bi-plus-lg"></i> Add New Book
                        </a>
                    </div>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="container-fluid">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
                    <?php while ($book = $result->fetch_assoc()): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <!-- Book Cover Image -->
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                    style="height: 300px;">
                                    <?php if (!empty($book['title'])): ?>
                                        <img src="<?php echo getBookCoverImageUrl($book['serial_number']); ?>"
                                            class=" img-fluid" alt="<?php echo htmlspecialchars($book['title']); ?>"
                                            style="max-height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="text-center text-muted">
                                            <i class="bi bi-book" style="font-size: 4rem;"></i>
                                            <p class="mt-2">No Cover Available</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title text-truncate"
                                        title="<?php echo htmlspecialchars($book['title']); ?>">
                                        <?php echo htmlspecialchars($book['title']); ?>
                                    </h5>
                                    <p class="card-text mb-1">
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i>
                                            <?php echo htmlspecialchars($book['author']); ?>
                                        </small>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="bi bi-tag"></i>
                                            <?php echo htmlspecialchars(getCategoryName($book['category_id'], $all_categories)); ?>
                                        </small>
                                    </p>
                                </div>

                                <div class="card-footer bg-transparent border-top-0">
                                    <div class="d-flex justify-content-between gap-2">
                                        <a href="edit_book.php?id=<?php echo $book['id']; ?>"
                                            class="btn btn-outline-primary flex-grow-1">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <!-- <a href="delete_book.php?id=<?php echo $book['id']; ?>"
                                            class="btn btn-outline-danger flex-grow-1"
                                            onclick="return confirm('Are you sure you want to delete this book?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a> -->
                                        <a href="javascript:void(0);" class="btn btn-outline-danger flex-grow-1 delete-btn"
                                            data-id="<?php echo $book['id']; ?>">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const bookId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`delete_book.php?id=${bookId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire('Deleted!', 'The book has been deleted.', 'success').then(() => {
                                            window.location.reload();
                                        });
                                    } else if (data.status === 'error') {
                                        Swal.fire('Error', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                                });
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>

<!-- src="<?php echo getBookCoverImageUrl($book['serial_number']); ?>"  -->