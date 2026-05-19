</div></main>

<footer class="bg-dark text-light pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-shop me-2"></i>MarketHub</h5>
                <p class="text-muted small">Your one-stop marketplace connecting buyers with the best sellers across Ghana and beyond.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-white fw-semibold mb-3">Shop</h6>
                <ul class="list-unstyled small">
                    <li><a href="<?= BASE_URL ?>index.php?page=products" class="text-muted text-decoration-none">All Products</a></li>
                    <li><a href="<?= BASE_URL ?>index.php?page=register" class="text-muted text-decoration-none">Become a Seller</a></li>
                    <li><a href="<?= BASE_URL ?>index.php?page=cart" class="text-muted text-decoration-none">Cart</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-white fw-semibold mb-3">Account</h6>
                <ul class="list-unstyled small">
                    <li><a href="<?= BASE_URL ?>index.php?page=login" class="text-muted text-decoration-none">Login</a></li>
                    <li><a href="<?= BASE_URL ?>index.php?page=register" class="text-muted text-decoration-none">Register</a></li>
                    <li><a href="<?= BASE_URL ?>index.php?page=my_orders" class="text-muted text-decoration-none">My Orders</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="text-white fw-semibold mb-3">Contact</h6>
                <ul class="list-unstyled small text-muted">
                    <li><i class="bi bi-envelope me-2"></i>support@markethub.com</li>
                    <li class="mt-1"><i class="bi bi-telephone me-2"></i>+233 30 000 0000</li>
                    <li class="mt-1"><i class="bi bi-geo-alt me-2"></i>Accra, Ghana</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center small text-muted">
            <span>&copy; <?= date('Y') ?> MarketHub. All rights reserved.</span>
            <span>Built with <i class="bi bi-heart-fill text-danger"></i> using PHP & Bootstrap</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
