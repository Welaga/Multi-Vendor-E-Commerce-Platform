<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Seller Dashboard' ?> — MarketHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="bg-light">
<div class="d-flex" id="adminWrapper">
    <nav id="sidebar" class="sidebar bg-primary text-white d-flex flex-column">
        <div class="sidebar-brand p-3 border-bottom border-primary-subtle">
            <a href="<?= BASE_URL ?>index.php?page=seller_dashboard" class="text-white text-decoration-none fw-bold fs-5">
                <i class="bi bi-shop-window me-2"></i>Seller Hub
            </a>
            <div class="small opacity-75 mt-1"><?= e($_SESSION['user_name'] ?? '') ?></div>
        </div>
        <ul class="nav flex-column p-2 flex-grow-1 overflow-auto">
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='dashboard'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=seller_dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li class="sidebar-section-title">Products</li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='products'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=seller_products"><i class="bi bi-box-seam me-2"></i>My Products</a></li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='add_product'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=seller_add_product"><i class="bi bi-plus-circle me-2"></i>Add Product</a></li>
            <li class="sidebar-section-title">Sales</li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='orders'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=seller_orders"><i class="bi bi-bag-check me-2"></i>Orders</a></li>
            <li><a class="nav-link sidebar-link <?= ($activePage??'')==='profile'?'active':'' ?>" href="<?= BASE_URL ?>index.php?page=seller_profile"><i class="bi bi-person me-2"></i>Shop Profile</a></li>
        </ul>
        <div class="p-3 border-top border-primary-subtle small">
            <a href="<?= BASE_URL ?>index.php" class="text-white-50 text-decoration-none d-block mb-2"><i class="bi bi-house me-2"></i>Storefront</a>
            <a href="<?= BASE_URL ?>index.php?page=logout" class="text-warning text-decoration-none"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </div>
    </nav>
    <div class="flex-grow-1 d-flex flex-column min-vh-100" id="adminContent">
        <div class="bg-white border-bottom px-4 py-2 d-flex align-items-center justify-content-between sticky-top shadow-sm">
            <button class="btn btn-sm btn-outline-secondary" id="sidebarToggle"><i class="bi bi-list fs-5"></i></button>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= BASE_URL ?>index.php?page=seller_add_product" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Add Product
                </a>
                <a href="<?= BASE_URL ?>index.php?page=logout" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
        <div class="p-4 flex-grow-1">
