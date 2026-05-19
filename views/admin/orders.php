<?php $pageTitle = 'Orders'; $activePage = 'orders'; require_once __DIR__ . '/../partials/admin_header.php'; ?>

<h4 class="fw-bold mb-4">All Orders</h4>
<?= flash('order') ?>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Buyer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong>#<?= $order['id'] ?></strong></td>
                    <td>
                        <div class="fw-semibold small"><?= e($order['buyer_name']) ?></div>
                    </td>
                    <td><?= $order['item_count'] ?></td>
                    <td class="fw-bold"><?= currency($order['final_amount']) ?></td>
                    <td>
                        <span class="badge <?= $order['payment_status']==='paid'?'bg-success':'bg-warning text-dark' ?>">
                            <?= ucfirst($order['payment_status']) ?>
                        </span>
                    </td>
                    <td><?php
                        $sc = match($order['status']) {
                            'delivered'=>'success','shipped'=>'info','processing'=>'primary',
                            'cancelled'=>'danger',default=>'warning text-dark'
                        };
                    ?><span class="badge bg-<?= $sc ?>"><?= ucfirst($order['status']) ?></span></td>
                    <td class="text-muted small"><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="<?= BASE_URL ?>index.php?page=order_detail&id=<?= $order['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <!-- Inline status update -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                    <li>
                                        <form method="POST" action="<?= BASE_URL ?>index.php?page=update_order_status">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <input type="hidden" name="status" value="<?= $s ?>">
                                            <button class="dropdown-item <?= $order['status']===$s?'fw-bold':'' ?>">
                                                <?= ucfirst($s) ?>
                                            </button>
                                        </form>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No orders yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
