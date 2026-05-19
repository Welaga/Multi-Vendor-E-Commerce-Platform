<?php $pageTitle = 'My Profile'; require_once __DIR__ . '/../partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <h4 class="fw-bold mb-4"><i class="bi bi-person-circle me-2"></i>My Profile</h4>
        <?= flash('profile') ?>

        <div class="row g-4">
            <!-- Profile form -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Personal Information</h6>
                        <form method="POST" action="<?= BASE_URL ?>index.php?page=profile" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" value="<?= e($user['email']) ?>" readonly disabled>
                                <div class="form-text">Email cannot be changed.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Delivery Address</label>
                                <textarea name="address" class="form-control" rows="3"><?= e($user['address'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Change Password</h6>
                        <form method="POST" action="<?= BASE_URL ?>index.php?page=profile">
                            <input type="hidden" name="name" value="<?= e($user['name']) ?>">
                            <input type="hidden" name="phone" value="<?= e($user['phone'] ?? '') ?>">
                            <input type="hidden" name="address" value="<?= e($user['address'] ?? '') ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Password</label>
                                <input type="password" name="current_password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">New Password</label>
                                <input type="password" name="new_password" class="form-control" minlength="6">
                            </div>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-lock me-2"></i>Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Account info sidebar -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-4">
                        <div class="avatar-circle mx-auto mb-3" style="width:64px;height:64px;font-size:1.5rem">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                        <h5 class="fw-bold mb-0"><?= e($user['name']) ?></h5>
                        <div class="text-muted small mb-3"><?= e($user['email']) ?></div>
                        <span class="badge bg-primary mb-3"><?= ucfirst($user['role_name']) ?></span>
                        <div class="list-group list-group-flush text-start small mt-3">
                            <div class="list-group-item px-0 d-flex justify-content-between border-0">
                                <span class="text-muted">Member since</span>
                                <strong><?= date('M Y', strtotime($user['created_at'])) ?></strong>
                            </div>
                            <div class="list-group-item px-0 d-flex justify-content-between border-0">
                                <span class="text-muted">Status</span>
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>index.php?page=my_orders" class="btn btn-outline-primary w-100 mt-3 btn-sm">
                            <i class="bi bi-bag-check me-2"></i>View My Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
