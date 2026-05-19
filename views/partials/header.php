<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'MarketHub — Multi-Vendor Store' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="<?= BASE_URL ?>index.php">
            <i class="bi bi-shop me-1"></i>MarketHub
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <form class="d-flex mx-auto my-2 my-lg-0" style="max-width:420px;width:100%"
                  action="<?= BASE_URL ?>index.php" method="GET">
                <input type="hidden" name="page" value="products">
                <div class="input-group">
                    <input class="form-control" type="search" name="q"
                           placeholder="Search products…" value="<?= e($_GET['q'] ?? '') ?>">
                    <button class="btn btn-warning" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>index.php?page=products">
                        <i class="bi bi-grid me-1"></i>Shop
                    </a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <?php if (currentRole() === 'buyer'): ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= BASE_URL ?>index.php?page=cart">
                            <i class="bi bi-cart3 fs-5"></i>
                            <?php $cnt = cartCount(); if ($cnt > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"><?= $cnt ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="d-none d-lg-inline"><?= e($_SESSION['user_name']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <?php if (currentRole() === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?page=admin_dashboard"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                            <?php elseif (currentRole() === 'seller'): ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?page=seller_dashboard"><i class="bi bi-shop me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?page=seller_products"><i class="bi bi-box me-2"></i>My Products</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?page=seller_orders"><i class="bi bi-bag-check me-2"></i>My Orders</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?page=my_orders"><i class="bi bi-bag-check me-2"></i>My Orders</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?page=profile"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>index.php?page=logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php?page=login"><i class="bi bi-person me-1"></i>Login</a></li>
                    <li class="nav-item"><a class="btn btn-warning btn-sm ms-1 fw-semibold" href="<?= BASE_URL ?>index.php?page=register">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Category strip -->
<?php $_cats = (new Category())->getAll(); ?>
<div class="bg-white border-bottom py-1 d-none d-lg-block">
    <div class="container">
        <div class="d-flex gap-3 flex-wrap">
            <a href="<?= BASE_URL ?>index.php?page=products" class="text-decoration-none text-secondary small fw-semibold py-1">
                <i class="bi bi-grid-3x3-gap me-1"></i>All
            </a>
            <?php foreach ($_cats as $_cat): ?>
            <a href="<?= BASE_URL ?>index.php?page=products&cat=<?= $_cat['id'] ?>"
               class="text-decoration-none text-secondary small py-1">
                <i class="bi <?= e($_cat['icon']) ?> me-1"></i><?= e($_cat['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<main class="py-4"><div class="container">
