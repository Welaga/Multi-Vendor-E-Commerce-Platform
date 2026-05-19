<?php
$pageTitle  = 'Earnings';
$activePage = 'earnings';
require_once __DIR__ . '/../partials/seller_header.php';

$db       = Database::connect();
$sellerId = currentUserId();

// Detailed earnings per order item
$stmt = $db->prepare("
    SELECT se.*, o.id AS order_num, o.created_at AS order_date, o.status AS order_status,
           p.name AS product_name, p.image AS product_image, u.name AS buyer_name
    FROM seller_earnings se
    JOIN orders o ON se.order_id = o.id
    JOIN order_items oi ON se.order_item_id = oi.id
    JOIN products p ON oi.product_id = p.id
    JOIN users u ON o.buyer_id = u.id
    WHERE se.seller_id = ?
    ORDER BY se.created_at DESC
");
$stmt->execute([$sellerId]);
$earningsDetail = $stmt->fetchAll();

// Summary stats
$summary = (new Order())->getSellerEarnings($sellerId);

// Monthly breakdown
$monthStmt = $db->prepare("
    SELECT DATE_FORMAT(se.created_at, '%Y-%m') AS month,
           DATE_FORMAT(se.created_at, '%b %Y')  AS label,
           SUM(se.amount)      AS gross,
           SUM(se.commission)  AS commission,
           SUM(se.net_earning) AS net,
           COUNT(*)            AS sales
    FROM seller_earnings se
    WHERE se.seller_id = ?
    GROUP BY DATE_FORMAT(se.created_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");
$monthStmt->execute([$sellerId]);
$monthly = $monthStmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2"></i>My Earnings</h4>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <?php foreach ([
        ['Total Earned',   currency($summary['total_earned'] ?? 0), 'bi-graph-up',    'success', 'All-time net earnings'],
        ['Pending Payout', currency($summary['pending'] ?? 0),      'bi-hourglass',   'warning', 'Awaiting admin payout'],
        ['Paid Out',       currency($summary['paid_out'] ?? 0),     'bi-check-circle','primary', 'Already received'],
        ['Total Sales',    count($earningsDetail),                   'bi-bag-check',   'info',    'Order line items sold'],
    ] as [$label,$value,$icon,$color,$sub]): ?>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-<?= $color ?> bg-opacity-10 text-<?= $color ?>">
                    <i class="bi <?= $icon ?> fs-3"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5"><?= $value ?></div>
                    <div class="small text-muted"><?= $label ?></div>
                    <div class="text-muted" style="font-size:.72rem"><?= $sub ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <!-- Monthly Breakdown -->
    <?php if (!empty($monthly)): ?>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Monthly Breakdown</div>
            <div class="card-body p-0">
                <?php foreach ($monthly as $i => $m): ?>
                <div class="d-flex align-items-center justify-content-between px-3 py-2 <?= $i < count($monthly)-1 ? 'border-bottom' : '' ?>">
                    <div>
                        <div class="fw-semibold small"><?= e($m['label']) ?></div>
                        <div class="text-muted" style="font-size:.75rem"><?= $m['sales'] ?> sale<?= $m['sales']!=1?'s':'' ?></div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success small"><?= currency($m['net']) ?></div>
                        <div class="text-muted" style="font-size:.72rem">Gross: <?= currency($m['gross']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Commission info box -->
        <div class="card border-0 shadow-sm mt-3 bg-light">
            <div class="card-body small text-muted">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle me-1 text-primary"></i>How Earnings Work</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-1"><i class="bi bi-dot"></i>Sale price × quantity = <strong>Gross</strong></li>
                    <li class="mb-1"><i class="bi bi-dot"></i>Gross × 10% = <strong>Platform commission</strong></li>
                    <li class="mb-1"><i class="bi bi-dot"></i>Gross – Commission = <strong>Your net earning</strong></li>
                    <li class="mt-2 text-warning-emphasis"><i class="bi bi-clock me-1"></i>Payouts are processed by admin manually.</li>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Detailed Transactions -->
    <div class="col-lg-<?= !empty($monthly) ? '8' : '12' ?>">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span>Transaction History</span>
                <input type="text" id="tableSearch" class="form-control form-control-sm" placeholder="Search…" style="width:180px">
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Order</th>
                            <th>Buyer</th>
                            <th>Gross</th>
                            <th>Commission</th>
                            <th class="text-success">Net</th>
                            <th>Payout</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($earningsDetail as $e): ?>
                        <tr data-table-row>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= e(productImage($e['product_image'], $e['product_name'])) ?>"
                                         class="rounded" style="width:36px;height:36px;object-fit:cover" alt="" loading="lazy">
                                    <span class="small fw-semibold"><?= e(truncate($e['product_name'], 28)) ?></span>
                                </div>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>index.php?page=order_detail&id=<?= $e['order_id'] ?>" class="small fw-bold">
                                    #<?= $e['order_num'] ?>
                                </a>
                            </td>
                            <td class="small text-muted"><?= e($e['buyer_name']) ?></td>
                            <td class="small"><?= currency($e['amount']) ?></td>
                            <td class="small text-danger">-<?= currency($e['commission']) ?></td>
                            <td class="small fw-bold text-success"><?= currency($e['net_earning']) ?></td>
                            <td>
                                <span class="badge <?= $e['payout_status']==='completed'?'bg-success':'bg-warning text-dark' ?>">
                                    <?= ucfirst($e['payout_status']) ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= date('M j, Y', strtotime($e['order_date'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($earningsDetail)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-cash display-4 opacity-25 d-block mb-2"></i>
                                No earnings yet. Start selling to see your income here.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/seller_footer.php'; ?>
