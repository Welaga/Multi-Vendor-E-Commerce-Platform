<?php $pageTitle = 'Login'; require_once __DIR__ . '/../partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle display-4 text-primary"></i>
                    <h4 class="fw-bold mt-2">Welcome Back</h4>
                    <p class="text-muted small">Sign in to your account</p>
                </div>
                <?= flash('auth') ?>
                <form method="POST" action="<?= BASE_URL ?>index.php?page=login">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="pwField" class="form-control" placeholder="••••••••" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePw('pwField',this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </form>
                <hr>
                <div class="text-center small">
                    Don't have an account? <a href="<?= BASE_URL ?>index.php?page=register" class="fw-semibold">Register here</a>
                </div>
                <div class="alert alert-light border mt-3 small text-muted mb-0">
                    <strong>Demo Logins (password: <code>password</code>)</strong><br>
                    Admin: admin@shop.com<br>
                    Seller: seller@shop.com<br>
                    Buyer: buyer@shop.com
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
