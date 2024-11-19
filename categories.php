<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
include 'db_connect.php';

// Fetch all categories
$query = "SELECT * FROM categories";
$result = $conn->query($query);
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
    
<?php include 'header.php'; ?>

<body class="bg-light">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <!-- <div class="main-content p-4"> -->
        <div class="flex-grow-1 p-4" style="margin-left: 250px;">
            <div class="container-fluid">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0 fw-bold">Manage Categories</h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="text-secondary fw-semibold">ID</th>
                                        <th scope="col" class="text-secondary fw-semibold">Name</th>
                                        <th scope="col" class="text-secondary fw-semibold">Description</th>
                                        <th scope="col" class="text-secondary fw-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $index => $category): ?>
                                        <tr id="row-<?php echo $index; ?>">
                                            <td class="fw-medium"><?php echo $category['id']; ?></td>
                                            <td class="name">
                                                <span class="static-text"><?php echo htmlspecialchars($category['name']); ?></span>
                                                <input type="text" class="form-control form-control-sm edit-input shadow-none"
                                                    value="<?php echo htmlspecialchars($category['name']); ?>" style="display:none;">
                                            </td>
                                            <td class="description">
                                                <span class="static-text"><?php echo htmlspecialchars($category['description']); ?></span>
                                                <input type="text" class="form-control form-control-sm edit-input shadow-none"
                                                    value="<?php echo htmlspecialchars($category['description']); ?>" style="display:none;">
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" id="edit-btn" class="btn btn-outline-primary btn-sm"
                                                        onclick="editRow('row-<?php echo $index; ?>')">
                                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                                    </button>
                                                    <button type="button" id="save-btn" class="btn btn-success btn-sm" style="display:none;"
                                                        onclick="saveRow('row-<?php echo $index; ?>', <?php echo $category['id']; ?>)">
                                                        <i class="bi bi-check-lg me-1"></i>Save
                                                    </button>
                                                    <a href="delete_category.php?id=<?php echo $category['id']; ?>" id="delete-btn"
                                                        class="btn btn-outline-danger btn-sm ms-1" onclick="return confirm('Are you sure you want to delete this category?')">
                                                        <i class="bi bi-trash me-1"></i>Delete
                                                    </a>
                                                    <button type="button" id="cancelEdit-btn" class="btn btn-outline-secondary btn-sm ms-1"
                                                        style="display:none;" onclick="cancelEdit('row-<?php echo $index; ?>')">
                                                        <i class="bi bi-x-lg me-1"></i>Cancel
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <!-- New Category Form -->
                                    <tr class="table-light">
                                        <td>
                                            <span class="badge bg-primary">New</span>
                                        </td>
                                        <td>
                                            <input class="form-control form-control-sm shadow-none" id="new-category-name" 
                                                type="text" placeholder="Enter category name">
                                        </td>
                                        <td>
                                            <input class="form-control form-control-sm shadow-none" id="new-category-description"
                                                type="text" placeholder="Enter category description">
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" id="new-category-btn" onclick="saveNewCategory()">
                                                <i class="bi bi-plus-lg me-1"></i>Add Category
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to make a row editable
        function editRow(rowId) {
            const row = document.getElementById(rowId);
            const nameSpan = row.querySelector('.name .static-text');
            const nameInput = row.querySelector('.name .edit-input');
            const descSpan = row.querySelector('.description .static-text');
            const descInput = row.querySelector('.description .edit-input');
            const deleteBtn = row.querySelector("#delete-btn");

            // Toggle visibility
            nameSpan.style.display = 'none';
            nameInput.style.display = 'inline-block';
            descSpan.style.display = 'none';
            descInput.style.display = 'inline-block';
            deleteBtn.style.display = 'none';

            // Show Save and Cancel buttons, hide Edit button
            row.querySelector('.btn-outline-primary').style.display = 'none'; // Edit
            row.querySelector('.btn-success').style.display = 'inline-block'; // Save
            row.querySelector('.btn-outline-secondary').style.display = 'inline-block'; // Cancel
        }

        function cancelEdit(rowId) {
            const row = document.getElementById(rowId);
            row.querySelector('.name .edit-input').style.display = 'none';
            row.querySelector('.description .edit-input').style.display = 'none';
            row.querySelector('.name .static-text').style.display = 'inline';
            row.querySelector('.description .static-text').style.display = 'inline';
            row.querySelector('#delete-btn').style.display = 'inline-block';

            // Show Edit button, hide Save and Cancel
            row.querySelector('.btn-outline-primary').style.display = 'inline-block';
            row.querySelector('.btn-success').style.display = 'none';
            row.querySelector('.btn-outline-secondary').style.display = 'none';
        }

        // Function to save edited row
        function saveRow(rowId, categoryId) {
            const row = document.getElementById(rowId);
            const name = row.querySelector('.name .edit-input').value;
            const description = row.querySelector('.description .edit-input').value;

            fetch(`update_category.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${categoryId}&name=${encodeURIComponent(name)}&description=${encodeURIComponent(description)}`
            }).then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        row.querySelector('.name .static-text').innerText = name;
                        row.querySelector('.description .static-text').innerText = description;
                        cancelEdit(rowId); // Exit edit mode
                    } else {
                        alert('Failed to update category');
                    }
                });
        }

        // Function to save new category
        function saveNewCategory() {
            const name = document.getElementById('new-category-name').value;
            const description = document.getElementById('new-category-description').value;

            if (!name.trim()) {
                alert('Please enter a category name');
                return;
            }

            fetch(`add_category.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `name=${encodeURIComponent(name)}&description=${encodeURIComponent(description)}`
            }).then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        location.reload(); // Reload the page to see the new category
                    } else {
                        alert('Failed to add category');
                    }
                });
        }
    </script>
</body>
</html>