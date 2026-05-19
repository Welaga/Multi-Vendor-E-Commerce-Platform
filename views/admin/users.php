<?php $pageTitle = 'Manage Users'; $activePage = 'users'; require_once __DIR__ . '/../partials/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">All Users</h4>
    <div class="d-flex gap-2">
        <?php foreach (['','buyer','seller'] as $r): ?>
        <a href="<?= BASE_URL ?>index.php?page=admin_users<?= $r ? '&role='.$r : '' ?>"
           class="btn btn-sm <?= (($_GET['role'] ?? '') === $r) ? 'btn-primary' : 'btn-outline-secondary' ?>">
            <?= $r ? ucfirst($r) . 's' : 'All' ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<?= flash('users') ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="text-muted small"><?= $u['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle"><?= strtoupper(substr($u['name'],0,1)) ?></div>
                                <span class="fw-semibold"><?= e($u['name']) ?></span>
                            </div>
                        </td>
                        <td class="text-muted small"><?= e($u['email']) ?></td>
                        <td class="text-muted small"><?= e($u['phone'] ?? '-') ?></td>
                        <td>
                            <?php $rc = match($u['role_name']) {'admin'=>'danger','seller'=>'success',default=>'primary'}; ?>
                            <span class="badge bg-<?= $rc ?>"><?= ucfirst($u['role_name']) ?></span>
                        </td>
                        <td>
                            <span class="badge <?= $u['status']==='active'?'bg-success':'bg-secondary' ?>">
                                <?= ucfirst($u['status']) ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= BASE_URL ?>index.php?page=toggle_user_status&id=<?= $u['id'] ?>"
                                   class="btn btn-sm <?= $u['status']==='active'?'btn-outline-warning':'btn-outline-success' ?>"
                                   title="<?= $u['status']==='active'?'Deactivate':'Activate' ?>">
                                    <i class="bi bi-<?= $u['status']==='active'?'pause':'play' ?>-circle"></i>
                                </a>
                                <?php if ($u['id'] !== currentUserId()): ?>
                                <a href="<?= BASE_URL ?>index.php?page=delete_user&id=<?= $u['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this user permanently?')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
