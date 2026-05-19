<!-- Reusable product card — expects $prod array -->
<div class="card product-card h-100 border-0 shadow-sm">
    <div class="position-relative overflow-hidden product-img-wrap">
        <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $prod['id'] ?>">
            <img src="<?= e(productImage($prod['image'])) ?>"
                 class="card-img-top product-img" alt="<?= e($prod['name']) ?>">
        </a>
        <?php if ($prod['discount_price']): ?>
        <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
        <?php endif; ?>
        <?php if ($prod['stock'] == 0): ?>
        <div class="out-of-stock-overlay">Out of Stock</div>
        <?php endif; ?>
    </div>
    <div class="card-body d-flex flex-column p-3">
        <div class="text-muted small mb-1"><?= e($prod['shop_name'] ?? $prod['seller_name']) ?></div>
        <h6 class="card-title fw-semibold mb-1 lh-sm">
            <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $prod['id'] ?>" class="text-dark text-decoration-none">
                <?= e(truncate($prod['name'], 55)) ?>
            </a>
        </h6>
        <div class="mb-2"><?= starRating((float)$prod['avg_rating'], (int)$prod['review_count']) ?></div>
        <div class="mt-auto">
            <div class="d-flex align-items-center gap-2">
                <?php if ($prod['discount_price']): ?>
                    <span class="fw-bold text-primary"><?= currency($prod['discount_price']) ?></span>
                    <span class="text-muted text-decoration-line-through small"><?= currency($prod['price']) ?></span>
                <?php else: ?>
                    <span class="fw-bold text-primary"><?= currency($prod['price']) ?></span>
                <?php endif; ?>
            </div>
            <?php if ($prod['stock'] > 0 && isLoggedIn() && currentRole() === 'buyer'): ?>
            <form method="POST" action="<?= BASE_URL ?>index.php?page=cart_add" class="mt-2">
                <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                <input type="hidden" name="qty" value="1">
                <button class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-cart-plus me-1"></i>Add to Cart
                </button>
            </form>
            <?php elseif (!isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>index.php?page=login" class="btn btn-outline-primary btn-sm w-100 mt-2">
                <i class="bi bi-cart-plus me-1"></i>Add to Cart
            </a>
            <?php else: ?>
            <button class="btn btn-secondary btn-sm w-100 mt-2" disabled>Out of Stock</button>
            <?php endif; ?>
        </div>
    </div>
</div>
