<?php
$pageTitle  = 'Shop Profile';
$activePage = 'profile';
require_once __DIR__ . '/../partials/seller_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Shop Profile</h4>
</div>

<?= flash('sellerprofile') ?>

<div class="row g-4">
    <div class="col-lg-8">
        <form method="POST" action="<?= BASE_URL ?>index.php?page=seller_profile" enctype="multipart/form-data">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Personal Details</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= e($profile['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="tel" name="phone" class="form-control"
                                   value="<?= e($profile['phone'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Shop Details</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Shop Name <span class="text-danger">*</span></label>
                        <input type="text" name="shop_name" class="form-control"
                               value="<?= e($profile['shop_name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Shop Description</label>
                        <textarea name="shop_description" class="form-control" rows="4"><?= e($profile['shop_description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Shop Logo</label>
                        <div class="d-flex gap-3 align-items-center">
                            <img src="<?= e(productImage($profile['shop_logo'] ?? null)) ?>"
                                 class="rounded-circle border" style="width:60px;height:60px;object-fit:cover"
                                 id="logoPreview" alt="Shop Logo">
                            <input type="file" name="shop_logo" class="form-control" accept="image/*"
                                   onchange="previewImage(this,'logoPreview')">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                <i class="bi bi-save me-2"></i>Save Profile
            </button>
        </form>
    </div>

    <!-- Sidebar info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <img src="<?= e(productImage($profile['shop_logo'] ?? null)) ?>"
                     class="rounded-circle border mb-3" style="width:80px;height:80px;object-fit:cover" alt="">
                <h5 class="fw-bold mb-0"><?= e($profile['shop_name'] ?? 'My Shop') ?></h5>
                <div class="text-muted small mb-3"><?= e($profile['email'] ?? '') ?></div>
                <span class="badge <?= ($profile['approval_status'] ?? '') === 'approved' ? 'bg-success' : 'bg-warning text-dark' ?>">
                    <?= ucfirst($profile['approval_status'] ?? 'pending') ?>
                </span>
                <hr>
                <div class="small text-muted text-start">
                    <div class="mb-1"><i class="bi bi-calendar me-2"></i>Joined <?= date('M Y', strtotime($profile['created_at'] ?? 'now')) ?></div>
                    <div class="mb-1"><i class="bi bi-percent me-2"></i>10% platform commission on all sales</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/seller_footer.php'; ?>
