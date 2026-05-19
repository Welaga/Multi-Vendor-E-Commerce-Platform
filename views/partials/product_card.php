<?php
// views/partials/product_card.php
// Expects: $product array
$imgSrc = file_exists(UPLOAD_PATH . $product['image'])
    ? UPLOAD_URL . $product['image']
    : BASE_URL . 'assets/images/no-image.png';
$effPrice = $product['discount_price'] ?: $product['price'];
?>
<div class="product-card card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
    <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $product['id'] ?>">
        <div class="product-img-wrap overflow-hidden">
            <img src="<?= $imgSrc ?>" class="card-img-top product-img" alt="<?= e($product['name']) ?>">
        </div>
        <?php if ($product['discount_price']): ?>
            <span class="badge bg-danger position-absolute top-0 end-0 m-2">SALE</span>
        <?php endif; ?>
    </a>
    <div class="card-body d-flex flex-column p-3">
        <p class="small text-muted mb-1"><?= e($product['shop_name'] ?? $product['seller_name']) ?></p>
        <h6 class="card-title fw-semibold mb-1 text-truncate">
            <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $product['id'] ?>" class="text-dark text-decoration-none">
                <?= e($product['name']) ?>
            </a>
        </h6>
        <div class="mb-2"><?= starRating((float)$product['avg_rating'], (int)$product['review_count']) ?></div>
        <div class="mt-auto">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="fw-bold text-primary"><?= currency($effPrice) ?></span>
                <?php if ($product['discount_price']): ?>
                    <span class="text-muted text-decoration-line-through small"><?= currency($product['price']) ?></span>
                <?php endif; ?>
            </div>
            <?php if ($product['stock'] > 0): ?>
                <form method="POST" action="<?= BASE_URL ?>index.php?page=cart_add">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="qty" value="1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-cart-plus me-1"></i>Add to Cart
                    </button>
                </form>
            <?php else: ?>
                <button class="btn btn-secondary btn-sm w-100" disabled>Out of Stock</button>
            <?php endif; ?>
        </div>
    </div>
</div>
