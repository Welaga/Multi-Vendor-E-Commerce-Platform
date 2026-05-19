<?php
/**
 * MarketHub — Multi-Vendor E-Commerce Platform
 * Main Front Controller / Router
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/helpers.php';

// Load all models
foreach (glob(__DIR__ . '/models/*.php') as $model) require_once $model;

// Load all controllers
foreach (glob(__DIR__ . '/controllers/*.php') as $ctrl) require_once $ctrl;

$page = $_GET['page'] ?? 'home';

switch ($page) {

    // ---- Public ----
    case 'home':
        $pageTitle = 'MarketHub';
        require_once __DIR__ . '/views/buyer/home.php';
        break;

    case 'products':
        (new ProductController())->index();
        break;

    case 'product_detail':
        (new ProductController())->detail();
        break;

    case 'add_review':
        (new ProductController())->addReview();
        break;

    // ---- Auth ----
    case 'login':
        $_SERVER['REQUEST_METHOD'] === 'POST'
            ? (new AuthController())->login()
            : (new AuthController())->showLogin();
        break;

    case 'register':
        $_SERVER['REQUEST_METHOD'] === 'POST'
            ? (new AuthController())->register()
            : (new AuthController())->showRegister();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    // ---- Cart ----
    case 'cart':          (new CartController())->index();       break;
    case 'cart_add':      (new CartController())->add();         break;
    case 'cart_update':   (new CartController())->update();      break;
    case 'cart_remove':   (new CartController())->remove();      break;
    case 'apply_coupon':  (new CartController())->applyCoupon(); break;

    // ---- Orders ----
    case 'checkout':              (new OrderController())->checkout();      break;
    case 'place_order':           (new OrderController())->placeOrder();    break;
    case 'my_orders':             (new OrderController())->myOrders();      break;
    case 'order_detail':          (new OrderController())->orderDetail();   break;
    case 'update_order_status':   (new OrderController())->updateStatus();  break;
    case 'mark_payout':           (new OrderController())->markPayout();    break;

    // ---- Buyer Profile ----
    case 'profile':
        requireRole('buyer');
        $userModel = new User();
        $user = $userModel->findById(currentUserId());
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = ['name' => trim($_POST['name']), 'phone' => trim($_POST['phone']), 'address' => trim($_POST['address'])];
            if (!empty($_FILES['avatar']['name'])) {
                $up = uploadImage($_FILES['avatar']);
                if ($up) $data['avatar'] = $up;
            }
            $userModel->update(currentUserId(), $data);
            if (!empty($_POST['new_password'])) {
                if (password_verify($_POST['current_password'], $user['password'])) {
                    $userModel->changePassword(currentUserId(), $_POST['new_password']);
                    flash('profile', 'Password updated!');
                } else {
                    flash('profile', 'Current password incorrect.', 'error');
                }
            } else {
                flash('profile', 'Profile updated!');
            }
            $_SESSION['user_name'] = $data['name'];
            redirect(BASE_URL . 'index.php?page=profile');
        }
        $pageTitle = 'My Profile';
        require_once __DIR__ . '/views/buyer/profile.php';
        break;

    // ---- Seller ----
    case 'seller_dashboard':
        requireRole('seller');
        require_once __DIR__ . '/views/seller/dashboard.php';
        break;

    case 'seller_products':          (new ProductController())->sellerProducts(); break;
    case 'seller_add_product_save':  (new ProductController())->addProduct();     break;
    case 'seller_edit_product':      (new ProductController())->editProduct();    break;
    case 'seller_update_product':    (new ProductController())->updateProduct();  break;
    case 'seller_delete_product':    (new ProductController())->deleteProduct();  break;
    case 'seller_orders':            (new OrderController())->sellerOrders();     break;

    case 'seller_add_product':
        requireRole('seller');
        $categories = (new Category())->getAll();
        $pageTitle = 'Add Product'; $activePage = 'add_product';
        require_once __DIR__ . '/views/seller/add_product.php';
        break;

    case 'seller_profile':
        requireRole('seller');
        $sellerModel = new SellerProfile();
        $userModel   = new User();
        $profile     = $sellerModel->getByUserId(currentUserId());
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $logo = $profile['shop_logo'];
            if (!empty($_FILES['shop_logo']['name'])) { $up = uploadImage($_FILES['shop_logo']); if ($up) $logo = $up; }
            $sellerModel->update(currentUserId(), [
                'shop_name' => trim($_POST['shop_name']),
                'shop_description' => trim($_POST['shop_description']),
                'shop_logo' => $logo,
            ]);
            $userModel->update(currentUserId(), ['name' => trim($_POST['name']), 'phone' => trim($_POST['phone'])]);
            $_SESSION['user_name'] = trim($_POST['name']);
            flash('sellerprofile', 'Profile updated!');
            redirect(BASE_URL . 'index.php?page=seller_profile');
        }
        $pageTitle = 'Shop Profile'; $activePage = 'profile';
        require_once __DIR__ . '/views/seller/profile.php';
        break;

    // ---- Admin ----
    case 'admin_dashboard':       (new AdminController())->dashboard();        break;
    case 'admin_users':           (new AdminController())->users();            break;
    case 'toggle_user_status':    (new AdminController())->toggleUserStatus(); break;
    case 'delete_user':           (new AdminController())->deleteUser();       break;
    case 'admin_sellers':         (new AdminController())->sellers();          break;
    case 'approve_seller':        (new AdminController())->approveSeller();    break;
    case 'admin_categories':      (new AdminController())->categories();       break;
    case 'admin_add_category':    (new AdminController())->addCategory();      break;
    case 'admin_delete_category': (new AdminController())->deleteCategory();   break;
    case 'admin_products':        (new AdminController())->products();         break;
    case 'admin_orders':          (new AdminController())->orders();           break;
    case 'admin_coupons':         (new AdminController())->coupons();          break;
    case 'admin_add_coupon':      (new AdminController())->addCoupon();        break;
    case 'admin_delete_coupon':   (new AdminController())->deleteCoupon();     break;
    case 'admin_payouts':         (new AdminController())->payouts();          break;

    case 'unauthorized':
        require_once __DIR__ . '/views/partials/header.php';
        echo '<div class="text-center py-5">
            <i class="bi bi-shield-exclamation display-1 text-danger opacity-50"></i>
            <h3 class="mt-4">Access Denied</h3>
            <p class="text-muted">You do not have permission to view this page.</p>
            <a href="' . BASE_URL . 'index.php" class="btn btn-primary mt-2">Go Home</a>
        </div>';
        require_once __DIR__ . '/views/partials/footer.php';
        break;

    default:
        require_once __DIR__ . '/views/partials/header.php';
        echo '<div class="text-center py-5">
            <i class="bi bi-question-circle display-1 text-muted opacity-25"></i>
            <h3 class="mt-4">Page Not Found</h3>
            <p class="text-muted">The page <code>' . e($page) . '</code> does not exist.</p>
            <a href="' . BASE_URL . 'index.php" class="btn btn-primary mt-2">Go Home</a>
        </div>';
        require_once __DIR__ . '/views/partials/footer.php';
        break;
}
