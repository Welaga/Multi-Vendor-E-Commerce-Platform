<?php $pageTitle = 'Categories'; $activePage = 'categories'; require_once __DIR__ . '/../partials/admin_header.php'; ?>

<h4 class="fw-bold mb-4">Product Categories</h4>
<?= flash('cat') ?>

<div class="row g-4">
    <!-- Add Category Form -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Add New Category</h6>
                <form method="POST" action="<?= BASE_URL ?>index.php?page=admin_add_category">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Electronics" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bootstrap Icon Class</label>
                        <div class="input-group">
                            <span class="input-group-text" id="iconPreview"><i class="bi bi-tag"></i></span>
                            <input type="text" name="icon" id="iconInput" class="form-control"
                                   placeholder="bi-phone" value="bi-tag"
                                   oninput="document.getElementById('iconPreview').innerHTML='<i class=\'bi \'+this.value+\'\'></i>'">
                        </div>
                        <div class="form-text">
                            Browse icons at <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-2"></i>Add Category
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><i class="bi <?= e($cat['icon']) ?> fs-4 text-primary"></i></td>
                            <td class="fw-semibold"><?= e($cat['name']) ?></td>
                            <td><code class="small"><?= e($cat['slug']) ?></code></td>
                            <td><span class="badge bg-secondary"><?= $cat['product_count'] ?></span></td>
                            <td>
                                <span class="badge <?= $cat['status']==='active'?'bg-success':'bg-secondary' ?>">
                                    <?= ucfirst($cat['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>index.php?page=admin_delete_category&id=<?= $cat['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this category? Products will be affected.')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($categories)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No categories yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
