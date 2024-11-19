<footer class="bg-white border-top py-5 mt-5">
    <div class="container">
        <!-- Main Footer Content -->
        <div class="row gy-4">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6">
                <div class="mb-4">
                    <a class="d-flex align-items-center text-dark text-decoration-none mb-3" href="#">
                        <i class="bi bi-book fs-3 me-2"></i>
                        <span class="fs-4 fw-bold">BookStore</span>
                    </a>
                    <p class="text-muted mb-3">Your go-to place for a wide selection of books across all genres.</p>
                    <!-- Newsletter Signup -->
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="email" class="form-control rounded-start-pill" placeholder="Enter your email">
                            <button class="btn btn-dark rounded-end-pill px-4">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-4">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-muted hover-dark">Home</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-muted hover-dark">Categories</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-muted hover-dark">About Us</a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none text-muted hover-dark">Contact</a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-4">Contact Us</h6>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <a href="tel:+1234567890"
                            class="text-decoration-none text-muted d-flex align-items-center hover-dark">
                            <i class="bi bi-telephone me-3"></i>
                            +123 456 7890
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="mailto:info@bookstore.com"
                            class="text-decoration-none text-muted d-flex align-items-center hover-dark">
                            <i class="bi bi-envelope me-3"></i>
                            info@bookstore.com
                        </a>
                    </li>
                    <li class="mb-3">
                        <div class="text-muted d-flex align-items-center">
                            <i class="bi bi-geo-alt me-3"></i>
                            123 Book St, Book City
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Social Links -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-4">Follow Us</h6>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-outline-dark rounded-circle p-2">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-outline-dark rounded-circle p-2">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-outline-dark rounded-circle p-2">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="btn btn-outline-dark rounded-circle p-2">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <hr class="my-4">

        <!-- Bottom Footer -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-muted mb-md-0">
                    &copy; <?php echo date("Y"); ?> BookStore. All rights reserved.
                </p>
            </div>
            <div class="col-md-6">
                <ul class="list-inline text-center text-md-end mb-0">
                    <li class="list-inline-item">
                        <a href="#" class="text-muted text-decoration-none hover-dark">Privacy Policy</a>
                    </li>
                    <li class="list-inline-item ms-3">
                        <a href="#" class="text-muted text-decoration-none hover-dark">Terms of Use</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <style>
        .hover-dark {
            transition: color 0.2s ease-in-out;
        }

        .hover-dark:hover {
            color: #212529 !important;
        }

        footer .btn-outline-dark {
            width: 40px;
            height: 40px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease-in-out;
        }

        footer .btn-outline-dark:hover {
            transform: translateY(-3px);
        }

        footer .input-group {
            max-width: 360px;
        }

        @media (max-width: 767.98px) {
            footer .list-inline-item {
                margin-bottom: 0.5rem;
            }
        }
    </style>
</footer>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>