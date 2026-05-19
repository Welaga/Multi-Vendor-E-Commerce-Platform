<?php
$pageTitle  = 'Order #' . $order['id'];
$activePage = 'orders';
require_once __DIR__ . '/../partials/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-receipt me-2"></i>Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
    </h4>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>index.php?page=invoice&id=<?= $order['id'] ?>"
           target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>Print Invoice
        </a>
        <a href="<?= BASE_URL ?>index.php?page=admin_orders" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Orders
        </a>
    </div>
</div>

<?= flash('order') ?>

<!-- Order Status Bar -->
<?php $statuses = ['pending','processing','shipped','delivered'];
$currIdx = array_search($order['status'], $statuses); ?>
<?php if ($order['status'] !== 'cancelled'): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-4">
        <div class="d-flex justify-content-between position-relative" style="padding:0 8%">
            <div class="progress position-absolute" style="top:18px;left:10%;right:10%;height:4px;z-index:0">
                <div class="progress-bar bg-primary" style="width:<?= max(0, $currIdx * 33) ?>%"></div>
            </div>
            <?php foreach ($statuses as $i => $s): ?>
            <div class="text-center position-relative" style="z-index:1">
                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold"
                     style="width:38px;height:38px;background:<?= $i <= $currIdx ? '#0d6efd' : '#dee2e6' ?>;color:<?= $i <= $currIdx ? '#fff' : '#6c757d' ?>">
                    <?php if ($i < $currIdx): ?>
                        <i class="bi bi-check"></i>
                    <?php else: ?>
                        <?= $i+1 ?>
                    <?php endif; ?>
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
<div class="alert alert-danger mb-4"><i class="bi bi-x-circle me-2"></i>This order was cancelled.</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Order Items -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">Order Items</div>
            <div class="card-body p-0">
                <?php foreach ($items as $i => $item): ?>
                <div class="d-flex align-items-center gap-3 p-3 <?= $i < count($items)-1 ? 'border-bottom' : '' ?>">
                    <img src="<?= e(productImage($item['image'], $item['product_name'])) ?>"
                         class="rounded" style="width:60px;height:60px;object-fit:cover" alt="" loading="lazy">
                    <div class="flex-grow-1">
                        <div class="fw-semibold"><?= e($item['product_name']) ?></div>
                        <div class="text-muted small">Seller: <?= e($item['seller_name']) ?></div>
                        <div class="text-muted small">Qty: <?= $item['quantity'] ?> × <?= currency($item['unit_price']) ?></div>
                    </div>
                    <div class="fw-bold text-end"><?= currency($item['total_price']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Buyer Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Customer Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="small text-muted text-uppercase fw-bold mb-1">Name</div>
                        <div><?= e($order['buyer_name']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="small text-muted text-uppercase fw-bold mb-1">Email</div>
                        <div><?= e($order['buyer_email']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="small text-muted text-uppercase fw-bold mb-1">Phone</div>
                        <div><?= e($order['buyer_phone'] ?: '—') ?></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="small text-muted text-uppercase fw-bold mb-1">Shipping Address</div>
                        <div class="small"><?= nl2br(e($order['shipping_address'])) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar: Summary + Controls -->
    <div class="col-lg-4">
        <!-- Financial Summary -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">Order Summary</div>
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
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="text-success fw-semibold">FREE</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-primary"><?= currency($order['final_amount']) ?></span>
                </div>
            </div>
        </div>

        <!-- Status & Payment -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">Status</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="small text-muted fw-bold mb-1">Order Status</div>
                    <?php $sc = match($order['status']) {'delivered'=>'success','shipped'=>'info','processing'=>'primary','cancelled'=>'danger',default=>'warning text-dark'}; ?>
                    <span class="badge bg-<?= $sc ?> fs-6"><?= ucfirst($order['status']) ?></span>
                </div>
                <div class="mb-3">
                    <div class="small text-muted fw-bold mb-1">Payment</div>
                    <span class="badge <?= $order['payment_status']==='paid'?'bg-success':'bg-warning text-dark' ?> fs-6">
                        <?= ucfirst($order['payment_status']) ?>
                    </span>
                    <span class="text-muted small ms-2"><?= ucwords(str_replace('_',' ',$order['payment_method'])) ?></span>
                </div>
                <div class="small text-muted">
                    <i class="bi bi-clock me-1"></i>
                    Placed: <?= date('M j, Y g:ia', strtotime($order['created_at'])) ?>
                </div>
            </div>
        </div>

        <!-- Update Status Control -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Update Order Status</div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>index.php?page=update_order_status">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <div class="mb-3">
                        <select name="status" class="form-select">
                            <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>>
                                <?= ucfirst($s) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-check-circle me-2"></i>Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
