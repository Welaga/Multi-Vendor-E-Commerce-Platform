<?php $pageTitle = 'Register'; require_once __DIR__ . '/../partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus display-4 text-primary"></i>
                    <h4 class="fw-bold mt-2">Create Account</h4>
                    <p class="text-muted small">Join MarketHub today</p>
                </div>
                <?= flash('auth') ?>

                <!-- Account type tabs -->
                <ul class="nav nav-pills nav-fill mb-4" id="regTabs">
                    <li class="nav-item">
                        <button class="nav-link active" onclick="setAccountType('buyer',this)">
                            <i class="bi bi-bag me-1"></i>Buyer
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="setAccountType('seller',this)">
                            <i class="bi bi-shop me-1"></i>Seller
                        </button>
                    </li>
                </ul>

                <form method="POST" action="<?= BASE_URL ?>index.php?page=register">
                    <input type="hidden" name="account_type" id="accountType" value="buyer">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="John Doe" required minlength="2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" placeholder="024XXXXXXX">
                    </div>

                    <!-- Seller-only field -->
                    <div id="shopNameWrap" class="mb-3 d-none">
                        <label class="form-label fw-semibold">Shop Name <span class="text-danger">*</span></label>
                        <input type="text" name="shop_name" id="shopName" class="form-control" placeholder="My Awesome Store">
                        <div class="form-text">This is the name customers will see for your store.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="pw1" class="form-control" placeholder="Min. 6 characters" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePw('pw1',this)"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="confirm_password" id="pw2" class="form-control" placeholder="Repeat password" required minlength="6">
                        <div id="pwMismatch" class="text-danger small d-none">Passwords do not match.</div>
                    </div>

                    <div id="sellerNotice" class="alert alert-info small d-none mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Seller accounts require admin approval before you can list products.
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold py-2" id="regBtn">
                        <i class="bi bi-person-plus me-2"></i>Create Account
                    </button>
                </form>
                <hr>
                <div class="text-center small">
                    Already have an account? <a href="<?= BASE_URL ?>index.php?page=login" class="fw-semibold">Login here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setAccountType(type, btn) {
    document.getElementById('accountType').value = type;
    document.querySelectorAll('#regTabs .nav-link').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const shopWrap = document.getElementById('shopNameWrap');
    const notice   = document.getElementById('sellerNotice');
    const shopInp  = document.getElementById('shopName');
    if (type === 'seller') {
        shopWrap.classList.remove('d-none');
        notice.classList.remove('d-none');
        shopInp.required = true;
    } else {
        shopWrap.classList.add('d-none');
        notice.classList.add('d-none');
        shopInp.required = false;
    }
}
document.getElementById('pw2').addEventListener('input', function() {
    const match = this.value === document.getElementById('pw1').value;
    document.getElementById('pwMismatch').classList.toggle('d-none', match);
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
