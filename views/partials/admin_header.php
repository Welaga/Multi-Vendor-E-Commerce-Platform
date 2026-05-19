<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Panel' ?> — MarketHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="bg-light">
<div class="d-flex" id="adminWrapper">
    <nav id="sidebar" class="sidebar bg-dark text-white d-flex flex-column">
        <div class="sidebar-brand p-3 border-bottom border-secondary">
            <a href="<?= BASE_URL ?>index.php?page=admin_dashboard" class="text-white text-decoration-none fw-bold fs-5">
                <i class="bi bi-shop me-2"></i>MarketHub
            </a>
            <div class="small text-muted">Admin Panel</div>
        </div>
        <ul class="nav flex-column p-2 flex-grow-1 overflow-auto">
            <li class="nav-item">
                <a class="nav-link sidebar-link <?= ($activePage??'')==='dashboard'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=admin_dashboard">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="sidebar-section-title">Users</li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='users'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=admin_users"><i class="bi bi-people me-2"></i>All Users</a></li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='sellers'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=admin_sellers"><i class="bi bi-shop me-2"></i>Sellers</a></li>
            <li class="sidebar-section-title">Catalogue</li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='categories'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=admin_categories"><i class="bi bi-tags me-2"></i>Categories</a></li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='products'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=admin_products"><i class="bi bi-box-seam me-2"></i>Products</a></li>
            <li class="sidebar-section-title">Commerce</li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='orders'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=admin_orders"><i class="bi bi-bag-check me-2"></i>Orders</a></li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='coupons'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=admin_coupons"><i class="bi bi-ticket-perforated me-2"></i>Coupons</a></li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='payouts'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=admin_payouts"><i class="bi bi-cash-stack me-2"></i>Payouts</a></li>
        </ul>
        <div class="p-3 border-top border-secondary small">
            <a href="<?= BASE_URL ?>index.php" class="text-muted text-decoration-none d-block mb-2"><i class="bi bi-house me-2"></i>Storefront</a>
            <a href="<?= BASE_URL ?>index.php?page=logout" class="text-danger text-decoration-none"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </div>
    </nav>
    <div class="flex-grow-1 d-flex flex-column min-vh-100" id="adminContent">
        <div class="bg-white border-bottom px-4 py-2 d-flex align-items-center justify-content-between sticky-top shadow-sm">
            <button class="btn btn-sm btn-outline-secondary" id="sidebarToggle"><i class="bi bi-list fs-5"></i></button>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">Welcome, <strong><?= e($_SESSION['user_name'] ?? 'Admin') ?></strong></span>
                <a href="<?= BASE_URL ?>index.php?page=logout" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
        <div class="p-4 flex-grow-1">
