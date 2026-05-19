<?php $pageTitle = 'Checkout'; require_once __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-credit-card me-2"></i>Checkout</h4>
    <a href="<?= BASE_URL ?>index.php?page=cart" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Cart
    </a>
</div>

<?= flash('checkout') ?>

<form method="POST" action="<?= BASE_URL ?>index.php?page=place_order">
<div class="row g-4">
    <!-- Shipping Info -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt me-2"></i>Shipping Information</h6>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" class="form-control" value="<?= e($_SESSION['user_name']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Delivery Address <span class="text-danger">*</span></label>
                    <textarea name="shipping_address" class="form-control" rows="3"
                              placeholder="Enter your full delivery address including region and city…" required></textarea>
                </div>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-wallet2 me-2"></i>Payment Method</h6>
                <?php foreach ([
                    ['cash_on_delivery','Cash on Delivery','bi-cash','Pay when your order arrives'],
                    ['mobile_money','Mobile Money','bi-phone','MTN, Vodafone, AirtelTigo'],
                    ['bank_transfer','Bank Transfer','bi-bank','Direct bank payment'],
                ] as [$val,$label,$icon,$desc]): ?>
                <div class="form-check payment-option p-3 border rounded mb-2">
                    <input class="form-check-input" type="radio" name="payment_method"
                           id="pm_<?= $val ?>" value="<?= $val ?>" <?= $val==='cash_on_delivery'?'checked':'' ?>>
                    <label class="form-check-label d-flex align-items-center gap-3 w-100" for="pm_<?= $val ?>">
                        <i class="bi <?= $icon ?> fs-4 text-primary"></i>
                        <div>
                            <div class="fw-semibold"><?= $label ?></div>
                            <div class="text-muted small"><?= $desc ?></div>
                        </div>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm sticky-top" style="top:80px">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Order Summary</h6>
                <!-- Items -->
                <?php foreach ($items as $item):
                    $price = $item['discount_price'] ?: $item['price'];
                ?>
                <div class="d-flex gap-2 align-items-center mb-2">
                    <img src="<?= e(productImage($item['image'])) ?>"
                         class="rounded" style="width:44px;height:44px;object-fit:cover" alt="">
                    <div class="flex-grow-1 small">
                        <div class="fw-semibold lh-sm"><?= e(truncate($item['name'],35)) ?></div>
                        <div class="text-muted">x<?= $item['quantity'] ?></div>
                    </div>
                    <div class="fw-semibold small"><?= currency($price * $item['quantity']) ?></div>
                </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Subtotal</span><span><?= currency($subtotal) ?></span>
                </div>
                <?php if ($discount > 0): ?>
                <div class="d-flex justify-content-between mb-1 text-success">
                    <span>Coupon (<?= e($coupon['code'] ?? '') ?>)</span>
                    <span>-<?= currency($discount) ?></span>
                </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Shipping</span><span class="text-success">Free</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                    <span>Total</span>
                    <span class="text-primary"><?= currency($total) ?></span>
                </div>
                <button type="submit" class="btn btn-success w-100 btn-lg fw-semibold"
                        onclick="return confirm('Confirm your order?')">
                    <i class="bi bi-check-circle me-2"></i>Place Order
                </button>
                <p class="text-muted text-center small mt-3 mb-0">
                    <i class="bi bi-shield-lock me-1"></i>Your information is safe and secure
                </p>
            </div>
        </div>
    </div>
</div>
</form>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
