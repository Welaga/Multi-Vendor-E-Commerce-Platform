<?php $pageTitle = 'Products'; $activePage = 'products'; require_once __DIR__ . '/../partials/admin_header.php'; ?>

<h4 class="fw-bold mb-4">All Products</h4>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Seller / Shop</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= e(productImage($p['image'])) ?>"
                                 class="rounded" style="width:44px;height:44px;object-fit:cover" alt="">
                            <span class="fw-semibold small"><?= e(truncate($p['name'],40)) ?></span>
                        </div>
                    </td>
                    <td class="small text-muted"><?= e($p['category_name']) ?></td>
                    <td class="small">
                        <div><?= e($p['seller_name']) ?></div>
                        <div class="text-muted"><?= e($p['shop_name'] ?? '') ?></div>
                    </td>
                    <td>
                        <?php if ($p['discount_price']): ?>
                            <span class="fw-bold text-primary"><?= currency($p['discount_price']) ?></span><br>
                            <span class="text-muted text-decoration-line-through small"><?= currency($p['price']) ?></span>
                        <?php else: ?>
                            <span class="fw-bold"><?= currency($p['price']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge <?= $p['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                            <?= $p['stock'] ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $p['status']==='active'?'bg-success':'bg-secondary' ?>">
                            <?= ucfirst($p['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $p['id'] ?>"
                           class="btn btn-sm btn-outline-primary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No products listed yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
