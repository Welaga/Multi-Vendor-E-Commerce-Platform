<?php $pageTitle = 'Edit Product'; $activePage = 'products'; require_once __DIR__ . '/../partials/seller_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Product</h4>
    <a href="<?= BASE_URL ?>index.php?page=seller_products" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<?= flash('product') ?>

<div class="row g-4">
    <div class="col-lg-8">
        <form method="POST" action="<?= BASE_URL ?>index.php?page=seller_update_product" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Basic Information</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= e($product['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id']==$product['category_id']?'selected':'' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="5"><?= e($product['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Pricing & Stock</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Price (GH₵) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control"
                                   value="<?= $product['price'] ?>" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sale Price (GH₵)</label>
                            <input type="number" name="discount_price" class="form-control"
                                   value="<?= $product['discount_price'] ?>" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control"
                                   value="<?= $product['stock'] ?>" min="0" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Product Image</h6>
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <img src="<?= e(productImage($product['image'])) ?>"
                                 class="rounded border" style="width:100px;height:100px;object-fit:cover"
                                 id="imgPreview" alt="Current image">
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Replace Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*"
                                   onchange="previewImage(this,'imgPreview')">
                            <div class="form-text">Leave blank to keep current image.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Visibility</h6>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="active"
                                   id="stActive" <?= $product['status']==='active'?'checked':'' ?>>
                            <label class="form-check-label" for="stActive">
                                <i class="bi bi-eye text-success me-1"></i>Active
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="inactive"
                                   id="stInactive" <?= $product['status']==='inactive'?'checked':'' ?>>
                            <label class="form-check-label" for="stInactive">
                                <i class="bi bi-eye-slash text-muted me-1"></i>Inactive
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                    <i class="bi bi-save me-2"></i>Save Changes
                </button>
                <a href="<?= BASE_URL ?>index.php?page=product_detail&id=<?= $product['id'] ?>"
                   class="btn btn-outline-secondary btn-lg">Preview</a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-2">Product Info</h6>
                <ul class="list-unstyled small text-muted mb-0">
                    <li>ID: <strong>#<?= $product['id'] ?></strong></li>
                    <li>Created: <?= date('M j, Y', strtotime($product['created_at'])) ?></li>
                    <li>Updated: <?= date('M j, Y', strtotime($product['updated_at'])) ?></li>
                    <li>Rating: <?= starRating((float)$product['avg_rating'], (int)$product['review_count']) ?></li>
                </ul>
                <hr>
                <a href="<?= BASE_URL ?>index.php?page=seller_delete_product&id=<?= $product['id'] ?>"
                   class="btn btn-outline-danger btn-sm w-100"
                   onclick="return confirm('Delete this product?')">
                    <i class="bi bi-trash me-2"></i>Delete Product
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById(previewId).src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../partials/seller_footer.php'; ?>
