<?php $pageTitle = 'Order #' . $order['id']; require_once __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Order #<?= $order['id'] ?></h4>
    <a href="<?= BASE_URL ?>index.php?page=my_orders" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Orders
    </a>
</div>

<?= flash('order') ?>

<!-- Status Bar -->
<?php
$statuses = ['pending','processing','shipped','delivered'];
$currIdx  = array_search($order['status'], $statuses);
?>
<?php if ($order['status'] !== 'cancelled'): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between position-relative" style="padding:0 8%">
            <div class="progress position-absolute" style="top:18px;left:10%;right:10%;height:4px;z-index:0">
                <div class="progress-bar bg-primary" style="width:<?= $currIdx * 33 ?>%"></div>
            </div>
            <?php foreach ($statuses as $i => $s): ?>
            <div class="text-center position-relative" style="z-index:1">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                     style="width:36px;height:36px;background:<?= $i <= $currIdx ? '#0d6efd' : '#dee2e6' ?>;color:<?= $i <= $currIdx ? '#fff' : '#6c757d' ?>">
                    <?= $i+1 ?>
                </div>
                <div class="small mt-1 <?= $i === $currIdx ? 'fw-bold text-primary' : 'text-muted' ?>">
                    <?= ucfirst($s) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>This order was cancelled.</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Items -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold border-bottom">Order Items</div>
            <div class="card-body p-0">
                <?php foreach ($items as $i => $item): ?>
                <div class="d-flex align-items-center gap-3 p-3 <?= $i < count($items)-1 ? 'border-bottom' : '' ?>">
                    <img src="<?= e(productImage($item['image'])) ?>"
                         class="rounded" style="width:64px;height:64px;object-fit:cover" alt="">
                    <div class="flex-grow-1">
                        <div class="fw-semibold"><?= e($item['product_name']) ?></div>
                        <div class="text-muted small">Sold by: <?= e($item['seller_name']) ?></div>
                        <div class="text-muted small">Qty: <?= $item['quantity'] ?> × <?= currency($item['unit_price']) ?></div>
                    </div>
                    <div class="fw-bold"><?= currency($item['total_price']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold border-bottom">Order Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span><?= currency($order['total_amount']) ?></span>
                </div>
                <?php if ($order['discount_amount'] > 0): ?>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Discount (<?= e($order['coupon_code'] ?? '') ?>)</span>
                    <span>-<?= currency($order['discount_amount']) ?></span>
                </div>
                <?php endif; ?>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-primary"><?= currency($order['final_amount']) ?></span>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-bold border-bottom">Shipping</div>
            <div class="card-body small text-muted">
                <i class="bi bi-geo-alt me-1"></i>
                <?= nl2br(e($order['shipping_address'])) ?>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body small">
                <div class="mb-1"><strong>Payment:</strong> <?= ucwords(str_replace('_',' ',$order['payment_method'])) ?></div>
                <div class="mb-1"><strong>Status:</strong>
                    <?php $pc = $order['payment_status']==='paid'?'success':'warning text-dark'; ?>
                    <span class="badge bg-<?= $pc ?>"><?= ucfirst($order['payment_status']) ?></span>
                </div>
                <div><strong>Placed:</strong> <?= date('M j, Y g:ia', strtotime($order['created_at'])) ?></div>
            </div>
        </div>

        <!-- Admin: update status -->
        <?php if (currentRole() === 'admin'): ?>
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <h6 class="fw-bold mb-2">Update Status</h6>
                <form method="POST" action="<?= BASE_URL ?>index.php?page=update_order_status">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status" class="form-select form-select-sm mb-2">
                        <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary btn-sm w-100">Update</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
