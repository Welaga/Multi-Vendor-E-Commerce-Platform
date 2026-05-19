<?php $pageTitle = 'Coupons'; $activePage = 'coupons'; require_once __DIR__ . '/../partials/admin_header.php'; ?>

<h4 class="fw-bold mb-4">Coupon Management</h4>
<?= flash('coupon') ?>

<div class="row g-4">
    <!-- Add Coupon -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Create Coupon</h6>
                <form method="POST" action="<?= BASE_URL ?>index.php?page=admin_add_coupon">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase"
                               placeholder="e.g. SAVE20" required style="text-transform:uppercase">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Discount Type</label>
                        <select name="discount_type" class="form-select" id="discType" onchange="updateDiscLabel(this)">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (GH₵)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Discount Value <span id="discLabel">(%)</span></label>
                        <input type="number" name="discount_value" class="form-control"
                               placeholder="e.g. 10" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Minimum Order (GH₵)</label>
                        <input type="number" name="minimum_order" class="form-control"
                               placeholder="0" step="0.01" min="0" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usage Limit</label>
                        <input type="number" name="usage_limit" class="form-control"
                               placeholder="Leave blank for unlimited" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Expiry Date <span class="text-danger">*</span></label>
                        <input type="date" name="expiry_date" class="form-control"
                               min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-2"></i>Create Coupon
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Coupons List -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Min Order</th>
                            <th>Usage</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupons as $c): ?>
                        <tr>
                            <td><code class="fw-bold"><?= e($c['code']) ?></code></td>
                            <td>
                                <span class="badge <?= $c['discount_type']==='percentage'?'bg-info':'bg-success' ?>">
                                    <?= $c['discount_type']==='percentage' ? '%' : 'Fixed' ?>
                                </span>
                            </td>
                            <td>
                                <?= $c['discount_type']==='percentage'
                                    ? $c['discount_value'].'%'
                                    : currency($c['discount_value']) ?>
                            </td>
                            <td class="small"><?= currency($c['minimum_order']) ?></td>
                            <td class="small">
                                <?= $c['used_count'] ?> / <?= $c['usage_limit'] ?? '∞' ?>
                            </td>
                            <td class="small <?= strtotime($c['expiry_date']) < time() ? 'text-danger' : 'text-muted' ?>">
                                <?= date('M j, Y', strtotime($c['expiry_date'])) ?>
                            </td>
                            <td>
                                <span class="badge <?= $c['status']==='active'?'bg-success':'bg-secondary' ?>">
                                    <?= ucfirst($c['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>index.php?page=admin_delete_coupon&id=<?= $c['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this coupon?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($coupons)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No coupons yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function updateDiscLabel(sel) {
    document.getElementById('discLabel').textContent = sel.value === 'percentage' ? '(%)' : '(GH₵)';
}
</script>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
