<!-- Sidebar -->
<div class="sidebar bg-dark text-white min-vh-100 py-4" style="width: 250px;">
    <div class="px-4 mb-4">
        <h4 class="fw-light">
            <?php echo isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true ? 'Admin Panel' : 'Customer Panel'; ?>
        </h4>
    </div>
    
    <nav>
        <ul class="nav nav-pills flex-column">
            <?php
            $currentPage = basename($_SERVER['PHP_SELF']);
            ?>

            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <li class="nav-item">
                    <a href="dashboard.php" 
                       class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : 'text-white-50'; ?>">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="categories.php" 
                       class="nav-link <?php echo $currentPage == 'categories.php' ? 'active' : 'text-white-50'; ?>">
                        <i class="bi bi-grid me-2"></i>
                        Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_orders.php" 
                       class="nav-link <?php echo $currentPage == 'admin_orders.php' ? 'active' : 'text-white-50'; ?>">
                        <i class="bi bi-bag me-2"></i>
                        Orders
                    </a>
                </li>
            <?php elseif (isset($_SESSION['customer_logged_in']) && $_SESSION['customer_logged_in'] === true): ?>
                <li class="nav-item">
                    <a href="customer_dashboard.php" 
                       class="nav-link <?php echo $currentPage == 'customer_dashboard.php' ? 'active' : 'text-white-50'; ?>">
                        <i class="bi bi-book me-2"></i>
                        Books
                    </a>
                </li>
                <li class="nav-item">
                    <a href="customer_orders.php" 
                       class="nav-link <?php echo $currentPage == 'customer_orders.php' ? 'active' : 'text-white-50'; ?>">
                        <i class="bi bi-bag me-2"></i>
                        Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a href="add_cart.php" 
                       class="nav-link <?php echo $currentPage == 'add_cart.php' ? 'active' : 'text-white-50'; ?>">
                        <i class="bi bi-cart me-2"></i>
                        Cart
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="nav-item mt-3">
                <a href="logout.php" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </a>
            </li>
        </ul>
    </nav>
</div>
