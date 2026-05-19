<?php $pageTitle = 'My Orders'; require_once __DIR__ . '/../partials/header.php'; ?>

<h4 class="fw-bold mb-4"><i class="bi bi-bag-check me-2"></i>My Orders</h4>
<?= flash('order') ?>

<?php if (empty($orders)): ?>
<div class="text-center py-5 text-muted">
    <i class="bi bi-bag-x display-1 opacity-25"></i>
    <h5 class="mt-4">No orders yet</h5>
    <a href="<?= BASE_URL ?>index.php?page=products" class="btn btn-primary mt-2">Start Shopping</a>
</div>
<?php else: ?>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong>#<?= $order['id'] ?></strong></td>
                    <td class="text-muted small"><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                    <td><?= $order['item_count'] ?> item<?= $order['item_count'] != 1 ? 's' : '' ?></td>
                    <td class="fw-bold"><?= currency($order['final_amount']) ?></td>
                    <td>
                        <span class="badge <?= $order['payment_status']==='paid'?'bg-success':'bg-warning text-dark' ?>">
                            <?= ucfirst($order['payment_status']) ?>
                        </span>
                    </td>
                    <td><?php
                        $sc = match($order['status']) {
                            'delivered'  => 'success',
                            'shipped'    => 'info',
                            'processing' => 'primary',
                            'cancelled'  => 'danger',
                            default      => 'warning text-dark'
                        };
                    ?><span class="badge bg-<?= $sc ?>"><?= ucfirst($order['status']) ?></span></td>
                    <td>
                        <a href="<?= BASE_URL ?>index.php?page=order_detail&id=<?= $order['id'] ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>View
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
