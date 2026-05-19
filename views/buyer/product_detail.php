<?php $pageTitle = e($product['name']); require_once __DIR__ . '/../partials/header.php'; ?>

<!-- Breadcrumb -->
<nav class="mb-4"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php?page=products">Shop</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php?page=products&cat=<?= $product['category_id'] ?>"><?= e($product['category_name']) ?></a></li>
    <li class="breadcrumb-item active"><?= e(truncate($product['name'], 40)) ?></li>
</ol></nav>

<?= flash('cart') ?><?= flash('review') ?>

<div class="row g-4 mb-5">
    <!-- Product Image -->
    <div class="col-md-5">
        <div class="product-detail-img-wrap rounded-3 overflow-hidden border">
            <img src="<?= e(productImage($product['image'])) ?>"
                 class="img-fluid w-100" alt="<?= e($product['name']) ?>"
                 style="max-height:420px;object-fit:cover;">
        </div>
    </div>

    <!-- Product Info -->
    <div class="col-md-7">
        <div class="small text-muted mb-1"><?= e($product['category_name']) ?></div>
        <h2 class="fw-bold mb-2"><?= e($product['name']) ?></h2>
        <div class="d-flex align-items-center gap-3 mb-3">
            <?= starRating((float)$product['avg_rating'], (int)$product['review_count']) ?>
            <span class="text-muted small">(<?= $product['review_count'] ?> reviews)</span>
        </div>

        <!-- Price -->
        <div class="mb-3">
            <?php if ($product['discount_price']): ?>
                <span class="display-6 fw-bold text-primary"><?= currency($product['discount_price']) ?></span>
                <span class="text-muted text-decoration-line-through fs-5 ms-2"><?= currency($product['price']) ?></span>
                <?php
                $pct = round((1 - $product['discount_price'] / $product['price']) * 100);
                ?>
                <span class="badge bg-danger ms-2"><?= $pct ?>% off</span>
            <?php else: ?>
                <span class="display-6 fw-bold text-primary"><?= currency($product['price']) ?></span>
            <?php endif; ?>
        </div>

        <p class="text-muted"><?= nl2br(e($product['description'])) ?></p>

        <!-- Stock & Seller -->
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <span class="badge <?= $product['stock'] > 0 ? 'bg-success' : 'bg-danger' ?> fs-6">
                    <?= $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ')' : 'Out of Stock' ?>
                </span>
            </div>
            <div class="col-auto">
                <span class="text-muted small"><i class="bi bi-shop me-1"></i>
                    <?= e($product['shop_name'] ?? $product['seller_name']) ?>
                </span>
            </div>
        </div>

        <!-- Add to Cart -->
        <?php if ($product['stock'] > 0): ?>
            <?php if (isLoggedIn() && currentRole() === 'buyer'): ?>
            <form method="POST" action="<?= BASE_URL ?>index.php?page=cart_add" class="d-flex gap-3 align-items-center">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="input-group" style="width:120px">
                    <button class="btn btn-outline-secondary" type="button" onclick="stepQty(-1)">-</button>
                    <input type="number" name="qty" id="qtyInput" class="form-control text-center" value="1" min="1" max="<?= $product['stock'] ?>">
                    <button class="btn btn-outline-secondary" type="button" onclick="stepQty(1)">+</button>
                </div>
                <button type="submit" class="btn btn-primary btn-lg fw-semibold flex-grow-1">
                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                </button>
            </form>
            <?php elseif (!isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>index.php?page=login" class="btn btn-primary btn-lg fw-semibold">
                <i class="bi bi-cart-plus me-2"></i>Login to Add to Cart
            </a>
            <?php else: ?>
            <div class="alert alert-info">Only buyers can add products to cart.</div>
            <?php endif; ?>
        <?php else: ?>
        <button class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
        <?php endif; ?>
    </div>
</div>

<!-- Tabs: Description & Reviews -->
<ul class="nav nav-tabs mb-4" id="prodTabs">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#descTab">Description</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviewTab">
        Reviews <span class="badge bg-primary ms-1"><?= count($reviews) ?></span>
    </button></li>
</ul>
<div class="tab-content mb-5">
    <div class="tab-pane fade show active" id="descTab">
        <div class="card border-0 bg-light p-4">
            <?= nl2br(e($product['description'])) ?: '<span class="text-muted">No description provided.</span>' ?>
        </div>
    </div>
    <div class="tab-pane fade" id="reviewTab">
        <!-- Write Review -->
        <?php if (isLoggedIn() && currentRole() === 'buyer'): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold">Write a Review</h6>
                <form method="POST" action="<?= BASE_URL ?>index.php?page=add_review">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Your Rating</label>
                        <div class="star-input d-flex gap-2">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" class="btn-check" name="rating" id="star<?= $i ?>" value="<?= $i ?>">
                            <label class="btn btn-outline-warning btn-sm" for="star<?= $i ?>">
                                <?= $i ?> <i class="bi bi-star-fill"></i>
                            </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Review</label>
                        <textarea name="review_text" class="form-control" rows="3" placeholder="Share your experience…"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Reviews List -->
        <?php if (empty($reviews)): ?>
        <p class="text-muted text-center py-4">No reviews yet. Be the first!</p>
        <?php else: ?>
        <?php foreach ($reviews as $r): ?>
        <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
            <div class="flex-shrink-0">
                <div class="avatar-circle"><?= strtoupper(substr($r['buyer_name'], 0, 1)) ?></div>
            </div>
            <div>
                <div class="d-flex gap-2 align-items-center">
                    <strong><?= e($r['buyer_name']) ?></strong>
                    <?= starRating($r['rating']) ?>
                    <span class="text-muted small"><?= date('M j, Y', strtotime($r['created_at'])) ?></span>
                </div>
                <p class="mb-0 mt-1"><?= nl2br(e($r['review_text'])) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Related Products -->
<?php if (!empty($related)): ?>
<div class="mb-4">
    <h5 class="fw-bold mb-3">Related Products</h5>
    <div class="row g-3">
        <?php foreach ($related as $prod): ?>
        <?php if ($prod['id'] !== $product['id']): ?>
        <div class="col-6 col-md-3">
            <?php include __DIR__ . '/product_card.php'; ?>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<script>
function stepQty(delta) {
    const inp = document.getElementById('qtyInput');
    const max = parseInt(inp.max) || 999;
    inp.value = Math.max(1, Math.min(max, parseInt(inp.value || 1) + delta));
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
