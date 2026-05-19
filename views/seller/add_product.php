<?php $pageTitle = 'Add Product'; $activePage = 'add_product'; require_once __DIR__ . '/../partials/seller_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Add New Product</h4>
    <a href="<?= BASE_URL ?>index.php?page=seller_products" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>My Products
    </a>
</div>

<?= flash('product') ?>

<div class="row g-4">
    <div class="col-lg-8">
        <form method="POST" action="<?= BASE_URL ?>index.php?page=seller_add_product_save"
              enctype="multipart/form-data">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Basic Information</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Wireless Headphones Pro" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">— Select Category —</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="5"
                                  placeholder="Describe your product in detail…"></textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Pricing & Stock</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Price (GH₵) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" placeholder="0.00"
                                   step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sale Price (GH₵)</label>
                            <input type="number" name="discount_price" class="form-control" placeholder="Optional"
                                   step="0.01" min="0">
                            <div class="form-text">Leave blank if no discount</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" placeholder="0"
                                   min="0" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Product Image</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*"
                               id="imgInput" onchange="previewImage(this)">
                        <div class="form-text">JPG, PNG, GIF or WEBP. Max 5MB.</div>
                    </div>
                    <div id="imgPreviewWrap" class="d-none mt-2">
                        <img id="imgPreview" src="" alt="Preview" class="rounded border"
                             style="max-width:200px;max-height:200px;object-fit:cover">
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Visibility</h6>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="active" id="stActive" checked>
                            <label class="form-check-label" for="stActive">
                                <i class="bi bi-eye text-success me-1"></i>Active (visible to buyers)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="inactive" id="stInactive">
                            <label class="form-check-label" for="stInactive">
                                <i class="bi bi-eye-slash text-muted me-1"></i>Inactive (hidden)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                <i class="bi bi-cloud-upload me-2"></i>Publish Product
            </button>
        </form>
    </div>

    <!-- Tips -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>Tips for Great Listings</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use clear, high-quality photos</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Write detailed descriptions</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Set competitive pricing</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Keep stock levels accurate</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Choose the right category</li>
                    <li><i class="bi bi-info-circle text-primary me-2"></i>A 10% platform commission applies to all sales.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('imgPreviewWrap').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../partials/seller_footer.php'; ?>
