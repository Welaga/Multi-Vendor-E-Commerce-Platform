<?php $pageTitle = 'Payouts'; $activePage = 'payouts'; require_once __DIR__ . '/../partials/admin_header.php'; ?>

<h4 class="fw-bold mb-4">Seller Payouts</h4>
<?= flash('payout') ?>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Seller</th>
                    <th>Shop</th>
                    <th>Total Earned</th>
                    <th>Pending Payout</th>
                    <th>Paid Out</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sellers as $s): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle"><?= strtoupper(substr($s['seller_name'],0,1)) ?></div>
                            <span class="fw-semibold"><?= e($s['seller_name']) ?></span>
                        </div>
                    </td>
                    <td class="text-muted small"><?= e($s['shop_name'] ?? '-') ?></td>
                    <td class="fw-bold"><?= currency($s['total_earned']) ?></td>
                    <td>
                        <span class="fw-bold text-warning"><?= currency($s['pending_amount']) ?></span>
                    </td>
                    <td class="text-success"><?= currency($s['paid_out']) ?></td>
                    <td>
                        <?php if ($s['pending_amount'] > 0): ?>
                        <a href="<?= BASE_URL ?>index.php?page=mark_payout&seller_id=<?= $s['seller_id'] ?>"
                           class="btn btn-sm btn-success"
                           onclick="return confirm('Mark GH₵<?= number_format($s['pending_amount'],2) ?> as paid to <?= e($s['seller_name']) ?>?')">
                            <i class="bi bi-check-circle me-1"></i>Mark Paid
                        </a>
                        <?php else: ?>
                        <span class="badge bg-success"><i class="bi bi-check me-1"></i>All Paid</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($sellers)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No earnings recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
