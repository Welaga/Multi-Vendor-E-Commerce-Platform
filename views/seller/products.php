<?php $pageTitle = 'My Products'; $activePage = 'products'; require_once __DIR__ . '/../partials/seller_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">My Products</h4>
    <a href="<?= BASE_URL ?>index.php?page=seller_add_product" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Product
    </a>
</div>

<?= flash('product') ?>

<?php if (empty($products)): ?>
<div class="text-center py-5 text-muted">
    <i class="bi bi-box-seam display-1 opacity-25"></i>
    <h5 class="mt-4">No products yet</h5>
    <a href="<?= BASE_URL ?>index.php?page=seller_add_product" class="btn btn-primary mt-2">Add Your First Product</a>
</div>
<?php else: ?>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Rating</th>
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
                                 class="rounded" style="width:48px;height:48px;object-fit:cover" alt="">
                            <span class="fw-semibold small"><?= e(truncate($p['name'], 40)) ?></span>
                        </div>
                    </td>
                    <td class="small text-muted"><?= e($p['category_name']) ?></td>
                    <td>
                        <?php if ($p['discount_price']): ?>
                            <span class="fw-bold text-primary"><?= currency($p['discount_price']) ?></span><br>
                            <span class="text-muted text-decoration-line-through" style="font-size:.75rem"><?= currency($p['price']) ?></span>
                        <?php else: ?>
                            <span class="fw-bold"><?= currency($p['price']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $sc = $p['stock'] == 0 ? 'danger' : ($p['stock'] <= 5 ? 'warning text-dark' : 'success');
                        ?>
                        <span class="badge bg-<?= $sc ?>"><?= $p['stock'] ?></span>
                    </td>
                    <td><?= starRating((float)$p['avg_rating'], (int)$p['review_count']) ?></td>
                    <td>
                        <span class="badge <?= $p['status']==='active'?'bg-success':'bg-secondary' ?>">
                            <?= ucfirst($p['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-secondary" title="Preview">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= BASE_URL ?>index.php?page=seller_edit_product&id=<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= BASE_URL ?>index.php?page=seller_delete_product&id=<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-danger" title="Delete"
                               onclick="return confirm('Delete this product permanently?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../partials/seller_footer.php'; ?>
