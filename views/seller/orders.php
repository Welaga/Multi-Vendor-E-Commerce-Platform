<?php $pageTitle = 'My Orders'; $activePage = 'orders'; require_once __DIR__ . '/../partials/seller_header.php'; ?>

<h4 class="fw-bold mb-4">Orders for My Products</h4>

<!-- Earnings Summary -->
<div class="row g-3 mb-4">
    <?php foreach ([
        ['Total Earned',   currency($earnings['total_earned'] ?? 0),   'success'],
        ['Pending Payout', currency($earnings['pending'] ?? 0),         'warning'],
        ['Paid Out',       currency($earnings['paid_out'] ?? 0),        'primary'],
    ] as [$label,$val,$color]): ?>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="text-<?= $color ?> bg-<?= $color ?> bg-opacity-10 rounded-3 p-3">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5"><?= $val ?></div>
                    <div class="text-muted small"><?= $label ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Buyer</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong>#<?= $order['id'] ?></strong></td>
                    <td><?= e($order['buyer_name']) ?></td>
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
                        <a href="<?= BASE_URL ?>index.php?page=order_detail&id=<?= $order['id'] ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Details
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-bag display-4 opacity-25 d-block mb-2"></i>
                        No orders yet. Once buyers purchase your products, orders will appear here.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/seller_footer.php'; ?>
