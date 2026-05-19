<?php
$pageTitle  = 'Seller Dashboard';
$activePage = 'dashboard';
require_once __DIR__ . '/../partials/seller_header.php';

$sellerModel  = new SellerProfile();
$orderModel   = new Order();
$productModel = new Product();

$profile  = $sellerModel->getByUserId(currentUserId());
$earnings = $orderModel->getSellerEarnings(currentUserId());
$products = $productModel->getBySeller(currentUserId());
$orders   = $orderModel->getBySeller(currentUserId());

$activeProducts = array_filter($products, fn($p) => $p['status'] === 'active');
$lowStock       = array_filter($products, fn($p) => $p['stock'] <= 5 && $p['stock'] > 0);
$outOfStock     = array_filter($products, fn($p) => $p['stock'] == 0);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><?= e($profile['shop_name'] ?? 'My Shop') ?></h4>
        <div class="text-muted small">Welcome back, <?= e($_SESSION['user_name']) ?></div>
    </div>
    <a href="<?= BASE_URL ?>index.php?page=seller_add_product" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Product
    </a>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <?php foreach ([
        ['Total Earned',   currency($earnings['total_earned'] ?? 0),   'bi-graph-up',    'success'],
        ['Pending Payout', currency($earnings['pending'] ?? 0),         'bi-hourglass',   'warning'],
        ['Paid Out',       currency($earnings['paid_out'] ?? 0),        'bi-cash-coin',   'primary'],
        ['Total Products', count($products),                             'bi-box-seam',    'info'],
        ['Active',         count($activeProducts),                       'bi-check-circle','success'],
        ['Orders Received',count($orders),                              'bi-bag-check',   'primary'],
    ] as [$label,$value,$icon,$color]): ?>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <i class="bi <?= $icon ?> fs-2 text-<?= $color ?> mb-1 d-block"></i>
                <div class="fw-bold fs-5"><?= $value ?></div>
                <div class="text-muted small"><?= $label ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Alerts -->
<?php if (!empty($lowStock)): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div><?= count($lowStock) ?> product(s) are running low on stock. <a href="<?= BASE_URL ?>index.php?page=seller_products" class="alert-link">Restock now →</a></div>
</div>
<?php endif; ?>
<?php if (!empty($outOfStock)): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-x-circle-fill fs-5"></i>
    <div><?= count($outOfStock) ?> product(s) are out of stock.</div>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Recent Products -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">My Products</h6>
                <a href="<?= BASE_URL ?>index.php?page=seller_products" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php foreach (array_slice($products, 0, 5) as $p): ?>
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <img src="<?= e(productImage($p['image'])) ?>"
                         class="rounded" style="width:44px;height:44px;object-fit:cover" alt="">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small"><?= e(truncate($p['name'],35)) ?></div>
                        <div class="text-muted" style="font-size:.75rem">Stock: <?= $p['stock'] ?></div>
                    </div>
                    <div class="text-end small">
                        <div class="fw-bold text-primary"><?= currency($p['discount_price'] ?: $p['price']) ?></div>
                        <span class="badge <?= $p['status']==='active'?'bg-success':'bg-secondary' ?>"><?= $p['status'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-box-seam display-6 opacity-25"></i>
                    <p class="mt-2 small">No products yet.</p>
                    <a href="<?= BASE_URL ?>index.php?page=seller_add_product" class="btn btn-sm btn-primary">Add First Product</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Recent Orders</h6>
                <a href="<?= BASE_URL ?>index.php?page=seller_orders" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">#<?= $order['id'] ?> — <?= e($order['buyer_name']) ?></div>
                        <div class="text-muted" style="font-size:.75rem"><?= date('M j, Y', strtotime($order['created_at'])) ?></div>
                    </div>
                    <div class="text-end small">
                        <div class="fw-bold"><?= currency($order['final_amount']) ?></div>
                        <?php $sc = match($order['status']) {'delivered'=>'success','shipped'=>'info','processing'=>'primary','cancelled'=>'danger',default=>'warning text-dark'}; ?>
                        <span class="badge bg-<?= $sc ?>"><?= ucfirst($order['status']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-bag display-6 opacity-25"></i>
                    <p class="mt-2 small">No orders yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/seller_footer.php'; ?>
