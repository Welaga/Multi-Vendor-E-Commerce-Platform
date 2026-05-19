<?php $pageTitle = 'Shopping Cart'; require_once __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-cart3 me-2"></i>Shopping Cart</h4>
    <a href="<?= BASE_URL ?>index.php?page=products" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Continue Shopping
    </a>
</div>

<?= flash('cart') ?><?= flash('coupon') ?>

<?php if (empty($items)): ?>
<div class="text-center py-5">
    <i class="bi bi-cart-x display-1 text-muted opacity-25"></i>
    <h5 class="mt-4 text-muted">Your cart is empty</h5>
    <a href="<?= BASE_URL ?>index.php?page=products" class="btn btn-primary mt-3">
        <i class="bi bi-bag me-2"></i>Start Shopping
    </a>
</div>
<?php else: ?>

<div class="row g-4">
    <!-- Cart Items -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <?php foreach ($items as $i => $item):
                    $price = $item['discount_price'] ?: $item['price'];
                    $lineTotal = $price * $item['quantity'];
                ?>
                <div class="cart-item d-flex align-items-center gap-3 p-3 <?= $i < count($items)-1 ? 'border-bottom' : '' ?>">
                    <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $item['product_id'] ?>">
                        <img src="<?= e(productImage($item['image'])) ?>"
                             class="rounded" style="width:80px;height:80px;object-fit:cover;" alt="">
                    </a>
                    <div class="flex-grow-1">
                        <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $item['product_id'] ?>"
                           class="text-dark text-decoration-none fw-semibold">
                            <?= e($item['name']) ?>
                        </a>
                        <div class="text-muted small"><?= e($item['shop_name'] ?? $item['seller_name']) ?></div>
                        <div class="mt-1">
                            <?php if ($item['discount_price']): ?>
                                <span class="fw-bold text-primary"><?= currency($item['discount_price']) ?></span>
                                <span class="text-muted text-decoration-line-through small ms-1"><?= currency($item['price']) ?></span>
                            <?php else: ?>
                                <span class="fw-bold text-primary"><?= currency($item['price']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Qty control -->
                    <form method="POST" action="<?= BASE_URL ?>index.php?page=cart_update" class="d-flex align-items-center gap-1">
                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        <div class="input-group input-group-sm" style="width:110px">
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="this.nextElementSibling.value=Math.max(1,parseInt(this.nextElementSibling.value)-1);this.closest('form').submit()">-</button>
                            <input type="number" name="quantity" class="form-control text-center"
                                   value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>"
                                   onchange="this.closest('form').submit()">
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="this.previousElementSibling.value=Math.min(<?= $item['stock'] ?>,parseInt(this.previousElementSibling.value)+1);this.closest('form').submit()">+</button>
                        </div>
                    </form>
                    <div class="text-end" style="min-width:90px">
                        <div class="fw-bold"><?= currency($lineTotal) ?></div>
                        <a href="<?= BASE_URL ?>index.php?page=cart_remove&id=<?= $item['product_id'] ?>"
                           class="text-danger small" onclick="return confirm('Remove this item?')">
                            <i class="bi bi-trash"></i> Remove
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="col-lg-4">
        <!-- Coupon -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-ticket-perforated me-2"></i>Coupon Code</h6>
                <form method="POST" action="<?= BASE_URL ?>index.php?page=apply_coupon">
                    <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                    <div class="input-group">
                        <input type="text" name="coupon_code" class="form-control text-uppercase"
                               placeholder="Enter code"
                               value="<?= e($_SESSION['coupon']['code'] ?? '') ?>">
                        <button class="btn btn-outline-primary" type="submit">Apply</button>
                    </div>
                </form>
                <?php if (!empty($_SESSION['coupon'])): ?>
                <div class="alert alert-success py-2 mt-2 mb-0 small">
                    <i class="bi bi-check-circle me-1"></i>
                    Coupon <strong><?= e($_SESSION['coupon']['code']) ?></strong> applied!
                    You save <?= currency($_SESSION['coupon']['discount']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Summary -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Order Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal (<?= count($items) ?> items)</span>
                    <span><?= currency($subtotal) ?></span>
                </div>
                <?php $discount = $_SESSION['coupon']['discount'] ?? 0; ?>
                <?php if ($discount > 0): ?>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Discount</span>
                    <span>-<?= currency($discount) ?></span>
                </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="text-success">Free</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                    <span>Total</span>
                    <span class="text-primary"><?= currency(max(0, $subtotal - $discount)) ?></span>
                </div>
                <a href="<?= BASE_URL ?>index.php?page=checkout" class="btn btn-primary w-100 btn-lg fw-semibold">
                    <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
