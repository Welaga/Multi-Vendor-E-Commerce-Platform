<?php
$pageTitle  = 'Platform Settings';
$activePage = 'settings';
require_once __DIR__ . '/../partials/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Platform Settings</h4>
    <span class="badge bg-info">Read from config.php</span>
</div>

<?= flash('settings') ?>

<div class="row g-4">
    <!-- Current Config Display -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">⚙️ Current Configuration</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tbody>
                        <?php foreach ([
                            ['Site Name',        'MarketHub'],
                            ['Base URL',          BASE_URL],
                            ['Commission Rate',   (COMMISSION_RATE * 100).'%'],
                            ['DB Host',           DB_HOST],
                            ['DB Name',           DB_NAME],
                            ['Upload Path',       UPLOAD_PATH],
                            ['PHP Version',       phpversion()],
                            ['Max Upload Size',   ini_get('upload_max_filesize')],
                            ['Post Max Size',     ini_get('post_max_size')],
                            ['Memory Limit',      ini_get('memory_limit')],
                        ] as [$key,$val]): ?>
                        <tr>
                            <td class="fw-semibold text-muted small" style="width:40%"><?= $key ?></td>
                            <td class="small font-monospace"><?= e($val) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upload Directory Status -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">📁 Upload Directory</div>
            <div class="card-body">
                <?php
                $uploadOk     = is_dir(UPLOAD_PATH);
                $uploadWrite  = $uploadOk && is_writable(UPLOAD_PATH);
                $uploadFiles  = $uploadOk ? count(glob(UPLOAD_PATH . '*')) : 0;
                ?>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <i class="bi bi-folder<?= $uploadOk ? '-check text-success' : '-x text-danger' ?> fs-2"></i>
                    <div>
                        <div class="fw-semibold"><?= $uploadOk ? 'Directory exists' : 'Directory missing' ?></div>
                        <div class="small text-muted"><?= UPLOAD_PATH ?></div>
                    </div>
                </div>
                <div class="row g-2 text-center">
                    <?php foreach ([
                        ['Exists',    $uploadOk    ? '✅' : '❌', $uploadOk    ? 'success' : 'danger'],
                        ['Writable',  $uploadWrite ? '✅' : '❌', $uploadWrite ? 'success' : 'danger'],
                        ['Files',     $uploadFiles,               'secondary'],
                    ] as [$lbl,$val,$c]): ?>
                    <div class="col-4">
                        <div class="border rounded p-2 bg-<?= $c ?> bg-opacity-10">
                            <div class="fs-5"><?= $val ?></div>
                            <div class="small text-muted"><?= $lbl ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (!$uploadWrite): ?>
                <div class="alert alert-warning mt-3 mb-0 small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Upload directory is not writable. Run: <code>chmod 755 uploads/products/</code>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions & DB Stats -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">📊 Database Summary</div>
            <div class="card-body">
                <?php
                $db = Database::connect();
                $tables = ['users','products','categories','orders','order_items','reviews','coupons','seller_earnings','cart'];
                ?>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Table</th><th class="text-end">Records</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables as $t):
                                $cnt = $db->query("SELECT COUNT(*) FROM $t")->fetchColumn();
                            ?>
                            <tr>
                                <td class="small font-monospace"><?= $t ?></td>
                                <td class="text-end"><span class="badge bg-secondary"><?= number_format($cnt) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">🔧 Quick Actions</div>
            <div class="card-body d-grid gap-2">
                <a href="<?= BASE_URL ?>index.php?page=admin_sellers" class="btn btn-outline-warning">
                    <i class="bi bi-hourglass me-2"></i>Review Pending Sellers
                </a>
                <a href="<?= BASE_URL ?>index.php?page=admin_orders" class="btn btn-outline-primary">
                    <i class="bi bi-bag-check me-2"></i>Manage Orders
                </a>
                <a href="<?= BASE_URL ?>index.php?page=admin_payouts" class="btn btn-outline-success">
                    <i class="bi bi-cash-stack me-2"></i>Process Payouts
                </a>
                <a href="<?= BASE_URL ?>index.php?page=admin_coupons" class="btn btn-outline-info">
                    <i class="bi bi-ticket-perforated me-2"></i>Create Coupon
                </a>
                <a href="<?= BASE_URL ?>index.php" target="_blank" class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-up-right me-2"></i>View Storefront
                </a>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info mt-4">
    <i class="bi bi-info-circle me-2"></i>
    To change configuration values like <strong>commission rate</strong>, <strong>base URL</strong>, or <strong>database credentials</strong>,
    edit the file <code>config/config.php</code> directly on your server.
</div>

<?php require_once __DIR__ . '/../partials/admin_footer.php'; ?>
