<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $order['id'] ?> — MarketHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; }
        }
        body { background: #f8f9fa; }
        .invoice-header { background: linear-gradient(135deg, #0d6efd, #6610f2); }
        .invoice-logo { font-size: 1.5rem; font-weight: 800; letter-spacing: -.02em; }
    </style>
</head>
<body>
<div class="container my-5" style="max-width:800px">

    <!-- Print / Back buttons -->
    <div class="d-flex justify-content-between mb-4 no-print">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="bi bi-printer me-2"></i>Print / Save PDF
        </button>
    </div>

    <div class="card border-0 shadow">
        <!-- Invoice Header -->
        <div class="invoice-header text-white p-4 rounded-top">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="invoice-logo"><i class="bi bi-shop me-2"></i>MarketHub</div>
                    <div class="opacity-75 small mt-1">Ghana's Multi-Vendor Marketplace</div>
                    <div class="opacity-75 small">support@markethub.com</div>
                </div>
                <div class="text-end">
                    <div class="fs-4 fw-bold">INVOICE</div>
                    <div class="opacity-75">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></div>
                    <div class="opacity-75 small mt-1">
                        Issued: <?= date('F j, Y', strtotime($order['created_at'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Bill To / Status row -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Bill To</h6>
                    <div class="fw-bold"><?= e($order['buyer_name']) ?></div>
                    <div class="text-muted small"><?= e($order['buyer_email']) ?></div>
                    <?php if ($order['buyer_phone']): ?>
                    <div class="text-muted small"><?= e($order['buyer_phone']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-sm-6">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Deliver To</h6>
                    <div class="text-muted small"><?= nl2br(e($order['shipping_address'])) ?></div>
                </div>
            </div>

            <!-- Order status badges -->
            <div class="d-flex gap-3 flex-wrap mb-4 p-3 bg-light rounded">
                <div>
                    <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size:.7rem">Order Status</div>
                    <?php $sc = match($order['status']) {
                        'delivered'=>'success','shipped'=>'info','processing'=>'primary',
                        'cancelled'=>'danger',default=>'warning text-dark'
                    }; ?>
                    <span class="badge bg-<?= $sc ?> fs-6"><?= ucfirst($order['status']) ?></span>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size:.7rem">Payment</div>
                    <span class="badge <?= $order['payment_status']==='paid'?'bg-success':'bg-warning text-dark' ?> fs-6">
                        <?= ucfirst($order['payment_status']) ?>
                    </span>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size:.7rem">Method</div>
                    <span class="badge bg-secondary fs-6"><?= ucwords(str_replace('_',' ',$order['payment_method'])) ?></span>
                </div>
            </div>

            <!-- Line Items Table -->
            <table class="table table-bordered align-middle mb-4">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th class="text-center" style="width:80px">Qty</th>
                        <th class="text-end" style="width:130px">Unit Price</th>
                        <th class="text-end" style="width:130px">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($item['product_name']) ?></div>
                            <div class="text-muted small">Seller: <?= e($item['seller_name']) ?></div>
                        </td>
                        <td class="text-center"><?= $item['quantity'] ?></td>
                        <td class="text-end"><?= currency($item['unit_price']) ?></td>
                        <td class="text-end fw-semibold"><?= currency($item['total_price']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-semibold">Subtotal</td>
                        <td class="text-end"><?= currency($order['total_amount']) ?></td>
                    </tr>
                    <?php if ($order['discount_amount'] > 0): ?>
                    <tr class="text-success">
                        <td colspan="3" class="text-end fw-semibold">
                            Discount <?= $order['coupon_code'] ? '('.$order['coupon_code'].')' : '' ?>
                        </td>
                        <td class="text-end">-<?= currency($order['discount_amount']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="3" class="text-end fw-semibold">Shipping</td>
                        <td class="text-end text-success fw-semibold">FREE</td>
                    </tr>
                    <tr class="table-primary">
                        <td colspan="3" class="text-end fw-bold fs-5">TOTAL</td>
                        <td class="text-end fw-bold fs-5 text-primary"><?= currency($order['final_amount']) ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Footer note -->
            <div class="border-top pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted">
                    Thank you for shopping with <strong>MarketHub</strong>!<br>
                    For support: support@markethub.com
                </div>
                <div class="text-muted small text-end">
                    Invoice generated: <?= date('M j, Y g:ia') ?><br>
                    Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
