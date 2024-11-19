<?php
$isCustomerLoggedIn = isset($_SESSION['customer_logged_in']) && $_SESSION['customer_logged_in'] == true;
$username = $_SESSION['username'] ?? '';
?>
<nav class="navbar navbar-expand-lg py-3 position-sticky top-0 bg-white shadow-sm" style="z-index: 1000;">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2 text-dark" href="customer_dashboard.php">
            <i class="bi bi-book fs-2"></i>
            <span class="fw-bold fs-4">BookStore</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-2"></i>
        </button>

        <!-- Nav Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <?php if ($isCustomerLoggedIn): ?>
                    <!-- Cart with Badge -->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 text-dark rounded-pill hover-bg-light" 
                           href="add_cart.php">
                            <i class="bi bi-cart"></i>
                            <span>Cart</span>
                        </a>
                    </li>

                    <!-- Orders -->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 text-dark rounded-pill hover-bg-light" 
                           href="customer_orders.php">
                            <i class="bi bi-bag"></i>
                            <span>Orders</span>
                        </a>
                    </li>

                    <!-- User Menu -->
                    <li class="nav-item ms-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-dark rounded-pill border-2 px-3 py-2 d-flex align-items-center gap-2" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                                <span><?php echo htmlspecialchars($username); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end mt-2 border-0 shadow-sm">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="logout.php">
                                        <i class="bi bi-box-arrow-right"></i>
                                        <span>Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                <?php else: ?>
                    <!-- <li class="nav-item">
                        <a class="btn btn-outline-dark rounded-pill px-4 py-2 me-2" data-toggle="modal" data-target="#staticBackdrop">
                            Sign in
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-dark rounded-pill px-4 py-2" href="#">
                            Sign up
                        </a>
                    </li> -->
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Add this CSS to your stylesheet -->
<style>
    .hover-bg-light:hover {
        background-color: rgba(0, 0, 0, 0.05);
        transition: background-color 0.2s ease;
    }
    
    .navbar {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.95) !important;
    }

    .dropdown-menu {
        border-radius: 1rem;
        padding: 0.5rem;
    }

    .dropdown-item {
        border-radius: 0.5rem;
        transition: background-color 0.2s ease;
    }

    @media (max-width: 991.98px) {
        .navbar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            width: 100%;
            margin: 0.25rem 0;
        }
        
        .btn {
            width: 100%;
            text-align: left;
        }
    }
</style>