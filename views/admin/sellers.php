<?php $pageTitle = 'Sellers'; $activePage = 'sellers'; require_once __DIR__ . '/../partials/admin_header.php'; ?>

<h4 class="fw-bold mb-4">Seller Management</h4>
<?= flash('sellers') ?>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Seller</th>
                    <th>Shop Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Account</th>
                    <th>Approval</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sellers as $s): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle"><?= strtoupper(substr($s['name'],0,1)) ?></div>
                            <span class="fw-semibold"><?= e($s['name']) ?></span>
                        </div>
                    </td>
                    <td><?= e($s['shop_name']) ?></td>
                    <td class="text-muted small"><?= e($s['email']) ?></td>
                    <td class="text-muted small"><?= e($s['phone'] ?? '-') ?></td>
                    <td>
                        <span class="badge <?= $s['user_status']==='active'?'bg-success':'bg-secondary' ?>">
                            <?= ucfirst($s['user_status']) ?>
                        </span>
                    </td>
                    <td><?php
                        $ac = match($s['approval_status']) {
                            'approved' => 'success', 'rejected' => 'danger', default => 'warning text-dark'
                        };
                    ?><span class="badge bg-<?= $ac ?>"><?= ucfirst($s['approval_status']) ?></span></td>
                    <td class="text-muted small"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <?php if ($s['approval_status'] !== 'approved'): ?>
                            <a href="<?= BASE_URL ?>index.php?page=approve_seller&id=<?= $s['user_id'] ?>&action=approved"
                               class="btn btn-sm btn-success" title="Approve">
                                <i class="bi bi-check-circle"></i>
                            </a>
                            <?php endif; ?>
                            <?php if ($s['approval_status'] !== 'rejected'): ?>
                            <a href="<?= BASE_URL ?>index.php?page=approve_seller&id=<?= $s['user_id'] ?>&action=rejected"
                               class="btn btn-sm btn-danger" title="Reject"
                               onclick="return confirm('Reject this seller?')">
                                <i class="bi bi-x-circle"></i>
                            </a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>index.php?page=toggle_user_status&id=<?= $s['user_id'] ?>"
                               class="btn btn-sm btn-outline-secondary" title="Toggle Account">
                                <i class="bi bi-toggle-on"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($sellers)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No sellers yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
