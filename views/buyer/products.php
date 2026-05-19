<?php $pageTitle = 'Shop Products'; require_once __DIR__ . '/../partials/header.php'; ?>

<div class="row g-4">
    <!-- Sidebar Filters -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm sticky-top" style="top:80px">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filters</h6>
                <form method="GET" action="<?= BASE_URL ?>index.php">
                    <input type="hidden" name="page" value="products">
                    <?php if (!empty($_GET['q'])): ?>
                    <input type="hidden" name="q" value="<?= e($_GET['q']) ?>">
                    <?php endif; ?>

                    <!-- Categories -->
                    <label class="form-label fw-semibold small text-uppercase text-muted">Category</label>
                    <div class="list-group list-group-flush mb-3">
                        <a href="<?= BASE_URL ?>index.php?page=products<?= !empty($_GET['q']) ? '&q='.urlencode($_GET['q']) : '' ?>"
                           class="list-group-item list-group-item-action small py-1 px-2 <?= empty($_GET['cat']) ? 'active' : '' ?>">
                           All Categories
                        </a>
                        <?php foreach ($categories as $cat): ?>
                        <a href="<?= BASE_URL ?>index.php?page=products&cat=<?= $cat['id'] ?><?= !empty($_GET['q']) ? '&q='.urlencode($_GET['q']) : '' ?>"
                           class="list-group-item list-group-item-action small py-1 px-2 <?= (($_GET['cat'] ?? '') == $cat['id']) ? 'active' : '' ?>">
                            <i class="bi <?= e($cat['icon']) ?> me-1"></i><?= e($cat['name']) ?>
                            <span class="badge bg-secondary float-end"><?= $cat['product_count'] ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Price Range -->
                    <label class="form-label fw-semibold small text-uppercase text-muted">Price Range</label>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <input type="number" name="min" class="form-control form-control-sm"
                                   placeholder="Min" value="<?= e($_GET['min'] ?? '') ?>">
                        </div>
                        <div class="col-6">
                            <input type="number" name="max" class="form-control form-control-sm"
                                   placeholder="Max" value="<?= e($_GET['max'] ?? '') ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100">Apply Filters</button>
                    <a href="<?= BASE_URL ?>index.php?page=products" class="btn btn-outline-secondary btn-sm w-100 mt-2">Clear</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="col-lg-9">
        <!-- Search summary -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <?php if (!empty($_GET['q'])): ?>
                <span class="fw-semibold">Results for "<em><?= e($_GET['q']) ?></em>"</span>
                <?php else: ?>
                <span class="fw-semibold">All Products</span>
                <?php endif; ?>
                <span class="text-muted small ms-2"><?= $total ?> item<?= $total !== 1 ? 's' : '' ?></span>
            </div>
        </div>

        <?= flash('cart') ?>

        <?php if (empty($products)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-search display-1 opacity-25"></i>
            <h5 class="mt-3">No products found</h5>
            <p>Try adjusting your filters or search query.</p>
            <a href="<?= BASE_URL ?>index.php?page=products" class="btn btn-outline-primary">Clear Filters</a>
        </div>
        <?php else: ?>
        <div class="row g-3">
            <?php foreach ($products as $prod): ?>
            <div class="col-6 col-md-4">
                <?php include __DIR__ . '/product_card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $pages; $i++):
                    $q   = http_build_query(array_merge($_GET, ['page' => 'products', 'pg' => $i]));
                ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>index.php?<?= $q ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
