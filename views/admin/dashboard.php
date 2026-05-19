<?php $pageTitle = 'Dashboard'; $activePage = 'dashboard'; require_once __DIR__ . '/../partials/admin_header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Dashboard</h4>
    <span class="text-muted small"><?= date('l, F j, Y') ?></span>
</div>

<?= flash('users') ?><?= flash('cat') ?><?= flash('coupon') ?><?= flash('payout') ?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <?php
    $ps = $stats['platform_stats'];
    $cards = [
        ['Total Revenue', currency($ps['total_revenue'] ?? 0), 'bi-graph-up-arrow', 'success', 'All time sales'],
        ['Total Orders',  number_format($ps['total_orders'] ?? 0), 'bi-bag-check', 'primary', 'All orders'],
        ['Commission Earned', currency($ps['platform_commission'] ?? 0), 'bi-cash-coin', 'warning', '10% platform fee'],
        ['Total Users',   number_format($stats['total_users']), 'bi-people', 'info', 'Registered accounts'],
        ['Buyers',        number_format($stats['total_buyers']), 'bi-person-shopping-cart', 'primary', 'Active buyers'],
        ['Sellers',       number_format($stats['total_sellers']), 'bi-shop', 'success', 'Registered sellers'],
        ['Products',      number_format($stats['total_products']), 'bi-box-seam', 'warning', 'Listed products'],
        ['Pending Sellers', number_format($stats['pending_sellers']), 'bi-hourglass-split', 'danger', 'Awaiting approval'],
    ];
    foreach ($cards as [$label, $value, $icon, $color, $sub]):
    ?>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> rounded-3 p-3">
                    <i class="bi <?= $icon ?> fs-3"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5"><?= $value ?></div>
                    <div class="text-muted small"><?= $label ?></div>
                    <div class="text-muted" style="font-size:.72rem"><?= $sub ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pending Sellers Alert -->
<?php if ($stats['pending_sellers'] > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
    <div>
        <strong><?= $stats['pending_sellers'] ?> seller<?= $stats['pending_sellers'] > 1 ? 's' : '' ?> awaiting approval.</strong>
        <a href="<?= BASE_URL ?>index.php?page=admin_sellers" class="alert-link ms-2">Review now →</a>
    </div>
</div>
<?php endif; ?>

<!-- Quick Links -->
<div class="row g-3">
    <?php foreach ([
        ['admin_users','Manage Users','bi-people','primary'],
        ['admin_sellers','Manage Sellers','bi-shop','success'],
        ['admin_orders','View Orders','bi-bag-check','info'],
        ['admin_categories','Categories','bi-tags','warning'],
        ['admin_coupons','Coupons','bi-ticket-perforated','danger'],
        ['admin_payouts','Payouts','bi-cash-stack','secondary'],
    ] as [$page,$label,$icon,$color]): ?>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="<?= BASE_URL ?>index.php?page=<?= $page ?>"
           class="card text-decoration-none border-0 shadow-sm h-100 text-center quick-link-card">
            <div class="card-body py-4">
                <i class="bi <?= $icon ?> fs-2 text-<?= $color ?> mb-2 d-block"></i>
                <div class="small fw-semibold text-dark"><?= $label ?></div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
