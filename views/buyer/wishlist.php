<?php
$pageTitle = 'My Wishlist';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-heart me-2 text-danger"></i>My Wishlist</h4>
    <a href="<?= BASE_URL ?>index.php?page=products" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-bag me-1"></i>Continue Shopping
    </a>
</div>

<?= flash('wishlist') ?>

<?php if (empty($items)): ?>
<div class="text-center py-5 text-muted">
    <i class="bi bi-heart display-1 opacity-25"></i>
    <h5 class="mt-4">Your wishlist is empty</h5>
    <p class="text-muted">Save products you love by clicking the heart icon.</p>
    <a href="<?= BASE_URL ?>index.php?page=products" class="btn btn-primary mt-2">
        <i class="bi bi-grid me-2"></i>Browse Products
    </a>
</div>
<?php else: ?>

<div class="row g-3">
    <?php foreach ($items as $item): ?>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm h-100 product-card">
            <div class="position-relative overflow-hidden product-img-wrap">
                <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $item['product_id'] ?>">
                    <img src="<?= e(productImage($item['image'], $item['name'], $item['category_name'])) ?>"
                         class="card-img-top product-img" alt="<?= e($item['name']) ?>" loading="lazy">
                </a>
                <?php if ($item['discount_price']): ?>
                <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
                <?php endif; ?>
                <?php if ($item['stock'] == 0): ?>
                <div class="out-of-stock-overlay">Out of Stock</div>
                <?php endif; ?>
                <!-- Remove from wishlist -->
                <a href="<?= BASE_URL ?>index.php?page=wishlist_toggle&id=<?= $item['product_id'] ?>&ref=wishlist"
                   class="position-absolute top-0 end-0 m-2 btn btn-sm btn-light rounded-circle p-1"
                   style="width:28px;height:28px;display:flex;align-items:center;justify-content:center"
                   title="Remove from wishlist">
                    <i class="bi bi-heart-fill text-danger" style="font-size:.75rem"></i>
                </a>
            </div>
            <div class="card-body d-flex flex-column p-3">
                <div class="text-muted small mb-1"><?= e($item['shop_name'] ?? $item['seller_name']) ?></div>
                <h6 class="card-title fw-semibold mb-1 lh-sm">
                    <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $item['product_id'] ?>"
                       class="text-dark text-decoration-none">
                        <?= e(truncate($item['name'], 50)) ?>
                    </a>
                </h6>
                <div class="mb-2"><?= starRating((float)$item['avg_rating'], (int)$item['review_count']) ?></div>
                <div class="mt-auto">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <?php if ($item['discount_price']): ?>
                            <span class="fw-bold text-primary"><?= currency($item['discount_price']) ?></span>
                            <span class="text-muted text-decoration-line-through small"><?= currency($item['price']) ?></span>
                        <?php else: ?>
                            <span class="fw-bold text-primary"><?= currency($item['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($item['stock'] > 0 && $item['product_status'] === 'active'): ?>
                    <form method="POST" action="<?= BASE_URL ?>index.php?page=cart_add">
                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        <input type="hidden" name="qty" value="1">
                        <button class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-cart-plus me-1"></i>Add to Cart
                        </button>
                    </form>
                    <?php else: ?>
                    <button class="btn btn-secondary btn-sm w-100" disabled>
                        <?= $item['product_status'] !== 'active' ? 'Unavailable' : 'Out of Stock' ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="mt-4 text-muted small text-center">
    <?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?> saved &mdash;
    <a href="<?= BASE_URL ?>index.php?page=products" class="text-decoration-none">Keep browsing →</a>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
