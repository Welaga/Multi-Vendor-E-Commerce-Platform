<?php $pageTitle = 'MarketHub — Shop Everything'; require_once __DIR__ . '/../partials/header.php'; ?>

<!-- Hero Banner -->
<div class="hero-banner rounded-3 mb-5 text-white p-5" style="background:linear-gradient(135deg,#0d6efd 0%,#6610f2 100%);min-height:300px;display:flex;align-items:center;">
    <div>
        <span class="badge bg-warning text-dark mb-3 fw-semibold fs-6">New Season Sale</span>
        <h1 class="display-5 fw-bold mb-3">Shop from Ghana's Best<br>Independent Sellers</h1>
        <p class="lead opacity-75 mb-4">Thousands of products. Verified sellers. Fast delivery.</p>
        <div class="d-flex gap-3 flex-wrap">
            <a href="<?= BASE_URL ?>index.php?page=products" class="btn btn-warning btn-lg fw-bold">
                <i class="bi bi-bag me-2"></i>Shop Now
            </a>
            <a href="<?= BASE_URL ?>index.php?page=register" class="btn btn-outline-light btn-lg">
                <i class="bi bi-shop me-2"></i>Sell With Us
            </a>
        </div>
    </div>
</div>

<!-- Category Cards -->
<?php $catModel = new Category(); $cats = $catModel->getAll(); ?>
<div class="mb-5">
    <h5 class="fw-bold mb-3">Browse Categories</h5>
    <div class="row g-3">
        <?php
        $catColors = ['primary','success','danger','warning','info','secondary'];
        foreach ($cats as $i => $cat):
            $color = $catColors[$i % count($catColors)];
        ?>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="<?= BASE_URL ?>index.php?page=products&cat=<?= $cat['id'] ?>"
               class="card text-center text-decoration-none border-0 shadow-sm h-100 cat-card">
                <div class="card-body py-4">
                    <i class="bi <?= e($cat['icon']) ?> fs-1 text-<?= $color ?> mb-2 d-block"></i>
                    <div class="fw-semibold small"><?= e($cat['name']) ?></div>
                    <div class="text-muted" style="font-size:.74rem"><?= $cat['product_count'] ?> items</div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured Products -->
<?php $prodModel = new Product(); $featured = $prodModel->getFeatured(8); ?>
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Featured Products</h5>
        <a href="<?= BASE_URL ?>index.php?page=products" class="btn btn-sm btn-outline-primary">View All <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-3">
        <?php foreach ($featured as $prod): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <?php include __DIR__ . '/product_card.php'; ?>
        </div>
        <?php endforeach; ?>
        <?php if (empty($featured)): ?>
        <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-box-seam display-1 opacity-25"></i>
            <p class="mt-3">No products yet. Check back soon!</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Value Props -->
<div class="row g-4 py-4 border-top">
    <?php foreach ([
        ['bi-truck','Fast Delivery','Orders shipped across Ghana'],
        ['bi-shield-check','Verified Sellers','All sellers are reviewed'],
        ['bi-arrow-counterclockwise','Easy Returns','Hassle-free return policy'],
        ['bi-headset','24/7 Support','Always here to help'],
    ] as [$icon,$title,$desc]): ?>
    <div class="col-6 col-md-3 text-center">
        <i class="bi <?= $icon ?> display-5 text-primary mb-2"></i>
        <h6 class="fw-bold"><?= $title ?></h6>
        <p class="text-muted small mb-0"><?= $desc ?></p>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
